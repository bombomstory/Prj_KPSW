<?php
// save_document_request.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'เซสชันหมดอายุ กรุณาล็อกอินใหม่ค่ะ']);
    exit();
}

include('db_connection.php'); // เรียกใช้ $conn (mysqli)

try {
    $user_id          = intval($_SESSION['user_id']);
    $document_type_id = isset($_POST['document_type_id']) ? intval($_POST['document_type_id']) : 0;
    $qty              = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    $purpose          = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';

    if ($document_type_id === 0 || empty($purpose) || $qty < 1) {
        throw new Exception('กรุณากรอกรายละเอียดวัตถุประสงค์ให้ครบถ้วนค่ะ');
    }

    // คำสั่งบันทึกข้อมูลคำขอ (ปล่อยให้ status ทำงานด้วยค่าดีฟอลต์คือ 'รอพิจารณา' ค่ะ)
    $sql = "INSERT INTO `document_requests` (`user_id`, `document_type_id`, `qty`, `purpose`) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("ระบบคิวรี่ขัดข้อง: " . $conn->error);
    }

    $stmt->bind_param("iiis", $user_id, $document_type_id, $qty, $purpose);
    
    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        $stmt->close();
        echo json_encode([
            'success' => true,
            'message' => 'ส่งคำร้องขอเอกสารทางการศึกษาสำเร็จเรียบร้อยแล้วค่ะ',
            'request_id' => $last_id
        ]);
    } else {
        throw new Exception("ไม่สามารถบันทึกข้อมูลได้: " . $stmt->error);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit();
?>