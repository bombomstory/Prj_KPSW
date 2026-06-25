<?php
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json');

$sql = "SELECT criterion_id, criterion_name 
        FROM evaluation_criteria 
        WHERE category = 'Advisor' AND status = 'active'
        ORDER BY criterion_id ASC";

$result = $conn->query($sql);
$criteria = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $criteria[] = $row;
    }
    echo json_encode([
        "success" => true,
        "criteria" => $criteria
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Query error"
    ]);
}
?>
