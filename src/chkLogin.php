<?php
session_start();
require_once('db_connection.php');

// ตรวจสอบค่าที่ส่งมาจากฟอร์ม
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';

if (empty($username) || empty($password) || empty($user_type)) {
    header('Location: login-error.php');
    exit;
}

// สร้างการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    header('Location: login-error.php');
    exit;
}

// สร้าง SQL ตามประเภทผู้ใช้
$user_type_map = [
    'student'   => ['table' => 'students',   'dashboard' => 'studentDashboard.php'],
    'parent'    => ['table' => 'parents',    'dashboard' => 'parentDashboard.php'],
    'advisor'   => ['table' => 'advisors',   'dashboard' => 'advisorDashboard.php'],
    'teacher'   => ['table' => 'teachers',   'dashboard' => 'teacherDashboard.php'],
    'registrar' => ['table' => 'registrars', 'dashboard' => 'registarDashboard.php'],
    'admin'     => ['table' => 'CEOs',       'dashboard' => 'managerDashboard.php'],
];

if (!isset($user_type_map[$user_type])) {
    header('Location: login-error.php');
    exit;
}

$role_table = $user_type_map[$user_type]['table'];
$redirect_page = $user_type_map[$user_type]['dashboard'];

// ใช้ prepared statement เพื่อความปลอดภัย
$sql = "
    SELECT u.* FROM users u
    INNER JOIN $role_table r ON u.user_id = r.user_id
    WHERE u.username = ? AND u.status = 'active'
    LIMIT 1
";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header('Location: login-error.php');
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่าพบผู้ใช้หรือไม่
if ($result->num_rows !== 1) {
    header('Location: login-error.php');
    exit;
}

$user = $result->fetch_assoc();

// ตรวจสอบรหัสผ่านด้วย password_verify()
if (!password_verify($password, $user['password'])) {
    header('Location: login-error.php');
    exit;
}

// บันทึก Session
$_SESSION['user_id']    = $user['user_id'];
$_SESSION['prefix_name'] = $user['prefix_name'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name']  = $user['last_name'];
$_SESSION['user_type']  = $user_type;

// เปลี่ยนหน้าไปยัง dashboard ตามบทบาท
header("Location: $redirect_page");
exit;
?>
