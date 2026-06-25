<?php
/* $servername = "localhost";
$username = "kpswacth_reg";
$password = "regP@ssw0rd";
$dbname = "kpswacth_reg"; */

$servername = "db";
$username = "kpswacth_reg";
$password = "regP@ssw0rd";
$dbname = "kpswacth_reg";

// การตั้งค่าฐานข้อมูล
$db_host = $servername;
$db_name = $dbname;  // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ
$db_user = $username;  // เปลี่ยนเป็น username ของคุณ
$db_pass = $password;  // เปลี่ยนเป็น password ของคุณ
// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");  // ตั้งค่า charset ให้รองรับภาษาไทย

?>
