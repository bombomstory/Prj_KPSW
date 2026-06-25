<?php
// setup_mockup.php - สคริปต์เตรียมความพร้อมระบบข้อมูลและไฟล์ PDF จำลองอัตโนมัติ
header('Content-Type: text/html; charset=utf-8');
include('db_connection.php');

echo "<div style='font-family: \"Kanit\", sans-serif; padding: 30px; background: #f8f9fa; border-radius: 8px; max-width: 600px; margin: 40px auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>";
echo "<h2 style='color: #0d6efd; margin-bottom: 20px;'>🚀 ระบบเตรียมข้อมูลและไฟล์ทดสอบคำขอเอกสาร</h2>";

// ==========================================
// สเต็ปที่ 1: ตรวจสอบและสร้างโฟลเดอร์เก็บไฟล์อัตโนมัติ
// ==========================================
$upload_dir = "uploads/documents/";
if (!file_exists($upload_dir)) {
    if (mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✅ สร้างโฟลเดอร์ <cod style='background:#e9ecef; padding:2px 6px;'>$upload_dir</cod> สำเร็จ</p>";
    } else {
        die("<p style='color: red;'>❌ ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บเอกสารได้ กรุณาตรวจสอบสิทธิ์โฟลเดอร์ค่ะ</p>");
    }
}

// ==========================================
// สเต็ปที่ 2: สร้างไฟล์ PDF ตัวจริง (จำลองโครงสร้างไฟล์เพื่อรองรับการดาวน์โหลด)
// ==========================================
$minimal_pdf_content = "%PDF-1.5\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000111 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n190\n%%EOF";

$mockup_files = [
    'student_certificate_64001.pdf',
    'transcript_64001.pdf',
    'conduct_certificate_64001.pdf',
    'health_certificate_64001.pdf'
];

foreach ($mockup_files as $filename) {
    $full_path = $upload_dir . $filename;
    file_put_contents($full_path, $minimal_pdf_content);
}
echo "<p style='color: green;'>✅ เจนเนอเรตไฟล์ PDF สแกนจำลองทั้ง 4 ประเภทลงโฟลเดอร์เรียบร้อยแล้ว</p>";

// ==========================================
// สเต็ปที่ 3: เคลียร์และบันทึกข้อมูลจำลองลงตารางฐานข้อมูลจริง
// ==========================================
try {
    // ล้างข้อมูลเก่าเพื่อป้องกัน Error คีย์ซ้ำ
    $conn->query("DELETE FROM `document_request_evaluations` WHERE `request_id` IN (1, 2, 3, 4)");
    $conn->query("DELETE FROM `document_request_logs` WHERE `request_id` IN (1, 2, 3, 4)");
    $conn->query("DELETE FROM `document_requests` WHERE `id` IN (1, 2, 3, 4)");

    // แทรกคำขอเอกสาร 4 ประเภทหลัก (สถานะ 'เสร็จสิ้น' พร้อมผูกชื่อไฟล์สแกนตรงตัว)
    // หมายเหตุ: สมมติ user_id คีย์นอกของนักเรียนผู้เทสอบคือเลข 1 ค่ะ
    $sql_requests = "INSERT INTO `document_requests` (`id`, `user_id`, `document_type_id`, `qty`, `purpose`, `status`, `file_path`, `is_downloaded`, `created_at`) VALUES
    (1, 1, 3, 1, 'ใช้สำหรับเปิดบัญชีธนาคารเพื่อรับทุนการศึกษาโรงเรียน', 'เสร็จสิ้น', 'student_certificate_64001.pdf', 0, NOW()),
    (2, 1, 1, 2, 'ใช้ยื่นสมัครเรียนต่อระดับชั้นมหาวิทยาลัย คณะวิศวกรรมศาสตร์', 'เสร็จสิ้น', 'transcript_64001.pdf', 0, NOW()),
    (3, 1, 4, 1, 'ใช้ประกอบการสมัครเข้ารับการคัดเลือกนักเรียนทุนความประพฤติดี', 'เสร็จสิ้น', 'conduct_certificate_64001.pdf', 0, NOW()),
    (4, 1, 5, 1, 'ใช้ยื่นรายงานตัวเข้าค่ายวิชาการโอลิมปิกวิชาการ (สอวน.)', 'เสร็จสิ้น', 'health_certificate_64001.pdf', 0, NOW())";
    
    $conn->query($sql_requests);

    // แทรกประวัติขั้นตอนการอนุมัติ (Logs) เพื่อความสมบูรณ์แบบของผัง Mermaid.js
    $sql_logs = "INSERT INTO `document_request_logs` (`request_id`, `actor_name`, `action_type`, `notes`, `created_at`) VALUES
    (1, 'คุณครูที่ปรึกษา (อภิชาติ)', 'ครูที่ปรึกษาพิจารณาอนุมัติ', 'ข้อมูลถูกต้อง อนุมัติยื่นเรื่องต่อ', NOW()),
    (1, 'นางสาวใจดี (เจ้าหน้าที่ทะเบียน)', 'เสร็จสิ้น', 'สแกนอัปโหลดไฟล์ใบรับรองเข้าระบบแล้ว', NOW()),
    (2, 'คุณครูที่ปรึกษา (อภิชาติ)', 'ครูที่ปรึกษาพิจารณาอนุมัติ', 'อนุมัติเพื่อใช้สมัครศึกษาต่อระดับอุดมศึกษา', NOW()),
    (2, 'นางสาวใจดี (เจ้าหน้าที่ทะเบียน)', 'เสร็จสิ้น', 'เจ้าหน้าที่สแกนไฟล์ Transcript ลงระบบแล้ว', NOW()),
    (3, 'คุณครูที่ปรึกษา (อภิชาติ)', 'ครูที่ปรึกษาพิจารณาอนุมัติ', 'นักเรียนมีความประพฤติเรียบร้อย อนุมัติค่ะ', NOW()),
    (3, 'นางสาวใจดี (เจ้าหน้าที่ทะเบียน)', 'เสร็จสิ้น', 'ฝ่ายกิจการนักเรียนอัปโหลดไฟล์สแกนเรียบร้อย', NOW()),
    (4, 'คุณครูที่ปรึกษา (อภิชาติ)', 'ครูที่ปรึกษาพิจารณาอนุมัติ', 'รับทราบวัตถุประสงค์ อนุมัติส่งเรื่องต่อ', NOW()),
    (4, 'นางสาวใจดี (เจ้าหน้าที่ทะเบียน)', 'เสร็จสิ้น', 'เจ้าหน้าที่งานพยาบาลอัปโหลดไฟล์สแกนแล้ว', NOW())";
    
    $conn->query($sql_logs);

    echo "<p style='color: green;'>✅ บันทึกข้อมูลคำขอและประวัติสเต็ปลงตารางสากลเรียบร้อยแล้วค่ะ</p>";
    echo "<hr style='border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;'>";
    echo "<p style='color: #0f5132; font-weight: bold; text-align: center; margin-bottom: 0;'>🎉 พร้อมทดสอบดาวน์โหลดคำขอที่ ID 1, 2, 3, 4 ได้เลยค่ะ!</p>";

} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>❌ เกิดข้อผิดพลาดทาง SQL: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";
?>