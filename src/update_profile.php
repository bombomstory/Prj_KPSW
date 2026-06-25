<?php
// update_profile.php
session_start();
include('db_connection.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่ค่ะ']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

if (empty($phone) || empty($email) || empty($address)) {
    echo json_encode(['success' => false, 'error' => 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วนค่ะ']);
    exit;
}

// เปิดระบบ Transaction ป้องกันข้อมูลบันทึกครึ่งๆ กลางๆ
$conn->begin_transaction();

try {
    $profile_picture_name = null;

    // 1. ตรวจสอบเงื่อนไขหากนักเรียนมีการอัปโหลดรูปภาพโปรไฟล์เข้ามาใหม่
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['profile_pic']['tmp_name'];
        $file_name = $_FILES['profile_pic']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // กรองนามสกุลไฟล์ที่ปลอดภัยและอนุญาตให้เข้าสู่เซิร์ฟเวอร์
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            throw new Exception('ระบบอนุญาตเฉพาะไฟล์รูปภาพประเภทนามสกุล .jpg, .jpeg, .png และ .gif เท่านั้นค่ะ');
        }
        
        // ตั้งชื่อไฟล์ใหม่ด้วยรหัสและ Timestamp ป้องกันชื่อซ้ำและปัญหาสระภาษาไทยเพี้ยน
        $new_file_name = "profile_stud_" . $user_id . "_" . time() . "." . $file_ext;
        $upload_dir = "images/";
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // ย้ายไฟล์เข้าสู่โฟลเดอร์ปลายทาง
        if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            $profile_picture_name = $new_file_name;
            
            // 🌟 ลบรูปภาพโปรไฟล์เก่าออกจากเซิร์ฟเวอร์เพื่อไม่ให้เป็นขยะตกค้าง
            $res_old = $conn->query("SELECT `profile_picture` FROM `users` WHERE `user_id` = " . $user_id);
            if ($res_old && $old_row = $res_old->fetch_assoc()) {
                $old_file = $upload_dir . $old_row['profile_picture'];
                if (!empty($old_row['profile_picture']) && file_exists($old_file)) {
                    @unlink($old_file);
                }
            }
        } else {
            throw new Exception('ไม่สามารถย้ายไฟล์รูปภาพเข้าสู่โฟลเดอร์ปลายทางได้ค่ะ');
        }
    }

    // 2. ดำเนินการอัปเดตข้อมูลลงตารางฐานข้อมูล users
    if ($profile_picture_name !== null) {
        // อัปเดตทั้งข้อความและชื่อไฟล์รูปภาพใหม่
        $sql = "UPDATE `users` SET `phone` = ?, `email` = ?, `address` = ?, `profile_picture` = ? WHERE `user_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $phone, $email, $address, $profile_picture_name, $user_id);
    } else {
        // อัปเดตเฉพาะรายละเอียดข้อความกรณีไม่ได้เปลี่ยนรูปภาพ
        $sql = "UPDATE `users` SET `phone` = ?, `email` = ?, `address` = ? WHERE `user_id` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $phone, $email, $address, $user_id);
    }

    if (!$stmt->execute()) {
        throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error);
    }
    
    $stmt->close();
    $conn->commit();
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback(); // ยกเลิกการอัปเดตทั้งหมดหากเกิดปัญหาผิดพลาด
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>