<?php
// get_profile.php (ฉบับเสถียรและเคลียร์ปัญหา Syntax Error)
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

header('Content-Type: application/json; charset=utf-8');

include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบก่อนค่ะ']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// 🌟 ใช้ SELECT * เพื่อความยืดหยุ่นและป้องกันชื่อคอลัมน์ในตาราง users ไม่ตรงกันค่ะ
$sql = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'SQL Prepare ล้มเหลว: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // ส่งข้อมูลแถวผู้ใช้งานออกไปเป็น JSON หน้าบ้านจะดึงฟิลด์ที่มีไปแมปเข้าฟอร์มให้อัตโนมัติค่ะ
    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'ไม่พบข้อมูลประวัติผู้ใช้งานรหัสนี้ในตาราง users ค่ะ']);
}

$stmt->close();
exit;
?>