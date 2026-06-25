<?php
// get_schedule.php - ไฟล์ API สำหรับดึงข้อมูลตารางเรียนรายสัปดาห์ส่งออกเป็นรูปแบบ JSON
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json; charset=utf-8');

// ดักจับสิทธิ์การเข้าถึงข้อมูล
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'กรุณาล็อกอินก่อนใช้งานค่ะ']);
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

$current_term = get_current_term();
$current_year = get_current_year();

$classroom_name = "ไม่ระบุห้องเรียน";
$classroom_id = 0;

// 1. 🔍 ตรวจสอบห้องเรียนของนักเรียนในปีการศึกษาปัจจุบัน
$class_sql = "SELECT sc.classroom_id, c.classroom_name 
              FROM `student_classrooms` sc
              INNER JOIN `classrooms` c ON sc.classroom_id = c.classroom_id
              WHERE sc.student_id = ? AND sc.academic_year = ? LIMIT 1";
              
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("is", $student_id, $current_year);
$class_stmt->execute();
$class_res = $class_stmt->get_result()->fetch_assoc();
$class_stmt->close();

if ($class_res) {
    $classroom_id = $class_res['classroom_id'];
    $classroom_name = $class_res['classroom_name'];
}

// 2. 🧠 คิวรี่รวมร่าง 6 ตารางเพื่อดึงวิชา คาบเรียน ห้องเรียน และตึกอาคารเรียนตัวจริง
$schedule_array = [];
if ($classroom_id > 0) {
    $sched_sql = "SELECT cs.day_of_week, cs.period,
                         r.room_name, b.building_name, 
                         sj.subject_code, sj.subject_name, 
                         CONCAT(u_t.prefix_name, u_t.first_name, ' ', u_t.last_name) AS teacher_full_name
                  FROM `class_schedules` cs
                  INNER JOIN `subjects` sj ON cs.subject_id = sj.subject_id
                  INNER JOIN `teachers` t ON cs.teacher_id = t.teacher_id
                  INNER JOIN `users` u_t ON t.user_id = u_t.user_id
                  INNER JOIN `rooms` r ON cs.room_id = r.room_id
                  INNER JOIN `buildings` b ON r.building_id = b.building_id
                  WHERE cs.classroom_id = ? 
                    AND cs.academic_year = ? 
                    AND cs.term = ?
                  ORDER BY cs.period ASC";
                  
    $sched_stmt = $conn->prepare($sched_sql);
    $sched_stmt->bind_param("isi", $classroom_id, $current_year, $current_term);
    $sched_stmt->execute();
    $sched_res = $sched_stmt->get_result();
    while ($row = $sched_res->fetch_assoc()) {
        $schedule_array[] = $row;
    }
    $sched_stmt->close();
}

// ส่งออกข้อมูลเพื่อให้ฝั่ง JavaScript หน้าบ้านนำไปลูปใช้งาน
echo json_encode([
    'classroom_name' => $classroom_name,
    'schedule' => $schedule_array
], JSON_UNESCAPED_UNICODE);