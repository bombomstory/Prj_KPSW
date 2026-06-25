<?php
// save_document_evaluation.php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("หมดสิทธิ์เข้าถึงระบบค่ะ");
}

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id']);
    $user_id    = intval($_SESSION['user_id']);
    $rating     = intval($_POST['rating']);
    $comment    = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($rating < 1 || $rating > 5 || $request_id === 0) {
        die("ข้อมูลไม่ถูกต้องค่ะ");
    }

    $sql = "INSERT INTO `document_request_evaluations` (`request_id`, `user_id`, `rating`, `comment`) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $request_id, $user_id, $rating, $comment);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>
                alert('บันทึกผลคะแนนเรียบร้อยแล้ว ขอบพระคุณค่ะ');
                window.location.href = 'document_status.php?id=" . $request_id . "';
              </script>";
    } else {
        echo "<script>
                alert('คำขอนี้เคยได้รับการประเมินไปแล้วค่ะ');
                window.location.href = 'document_status.php?id=" . $request_id . "';
              </script>";
    }
}
?>