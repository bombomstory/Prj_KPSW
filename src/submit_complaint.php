<?php
session_start();
header('Content-Type: application/json');
$response = [];

try {
    include('db_connection.php');
    include('lib.php');

    $user_id = $_SESSION['user_id'] ?? 0;
    $type = $_POST['complaintType'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $detail = $_POST['detail'] ?? '';

    if (!$user_id || !$type || !$subject || !$detail) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบ');
    }

    // จัดการไฟล์
    $filePath = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/complaints/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $targetFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $filePath = $targetFile;
        } else {
            throw new Exception('อัปโหลดไฟล์ล้มเหลว');
        }
    }

    // Map type → id (สมมติไว้ก่อนว่า: complaint=1, suggestion=2, problem=3, request=4)
    $typeMap = [
        'complaint' => 1,
        'suggestion' => 2,
        'problem' => 3,
        'request' => 4
    ];
    $typeId = $typeMap[$type] ?? 0;

    if (!$typeId) {
        throw new Exception('ประเภทไม่ถูกต้อง');
    }

    // บันทึกข้อมูล
    $stmt = $conn->prepare("INSERT INTO complaints (user_id, complaint_type_id, subject, detail, file_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $typeId, $subject, $detail, $filePath);
    $stmt->execute();

    $response = ['success' => true, 'message' => 'ส่งเรื่องเรียบร้อยแล้ว'];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
