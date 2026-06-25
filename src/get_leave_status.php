<?php
include('db_connection.php');
$leave_id = $_GET['leave_id'];

$sql = "SELECT r.*, 
        (SELECT COUNT(*) FROM leave_subject_branches WHERE leave_id = r.leave_id AND teacher_status = 'pending') as pending_count
        FROM leave_requests r WHERE r.leave_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $leave_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

echo json_encode($data);
?>