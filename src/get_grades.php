<?php
// get_grades.php
session_start();
include('db_connection.php'); // ดึงตัวแปรเชื่อมต่อฐานข้อมูล $conn (MySQLi)
include('lib.php');           // ดึงฟังก์ชัน getStudentID(), get_last_year(), get_last_term()

header('Content-Type: application/json; charset=utf-8');

// 1. ตรวจสอบสิทธิ์เซสชันความปลอดภัย
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

// 🔐 1. ตรวจสอบสิทธิ์ผู้ปกครอง
if ($_SESSION['user_type'] == 'parent') {

    // 👶 2. ดึง student_id ของลูกจากตาราง parents_items
    $parent_user_id = $_SESSION['user_id'];
    $student_id = 0;

    $sql_child = "SELECT pi.student_id FROM parents_items pi 
                INNER JOIN parents p ON pi.parent_id = p.parent_id 
                WHERE p.user_id = ? LIMIT 1";
    $stmt_child = $conn->prepare($sql_child);
    $stmt_child->bind_param("i", $parent_user_id);
    $stmt_child->execute();
    $res_child = $stmt_child->get_result();
    if ($row = $res_child->fetch_assoc()) {
        $student_id = $row['student_id'];
    }

}

if(!isset($student_id)){
    $student_user_id = intval($_SESSION['user_id']);
    $student_id = getStudentID($student_user_id);
} 
$academic_year = get_last_year();              
$term = get_last_term();                       

// 3. คำสั่ง SQL คิวรี่ดึงเกรดพร้อมตรวจสอบการประเมินแยกตามรายวิชาจริง (สอดคล้องกับตารางของอาจารย์)
$sql = "SELECT 
            sg.subject_id,
            sg.academic_year,
            sg.term,
            s.subject_code, 
            s.subject_name AS subject, 
            sg.grade, 
            s.credits,
            -- ดึงรหัสครูคนแรกออกมารองรับ Template ลิงก์ฝั่งหน้าบ้าน
            (SELECT ws.teacher_id FROM weekly_schedule ws WHERE ws.student_id = sg.student_id AND ws.subject_id = sg.subject_id LIMIT 1) AS teacher_id,
            
            -- 🌟 ลอจิกอัจฉริยะ: เช็คว่าจำนวนครูที่ประเมินแล้ว ครบเท่ากับจำนวนครูที่สอนจริงในวิชานั้นประจำห้องหรือไม่
            IF(
                (SELECT COUNT(DISTINCT ter.teacher_id) 
                 FROM teacher_evaluation_responses ter 
                 WHERE ter.student_id = sg.student_id 
                   AND ter.subject_id = sg.subject_id 
                   AND ter.academic_year = sg.academic_year 
                   AND ter.semester = sg.term) 
                >= 
                (SELECT COUNT(DISTINCT ws.teacher_id) 
                 FROM weekly_schedule ws 
                 WHERE ws.student_id = sg.student_id 
                   AND ws.subject_id = sg.subject_id), 
                1, 0
            ) AS is_evaluated
        FROM student_grades sg
        INNER JOIN subjects s ON sg.subject_id = s.subject_id
        WHERE sg.student_id = ? AND sg.academic_year = ? AND sg.term = ?";

$grades = [];
$stmt = $conn->prepare($sql);

if ($stmt) {
    // ผูกประเภทข้อมูลตัวแปร (i = integer, s = string) ให้เที่ยงตรงตาม Schema
    $stmt->bind_param("iss", $student_id, $academic_year, $term);
    $stmt->execute();
    
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
    $stmt->close();
}

// 4. ส่งออกข้อมูล JSON ให้หน้าบ้านใช้งาน
echo json_encode($grades, JSON_UNESCAPED_UNICODE);
exit();
?>