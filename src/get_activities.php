<?php
// get_activities.php
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json; charset=utf-8');

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

$activities_list = [];

if ($student_id > 0) {
    // 🔍 เพิ่มการดึง sa.certificate_file ออกมาด้วยค่ะ
    $sql = "SELECT sa.activity_name, sa.award_name, sa.activity_date, sa.academic_year, sa.term, sa.certificate_file,
                   CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) AS recorder_name
            FROM `student_activities` sa
            INNER JOIN `users` u ON sa.recorded_by = u.user_id
            WHERE sa.student_id = ?
            ORDER BY sa.academic_year DESC, sa.term DESC, sa.activity_date DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities_list[] = [
            'name' => $row['activity_name'],
            'award' => $row['award_name'],
            'date' => $row['activity_date'],
            'year' => $row['academic_year'],
            'term' => $row['term'],
            'certificate' => $row['certificate_file'], // 🌟 ส่งตัวแปรไฟล์รูปออกไป
            'recorder' => $row['recorder_name']
        ];
    }
    $stmt->close();
}

echo json_encode($activities_list, JSON_UNESCAPED_UNICODE);