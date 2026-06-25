<?php
// save_complaint.php (ฉบับสมบูรณ์ รองรับไฟล์แนบทางเลือกและใช้งาน mysqli)
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. ตรวจสอบสิทธิ์การเข้าถึงระบบ
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้งค่ะ']);
    exit();
}

include('db_connection.php'); // ดึงตัวแปรเชื่อมต่อฐานข้อมูล $conn (mysqli) มาใช้งาน

try {
    $user_id           = intval($_SESSION['user_id']);
    $complaint_type_id = isset($_POST['complaint_type_id']) ? intval($_POST['complaint_type_id']) : 0;
    $subject           = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $detail            = isset($_POST['detail']) ? trim($_POST['detail']) : '';
    $file_path         = null; // 🌟 ตั้งค่าเริ่มต้นเป็น NULL หากนักเรียนไม่ได้แนบไฟล์มา

    // 2. ตรวจสอบข้อมูลบังคับ (ประเภท, หัวข้อ, รายละเอียด)
    if ($complaint_type_id === 0 || empty($subject) || empty($detail)) {
        throw new Exception('กรุณากรอกข้อมูลและเลือกประเภทเรื่องให้ครบถ้วนค่ะ');
    }

    // =============================================================
    // 🌟 [จุดที่เพิ่ม] ลอจิกการจัดการไฟล์แนบ (อัปโหลดรูปภาพ/เอกสารที่เป็นทางเลือก)
    // =============================================================
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'uploads/'; // กำหนดชื่อโฟลเดอร์ปลายทางสำหรับเก็บไฟล์
        
        // ตรวจสอบว่ามีโฟลเดอร์นี้หรือยัง ถ้ายังไม่มีให้ระบบสร้างขึ้นมาอัตโนมัติ
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // ดึงนามสกุลไฟล์ออกมาตรวจสอบเพื่อความปลอดภัยของระบบ
        $file_info = pathinfo($_FILES['file']['name']);
        $extension = strtolower($file_info['extension']);
        
        // กำหนดประเภทไฟล์ที่อนุญาตให้ส่งขึ้นระบบได้
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array($extension, $allowed_extensions)) {
            throw new Exception('นามสกุลไฟล์ไม่ถูกต้อง อนุญาตเฉพาะรูปภาพ (JPG, PNG) หรือไฟล์เอกสาร (PDF, DOC) เท่านั้นค่ะ');
        }

        // ตั้งชื่อไฟล์ใหม่โดยใช้เวลาปัจจุบันผสมรหัสสุ่ม เพื่อป้องกันไม่ให้ชื่อไฟล์ซ้ำกันในเซิร์ฟเวอร์
        $new_file_name = 'complaint_' . time() . '_' . uniqid() . '.' . $extension;
        $target_file   = $upload_dir . $new_file_name;

        // ทำการย้ายไฟล์จากโฟลเดอร์ชั่วคราว (Temporary) ไปยังโฟลเดอร์เป้าหมายจริง
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file_path = $target_file; // 🌟 เปลี่ยนค่าจาก NULL เป็นพาธไฟล์จริงเพื่อเตรียมบันทึกคิวรี่ค่ะ
        } else {
            throw new Exception('เกิดข้อผิดพลาดในระบบ ไม่สามารถอัปโหลดไฟล์เข้าสู่โฟลเดอร์ได้ค่ะ');
        }
    }
    // =============================================================


    // 3. เตรียมคำสั่ง SQL และทำการบันทึกข้อมูล (ปล่อยฟิลด์ status ทำงานด้วยค่า Default 'รอพิจารณา')
    $sql = "INSERT INTO `complaints` (`user_id`, `complaint_type_id`, `subject`, `detail`, `file_path`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("ความผิดพลาดทางระบบ (Prepare ล้มเหลว): " . $conn->error);
    }

    // ผูกค่าตัวแปรเข้ากับเครื่องหมาย ? (i = integer, s = string)
    $stmt->bind_param("iisss", $user_id, $complaint_type_id, $subject, $detail, $file_path);
    
    // 4. ประมวลผลคำสั่งบันทึก
    if ($stmt->execute()) {
        $last_id = $conn->insert_id; // ดึงเลข ID คีย์หลักล่าสุดของแถวที่เพิ่งเพิ่มเข้าไป
        $stmt->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'บันทึกเรื่องร้องเรียน/แจ้งเหตุสำเร็จเรียบร้อยแล้วค่ะ',
            'complaint_id' => $last_id
        ]);
    } else {
        throw new Exception("ไม่สามารถบันทึกข้อมูลลงฐานข้อมูลได้: " . $stmt->error);
    }

} catch (Exception $e) {
    // 💡 ระบบ Safety: หากบันทึกฐานข้อมูลไม่สำเร็จ แต่ไฟล์ถูกอัปโหลดขึ้นเซิร์ฟเวอร์ไปแล้ว ให้สั่งลบไฟล์ทิ้งทันทีเพื่อไม่ให้รกพื้นที่ค่ะ
    if ($file_path !== null && file_exists($file_path)) {
        unlink($file_path);
    }
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit();
?>