<?php
include('db_connection.php');

if (isset($_GET['leave_id'])) {
    $leave_id = intval($_GET['leave_id']);

    // 1. อัปเดตสถานะใบลาหลักเป็น 'approved'
    $conn->query("UPDATE leave_requests SET advisor_status = 'approved' WHERE leave_id = $leave_id");

    // 2. ดึงข้อมูลใบลาเพื่อนำไปเช็คตารางสอน
    $leave = $conn->query("SELECT * FROM leave_requests WHERE leave_id = $leave_id")->fetch_assoc();
    $day_num = date('N', strtotime($leave['leave_date']));

    // 3. ดึงวิชาเรียนจากตารางสอน (ที่พี่ทูลใช้อยู่)
    // ตรงนี้พี่ทูลเช็คชื่อตารางและคอลัมน์อีกทีนะคะ (ถ้าต่างจากนี้แจ้งอ้ำได้เลย)
    $subjects = $conn->query("SELECT subject_id, teacher_id FROM student_schedules 
                             WHERE student_id = {$leave['student_id']} AND day_of_week = $day_num");

    // 4. แตกสาขา (Insert ลงตารางสาขา)
    while($row = $subjects->fetch_assoc()) {
        $conn->query("INSERT INTO leave_subject_branches (leave_id, subject_id, teacher_id) 
                      VALUES ($leave_id, {$row['subject_id']}, {$row['teacher_id']})");
    }

    echo "<script>alert('อนุมัติใบลาและกระจายงานไปรายวิชาเรียบร้อยแล้วค่ะ'); window.location='advisorDashboard.php';</script>";
}
?>