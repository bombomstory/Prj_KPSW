<?php
// teacher_acknowledge_leave.php
session_start();
include('db_connection.php'); // ปรับตามที่พี่ทูลแจ้งค่ะ

if (isset($_POST['branch_id'])) {
    $branch_id = intval($_POST['branch_id']);
    
    // 1. อัปเดตสถานะเป็น 'acknowledged'
    $update_sql = "UPDATE leave_subject_branches SET teacher_status = 'acknowledged', teacher_action_time = NOW() WHERE branch_id = $branch_id";
    $conn->query($update_sql);
    
    // 2. ดึงข้อมูลใบลามาบันทึก Attendance
    $data_sql = "SELECT b.subject_id, r.student_id, r.leave_date 
                 FROM leave_subject_branches b 
                 JOIN leave_requests r ON b.leave_id = r.leave_id 
                 WHERE b.branch_id = $branch_id";
    $data = $conn->query($data_sql)->fetch_assoc();
    
    if ($data) {
        $student_id = $data['student_id'];
        $subject_id = $data['subject_id'];
        $date = $data['leave_date'];
        
        // 3. บันทึกลงตาราง attendance จริง
        $insert_attendance = "INSERT INTO attendance (student_id, subject_id, date, status) 
                              VALUES ($student_id, $subject_id, '$date', 'ลา')";
        $conn->query($insert_attendance);
    }
    
    echo "<script>alert('บันทึกการรับทราบการลาเรียบร้อยแล้ว'); window.location='teacherDashboard.php';</script>";
}
?>