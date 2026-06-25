<?php
// เริ่มต้น Session และดึงไฟล์เชื่อมต่อฐานข้อมูลเดิมของระบบ
session_start();
include('db_connection.php');

header('Content-Type: application/json; charset=utf-8');

/* 💡 หมายเหตุสำหรับพี่ทูล:
อ้ำสมมติว่าในไฟล์ 'db_connection.php' ของพี่ทูล 
มีการประกาศตัวแปรเชื่อมต่อฐานข้อมูล MySQLi ไว้ในชื่อ $conn นะคะ 
(หากระบบเดิมใช้ชื่ออื่น เช่น $db หรือ $link สามารถเปลี่ยนในโค้ดด้านล่างได้เลยค่ะ)
*/

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบตัวแปรการเชื่อมต่อฐานข้อมูล ($conn) ค่ะ']);
    exit();
}

// 1. รับและตรวจสอบข้อมูลความปลอดภัยของอินพุต
$leave_id = isset($_POST['leave_id']) ? intval($_POST['leave_id']) : 0;
$rating_speed = isset($_POST['rating_speed']) ? intval($_POST['rating_speed']) : 0;
$rating_easiness = isset($_POST['rating_easiness']) ? intval($_POST['rating_easiness']) : 0;
$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

// 2. ดึงรหัสนักเรียนจาก Session ของระบบเดิม (สมมติว่าเก็บไว้ใน $_SESSION['student_id'])
// หากระบบเดิมใช้ชื่อคีย์อื่น พี่ทูลสามารถปรับเปลี่ยนให้ตรงกันได้เลยนะคะ
$student_id = isset($_SESSION['student_id']) ? intval($_SESSION['student_id']) : 101; 

if ($leave_id === 0 || $rating_speed === 0 || $rating_easiness === 0) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลและให้คะแนนการประเมินผลให้ครบถ้วนค่ะ']);
    exit();
}

// 3. ใช้ Prepared Statement สไตล์ MySQLi บันทึกข้อมูลลงตาราง
$sql = "INSERT INTO leave_satisfaction (leave_id, student_id, rating_speed, rating_easiness, feedback) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iiiis", $leave_id, $student_id, $rating_speed, $rating_easiness, $feedback);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'บันทึกผลการประเมินความพึงพอใจเรียบร้อยแล้วค่ะ']);
    } else {
        // ตรวจสอบกรณีที่มีการกดส่งคะแนนซ้ำสำหรับใบลาใบเดิม (Duplicate Entry)
        if ($conn->errno == 1062) {
            echo json_encode(['status' => 'error', 'message' => 'คุณได้ทำการประเมินใบลาฉบับนี้ไปแล้วค่ะ ขอบคุณค่ะ']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $conn->error]);
        }
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ระบบจัดเตรียมคำสั่ง SQL ผิดพลาด: ' . $conn->error]);
}

// ไม่ปิด $conn ในนี้ เพื่อป้องกันไม่ให้กระทบกับการทำงานส่วนอื่นของระบบหลักค่ะ
?>