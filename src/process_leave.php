<?php
session_start();
include('db_connection.php'); // ไฟล์เชื่อมต่อฐานข้อมูลของพี่ทูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $type = $_POST['leave_type'];
    $date = $_POST['leave_date'];
    $reason = $_POST['leave_reason'];

    $stmt = $conn->prepare("INSERT INTO leave_requests (student_id, leave_type, leave_date, leave_reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $student_id, $type, $date, $reason);
    
    if ($stmt->execute()) {
        echo "<script>alert('ส่งใบลาเรียบร้อยแล้ว'); window.location='studentDashboard.php';</script>";
    }
}
?>