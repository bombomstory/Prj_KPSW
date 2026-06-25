<?php
// get_tuition_fees.php
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json; charset=utf-8');

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
$current_year = get_current_year();
$current_term = get_current_term();

// ดึงรายการค่าเทอมทั้งหมดของนักเรียนคนนี้ในเทอมปัจจุบัน
$sql = "SELECT `fee_id`, `fee_name`, `fee_amount`, `due_date`, `is_paid`, `academic_year`, `term`
        FROM `student_fees` 
        WHERE `student_id` = ? AND `academic_year` = ? AND `term` = ?
        ORDER BY `fee_id` ASC";

$fees = [];
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isi", $student_id, $current_year, $current_term);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }
    $stmt->close();
}

echo json_encode($fees, JSON_UNESCAPED_UNICODE);
exit();
?>