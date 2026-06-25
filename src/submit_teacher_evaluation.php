<?php
// submit_teacher_evaluation.php
session_start();
include('db_connection.php'); // เรียกใช้งานตัวแปรเชื่อมต่อ $conn (MySQLi)
include('lib.php');           // เรียกใช้งานฟังก์ชัน getStudentID

header('Content-Type: application/json; charset=utf-8');

// 1. ตรวจสอบสิทธิ์ความปลอดภัยเบื้องต้น
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่อีกครั้ง']);
    exit();
}

$student_id = getStudentID($_SESSION['user_id']);
if (!$student_id) {
    echo json_encode(['success' => false, 'error' => 'ไม่พบข้อมูลประวัตินักเรียนในระบบ']);
    exit();
}

// 2. ดักรับข้อมูล JSON Payload ที่ถูกส่งข้ามมาจาก Fetch API หน้าบ้าน
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'ข้อมูลในแบบฟอร์มไม่ถูกต้อง']);
    exit();
}

$teacher_id    = isset($data['teacher_id']) ? intval($data['teacher_id']) : 0;
$subject_id    = isset($data['subject_id']) ? intval($data['subject_id']) : 0;
$academic_year = isset($data['academic_year']) ? trim($data['academic_year']) : '';
$semester      = isset($data['semester']) ? intval($data['semester']) : 0;
$responses     = isset($data['responses']) ? $data['responses'] : [];

// ตรวจสอบค่าความว่างของข้อมูลหลัก
if ($teacher_id === 0 || $subject_id === 0 || empty($academic_year) || $semester === 0 || empty($responses)) {
    echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน กรุณาเลือกครูผู้สอนและทำประเมินให้ครบค่ะ']);
    exit();
}

// 3. เริ่มต้นเปิดระบบ Transaction เพื่อความปลอดภัยระดับฐานข้อมูล
$conn->begin_transaction();

try {
    // เตรียมคิวรี่ SQL รูปแบบ MySQLi Prepared Statement
    $sql = "INSERT INTO `teacher_evaluation_responses` 
            (`student_id`, `teacher_id`, `subject_id`, `academic_year`, `semester`, `criterion_id`, `rating`, `comments`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("ล้มเหลวในการเตรียม SQL Statement: " . $conn->error);
    }

    // วนลูปบันทึกผลการประเมินทีละหัวข้อเกณฑ์ (Criterion) ลงในตารางลูก
    foreach ($responses as $resp) {
        $criterion_id = intval($resp['criterion_id']);
        $rating       = floatval($resp['rating']);
        $comments     = isset($resp['comments']) ? trim($resp['comments']) : '';

        // ผูกประเภทตัวแปร (i = integer, s = string, d = double) 
        // student_id(i), teacher_id(i), subject_id(i), academic_year(s), semester(i), criterion_id(i), rating(d), comments(s)
        $stmt->bind_param("iiissids", $student_id, $teacher_id, $subject_id, $academic_year, $semester, $criterion_id, $rating, $comments);
        
        // สั่งรันบันทึก
        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดระหว่างบันทึกข้อมูล: " . $stmt->error);
        }
    }

    // ปิด Statement การทำงาน
    $stmt->close();
    
    // หากทำงานครบถ้วนทุกข้อไร้ปัญหา สั่งบันทึกข้อมูลจริงลงฐานข้อมูลทั้งหมดพร้อมกัน (Commit)
    $conn->commit();
    
    // ส่งสัญญาณบอกหน้าบ้านว่าทำสำเร็จแล้ว
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // ❌ หากข้อใดข้อหนึ่ง Error ระบบจะคืนค่าเดิมก่อนทำทั้งหมดทันที (Rollback) ข้อมูลจะไม่พังแน่นอนค่ะ
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

exit();
?>