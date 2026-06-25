<?php
session_start();
include('db_connection.php'); // เชื่อมต่อฐานข้อมูล

$leave_id = $_POST['leave_id'];
// ดึงวันลาจากใบลา
$leave = $conn->query("SELECT * FROM leave_requests WHERE leave_id = $leave_id")->fetch_assoc();
$day_of_week = date('N', strtotime($leave['leave_date']));

// ดึงตารางเรียนจริงของนักเรียนในวันนั้น (ปรับชื่อตารางตามจริง)
$subjects = $conn->query("SELECT subject_id, teacher_id FROM student_schedules WHERE student_id = {$leave['student_id']} AND day_of_week = $day_of_week");

while($row = $subjects->fetch_assoc()) {
    $conn->query("INSERT INTO leave_subject_branches (leave_id, subject_id, teacher_id) VALUES ($leave_id, {$row['subject_id']}, {$row['teacher_id']})");
}
$conn->query("UPDATE leave_requests SET advisor_status = 'approved' WHERE leave_id = $leave_id");
?>