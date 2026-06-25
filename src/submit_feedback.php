<?php
include('db_connection.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_id = $_POST['leave_id'];
    $score = $_POST['score'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("UPDATE leave_requests SET feedback_score = ?, feedback_comment = ? WHERE leave_id = ?");
    $stmt->bind_param("isi", $score, $comment, $leave_id);
    $stmt->execute();
    echo "ขอบคุณสำหรับการประเมินค่ะ!";
}
?>