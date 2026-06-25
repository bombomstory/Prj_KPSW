<?php
// โปรแกรมทดสอบส่งอีเมล
session_start();

// ตัวแปรเก็บผลลัพธ์
$test_results = [];
$form_submitted = false;

// ฟังก์ชันตรวจสอบการตั้งค่า mail
function checkMailConfiguration() {
    $config = [];
    
    // ตรวจสอบ mail function
    $config['mail_function'] = function_exists('mail') ? 'พร้อมใช้งาน' : 'ไม่พร้อมใช้งาน';
    
    // ตรวจสอบค่า configuration
    $config['sendmail_path'] = ini_get('sendmail_path') ?: 'ไม่ได้ตั้งค่า';
    $config['smtp_server'] = ini_get('SMTP') ?: 'localhost';
    $config['smtp_port'] = ini_get('smtp_port') ?: '25';
    $config['sendmail_from'] = ini_get('sendmail_from') ?: 'ไม่ได้ตั้งค่า';
    
    return $config;
}

// ฟังก์ชันทดสอบส่งอีเมลด้วย mail()
function testBasicMail($to_email, $from_email, $subject, $message) {
    $headers = [
        'From: ' . $from_email,
        'Reply-To: ' . $from_email,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    $result = mail($to_email, $subject, $message, implode("\r\n", $headers));
    
    return [
        'success' => $result,
        'method' => 'PHP mail() function',
        'error' => $result ? null : error_get_last()['message'] ?? 'Unknown error'
    ];
}

// ฟังก์ชันสร้างอีเมล HTML
function createEmailHTML($test_type) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: "Kanit", Arial, sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
            .button { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 การทดสอบส่งอีเมล</h1>
                <p>ระบบส่งอีเมล: ' . $test_type . '</p>
            </div>
            <div class="content">
                <h2>สวัสดีครับ!</h2>
                <p>นี่คือการทดสอบส่งอีเมลจากเซิร์ฟเวอร์ของคุณ</p>
                <p><strong>เวลาส่ง:</strong> ' . date('Y-m-d H:i:s') . '</p>
                <p><strong>วิธีการส่ง:</strong> ' . $test_type . '</p>
                <p>หากคุณได้รับอีเมลนี้ แสดงว่าระบบส่งอีเมลของคุณทำงานได้ปกติ! 🎉</p>
                <p style="text-align: center; margin: 30px 0;">
                    <a href="#" class="button">ทดสอบสำเร็จ!</a>
                </p>
            </div>
            <div class="footer">
                <p>อีเมลนี้ส่งจากระบบทดสอบ • ' . $_SERVER['HTTP_HOST'] . '</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

// ประมวลผลฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_submitted = true;
    $to_email = filter_var($_POST['to_email'], FILTER_VALIDATE_EMAIL);
    $from_email = filter_var($_POST['from_email'], FILTER_VALIDATE_EMAIL);
    $test_type = $_POST['test_type'] ?? 'basic';
    
    if ($to_email && $from_email) {
        $subject = "ทดสอบส่งอีเมล - " . date('Y-m-d H:i:s');
        $message = createEmailHTML($test_type);
        
        // ทดสอบส่งอีเมล
        if ($test_type === 'basic') {
            $test_results[] = testBasicMail($to_email, $from_email, $subject, $message);
        } else {
            // ทดสอบหลายวิธี
            $test_results[] = testBasicMail($to_email, $from_email, $subject, $message);
        }
    } else {
        $test_results[] = [
            'success' => false,
            'method' => 'Validation',
            'error' => 'อีเมลไม่ถูกต้อง'
        ];
    }
}

$mail_config = checkMailConfiguration();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ทดสอบส่งอีเมล - Email Test</title>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 30px;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .config-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .config-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .config-value {
            color: #7f8c8d;
            font-family: monospace;
            background: white;
            padding: 5px 10px;
            border-radius: 5px;
            word-break: break-all;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e6ed;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 14px 30px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .results {
            margin-top: 25px;
        }

        .result-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid;
        }

        .result-success {
            background: #d4edda;
            border-left-color: #2ecc71;
            color: #155724;
        }

        .result-error {
            background: #f8d7da;
            border-left-color: #e74c3c;
            color: #721c24;
        }

        .result-method {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .result-message {
            font-size: 14px;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-ok { background: #2ecc71; }
        .status-error { background: #e74c3c; }
        .status-warning { background: #f39c12; }

        .tips {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 25px;
        }

        .tips h3 {
            margin-bottom: 15px;
        }

        .tips ul {
            margin-left: 20px;
        }

        .tips li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .config-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 ทดสอบส่งอีเมล</h1>
            <p>ตรวจสอบความสามารถในการส่งอีเมลของเซิร์ฟเวอร์</p>
        </div>

        <div class="content">
            <!-- Mail Configuration -->
            <div class="section">
                <h2>📋 การตั้งค่าระบบอีเมล</h2>
                <div class="config-grid">
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator <?php echo $mail_config['mail_function'] === 'พร้อมใช้งาน' ? 'status-ok' : 'status-error'; ?>"></span>
                            Mail Function
                        </div>
                        <div class="config-value"><?php echo $mail_config['mail_function']; ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator <?php echo $mail_config['sendmail_path'] !== 'ไม่ได้ตั้งค่า' ? 'status-ok' : 'status-warning'; ?>"></span>
                            Sendmail Path
                        </div>
                        <div class="config-value"><?php echo $mail_config['sendmail_path']; ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator status-ok"></span>
                            SMTP Server
                        </div>
                        <div class="config-value"><?php echo $mail_config['smtp_server']; ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator status-ok"></span>
                            SMTP Port
                        </div>
                        <div class="config-value"><?php echo $mail_config['smtp_port']; ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator <?php echo $mail_config['sendmail_from'] !== 'ไม่ได้ตั้งค่า' ? 'status-ok' : 'status-warning'; ?>"></span>
                            Default From
                        </div>
                        <div class="config-value"><?php echo $mail_config['sendmail_from']; ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">
                            <span class="status-indicator status-ok"></span>
                            PHP Version
                        </div>
                        <div class="config-value"><?php echo phpversion(); ?></div>
                    </div>
                </div>
            </div>

            <!-- Test Form -->
            <div class="section">
                <h2>✉️ ทดสอบส่งอีเมล</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="to_email" class="form-label">ส่งไปยังอีเมล:</label>
                        <input 
                            type="email" 
                            id="to_email" 
                            name="to_email" 
                            class="form-input" 
                            placeholder="example@gmail.com"
                            value="<?php echo isset($_POST['to_email']) ? htmlspecialchars($_POST['to_email']) : ''; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="from_email" class="form-label">ส่งจากอีเมล:</label>
                        <input 
                            type="email" 
                            id="from_email" 
                            name="from_email" 
                            class="form-input" 
                            placeholder="noreply@yourdomain.com"
                            value="<?php echo isset($_POST['from_email']) ? htmlspecialchars($_POST['from_email']) : 'noreply@' . $_SERVER['HTTP_HOST']; ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="test_type" class="form-label">ประเภทการทดสอบ:</label>
                        <select id="test_type" name="test_type" class="form-select">
                            <option value="basic">ทดสอบพื้นฐาน (mail function)</option>
                            <option value="advanced">ทดสอบขั้นสูง (รอการพัฒนา)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn">🚀 ทดสอบส่งอีเมล</button>
                </form>
            </div>

            <!-- Results -->
            <?php if ($form_submitted && !empty($test_results)): ?>
            <div class="section">
                <h2>📊 ผลการทดสอบ</h2>
                <div class="results">
                    <?php foreach ($test_results as $result): ?>
                    <div class="result-item <?php echo $result['success'] ? 'result-success' : 'result-error'; ?>">
                        <div class="result-method">
                            <?php echo $result['success'] ? '✅' : '❌'; ?> 
                            <?php echo htmlspecialchars($result['method']); ?>
                        </div>
                        <div class="result-message">
                            <?php if ($result['success']): ?>
                                ส่งอีเมลสำเร็จ! กรุณาตรวจสอบในกล่องจดหมายของคุณ
                            <?php else: ?>
                                เกิดข้อผิดพลาด: <?php echo htmlspecialchars($result['error'] ?? 'Unknown error'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tips -->
            <div class="tips">
                <h3>💡 เคล็ดลับการแก้ไขปัญหา</h3>
                <ul>
                    <li><strong>หากส่งไม่ได้:</strong> ตรวจสอบว่า mail() function เปิดใช้งานใน PHP</li>
                    <li><strong>Gmail/Hotmail:</strong> อาจต้องใช้ SMTP authentication แทน mail() function</li>
                    <li><strong>Sendmail Path:</strong> ตั้งค่าใน php.ini หาก sendmail_path ไม่ถูกต้อง</li>
                    <li><strong>Spam Folder:</strong> ตรวจสอบในโฟลเดอร์สแปมด้วย</li>
                    <li><strong>Hosting:</strong> บางโฮสติ้งจำกัดการส่งอีเมล ต้องใช้ SMTP ของพวกเขา</li>
                    <li><strong>SPF/DKIM:</strong> ตั้งค่า DNS records เพื่อเพิ่มความน่าเชื่อถือ</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn');
            
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '📤 กำลังส่ง...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds in case of issues
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = '🚀 ทดสอบส่งอีเมล';
                        submitBtn.disabled = false;
                    }
                }, 5000);
            });

            // Auto-fill domain for from email
            const fromEmail = document.getElementById('from_email');
            const toEmail = document.getElementById('to_email');
            
            if (fromEmail.value === '') {
                fromEmail.value = 'noreply@<?php echo $_SERVER['HTTP_HOST']; ?>';
            }
        });
    </script>
</body>
</html>