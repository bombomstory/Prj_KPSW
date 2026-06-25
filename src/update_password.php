<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'คุณยังไม่ได้เข้าสู่ระบบ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$new_password = $_POST['new_password'] ?? '';

// ตรวจสอบข้อมูลว่างเปล่า
if (empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกรหัสผ่านใหม่']);
    exit();
}

// ตรวจสอบความยาวรหัสผ่านใหม่
if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร']);
    exit();
}

// ทำการเข้ารหัสรหัสผ่านใหม่ (ด้วย Bcrypt เพื่อความปลอดภัย)
$new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// อัปเดตลงฐานข้อมูลได้เลย
$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$update_stmt->bind_param("si", $new_hashed_password, $user_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกรหัสผ่าน: ' . $conn->error]);
}
?>