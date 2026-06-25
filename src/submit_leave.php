<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id']; // รหัสนักเรียนจาก Session
    $leave_type = $_POST['leave_type'];
    $leave_date = $_POST['leave_date'];
    $leave_reason = $_POST['leave_reason'];

    $stmt = $conn->prepare("INSERT INTO leave_requests (student_id, leave_type, leave_date, leave_reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $student_id, $leave_type, $leave_date, $leave_reason);
    
    if ($stmt->execute()) {
        echo "<script>alert('ส่งใบลาสำเร็จแล้วค่ะ'); window.location='studentDashboard.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด ลองใหม่อีกครั้งค่ะ'); window.history.back();</script>";
    }
}
?>