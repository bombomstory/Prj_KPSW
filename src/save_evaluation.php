<?php
// save_evaluation.php (ไฟล์บันทึกคะแนนประเมิน)
session_start();
if (!isset($_SESSION['user_id'])) {
    die("สิทธิ์เข้าถึงระบบหมดอายุค่ะ");
}

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_id       = intval($_POST['log_id']);
    $complaint_id = intval($_POST['complaint_id']);
    $user_id      = intval($_SESSION['user_id']);
    $rating       = intval($_POST['rating']);
    $comment      = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // ตรวจสอบความถูกต้องของคะแนนให้อยู่ในช่วง 1-5 
    if ($rating < 1 || $rating > 5 || $log_id === 0 || $complaint_id === 0) {
        die("ข้อมูลพารามิเตอร์คะแนนไม่ถูกต้องค่ะ");
    }

    // สั่งเพิ่มข้อมูลเข้าสู่ฐานข้อมูลประเมิน
    $sql = "INSERT INTO `complaint_evaluations` (`complaint_id`, `log_id`, `user_id`, `rating`, `comment`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("ความผิดพลาดคิวรี่ระบบ: " . $conn->error);
    }

    $stmt->bind_param("iiiis", $complaint_id, $log_id, $user_id, $rating, $comment);
    
    if ($stmt->execute()) {
        $stmt->close();
        // ส่งผู้ใช้งานกลับไปยังหน้าสรุปผังตัวเดิม ซึ่งตอนนี้ปุ่มจะเปลี่ยนป้ายเป็นคะแนนดาวเรียบร้อยแล้วค่ะ
        echo "<script>
                alert('ขอบพระคุณสำหรับการส่งคะแนนประเมินความพึงพอใจเรียบร้อยแล้วค่ะ');
                window.location.href = 'complaint_status.php?id=" . $complaint_id . "';
              </script>";
    } else {
        // หากฝืนกดซ้ำ ข้อจำกัด UNIQUE KEY ของตารางจะทำงานและปฏิเสธตรงนี้ทันทีค่ะ
        echo "<script>
                alert('กิ่งสายงานการช่วยเหลือนี้เคยได้รับการประเมินความพึงพอใจไปแล้วค่ะ');
                window.location.href = 'complaint_status.php?id=" . $complaint_id . "';
              </script>";
    }
}
?>