<?php
session_start();

// กำหนดข้อมูลการเชื่อมต่อฐานข้อมูล (ปรับตามความเหมาะสม)
include('db_connection.php');

// ฟังก์ชันเชื่อมต่อฐานข้อมูล
function connectDB() {
    global $db_host, $db_name, $db_user, $db_pass;

    // เชื่อมต่อด้วย MySQLi
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // ตรวจสอบข้อผิดพลาด
    if ($mysqli->connect_error) {
        // หากไม่มีฐานข้อมูล ให้ใช้ไฟล์แทน
        error_log("Database connection failed: " . $mysqli->connect_error);
        return null;
    }

    // ตั้งค่า charset
    $mysqli->set_charset("utf8mb4");

    return $mysqli;
}


// ฟังก์ชันบันทึก reset token (ใช้ไฟล์หากไม่มีฐานข้อมูล)
function saveResetToken($email, $token) {
    $mysqli = connectDB();

    if ($mysqli) {
        // ใช้ฐานข้อมูล
        // ลบ token เก่าของอีเมลนี้
        $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("Delete token prepare failed: " . $mysqli->error);
            return false;
        }

        // เพิ่ม token ใหม่
        $stmt = $mysqli->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $token);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            error_log("Insert token prepare failed: " . $mysqli->error);
            return false;
        }

    } else {
        // ใช้ไฟล์ JSON
        $reset_file = 'password_resets.json';
        $resets = [];

        if (file_exists($reset_file)) {
            $resets = json_decode(file_get_contents($reset_file), true) ?: [];
        }

        // ลบ token เก่าที่หมดอายุ
        $resets = array_filter($resets, function($reset) {
            return strtotime($reset['expires_at']) > time();
        });

        // เพิ่ม token ใหม่
        $resets[] = [
            'email' => $email,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ];

        return file_put_contents($reset_file, json_encode(array_values($resets), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
}

// ฟังก์ชันตรวจสอบว่าอีเมลมีอยู่ในระบบหรือไม่
function emailExists($email) {
    $mysqli = connectDB();

    if ($mysqli) {
        // ตรวจสอบอีเมลในตาราง users
        $stmt = $mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result(); // จำเป็นสำหรับการใช้งาน num_rows
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            return $exists;
        } else {
            // หากเตรียม statement ไม่ได้ เช่น ตารางไม่มีอยู่
            error_log("Query error in emailExists: " . $mysqli->error);
            return true; // สำหรับการทดสอบ
        }
    } else {
        // หากไม่มีฐานข้อมูล ให้ return true (สำหรับการทดสอบ)
        return true;
    }
}

// ฟังก์ชันสร้างอีเมล HTML
function createResetEmailHTML($reset_link) {
    $html = '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            body { font-family: "Kanit", Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 40px 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; font-size: 16px; }
            .content { padding: 40px 30px; }
            .content h2 { color: #2c3e50; margin-bottom: 20px; font-size: 22px; }
            .content p { color: #7f8c8d; line-height: 1.6; margin-bottom: 15px; font-size: 16px; }
            .button-container { text-align: center; margin: 30px 0; }
            .reset-button { 
                display: inline-block; 
                padding: 15px 30px; 
                background: linear-gradient(45deg, #e74c3c, #c0392b); 
                color: white; 
                text-decoration: none; 
                border-radius: 10px; 
                font-weight: 500;
                font-size: 16px;
                transition: all 0.3s ease;
            }
            .reset-button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3); }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0; }
            .warning h4 { color: #856404; margin: 0 0 10px 0; font-size: 16px; }
            .warning p { color: #856404; margin: 0; font-size: 14px; }
            .footer { background: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #e9ecef; }
            .footer p { color: #6c757d; margin: 0; font-size: 14px; line-height: 1.5; }
            .link-text { color: #6c757d; font-size: 12px; word-break: break-all; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🔐 รีเซ็ตรหัสผ่าน</h1>
                <p>คำขอเปลี่ยนรหัสผ่านใหม่</p>
            </div>
            <div class="content">
                <h2>สวัสดีครับ!</h2>
                <p>เราได้รับคำขอให้รีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ หากคุณเป็นผู้ทำการขอรีเซ็ต กรุณาคลิกปุ่มด้านล่างเพื่อตั้งรหัสผ่านใหม่</p>
                
                <div class="button-container">
                    <a href="' . $reset_link . '" class="reset-button">รีเซ็ตรหัสผ่าน</a>
                </div>
                
                <div class="warning">
                    <h4>⚠️ ข้อมูลสำคัญ:</h4>
                    <p>• ลิงค์นี้จะหมดอายุใน 1 ชั่วโมง<br>
                    • หากคุณไม่ได้ขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยต่ออีเมลนี้<br>
                    • อย่าแชร์ลิงค์นี้กับผู้อื่น</p>
                </div>
                
                <p><strong>เวลาที่ขอรีเซ็ต:</strong> ' . date('d/m/Y H:i:s') . '</p>
                <p>หากปุ่มไม่ทำงาน กรุณาคัดลอกลิงค์ด้านล่างไปวางในเบราว์เซอร์:</p>
                <p class="link-text">' . $reset_link . '</p>
            </div>
            <div class="footer">
                <p>อีเมลนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ<br>
                หากมีปัญหา กรุณาติดต่อผู้ดูแลระบบ</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

// ตัวแปรเก็บข้อความ
$success_message = '';
$error_message = '';

// ตรวจสอบว่ามีการส่งฟอร์มกู้คืนรหัสผ่านหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        // ตรวจสอบว่าอีเมลมีอยู่ในระบบหรือไม่
        if (emailExists($email)) {
            // สร้าง reset token
            $reset_token = bin2hex(random_bytes(32));
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $reset_link = $protocol . "://" . $host . dirname($_SERVER['REQUEST_URI']) . "reset-password.php?token=" . $reset_token . "&debug=1";
            
            // บันทึก token
            if (saveResetToken($email, $reset_token)) {
                // ส่งอีเมล
                $subject = "รีเซ็ตรหัสผ่าน - " . $_SERVER['HTTP_HOST'];
                $message = createResetEmailHTML($reset_link);
                $headers = [
                    'From: noreply@' . $_SERVER['HTTP_HOST'],
                    'Reply-To: noreply@' . $_SERVER['HTTP_HOST'],
                    'X-Mailer: PHP/' . phpversion(),
                    'MIME-Version: 1.0',
                    'Content-Type: text/html; charset=UTF-8'
                ];
                
                if (mail($email, $subject, $message, implode("\r\n", $headers))) {
                    $success_message = "ส่งลิงค์รีเซ็ตรหัสผ่านไปยัง " . $email . " แล้ว กรุณาตรวจสอบอีเมล (รวมถึงโฟลเดอร์สแปม)";
                } else {
                    $error_message = "เกิดข้อผิดพลาดในการส่งอีเมล กรุณาลองใหม่อีกครั้ง";
                }
            } else {
                $error_message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง";
            }
        } else {
            // แม้อีเมลไม่มีในระบบ ก็แสดงข้อความเดียวกันเพื่อความปลอดภัย
            $success_message = "หากอีเมลนี้มีอยู่ในระบบ เราจะส่งลิงค์รีเซ็ตรหัสผ่านให้คุณ";
        }
    } else {
        $error_message = "กรุณาระบุอีเมลให้ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อผิดพลาดในการเข้าสู่ระบบ</title>
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

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 107, 107, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 107, 107, 0);
            }
        }

        .error-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .error-message {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .reset-form {
            margin-top: 30px;
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

        .btn-secondary {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
        }

        .btn-secondary:hover {
            box-shadow: 0 10px 20px rgba(149, 165, 166, 0.3);
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

        .divider {
            margin: 25px 0;
            position: relative;
            text-align: center;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e6ed;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            color: #7f8c8d;
            font-weight: 500;
        }

        .back-to-login {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-to-login:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .loading {
            display: none;
            margin-top: 10px;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 0 10px;
            }

            .error-title {
                font-size: 20px;
            }

            .error-message {
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
        <div class="error-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                <path d="M11 7h2v6h-2zm0 8h2v2h-2z"/>
            </svg>
        </div>

        <h1 class="error-title">รหัสผ่านไม่ถูกต้อง</h1>
        <p class="error-message">
            ขออภัย รหัสผ่านที่คุณป้อนไม่ถูกต้อง กรุณาตรวจสอบและลองใหม่อีกครั้ง 
            หรือคุณสามารถกู้คืนรหัสผ่านได้ด้านล่าง
        </p>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="reset-form" id="resetForm">
            <div class="form-group">
                <label for="email" class="form-label">อีเมลของคุณ:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="example@email.com"
                    required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>

            <button type="submit" name="reset_password" class="btn" id="submitBtn">
                ส่งลิงค์กู้คืนรหัสผ่าน
            </button>
            
            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p style="margin-top: 10px; color: #7f8c8d;">กำลังส่งอีเมล...</p>
            </div>
        </form>

        <div class="divider">
            <span>หรือ</span>
        </div>

        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
            ลองเข้าสู่ระบบอีกครั้ง
        </button>

        <p style="margin-top: 20px;">
            <a href="login.php" class="back-to-login">← กลับไปหน้าเข้าสู่ระบบ</a>
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('resetForm');
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            
            // Auto focus ที่ input email
            emailInput.focus();
            
            // เพิ่ม loading state เมื่อส่งฟอร์ม
            form.addEventListener('submit', function(e) {
                submitBtn.style.display = 'none';
                loading.style.display = 'block';
                
                // หากไม่มีการ redirect ใน 10 วินาที ให้แสดงปุ่มอีกครั้ง
                setTimeout(() => {
                    submitBtn.style.display = 'block';
                    loading.style.display = 'none';
                }, 10000);
            });
            
            // Validation แบบ real-time
            emailInput.addEventListener('input', function() {
                const email = this.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#e0e6ed';
                }
            });

            // เพิ่ม Enter key support
            emailInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>