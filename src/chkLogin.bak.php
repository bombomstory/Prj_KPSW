<?php
session_start();
include('db_connection.php');

$username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';

// ตรวจสอบข้อมูลครบถ้วน
if (empty($username) || empty($password) || empty($user_type)) {
    header('Location: login-error.php');
    exit;
}

// สร้าง SQL query เชื่อมตาราง users กับตารางประเภทผู้ใช้ตาม $user_type
switch ($user_type) {
    case 'student':
        $sql = "SELECT u.* FROM users u
                INNER JOIN students s ON u.user_id = s.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'studentDashboard.php';
        break;

    case 'parent':
        $sql = "SELECT u.* FROM users u
                INNER JOIN parents p ON u.user_id = p.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'parentDashboard.php';
        break;

    case 'advisor':
        $sql = "SELECT u.* FROM users u
                INNER JOIN advisors a ON u.user_id = a.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'advisorDashboard.php';
        break;

    case 'teacher':
        $sql = "SELECT u.* FROM users u
                INNER JOIN teachers t ON u.user_id = t.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'teacherDashboard.php';
        break;

    case 'registrar':
        $sql = "SELECT u.* FROM users u
                INNER JOIN registrars r ON u.user_id = r.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'registarDashboard.php';
        break;

    case 'admin':
        $sql = "SELECT u.* FROM users u
                INNER JOIN CEOs c ON u.user_id = c.user_id
                WHERE u.username = '$username' AND u.status = 'active' LIMIT 1";
        $redirect_page = 'managerDashboard.php';
        break;

    default:
        header('Location: login-error');
        exit;
}

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) !== 1) {
    header('Location: login-error.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

// ตรวจสอบรหัสผ่าน (สมมติเก็บแบบ hashed)
if (!(sha1($password)===$user['password'])) {
    header('Location: login-error.php');
    exit;
}

// เก็บข้อมูล session
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['user_type'] = $user_type;

// เปลี่ยนเส้นทางไปแดชบอร์ดของผู้ใช้ตามประเภท
header("Location: $redirect_page");
exit;
?>
