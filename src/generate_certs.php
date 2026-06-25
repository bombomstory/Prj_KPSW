<?php
// generate_certs.php - ฉบับ No-GD Library ดึงรูปจากคลาวด์จำลองพาธอัตโนมัติ
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    die("กรุณาล็อกอินเข้าระบบก่อนรันไฟล์นี้เพื่อให้เซสชันจำค่า user_id ค่ะ");
}

$user_id = intval($_SESSION['user_id']);
$target_dir = "uploads/portfolio/{$user_id}/";

// สั่งสร้างโฟลเดอร์รองรับโครงสร้างใหม่ของพี่ทูล
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// รายการไฟล์และข้อความแสตมป์บนใบประกาศ
$certificate_files = [
    'cert_science_2567.jpg'   => 'SCIENCE+OLYMPIAD+2024',
    'cert_robot_2567.jpg'     => 'ROBOTIC+COMPUTER+CONTEST',
    'cert_sports_2568.jpg'    => 'KPSW+SPORTS+TOURNAMENT',
    'cert_creative_2568.jpg'  => 'CREATIVE+ACADEMIC+EXHIBITION'
];

echo "<h3>🚀 [No-GD Mode] กำลังดาวน์โหลดไฟล์รูปใบประกาศจำลองลงพาธ: {$target_dir}</h3><hr>";

foreach ($certificate_files as $filename => $text) {
    $file_path = $target_dir . $filename;
    
    // ยิงไปดึงรูปเกียรติบัตรจำลองสีครีม-เทาขนาดมาตรฐาน A4 จากเว็บ Placeholder 
    $mockup_url = "https://placehold.co/842x595/fdfbf7/1e293b.jpg?text=" . $text;
    
    // ดึงข้อมูลดิบของไฟล์ภาพ
    $image_data = @file_get_contents($mockup_url);
    
    if ($image_data !== false) {
        // บันทึกไฟล์ลงไดเรกทอรีปลายทาง
        file_put_contents($file_path, $image_data);
        echo "✅ ดาวน์โหลดสำเร็จ: <a href='{$file_path}' target='_blank'><strong>{$filename}</strong></a><br>";
    } else {
        echo "❌ เกิดข้อผิดพลาดในการดึงรูปไฟล์: {$filename} (กรุณาเช็กการเชื่อมต่ออินเทอร์เน็ตค่ะ)<br>";
    }
}

echo "<hr>🎉 <strong>เสร็จเรียบร้อยค่ะ!</strong> ไฟล์หลักฐานเข้าไปอยู่ในระบบครบถ้วน พร้อมให้พี่ทูลรันระบบสั่งพิมพ์เล่ม Portfolio PDF แล้วค่ะ";
?>