<?php
session_start();
include('db_connection.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดสำหรับครูงานทะเบียน - โรงเรียนกำแพงแสนวิทยา</title>
    
    <link rel="shortcut icon" href="images/favicon.svg" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-primary: 0 10px 25px rgba(0,0,0,0.1);
            --shadow-hover: 0 15px 35px rgba(0,0,0,0.15);
        }

        * {
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Header Styles */
        .navbar {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-primary);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 600;
            color: white !important;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 0.2rem;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        /* 🌟 CSS ส่วนที่เพิ่มไฮไลท์เมนูด้านบน 🌟 */
        .navbar-nav .nav-link.active-menu {
            color: #ffd700 !important; /* สีเหลืองทอง */
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15); /* พื้นหลังโปร่งแสง */
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            box-shadow: var(--shadow-primary);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            color: var(--dark-text);
            padding: 0.7rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            opacity: 0.3;
        }

        .profile-dropdown .dropdown-item.text-danger:hover {
            background: var(--danger-color);
            color: white;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Main Content */
        .hero-section {
            padding: 4rem 0;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            font-weight: 400;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Dashboard Cards */
        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow-primary);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-primary);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent; 
            overflow: hidden;
            position: relative;
            height: calc(100% - 1.5rem);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .dashboard-card:hover::before {
            transform: scaleX(1);
        }
        
        /* 🌟 CSS ไฮไลท์การ์ด (Active Card) 🌟 */
        .dashboard-card.active-card {
            border: 2px dashed var(--primary-color) !important;
            background-color: #f4f7fe;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
        }
        .dashboard-card.active-card::after {
            content: '\F26A'; 
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 10px;
            right: 15px;
            color: var(--primary-color);
            font-size: 1.2rem;
            animation: popIn 0.3s ease;
        }
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            background: var(--gradient-primary);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .card-description {
            color: var(--secondary-color);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Stats Cards */
        .stats-card {
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translate(-100%, -100%) rotate(0deg); }
            100% { transform: translate(100%, 100%) rotate(360deg); }
        }

        .stats-card:hover {
            transform: scale(1.05);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-content {
            text-align: center;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #64748b;
        }

        .footer p {
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }

        /* Custom animations */
        .animate-in { animation: slideInUp 0.8s ease; }
        @keyframes slideInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Gradient variations for different cards */
        .card-students .card-icon { background: var(--gradient-primary); }
        .card-teachers .card-icon { background: var(--gradient-secondary); }
        .card-courses .card-icon { background: var(--gradient-success); }
        .card-reports .card-icon { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="60">
                โรงเรียนกำแพงแสนวิทยา
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-menu-item active-menu" href="#dashboard">
                            <i class="bi bi-house-door me-1"></i>หน้าหลัก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-menu-item" href="manage_students.php">
                            <i class="bi bi-people me-1"></i>นักเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-menu-item" href="#import" onclick="showImportData()">
                            <i class="bi bi-cloud-upload me-1"></i>นำเข้าข้อมูล
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-menu-item" href="#reports">
                            <i class="bi bi-graph-up me-1"></i>รายงาน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-menu-item" href="#settings">
                            <i class="bi bi-gear me-1"></i>ตั้งค่า
                        </a>
                    </li>
                    <li class="nav-item dropdown profile-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if(isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])): ?>
                                <img src="images/<?php echo $_SESSION['profile_picture']; ?>" class="rounded-circle me-1" style="width: 25px; height: 25px; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-person-circle me-1"></i> 
                            <?php endif; ?>
                            <?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'ผู้ใช้งาน'; ?> 
                            <?php echo isset($_SESSION['last_name']) ? $_SESSION['last_name'] : ''; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="#profile-info" onclick="showProfileInfo()">
                                    <i class="bi bi-person-lines-fill"></i>ข้อมูลส่วนตัว
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#change-password" onclick="showChangePassword()">
                                    <i class="bi bi-key-fill"></i>เปลี่ยนรหัสผ่าน
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#logout" onclick="handleLogout()">
                                    <i class="bi bi-box-arrow-right"></i>ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section" style="margin-top: 80px;">
        <div class="container">
            <h1 class="hero-title">ระบบบริหารจัดการทะเบียนนักเรียน</h1>
            <p class="hero-subtitle">แดชบอร์ดสำหรับงานทะเบียน โรงเรียนกำแพงแสนวิทยา</p>
        </div>
    </div>

    <div class="container" id="dashboard">
        <div class="dashboard-container animate-in">
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">1,247</div>
                        <div class="stats-label">นักเรียนทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card" style="background: var(--gradient-secondary);">
                        <div class="stats-number">87</div>
                        <div class="stats-label">ครูและบุคลากร</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card" style="background: var(--gradient-success);">
                        <div class="stats-number">36</div>
                        <div class="stats-label">ห้องเรียน</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                        <div class="stats-number">98.5%</div>
                        <div class="stats-label">อัตราการมาเรียน</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card card-students clickable-card" id="card-students" onclick="window.location.href='manage_students.php'">
                        <div class="card-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="card-title">จัดการข้อมูลนักเรียน</h3>
                        <p class="card-description">
                            ลงทะเบียนนักเรียนใหม่ แก้ไขข้อมูล ติดตามสถานะการเรียน และจัดการข้อมูลส่วนตัวของนักเรียน
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card card-teachers clickable-card" id="card-teachers">
                        <div class="card-icon">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h3 class="card-title">จัดการข้อมูลครู</h3>
                        <p class="card-description">
                            บริหารจัดการข้อมูลครูและบุคลากร การมอบหมายงาน และติดตามผลการปฏิบัติงาน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card clickable-card" id="card-import" onclick="showImportData()">
                        <div class="card-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        <h3 class="card-title">นำเข้าข้อมูล (Import)</h3>
                        <p class="card-description">
                            อัปโหลดไฟล์ Excel/CSV เพื่อนำเข้าผลการเรียน, รายชื่อนักเรียน, หรือข้อมูลตารางสอนเข้าระบบ
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card card-courses clickable-card" id="card-courses">
                        <div class="card-icon">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <h3 class="card-title">หลักสูตรและวิชาเรียน</h3>
                        <p class="card-description">
                            จัดการหลักสูตร กำหนดตารางเรียน การลงทะเบียนเรียน และติดตามผลการเรียน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card clickable-card" id="card-attendance">
                        <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <h3 class="card-title">การเข้าเรียนและลา</h3>
                        <p class="card-description">
                            ติดตามการเข้าเรียน บันทึกการลา สถิติการมาเรียน และรายงานการขาดเรียน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card card-reports clickable-card" id="card-reports">
                        <div class="card-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="card-title">รายงานและสถิติ</h3>
                        <p class="card-description">
                            ดูรายงานผลการเรียน สถิติโรงเรียน การวิเคราะห์ข้อมูล และส่งออกรายงาน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card clickable-card" id="card-finance">
                        <div class="card-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h3 class="card-title">การเงินและค่าธรรมเนียม</h3>
                        <p class="card-description">
                            จัดการค่าเล่าเรียน ค่าธรรมเนียมต่างๆ การชำระเงิน และรายงานทางการเงิน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card clickable-card" id="card-communication">
                        <div class="card-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h3 class="card-title">การสื่อสารและแจ้งเตือน</h3>
                        <p class="card-description">
                            ส่งประกาศ แจ้งข่าวสาร ติดต่อผู้ปกครอง และจัดการการสื่อสารภายในโรงเรียน
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card clickable-card" id="card-settings">
                        <div class="card-icon" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <h3 class="card-title">ตั้งค่าระบบ</h3>
                        <p class="card-description">
                            กำหนดค่าต่างๆ ของระบบ การสำรองข้อมูล การบำรุงรักษา และการอัพเดท
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-md-4">
                        <h5><img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="40"> โรงเรียนกำแพงแสนวิทยา</h5>
                        <p><i class="bi bi-geo-alt me-2"></i>123 ถนนกำแพงแสน อำเภอกำแพงแสน<br />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จังหวัดนครปฐม 73140</p>
                        <p><i class="bi bi-telephone me-2"></i>Tel.034-351047, Fax 034-351264</p>
                        <p><i class="bi bi-envelope me-2"></i><a href='mailto:info@kpsw.ac.th'>info@kpsw.ac.th</a></p>
                    </div>
                    <div class="col-md-4">
                        <h5>เมนูด่วน</h5>
                        <p><a href="#" class="text-white-50 text-decoration-none" onclick="showImportData()">นำเข้าข้อมูลผลการเรียน</a></p>
                        <p><a href="manage_students.php" class="text-white-50 text-decoration-none">ตรวจสอบข้อมูลนักเรียน</a></p>
                        <p><a href="#" class="text-white-50 text-decoration-none">ออกรายงาน ปพ.</a></p>
                    </div>
                    <div class="col-md-4">
                        <h5>ติดต่อผู้ดูแลระบบ</h5>
                        <p>จันทร์ - ศุกร์ 08:00 - 16:30</p>
                        <p><i class="bi bi-envelope me-2"></i><a href='mailto:support@kpsw.ac.th'>support@kpsw.ac.th</a></p>
                    </div>
                </div>
                <hr class="my-4 opacity-25">
                <div class="text-center">
                    <p>&copy; 2568 โรงเรียนกำแพงแสนวิทยา. สงวนลิขสิทธิ์ทุกประการ.</p>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="universalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="universalModalLabel">Modal Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="universalModalBody">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="modalActionButton" style="display: none;">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Utility functions
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        const alertId = 'alert-' + Date.now();
        const alertHTML = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        setTimeout(() => {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
                bsAlert.close();
            }
        }, 5000);
    }

    function showModal(title, content, showActionButton = false, actionButtonText = 'บันทึก', actionCallback = null) {
        document.getElementById('universalModalLabel').textContent = title;
        document.getElementById('universalModalBody').innerHTML = content;

        const actionButton = document.getElementById('modalActionButton');
        if (showActionButton) {
            actionButton.style.display = 'inline-block';
            actionButton.textContent = actionButtonText;
            actionButton.onclick = actionCallback;
        } else {
            actionButton.style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('universalModal'));
        modal.show();
    }

    // ==========================================
    // 🌟 ระบบ Sync Highlight อัตโนมัติ 🌟
    // ==========================================
    function syncHighlight(targetId) {
        // 1. จัดการแถบเมนูด้านบน
        document.querySelectorAll('.nav-menu-item').forEach(m => m.classList.remove('active-menu'));
        const navMenu = document.querySelector(`.nav-menu-item[href="#${targetId}"]`);
        if (navMenu) navMenu.classList.add('active-menu');

        // 2. จัดการการ์ดด้านล่าง
        document.querySelectorAll('.clickable-card').forEach(c => c.classList.remove('active-card'));
        if (targetId !== 'dashboard') { 
            const card = document.getElementById(`card-${targetId}`);
            if (card) card.classList.add('active-card');
        }
    }

    // ติดตั้ง Event เมื่อคลิกการ์ด
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', function() {
            const cardId = this.id.replace('card-', '');
            syncHighlight(cardId);
        });
    });

    // ติดตั้ง Event เมื่อคลิกเมนูด้านบน
    document.querySelectorAll('.nav-menu-item').forEach(menu => {
        menu.addEventListener('click', function(e) {
            // ถ้าลิงก์ไปหน้าอื่น (เช่น manage_students.php) ปล่อยให้มันทำงานปกติ
            if (!this.getAttribute('href').startsWith('#')) return;
            
            const targetId = this.getAttribute('href').replace('#', '');
            syncHighlight(targetId);
        });
    });

    // ==========================================
    // 🌟 ฟังก์ชันจัดการข้อมูลส่วนตัว + รูปโปรไฟล์ 🌟
    // ==========================================
    function showProfileInfo() {
        const currentPic = '<?php echo isset($_SESSION["profile_picture"]) && !empty($_SESSION["profile_picture"]) ? "images/".$_SESSION["profile_picture"] : ""; ?>';
        const defaultPic = '<i class="bi bi-person-fill"></i>';
        const picHtml = currentPic 
            ? `<img src="${currentPic}" alt="Profile" class="img-fluid rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff;">` 
            : `<div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-light rounded-circle shadow-sm" style="width: 120px; height: 120px; font-size: 4rem; color: #cbd5e1; border: 3px solid #fff;">${defaultPic}</div>`;

        const content = `
            <form id="profileForm" enctype="multipart/form-data">
                <div class="row mb-4 align-items-center">
                    <div class="col-md-4 text-center">
                        <div id="profilePicPreview" class="mb-3 position-relative d-inline-block">
                            ${picHtml}
                        </div>
                        <div class="mb-2">
                            <label for="profilePicture" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                <i class="bi bi-camera-fill me-1"></i> เปลี่ยนรูปภาพ
                            </label>
                            <input class="form-control d-none" type="file" id="profilePicture" name="profile_picture" accept="image/jpeg, image/png, image/jpg" onchange="previewProfilePic(this)">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">รองรับ .jpg, .png (ไม่เกิน 2MB)</small>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">คำนำหน้า</label>
                                <input type="text" class="form-control" id="profilePrefix" value="<?php echo isset($_SESSION['prefix_name']) ? $_SESSION['prefix_name'] : ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ชื่อ</label>
                                <input type="text" class="form-control" id="profileFirstName" value="<?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">นามสกุล</label>
                                <input type="text" class="form-control" id="profileLastName" value="<?php echo isset($_SESSION['last_name']) ? $_SESSION['last_name'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold">อีเมล</label>
                                <input type="email" class="form-control" id="profileEmail" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control" id="profilePhone" value="<?php echo isset($_SESSION['phone']) ? $_SESSION['phone'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light border border-warning" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle-fill text-warning me-1"></i> หากต้องการเปลี่ยนรหัสผ่าน กรุณาใช้เมนู <strong>"เปลี่ยนรหัสผ่าน"</strong>
                </div>
            </form>
        `;
        
        showModal('ข้อมูลส่วนตัว', content, true, 'บันทึกการเปลี่ยนแปลง', function() {
            saveProfileInfo();
        });
    }

    function previewProfilePic(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePicPreview');
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff;">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function saveProfileInfo() {
        const prefix = document.getElementById('profilePrefix').value;
        const firstName = document.getElementById('profileFirstName').value;
        const lastName = document.getElementById('profileLastName').value;
        const email = document.getElementById('profileEmail').value;
        const phone = document.getElementById('profilePhone').value;
        const profilePic = document.getElementById('profilePicture').files[0];

        if (!firstName || !lastName || !email) {
            showAlert('กรุณากรอกข้อมูล ชื่อ, นามสกุล และอีเมล ให้ครบถ้วน', 'warning');
            return;
        }

        const btn = document.getElementById('modalActionButton');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังอัปโหลด...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('prefix_name', prefix);
        formData.append('first_name', firstName);
        formData.append('last_name', lastName);
        formData.append('email', email);
        formData.append('phone', phone);
        
        if (profilePic) {
            formData.append('profile_picture', profilePic);
        }

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showAlert('อัปเดตข้อมูลและรูปโปรไฟล์เรียบร้อยแล้ว!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('เกิดข้อผิดพลาด: ' + data.message, 'danger');
                btn.innerHTML = 'บันทึกการเปลี่ยนแปลง';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'danger');
            btn.innerHTML = 'บันทึกการเปลี่ยนแปลง';
            btn.disabled = false;
        });
    }

    // ==========================================
    // 🌟 ฟังก์ชันเปลี่ยนรหัสผ่าน (แบบไม่ต้องใช้รหัสเดิม) 🌟
    // ==========================================
    function showChangePassword() {
        const content = `
            <div class="alert alert-info py-2" style="font-size: 0.9rem;">
                <i class="bi bi-info-circle-fill me-1"></i> คุณสามารถกำหนดรหัสผ่านใหม่สำหรับเข้าสู่ระบบได้ทันที
            </div>
            <form id="changePasswordForm">
                <div class="mb-3">
                    <label for="newPassword" class="form-label fw-bold text-primary">รหัสผ่านใหม่</label>
                    <input type="password" class="form-control border-primary form-control-lg" id="newPassword" placeholder="อย่างน้อย 8 ตัวอักษร" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label fw-bold text-primary">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" class="form-control border-primary form-control-lg" id="confirmPassword" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>
                </div>
            </form>
        `;
        showModal('ตั้งรหัสผ่านใหม่', content, true, 'บันทึกรหัสผ่าน', function() {
            savePassword();
        });
    }

    function savePassword() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!newPassword || !confirmPassword) {
            showAlert('กรุณากรอกข้อมูลให้ครบทุกช่อง', 'warning');
            return;
        }

        if (newPassword !== confirmPassword) {
            showAlert('รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน', 'danger');
            return;
        }

        if (newPassword.length < 8) {
            showAlert('รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร', 'warning');
            return;
        }

        const btn = document.getElementById('modalActionButton');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('new_password', newPassword);

        fetch('update_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showAlert('เปลี่ยนรหัสผ่านเรียบร้อยแล้ว!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
            } else {
                showAlert('ข้อผิดพลาด: ' + data.message, 'danger');
                btn.innerHTML = 'บันทึกรหัสผ่าน';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'danger');
            btn.innerHTML = 'บันทึกรหัสผ่าน';
            btn.disabled = false;
        });
    }

    // ==========================================
    // 🌟 ฟังก์ชันจัดการการนำเข้าข้อมูล 🌟
    // ==========================================
    function showImportData() {
        const content = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill me-2"></i> กรุณาเตรียมไฟล์ข้อมูลให้อยู่ในรูปแบบ .csv ตามเทมเพลตที่ส่งออกมาจากระบบของโรงเรียน
            </div>
            <form id="importDataForm" action="import_grades.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="importType" class="form-label fw-bold">1. ประเภทข้อมูลที่ต้องการนำเข้า</label>
                        <select class="form-select" id="importType" name="importType" required>
                            <option value="">-- เลือกประเภทข้อมูล --</option>
                            <option value="grades">ผลการเรียน (Grade_1-1.csv)</option>
                            <option value="students">รายชื่อนักเรียน (Students.csv)</option>
                            <option value="schedule">ตารางสอน (Schedule.csv)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academicYear" class="form-label fw-bold">2. ปีการศึกษา</label>
                        <input type="number" class="form-control" id="academicYear" name="academic_year" value="2567" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="term" class="form-label fw-bold">3. ภาคเรียนที่</label>
                        <select class="form-select" id="term" name="term" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="importFile" class="form-label fw-bold">4. เลือกไฟล์ข้อมูล (.csv)</label>
                        <input class="form-control form-control-lg" type="file" id="importFile" name="file" accept=".csv" required>
                        <div class="form-text text-success mt-2">
                            * ระบบจะทำการตรวจสอบเลขประจำตัวนักเรียนและรหัสวิชาให้อัตโนมัติ
                        </div>
                    </div>
                </div>
                <button type="submit" id="realSubmitImport" name="import" class="d-none"></button>
            </form>
        `;
        
        showModal('นำเข้าข้อมูลสู่ระบบ (Import Data)', content, true, 'เริ่มอัปโหลดข้อมูล', function() {
            const importType = document.getElementById('importType').value;
            const fileInput = document.getElementById('importFile').value;
            
            if(!importType) {
                showAlert('กรุณาเลือกประเภทข้อมูลที่ต้องการนำเข้า', 'warning');
                return;
            }
            if(!fileInput) {
                showAlert('กรุณาเลือกไฟล์ .csv ที่ต้องการนำเข้า', 'warning');
                return;
            }
            
            const btn = document.getElementById('modalActionButton');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>กำลังนำเข้า...';
            btn.disabled = true;

            document.getElementById('realSubmitImport').click();
        });
    }

    function handleLogout() {
        if (confirm('คุณต้องการออกจากระบบหรือไม่?')) {
            showAlert('กำลังออกจากระบบ...', 'success');
            setTimeout(() => {
                window.location.href = 'index.php'; 
            }, 1000);
        }
    }
    </script>
</body>
</html>