<?php

function connectDB() {
    global $conn;
    return $conn;
} 

function get_current_term() {
    global $conn;
    $sql = "SELECT * FROM site_config WHERE config_name = 'current_term'";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['config_value'];
}

function get_current_year() {
    global $conn;
    $sql = "SELECT * FROM site_config WHERE config_name = 'current_acadyear' and status = 'active'";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['config_value'];
}

function get_last_term() {
    global $conn;
    $sql = "SELECT * FROM site_config WHERE config_name = 'last_term'";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['config_value'];
}

function get_last_year() {
    global $conn;
    $sql = "SELECT * FROM site_config WHERE config_name = 'last_acadyear' and status = 'active'";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['config_value'];
}

function getStudentID(int $user_id) {
    global $conn; // ใช้ตัวแปรเชื่อมต่อฐานข้อมูลจากภายนอก

    $student_id = null;

    $stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($student_id);
        $stmt->fetch();
        $stmt->close();
    }

    return $student_id; // ถ้าไม่พบจะคืนค่า null
}

function getStudentClassroom(int $user_id) {
    global $conn;

    $classroom_name = null;

    $stmt = $conn->prepare("
        SELECT c.classroom_name
        FROM students s
        INNER JOIN student_classrooms sc ON s.student_id = sc.student_id
        INNER JOIN classrooms c ON sc.classroom_id = c.classroom_id
        WHERE s.user_id = ?
        LIMIT 1
    ");

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($classroom_name);
        $stmt->fetch();
        $stmt->close();
    }

    return $classroom_name;
}

function getAdvisorClassroomIdByUserId(int $user_id) {
    global $conn;
    $sql = "SELECT ai.classroom_id
            FROM advisors a
            JOIN advisor_items ai ON a.advisor_id = ai.advisor_id
            WHERE a.user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($classroom_id);
    
    if ($stmt->fetch()) {
        return $classroom_id;
    } else {
        return null;
    }
}

function getStudentClassroomIdByUserId(int $user_id) {
    global $conn;
    // หานักเรียนที่มี user_id ตรงกับที่ส่งเข้ามา
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];

        // ค้นหา classroom_id ล่าสุดจาก student_classrooms
        $stmt2 = $conn->prepare("
            SELECT classroom_id 
            FROM student_classrooms 
            WHERE student_id = ? 
            ORDER BY end_date DESC 
            LIMIT 1
        ");
        $stmt2->bind_param("i", $student_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($row2 = $result2->fetch_assoc()) {
            return $row2['classroom_id'];
        }
    }

    return null; // ไม่พบข้อมูล
}


function getLastAcademicYearAndTerm() {
    global $conn;
    $sql = "SELECT config_name, config_value FROM site_config 
            WHERE config_name IN ('last_acadyear', 'last_term') AND status = 'active'";
    
    $result = $conn->query($sql);
    $config = [];

    while ($row = $result->fetch_assoc()) {
        $config[$row['config_name']] = $row['config_value'];
    }

    // ตรวจสอบว่าข้อมูลครบหรือไม่
    if (!isset($config['last_acadyear']) || !isset($config['last_term'])) {
        return null; // หรือโยน error ได้ตามต้องการ
    }

    return [
        'academic_year' => $config['last_acadyear'],
        'term' => $config['last_term']
    ];
}


/**
 * ฟังก์ชันตรวจสอบยอดค้างชำระค่าเทอม
 * @param int $student_id รหัสนักเรียน
 * @param string $academic_year ปีการศึกษา (เช่น '2567')
 * @param int $term ภาคเรียน (เช่น 1 หรือ 2)
 * @return bool คืนค่า true หากมียอดค้างชำระ (is_paid = 0) และ false หากชำระครบแล้ว
 */
function hasUnpaidFees($student_id, $academic_year, $term) {
    global $conn; // เรียกใช้งานตัวแปรเชื่อมต่อฐานข้อมูลจากภายนอก

    // เตรียมคำสั่ง SQL เพื่อเช็ครายการที่สถานะการจ่ายเป็น 0 (ยังไม่จ่าย)
    $sql = "SELECT COUNT(*) AS unpaid_count 
            FROM student_fees 
            WHERE student_id = ? 
            AND academic_year = ? 
            AND term = ? 
            AND is_paid = 0";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // ผูกค่าตัวแปร: i = integer (student_id), s = string (academic_year), i = integer (term)
        $stmt->bind_param("isi", $student_id, $academic_year, $term);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $unpaid_count = $row['unpaid_count'];
        $stmt->close();

        // ถ้าพบรายการค้างชำระตั้งแต่ 1 รายการขึ้นไป ให้ส่งค่ากลับเป็น true
        return $unpaid_count > 0;
    }

    return false;
}

?>