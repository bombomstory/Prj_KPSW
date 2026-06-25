<?php
session_start();

// การตั้งค่าฐานข้อมูล
include('db_connection.php');

// เปิด debug mode
$debug_mode = isset($_GET['debug']);
if ($debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// ฟังก์ชันเชื่อมต่อฐานข้อมูล
function connectDatabase() {
    global $db_host, $db_name, $db_user, $db_pass;

    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($mysqli->connect_error) {
        error_log("Database connection failed: " . $mysqli->connect_error);
        return null;
    }

    // ตั้งค่าภาษาและ encoding
    $mysqli->set_charset("utf8mb4");

    return $mysqli;
}


// ฟังก์ชันตรวจสอบ reset token
function validateResetToken($token) {
    $mysqli = connectDatabase();

    if ($mysqli) {
        // ใช้ prepared statement แบบ MySQLi
        $stmt = $mysqli->prepare("SELECT email, expires_at FROM password_resets WHERE token = ? AND expires_at > NOW()");
        if ($stmt) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row;
            }
        } else {
            error_log("Database error in validateResetToken: " . $mysqli->error);
        }
    } else {
        // ใช้ไฟล์ JSON หากเชื่อมต่อ DB ไม่ได้
        $reset_file = 'password_resets.json';
        if (file_exists($reset_file)) {
            $resets = json_decode(file_get_contents($reset_file), true) ?: [];

            foreach ($resets as $reset) {
                if ($reset['token'] === $token && strtotime($reset['expires_at']) > time()) {
                    return $reset;
                }
            }
        }
    }

    return false;
}


// ฟังก์ชันอัปเดตรหัสผ่านในฐานข้อมูล (ใช้ MD5 ใน SQL)
function updateUserPassword($email, $plain_password, $debug = true) {
    $mysqli = connectDatabase();

    if ($mysqli) {
        // ตรวจสอบว่าผู้ใช้มีอยู่จริง
        $check_sql = "SELECT user_id, username, email, status FROM users WHERE email = ?";
        $stmt = $mysqli->prepare($check_sql);
        if (!$stmt) {
            error_log("MySQL prepare error: " . $mysqli->error);
            if ($debug) echo "Debug: Prepare failed for check_sql<br>";
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($debug) {
            echo "Debug: Check user query: $check_sql<br>";
            echo "Debug: Email parameter: $email<br>";
            if ($user) {
                echo "Debug: User found - ID: {$user['user_id']}, Username: {$user['username']}, Status: {$user['status']}<br>";
            } else {
                echo "Debug: No user found for email: $email<br>";
            }
        }

        if (!$user) return false;
        if ($user['status'] !== 'active') {
            if ($debug) echo "Debug: User account is not active (status: {$user['status']})<br>";
            return false;
        }

        // อัปเดตรหัสผ่าน
        /*
        $update_sql = "UPDATE users SET password = SHA1(?), updated_at = CURRENT_TIMESTAMP WHERE email = ? AND status = 'active'";
        // echo $update_sql;
        $stmt = $mysqli->prepare($update_sql);
        if (!$stmt) {
            error_log("MySQL prepare error (update): " . $mysqli->error);
            if ($debug) echo "Debug: Prepare failed for update_sql<br>";
            return false;
        }
        $stmt->bind_param("ss", $plain_password, $email);
        */

        $hashed = password_hash($plain_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE email = ? AND status = 'active'";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param("ss", $hashed, $email);
        

        $result = $stmt->execute();
        $affected_rows = $stmt->affected_rows;

        if ($debug) {
            echo "Debug: Update query: $update_sql<br>";
            echo "Debug: Parameters: ['$plain_password', '$email']<br>";
            echo "Debug: Query execution result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";
            echo "Debug: Affected rows: $affected_rows<br>";
        }

        if ($result && $affected_rows > 0) {
            // ตรวจสอบข้อมูลที่อัปเดต
            $verify_sql = "SELECT password, updated_at FROM users WHERE email = ?";
            $stmt = $mysqli->prepare($verify_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $updated_user = $res->fetch_assoc();

            if ($debug && $updated_user) {
                echo "Debug: Verification - Password hash (SHA1): " . $updated_user['password'] . "<br>";
                echo "Debug: Verification - Updated at: {$updated_user['updated_at']}<br>";
                echo "Debug: Expected SHA1: " . sha1($plain_password) . "<br>";
            }

            return true;
        } else {
            if ($debug) echo "Debug: No rows were affected by the update<br>";
            return false;
        }

    } else {
        if ($debug) echo "Debug: Database connection failed, using JSON file<br>";

        // ใช้ไฟล์ JSON (fallback)
        $users_file = 'users_passwords.json';
        $users = [];

        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true) ?: [];
        }

        $found = false;
        foreach ($users as &$user) {
            if ($user['email'] === $email) {
                $user['password'] = sha1($plain_password);
                $user['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                if ($debug) echo "Debug: Updated user in JSON file with SHA1 hash<br>";
                break;
            }
        }

        if (!$found) {
            $users[] = [
                'user_id' => count($users) + 1,
                'username' => 'user_' . time(),
                'email' => $email,
                'password' => sha1($plain_password),
                'first_name' => 'Unknown',
                'last_name' => 'User',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($debug) echo "Debug: Added new user to JSON file with SHA1 hash<br>";
        }

        $save_result = file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
        if ($debug) echo "Debug: JSON save result: " . ($save_result ? 'SUCCESS' : 'FAILED') . "<br>";

        return $save_result;
    }
}

// ฟังก์ชันลบ reset token
function deleteResetToken($token) {
    $mysqli = connectDatabase();

    if ($mysqli) {
        // ลบ token จากฐานข้อมูล
        $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE token = ?");
        if ($stmt) {
            $stmt->bind_param("s", $token);
            return $stmt->execute();
        } else {
            error_log("Error preparing delete statement: " . $mysqli->error);
            return false;
        }
    } else {
        // fallback: ใช้ JSON file
        $reset_file = 'password_resets.json';
        if (file_exists($reset_file)) {
            $resets = json_decode(file_get_contents($reset_file), true) ?: [];

            // กรองออก token ที่ต้องการลบ
            $resets = array_filter($resets, function($reset) use ($token) {
                return $reset['token'] !== $token;
            });

            // บันทึกไฟล์ใหม่
            return file_put_contents($reset_file, json_encode(array_values($resets), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
        }
        return false;
    }
}

// ฟังก์ชันส่งอีเมลยืนยันการเปลี่ยนรหัสผ่าน
function sendPasswordChangeConfirmation($email) {
    $subject = "รหัสผ่านถูกเปลี่ยนแล้ว";
    $message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Kanit', Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2ecc71; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>✅ รหัสผ่านเปลี่ยนแล้ว</h2>
            </div>
            <div class='content'>
                <p>สวัสดีครับ!</p>
                <p>รหัสผ่านของคุณได้ถูกเปลี่ยนเรียบร้อยแล้ว</p>
                <p><strong>เวลาที่เปลี่ยน:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p><strong>IP Address:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "</p>
                <p>หากคุณไม่ได้เป็นผู้ทำการเปลี่ยน กรุณาติดต่อผู้ดูแลระบบทันที</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = [
        'From: noreply@' . $_SERVER['HTTP_HOST'],
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    return mail($email, $subject, $message, implode("\r\n", $headers));
}

// ตัวแปรเก็บสถานะ
global $token;
$token = $_GET['token'] ?? '';

global $token_data;
$token_data = null;

global $debug_info;
$debug_info = [];

$error_message = '';
$success_message = '';

// ตรวจสอบ token
if (empty($token)) {
    $error_message = 'ลิงค์ไม่ถูกต้องหรือหมดอายุ';
} else {
    $token_data = validateResetToken($token);
    if (!$token_data) {
        $error_message = 'ลิงค์หมดอายุหรือไม่ถูกต้อง';
    }
}

// ประมวลผลฟอร์มรีเซ็ตรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password']) && $token_data) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $submitted_token = $_POST['token'] ?? '';
    
    $debug_info[] = "POST data received";
    $debug_info[] = "Email from token: " . $token_data['email'];
    
    // ตรวจสอบ token อีกครั้ง
    if ($submitted_token !== $token || !validateResetToken($submitted_token)) {
        $error_message = 'ลิงค์หมดอายุ กรุณาขอลิงค์ใหม่';
        $debug_info[] = "Token validation failed";
    }
    // ตรวจสอบความถูกต้อง
    elseif (empty($new_password) || empty($confirm_password)) {
        $error_message = 'กรุณากรอกรหัสผ่านให้ครบถ้วน';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'รหัสผ่านไม่ตรงกัน';
    } else {
        // ตรวจสอบความแข็งแกร่งของรหัสผ่าน
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
            $error_message = 'รหัสผ่านต้องประกอบด้วย: ตัวอักษรใหญ่-เล็ก, ตัวเลข, และอักขระพิเศษ';
        } else {
            $debug_info[] = "Password validation passed";
            $debug_info[] = "Plain password will be hashed with SHA1 in SQL query";
            
            // อัปเดตรหัสผ่านในฐานข้อมูล (ส่งรหัสผ่านแบบ plain text)
            $user_email = $token_data['email'];
            $debug_info[] = "Attempting to update password for: $user_email";
            
            if (updateUserPassword($user_email, $new_password, $debug_mode)) {
                $debug_info[] = "Password update successful";
                
                // ลบ token ที่ใช้แล้ว
                if (deleteResetToken($submitted_token)) {
                    $debug_info[] = "Reset token deleted successfully";
                } else {
                    $debug_info[] = "Warning: Failed to delete reset token";
                }
                
                // ส่งอีเมลยืนยัน
                if (sendPasswordChangeConfirmation($user_email)) {
                    $debug_info[] = "Confirmation email sent successfully";
                } else {
                    $debug_info[] = "Warning: Failed to send confirmation email";
                }
                
                $success_message = 'รีเซ็ตรหัสผ่านสำเร็จ! คุณสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้แล้ว';
                
                // บันทึก log
                error_log("Password reset successful for: " . $user_email);
            } else {
                $error_message = 'เกิดข้อผิดพลาดในการอัปเดตรหัสผ่าน กรุณาลองใหม่อีกครั้ง';
                $debug_info[] = "Password update failed - Check database connection and user existence";
                error_log("Failed to update password for: " . $user_email);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน</title>
    <link rel="shortcut icon" href="images/favicon.svg" />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 450px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .debug-info {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 12px;
            text-align: left;
            max-height: 300px;
            overflow-y: auto;
        }

        .reset-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .reset-icon.error {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .reset-icon.success {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            animation: bounce 0.6s ease-out;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .reset-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: #e0e6ed;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #e74c3c; width: 25%; }
        .strength-fair { background: #f39c12; width: 50%; }
        .strength-good { background: #f1c40f; width: 75%; }
        .strength-strong { background: #2ecc71; width: 100%; }

        .btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .success-message {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: fadeIn 0.5s ease-out;
        }

        .error-alert {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .requirements {
            text-align: left;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .requirements h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .requirements li {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .requirements li.valid {
            color: #2ecc71;
        }

        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .countdown {
            color: #2ecc71;
            font-weight: 600;
            font-size: 18px;
            margin-top: 10px;
        }

        .email-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1976d2;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 0 10px;
            }

            .page-title {
                font-size: 20px;
            }

            .page-subtitle {
                font-size: 14px;
            }

            .btn {
                padding: 12px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <?php if ($debug_mode && !empty($debug_info)): ?>
        <div class="debug-info">
            <strong>🐛 Debug Information:</strong><br>
            <?php foreach($debug_info as $info): ?>
                • <?php echo htmlspecialchars($info); ?><br>
            <?php endforeach; ?>
            <br>
            <strong>Database Connection Test:</strong><br>
            <?php 
            $test_conn = connectDatabase();
            if ($test_conn) {
                echo "• Database: CONNECTED<br>";
                // ตรวจสอบจำนวนผู้ใช้ทั้งหมด
                $stmt = $test_conn->prepare("SELECT COUNT(*) as user_count FROM users");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        echo "• Total users in database: " . $row['user_count'] . "<br>";
                    }
                } else {
                    echo "• Database query error (count): " . $test_conn->error . "<br>";
                }

                // ตรวจสอบข้อมูลผู้ใช้ตามอีเมล
                if (isset($token_data['email'])) {
                    $stmt = $test_conn->prepare("SELECT user_id, username, status FROM users WHERE email = ?");
                    if ($stmt) {
                        $stmt->bind_param("s", $token_data['email']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($user_check = $result->fetch_assoc()) {
                            echo "• Target user found: ID {$user_check['user_id']}, Username: {$user_check['username']}, Status: {$user_check['status']}<br>";
                        } else {
                            echo "• Target user NOT found for email: {$token_data['email']}<br>";
                        }
                    } else {
                        echo "• Database query error (email): " . $test_conn->error . "<br>";
                    }
                }
            } else {
                echo "• Database: NOT CONNECTED (using JSON files)<br>";
            }
            ?>

        </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="reset-icon error">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    <path d="M11 7h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
            </div>
            <h1 class="page-title">เกิดข้อผิดพลาด</h1>
            <div class="error-alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <p style="margin-top: 20px;">
                <a href="login-error.php" class="back-link">← ขอลิงค์รีเซ็ตใหม่</a>
            </p>
            
        <?php elseif (!empty($success_message)): ?>
            <div class="reset-icon success">
                <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            <h1 class="page-title">รีเซ็ตสำเร็จ!</h1>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
            <div class="countdown" id="countdown">กำลังเปลี่ยนหน้าใน 5 วินาที...</div>
            <p style="margin-top: 20px;">
                <a href="login.php" class="back-link">เข้าสู่ระบบทันที →</a>
            </p>
            
        <?php else: ?>
            <div class="reset-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
            <h1 class="page-title">รีเซ็ตรหัสผ่าน</h1>
            <p class="page-subtitle">
                กรุณาตั้งรหัสผ่านใหม่ที่ปลอดภัยและจำง่าย
            </p>

            <?php if ($token_data): ?>
            <div class="email-info">
                📧 กำลังรีเซ็ตรหัสผ่านสำหรับ: <strong><?php echo htmlspecialchars($token_data['email']); ?></strong>
            </div>
            <?php endif; ?>

            <div class="requirements">
                <h4>รหัสผ่านต้องมีคุณสมบัติ:</h4>
                <ul id="requirements-list">
                    <li id="req-length">มีอย่างน้อย 8 ตัวอักษร</li>
                    <li id="req-uppercase">มีตัวอักษรใหญ่ (A-Z)</li>
                    <li id="req-lowercase">มีตัวอักษรเล็ก (a-z)</li>
                    <li id="req-number">มีตัวเลข (0-9)</li>
                    <li id="req-special">มีอักขระพิเศษ (!@#$%^&*)</li>
                </ul>
            </div>

            <form method="POST" action="" id="reset-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="new_password" class="form-label">รหัสผ่านใหม่:</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" required>
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="password-strength" id="strength-text">ความแข็งแกร่ง: ไม่ระบุ</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    <div class="password-strength" id="match-text"></div>
                </div>

                <button type="submit" name="reset_password" class="btn" id="submit-btn">
                    รีเซ็ตรหัสผ่าน
                </button>
            </form>

            <p style="margin-top: 20px;">
                <a href="login.php" class="back-link">← กลับไปหน้าเข้าสู่ระบบ</a>
            </p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            const matchText = document.getElementById('match-text');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.getElementById('reset-form');

            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    const password = this.value;
                    checkPasswordStrength(password);
                    updateRequirements(password);
                    checkPasswordMatch();
                    updateSubmitButton();
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    checkPasswordMatch();
                    updateSubmitButton();
                });
            }

            function checkPasswordStrength(password) {
                let strength = 0;
                let strengthClass = '';
                let strengthLabel = '';

                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;

                switch (strength) {
                    case 0:
                    case 1:
                        strengthClass = 'strength-weak';
                        strengthLabel = 'อ่อนแอ';
                        break;
                    case 2:
                        strengthClass = 'strength-fair';
                        strengthLabel = 'ปานกลาง';
                        break;
                    case 3:
                        strengthClass = 'strength-good';
                        strengthLabel = 'ดี';
                        break;
                    case 4:
                    case 5:
                        strengthClass = 'strength-strong';
                        strengthLabel = 'แข็งแกร่ง';
                        break;
                }

                if (strengthFill) {
                    strengthFill.className = `strength-fill ${strengthClass}`;
                }
                if (strengthText) {
                    strengthText.textContent = `ความแข็งแกร่ง: ${strengthLabel}`;
                }
            }

            function updateRequirements(password) {
                const requirements = [
                    { id: 'req-length', test: password.length >= 8 },
                    { id: 'req-uppercase', test: /[A-Z]/.test(password) },
                    { id: 'req-lowercase', test: /[a-z]/.test(password) },
                    { id: 'req-number', test: /[0-9]/.test(password) },
                    { id: 'req-special', test: /[^a-zA-Z0-9]/.test(password) }
                ];

                requirements.forEach(req => {
                    const element = document.getElementById(req.id);
                    if (element) {
                        element.className = req.test ? 'valid' : '';
                    }
                });
            }

            function checkPasswordMatch() {
                if (!newPasswordInput || !confirmPasswordInput || !matchText) return;

                const password = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        matchText.textContent = '✓ รหัสผ่านตรงกัน';
                        matchText.style.color = '#2ecc71';
                        confirmPasswordInput.style.borderColor = '#2ecc71';
                    } else {
                        matchText.textContent = '✗ รหัสผ่านไม่ตรงกัน';
                        matchText.style.color = '#e74c3c';
                        confirmPasswordInput.style.borderColor = '#e74c3c';
                    }
                } else {
                    matchText.textContent = '';
                    confirmPasswordInput.style.borderColor = '#e0e6ed';
                }
            }

            function updateSubmitButton() {
                if (!submitBtn || !newPasswordInput || !confirmPasswordInput) return;

                const password = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                // ตรวจสอบเงื่อนไขทั้งหมด
                const isLengthValid = password.length >= 8;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                const hasSpecial = /[^a-zA-Z0-9]/.test(password);
                const isMatchValid = password === confirmPassword && password.length > 0;
                
                const isValid = isLengthValid && hasUppercase && hasLowercase && hasNumber && hasSpecial && isMatchValid;

                submitBtn.disabled = !isValid;
                submitBtn.style.opacity = isValid ? '1' : '0.6';
                
                if (isValid) {
                    submitBtn.style.cursor = 'pointer';
                } else {
                    submitBtn.style.cursor = 'not-allowed';
                }
            }

            // Form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = newPasswordInput.value;
                    const confirmPassword = confirmPasswordInput.value;

                    // ตรวจสอบอีกครั้งก่อนส่ง
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('รหัสผ่านไม่ตรงกัน');
                        return;
                    }

                    if (password.length < 8) {
                        e.preventDefault();
                        alert('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
                        return;
                    }

                    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(password)) {
                        e.preventDefault();
                        alert('รหัสผ่านต้องประกอบด้วย: ตัวอักษรใหญ่-เล็ก, ตัวเลข, และอักขระพิเศษ');
                        return;
                    }

                    // แสดง loading state
                    submitBtn.innerHTML = '🔄 กำลังรีเซ็ต...';
                    submitBtn.disabled = true;
                    
                    // ป้องกันการส่งซ้ำ
                    setTimeout(() => {
                        if (submitBtn.innerHTML.includes('กำลังรีเซ็ต')) {
                            submitBtn.innerHTML = 'รีเซ็ตรหัสผ่าน';
                            submitBtn.disabled = false;
                        }
                    }, 10000); // 10 วินาที
                });
            }

            // Countdown timer for redirect
            const countdown = document.getElementById('countdown');
            if (countdown) {
                let seconds = 5;
                const timer = setInterval(() => {
                    seconds--;
                    countdown.textContent = `กำลังเปลี่ยนหน้าใน ${seconds} วินาที...`;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        countdown.textContent = 'กำลังเปลี่ยนหน้า...';
                        window.location.href = 'login.php';
                    }
                }, 1000);
            }

            // เริ่มต้นตรวจสอบปุ่ม
            updateSubmitButton();

            // Focus ที่ input แรก
            if (newPasswordInput) {
                newPasswordInput.focus();
            }

            // เพิ่ม Enter key support
            if (newPasswordInput) {
                newPasswordInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        confirmPasswordInput.focus();
                    }
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !submitBtn.disabled) {
                        form.submit();
                    }
                });
            }
        });
    </script>
</body>
</html>