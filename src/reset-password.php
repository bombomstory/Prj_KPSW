<?php
session_start();

// การตั้งค่าฐานข้อมูล
include('db_connection.php');

// ------------------------ ตัวแปรเริ่มต้น ------------------------
$token = $_GET['token'] ?? '';
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $token    = $_POST['token'] ?? '';

    if ($password !== $confirm || strlen($password) < 4) {
        $error = 'รหัสผ่านไม่ตรงกัน หรือสั้นเกินไป';
    } else {

        if ($conn) {
            $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $email = $user['email'];
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
                $stmt->bind_param("ss", $hashed, $email);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $del = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $del->bind_param("s", $token);
                    $del->execute();
                    $success = "🎉 เปลี่ยนรหัสผ่านเรียบร้อยแล้ว! กำลังเปลี่ยนหน้าไปยังหน้าล็อกอิน...";
                } else {
                    $error = "อัปเดตรหัสผ่านไม่สำเร็จ";
                }
            } else {
                $error = "ลิงก์รีเซ็ตไม่ถูกต้องหรือหมดอายุ";
            }
        } else {
            $error = "เชื่อมต่อฐานข้อมูลล้มเหลว";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รีเซ็ตรหัสผ่าน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
            margin: 0;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
        }
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: linear-gradient(45deg, #5a67d8, #6b46c1);
        }
        .message {
            margin-top: 1rem;
            text-align: center;
            font-weight: 600;
        }
        .message.success {
            color: #2ecc71;
        }
        .message.error {
            color: #e74c3c;
        }
        .back-link {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .back-link:hover {
            background: #2980b9;
        }
        .match-status {
            font-size: 0.85rem;
            margin-top: 5px;
            color: #888;
        }
        @media (max-width: 480px) {
            .card {
                padding: 1.5rem;
            }
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="card">
    <h2>รีเซ็ตรหัสผ่าน</h2>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
        <div style="text-align: center;">
            <a href="login.php" class="back-link">← กลับไปหน้าล็อกอิน</a>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = "login.php";
            }, 5000); // 5 วินาที
        </script>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validatePasswords();">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="password">รหัสผ่านใหม่</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm">ยืนยันรหัสผ่าน</label>
                <input type="password" id="confirm" name="confirm" required>
                <div class="match-status" id="matchStatus"></div>
            </div>

            <button type="submit" class="btn">รีเซ็ตรหัสผ่าน</button>
        </form>
    <?php endif; ?>
</div>

<script>
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm');
    const statusText = document.getElementById('matchStatus');

    function validatePasswords() {
        if (password.value !== confirm.value) {
            alert('❌ รหัสผ่านไม่ตรงกัน');
            return false;
        }
        return true;
    }

    confirm.addEventListener('input', () => {
        if (password.value === confirm.value) {
            statusText.textContent = '✓ รหัสผ่านตรงกัน';
            statusText.style.color = 'green';
        } else {
            statusText.textContent = '✗ รหัสผ่านไม่ตรงกัน';
            statusText.style.color = 'red';
        }
    });
</script>
</body>
</html>
