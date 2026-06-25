<?php
session_start();
include('db_connection.php');
header('Content-Type: application/json; charset=utf-8');

// ดึงไอดีเด็กจากเซสชัน
$student_id = isset($_SESSION['student_id']) ? intval($_SESSION['student_id']) : 1;

$leave_type = isset($_POST['leave_type']) ? $_POST['leave_type'] : '';
$leave_date = isset($_POST['leave_date']) ? $_POST['leave_date'] : '';
$leave_reason = isset($_POST['leave_reason']) ? trim($_POST['leave_reason']) : '';

if (empty($leave_type) || empty($leave_date) || empty($leave_reason)) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วนค่ะ']);
    exit();
}

// บันทึกลงตาราง leave_requests
$sql = "INSERT INTO leave_requests (student_id, leave_type, leave_reason, leave_date, parent_status, advisor_status, overall_status) VALUES (?, ?, ?, ?, 'pending', 'pending', 'processing')";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isss", $student_id, $leave_type, $leave_reason, $leave_date);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$conn->close();
?>