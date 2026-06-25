<?php
$ut = (!isset($_GET["ut"])) ? "student" : $_GET["ut"];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบงานทะเบียนนักเรียนโรงเรียนกำแพงแสนวิทยา</title>
    <link rel="shortcut icon" href="images/favicon.svg" />
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .logo p {
            color: #666;
            font-size: 16px;
            font-weight: 300;
        }

        .user-type-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #333;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: left;
        }

        .user-type-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }

        .user-type-btn {
            background: linear-gradient(145deg, #f8f9fa, #e9ecef);
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Kanit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            position: relative;
            overflow: hidden;
        }

        .user-type-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .user-type-btn.active {
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .user-type-btn i {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .login-form {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .login-form.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: auto;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            margin: 25px 0;
            position: relative;
            text-align: center;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 15px;
            color: #666;
            font-size: 14px;
        }

        .support-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .support-link {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .support-link:hover {
            color: #764ba2;
        }

        .selected-user-type {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            color: #667eea;
            font-weight: 500;
            font-size: 14px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 0;
            border: none;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease;
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .modal-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .close:hover {
            opacity: 0.7;
        }

        .modal-body {
            padding: 30px;
        }

        .reset-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .user-type-display {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            color: #667eea;
            font-weight: 500;
        }

        .user-type-display i {
            font-size: 20px;
            margin-right: 8px;
        }

        .email-input-group {
            position: relative;
        }

        .email-input-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .email-input-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .email-input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .reset-btn {
            background: linear-gradient(145deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .reset-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-family: 'Kanit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background: #5a6268;
        }

        .success-message {
            background: linear-gradient(145deg, #28a745, #20c997);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            display: none;
        }

        .success-message i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .success-message h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .success-message p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
                margin: 10px;
            }
            
            .user-type-grid {
                grid-template-columns: 1fr;
            }
            
            .user-type-btn {
                padding: 12px;
                font-size: 13px;
            }

            .modal-content {
                margin: 5% auto;
                width: 95%;
            }

            .modal-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="80"> 
            <!--<i class="fas fa-graduation-cap"></i>-->
            <h1>ระบบงานทะเบียนนักเรียน</h1>
            <h3><strong>โรงเรียนกำแพงแสนวิทยา</h3>
            <p>เข้าสู่ระบบเพื่อใช้งาน</p>
        </div>

        <div class="user-type-section">
            <div class="section-title">
                <i class="fas fa-users"></i> เลือกประเภทผู้ใช้งาน
            </div>
            <div class="user-type-grid">
                <?php 
                    if ($ut=="student"){
                ?>
                <button class="user-type-btn" data-type="student">
                    <i class="fas fa-user-graduate"></i>
                    นักเรียน
                </button>
                <button class="user-type-btn" data-type="parent">
                    <i class="fas fa-user-friends"></i>
                    ผู้ปกครอง
                </button>
                <?php
                    }else{
                ?>
                <button class="user-type-btn" data-type="advisor">
                    <i class="fas fa-chalkboard-teacher"></i>
                    ครูที่ปรึกษา
                </button>
                <button class="user-type-btn" data-type="teacher">
                    <i class="fas fa-user-tie"></i>
                    ครูผู้สอน
                </button>
                <button class="user-type-btn" data-type="registrar">
                    <i class="fas fa-clipboard-list"></i>
                    ครูงานทะเบียน
                </button>
                <button class="user-type-btn" data-type="admin">
                    <i class="fas fa-user-cog"></i>
                    ผู้บริหาร
                </button>
                <?php
                    }
                ?>
            </div>
        </div>

        <form class="login-form" id="loginForm" method="POST" action="chkLogin.php">
            <div class="selected-user-type" id="selectedUserType" style="display: none;">
                <i class="fas fa-info-circle"></i>
                <span id="userTypeText"></span>
            </div>

            <input type="hidden" name="user_type" id="userTypeInput">

            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> ชื่อผู้ใช้
                </label>
                <input type="text" id="username" name="username" required placeholder="กรอกชื่อผู้ใช้">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> รหัสผ่าน
                </label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="กรอกรหัสผ่าน">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    จดจำการเข้าสู่ระบบ
                </label>
                <a href="#" class="forgot-password" onclick="showForgotPassword()">
                    ลืมรหัสผ่าน?
                </a>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </button>
        </form>

        <div class="divider">
            <span>ต้องการความช่วยเหลือ?</span>
        </div>

        <div class="support-links">
            <a href="#" class="support-link" onclick="showHelp()">
                <i class="fas fa-question-circle"></i> ช่วยเหลือ
            </a>
            <a href="#" class="support-link" onclick="contactAdmin()">
                <i class="fas fa-envelope"></i> ติดต่อผู้ดูแล
            </a>
        </div>
    </div>

    <!-- Modal กู้คืนรหัสผ่าน -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeForgotPasswordModal()">&times;</span>
                <h2><i class="fas fa-key"></i> กู้คืนรหัสผ่าน</h2>
                <p>กรอกอีเมลเพื่อรับลิงค์สำหรับรีเซ็ตรหัสผ่าน</p>
            </div>
            <div class="modal-body">
                <div id="resetForm" class="reset-form">
                    <div class="user-type-display" id="modalUserType">
                        <i class="fas fa-user"></i>
                        <span>กรุณาเลือกประเภทผู้ใช้ก่อน</span>
                    </div>
                    
                    <div class="email-input-group">
                        <label for="resetEmail">
                            <i class="fas fa-envelope"></i> อีเมล
                        </label>
                        <input type="email" id="resetEmail" name="resetEmail" required 
                               placeholder="กรอกอีเมลที่ลงทะเบียนไว้">
                    </div>
                    
                    <button type="button" class="reset-btn" onclick="sendResetLink()">
                        <i class="fas fa-paper-plane"></i> ส่งลิงค์รีเซ็ตรหัสผ่าน
                    </button>
                    
                    <button type="button" class="cancel-btn" onclick="closeForgotPasswordModal()">
                        <i class="fas fa-times"></i> ยกเลิก
                    </button>
                </div>

                <div id="loadingSpinner" class="loading-spinner">
                    <div class="spinner"></div>
                    <p>กำลังส่งอีเมล กรุณารอสักครู่...</p>
                </div>

                <div id="successMessage" class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h3>ส่งลิงค์สำเร็จ!</h3>
                    <p>เราได้ส่งลิงค์สำหรับรีเซ็ตรหัสผ่านไปยังอีเมลของคุณแล้ว<br>
                    กรุณาตรวจสอบอีเมล (รวมถึงโฟลเดอร์ Spam) และคลิกลิงค์เพื่อดำเนินการต่อ</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const userTypeButtons = document.querySelectorAll('.user-type-btn');
        const loginForm = document.getElementById('loginForm');
        const userTypeInput = document.getElementById('userTypeInput');
        const selectedUserType = document.getElementById('selectedUserType');
        const userTypeText = document.getElementById('userTypeText');

        const userTypeLabels = {
            'student': 'นักเรียน',
            'parent': 'ผู้ปกครอง',
            'advisor': 'ครูที่ปรึกษา',
            'teacher': 'ครูผู้สอน',
            'registrar': 'ครูงานทะเบียน',
            'admin': 'ผู้บริหาร'
        };

        userTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                userTypeButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get user type
                const userType = this.dataset.type;
                
                // Set hidden input value
                userTypeInput.value = userType;
                
                // Show selected user type
                userTypeText.textContent = `ประเภทผู้ใช้: ${userTypeLabels[userType]}`;
                selectedUserType.style.display = 'block';
                
                // Show login form
                loginForm.classList.add('active');
                
                // Scroll to form
                loginForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        function showForgotPassword() {
            const userType = userTypeInput.value;
            if (!userType) {
                alert('กรุณาเลือกประเภทผู้ใช้ก่อน');
                return;
            }
            
            // อัพเดทข้อมูลประเภทผู้ใช้ใน modal
            const modalUserType = document.getElementById('modalUserType');
            const userTypeIcon = getUserTypeIcon(userType);
            modalUserType.innerHTML = `<i class="${userTypeIcon}"></i> ประเภทผู้ใช้: ${userTypeLabels[userType]}`;
            
            // รีเซ็ตฟอร์ม
            resetModal();
            
            // แสดง modal
            document.getElementById('forgotPasswordModal').style.display = 'block';
            
            // โฟกัสที่ input อีเมล
            setTimeout(() => {
                document.getElementById('resetEmail').focus();
            }, 300);
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').style.display = 'none';
            resetModal();
        }

        function resetModal() {
            // รีเซ็ตฟอร์ม
            document.getElementById('resetEmail').value = '';
            document.getElementById('resetForm').style.display = 'block';
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('successMessage').style.display = 'none';
            
            // รีเซ็ตปุ่ม
            const resetBtn = document.querySelector('.reset-btn');
            resetBtn.disabled = false;
            resetBtn.innerHTML = '<i class="fas fa-paper-plane"></i> ส่งลิงค์รีเซ็ตรหัสผ่าน';
        }

        function getUserTypeIcon(userType) {
            const icons = {
                'student': 'fas fa-user-graduate',
                'parent': 'fas fa-user-friends',
                'advisor': 'fas fa-chalkboard-teacher',
                'teacher': 'fas fa-user-tie',
                'registrar': 'fas fa-clipboard-list',
                'admin': 'fas fa-user-cog'
            };
            return icons[userType] || 'fas fa-user';
        }

        function sendResetLink() {
            const email = document.getElementById('resetEmail').value;
            const userType = userTypeInput.value;
            
            // ตรวจสอบข้อมูล
            if (!email.trim()) {
                alert('กรุณากรอกอีเมล');
                document.getElementById('resetEmail').focus();
                return;
            }
            
            if (!validateEmail(email)) {
                alert('รูปแบบอีเมลไม่ถูกต้อง');
                document.getElementById('resetEmail').focus();
                return;
            }
            
            // แสดง loading
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'block';
            
            // จำลองการส่งอีเมล (ในการใช้งานจริงจะต้องเรียก API)
            setTimeout(() => {
                // ซ่อน loading
                document.getElementById('loadingSpinner').style.display = 'none';
                
                // แสดงข้อความสำเร็จ
                document.getElementById('successMessage').style.display = 'block';
                
                // ปิด modal อัตโนมัติหลังจาก 5 วินาที
                setTimeout(() => {
                    closeForgotPasswordModal();
                }, 5000);
                
                // ส่งข้อมูลไปยัง server (ในการใช้งานจริง)
                sendResetEmailToServer(email, userType);
            }, 2000);
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function sendResetEmailToServer(email, userType) {
            // ฟังก์ชันสำหรับส่งข้อมูลไปยัง server
            // ในการใช้งานจริงจะใช้ fetch หรือ AJAX
            
            const data = {
                email: email,
                user_type: userType,
                action: 'reset_password'
            };
            
            console.log('Sending reset email request:', data);
            
            /*
            // ตัวอย่างการส่งด้วย fetch
            fetch('process_reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Reset email sent:', result);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการส่งอีเมล กรุณาลองใหม่อีกครั้ง');
            });
            */
        }

        // ปิด modal เมื่อคลิกนอกพื้นที่
        window.onclick = function(event) {
            const modal = document.getElementById('forgotPasswordModal');
            if (event.target === modal) {
                closeForgotPasswordModal();
            }
        }

        // ปิด modal เมื่อกด ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeForgotPasswordModal();
            }
        });

        function showHelp() {
            alert('หากพบปัญหาในการเข้าสู่ระบบ กรุณาติดต่อเจ้าหน้าที่ หรือส่งอีเมลไปที่ support@kpsw.ac.th');
        }

        function contactAdmin() {
            alert('ติดต่อผู้ดูแลระบบ:\nโทรศัพท์: 034-351047\nอีเมล: support@kpsw.ac.th\nเวลาทำการ: จันทร์-ศุกร์ 8:30-16:30 น.');
        }

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const userType = userTypeInput.value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (!userType) {
                e.preventDefault();
                alert('กรุณาเลือกประเภทผู้ใช้');
                return;
            }

            if (!username.trim()) {
                e.preventDefault();
                alert('กรุณากรอกชื่อผู้ใช้');
                document.getElementById('username').focus();
                return;
            }

            if (!password.trim()) {
                e.preventDefault();
                alert('กรุณากรอกรหัสผ่าน');
                document.getElementById('password').focus();
                return;
            }

            // แสดง loading state
            const submitBtn = this.querySelector('.login-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเข้าสู่ระบบ...';
            submitBtn.disabled = true;
        });

        // Auto focus on username when form is shown
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.target.classList.contains('active')) {
                    setTimeout(() => {
                        document.getElementById('username').focus();
                    }, 300);
                }
            });
        });

        observer.observe(loginForm, { 
            attributes: true, 
            attributeFilter: ['class'] 
        });
    </script>
</body>
</html>