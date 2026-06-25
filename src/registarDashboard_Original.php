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
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Kanit -->
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
        }

        .navbar-brand {
            font-weight: 600;
            color: white !important;
            font-size: 1.5rem;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
            left: 0;
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

        /* Profile dropdown specific styles */
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
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            border: none;
            overflow: hidden;
            position: relative;
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
            transform: translateY(1.05);
            box-shadow: var(--shadow-hover);
        }

        .dashboard-card:hover::before {
            transform: scaleX(1);
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .dashboard-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }

        /* Custom animations */
        .animate-in {
            animation: slideInUp 0.8s ease;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Gradient variations for different cards */
        .card-students .card-icon { background: var(--gradient-primary); }
        .card-teachers .card-icon { background: var(--gradient-secondary); }
        .card-courses .card-icon { background: var(--gradient-success); }
        .card-reports .card-icon { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="60">
                <!--<i class="bi bi-mortarboard-fill me-2"></i>-->
                โรงเรียนกำแพงแสนวิทยา
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#dashboard">
                            <i class="bi bi-house-door me-1"></i>หน้าหลัก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#students">
                            <i class="bi bi-people me-1"></i>นักเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#teachers">
                            <i class="bi bi-person-badge me-1"></i>ครู
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reports">
                            <i class="bi bi-graph-up me-1"></i>รายงาน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#settings">
                            <i class="bi bi-gear me-1"></i>ตั้งค่า
                        </a>
                    </li>
                    <li class="nav-item dropdown profile-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> 
                            <?=$_SESSION['first_name'];?> <?=$_SESSION['last_name'];?>
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
                            <li>
                                <a class="dropdown-item" href="#account-settings" onclick="showAccountSettings()">
                                    <i class="bi bi-gear-fill"></i>ตั้งค่าบัญชี
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#notification-settings" onclick="showNotifications()">
                                    <i class="bi bi-bell-fill"></i>ตั้งค่าการแจ้งเตือน
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#activity-log" onclick="showActivityLog()">
                                    <i class="bi bi-clock-history"></i>ประวัติการใช้งาน
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#help" onclick="showHelp()">
                                    <i class="bi bi-question-circle-fill"></i>ช่วยเหลือ
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

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 80px;">
        <div class="container">
            <h1 class="hero-title">ระบบบริหารจัดการทะเบียนนักเรียน</h1>
            <p class="hero-subtitle">แดขบอร์ดสำหรับงานทะเบียน โรงเรียนกำแพงแสนวิทยา</p>
        </div>
    </div>

    <!-- Main Dashboard -->
    <div class="container">
        <div class="dashboard-container animate-in">
            <!-- Statistics Row -->
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

            <!-- Menu Cards -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card card-students">
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
                    <div class="dashboard-card card-teachers">
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
                    <div class="dashboard-card card-courses">
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
                    <div class="dashboard-card">
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
                    <div class="dashboard-card card-reports">
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
                    <div class="dashboard-card">
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
                    <div class="dashboard-card">
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
                    <div class="dashboard-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                            <i class="bi bi-shield-check-fill"></i>
                        </div>
                        <h3 class="card-title">การจัดการผู้ใช้งาน</h3>
                        <p class="card-description">
                            จัดการบัญชีผู้ใช้ กำหนดสิทธิ์การเข้าถึง รักษาความปลอดภัยของระบบ
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="dashboard-card">
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

        <!-- Footer -->
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
                        <p><i class="bi bi-envelope me-2"></i></i><a href='mailto:manager@kpsw.ac.th'>อีเมลล์ผู้อำนวยการ: manager@kpsw.ac.th</a></p>
                        <div class="social-links">
                            <a href="#" class="footer-link d-inline-block me-3"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="footer-link d-inline-block me-3"><i class="fab fa-line"></i></a>
                            <a href="#" class="footer-link d-inline-block me-3"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>เมนูหลัก</h5>
                        <p><a href="#" class="text-white-50 text-decoration-none" onclick="showGrades()">ผลการเรียน</a></p>
                        <p><a href="#" class="text-white-50 text-decoration-none" onclick="showSchedule()">ตารางเรียน</a></p>
                        <p><a href="#" class="text-white-50 text-decoration-none" onclick="showComplaints()">แจ้งเหตุ/ร้องเรียน</a></p>
                        <p><a href="#" class="text-white-50 text-decoration-none" onclick="showTeacherEvaluation()">ประเมินครู</a></p>
                    </div>
                    <div class="col-md-4">
                        <h5>ติดต่อเรา</h5>
                        <p>ช่วงเวลาทำการ: จันทร์ - ศุกร์ 08:00 - 16:30</p>
                        <p>สำหรับการสนับสนุนทางเทคนิค</p>
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

    <!-- Universal Modal -->
    <div class="modal fade" id="universalModal" tabindex="-1" aria-labelledby="universalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="universalModalLabel">Modal Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="universalModalBody">
                    <!-- Modal content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="modalActionButton" style="display: none;">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Sample data for demonstration
    const studentData = {
        grades: [
            { subject: 'คณิตศาสตร์', score: 85, grade: 'A', credit: 3 },
            { subject: 'ภาษาไทย', score: 78, grade: 'B+', credit: 2 },
            { subject: 'ภาษาอังกฤษ', score: 82, grade: 'A-', credit: 3 },
            { subject: 'วิทยาศาสตร์', score: 88, grade: 'A', credit: 4 },
            { subject: 'สังคมศึกษา', score: 75, grade: 'B', credit: 2 },
            { subject: 'ศิลปะ', score: 90, grade: 'A', credit: 1 }
        ],
        schedule: [
            { day: 'จันทร์', period: '1', subject: 'คณิตศาสตร์', room: 'A201', teacher: 'อ.สมชาย' },
            { day: 'จันทร์', period: '2', subject: 'ภาษาไทย', room: 'B105', teacher: 'อ.สมหญิง' },
            { day: 'จันทร์', period: '3', subject: 'วิทยาศาสตร์', room: 'Lab1', teacher: 'อ.สมศักดิ์' },
            { day: 'อังคาร', period: '1', subject: 'ภาษาอังกฤษ', room: 'C301', teacher: 'อ.มาลี' },
            { day: 'อังคาร', period: '2', subject: 'สังคมศึกษา', room: 'B204', teacher: 'อ.สมบัติ' },
            { day: 'อังคาร', period: '3', subject: 'ศิลปะ', room: 'Art1', teacher: 'อ.ศิริ' }
        ],
        attendance: {
            present: 185,
            absent: 8,
            late: 12,
            percentage: 95.2
        },
        activities: [
            { name: 'การแข่งขันคณิตศาสตร์', award: 'รางวัลชนะเลิศ', date: '2024-01-15' },
            { name: 'กิจกรรมจิตอาสา', award: 'ใบประกาศ', date: '2024-02-20' },
            { name: 'การแสดงดนตรี', award: 'เข้าร่วม', date: '2024-03-10' }
        ],
        teachers: {
            advisor: { name: 'อ.สมศรี ใจดี', id: 'T001' },
            subjectTeachers: [
                { name: 'อ.สมชาย', subject: 'คณิตศาสตร์', id: 'T002' },
                { name: 'อ.สมหญิง', subject: 'ภาษาไทย', id: 'T003' },
                { name: 'อ.สมศักดิ์', subject: 'วิทยาศาสตร์', id: 'T004' },
                { name: 'อ.มาลี', subject: 'ภาษาอังกฤษ', id: 'T005' }
            ]
        }
    };

    // Add smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'slideInUp 0.8s ease forwards';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.dashboard-card').forEach(card => {
        observer.observe(card);
    });

    // Add click handlers for dashboard cards
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.addEventListener('click', function() {
            // Add your navigation logic here
            const title = this.querySelector('.card-title').textContent;
            console.log('Clicked on:', title);
            // You can add actual navigation logic here
        });
    });

    // Add navbar background change on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.background = 'linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%)';
        } else {
            navbar.style.background = 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)';
        }
    });

    // Add dropdown menu animations
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.getElementById('profileDropdown');
        const dropdownMenu = dropdownToggle.nextElementSibling;
        
        dropdownToggle.addEventListener('show.bs.dropdown', function() {
            dropdownMenu.style.animation = 'fadeInDown 0.3s ease';
        });
    });

   // Utility functions
   function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            console.warn('Alert container not found. Cannot display alert:', message);
            return;
        }

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

    function showLoading(element) {
            element.innerHTML = '<div class="text-center"><div class="spinner"></div><p class="mt-2">กำลังโหลด...</p></div>';
    }

    function getGradeBadgeClass(grade) {
        if (grade.includes('A')) return 'bg-success';
        if (grade.includes('B')) return 'bg-primary';
        if (grade.includes('C')) return 'bg-warning';
        return 'bg-danger';
    }

    function getActivityBadgeClass(award) {
        if (award.includes('ชนะ')) return 'bg-success';
        if (award.includes('ใบประกาศ')) return 'bg-primary';
        return 'bg-info';
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Main menu functions
    function showGrades() {
        const content = `
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>วิชา</th>
                            <th>คะแนน</th>
                            <th>เกรด</th>
                            <th>หน่วยกิต</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${studentData.grades.map(grade => `
                            <tr>
                                <td>${grade.subject}</td>
                                <td>${grade.score}</td>
                                <td><span class="badge ${getGradeBadgeClass(grade.grade)}">${grade.grade}</span></td>
                                <td>${grade.credit}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                เกรดเฉลี่ยรวม: <strong>3.45</strong> | หน่วยกิตรวม: <strong>15</strong>
            </div>
        `;
        showModal('ผลการเรียนปีการศึกษา 2567', content);
    }

    function showSchedule() {
        const scheduleByDay = {};
        studentData.schedule.forEach(item => {
            if (!scheduleByDay[item.day]) {
                scheduleByDay[item.day] = [];
            }
            scheduleByDay[item.day].push(item);
        });

        let content = '';
        Object.keys(scheduleByDay).forEach(day => {
            content += `
                <div class="mb-4">
                    <h5 class="text-primary">${day}</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>คาบ</th>
                                    <th>วิชา</th>
                                    <th>ห้อง</th>
                                    <th>อาจารย์</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${scheduleByDay[day].map(item => `
                                    <tr>
                                        <td>${item.period}</td>
                                        <td>${item.subject}</td>
                                        <td>${item.room}</td>
                                        <td>${item.teacher}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        });

        showModal('ตารางเรียนรายสัปดาห์', content);
    }

    function showAttendance() {
        const { present, absent, late, percentage } = studentData.attendance;
        const total = present + absent + late;

        const content = `
            <div class="row text-center mb-4">
                <div class="col-md-3">
                    <div class="stat-card success">
                        <div class="stat-number">${present}</div>
                        <div class="stat-label">วันที่เข้าเรียน</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                        <div class="stat-number">${absent}</div>
                        <div class="stat-label">วันที่ขาดเรียน</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card warning">
                        <div class="stat-number">${late}</div>
                        <div class="stat-label">วันที่มาสาย</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card primary">
                        <div class="stat-number">${percentage}%</div>
                        <div class="stat-label">เปอร์เซ็นต์การเข้าเรียน</div>
                    </div>
                </div>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-calendar3 me-2"></i>
                จำนวนวันเรียนทั้งหมด: <strong>${total}</strong> วัน
            </div>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                การเข้าเรียนของคุณอยู่ในเกณฑ์ดี
            </div>
        `;
        showModal('สถิติการเข้าเรียน', content);
    }

    function showDocuments() {
        const content = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-text display-4 text-primary mb-3"></i>
                            <h5>ใบรับรองการเป็นนักเรียน</h5>
                            <p class="text-muted">เอกสารยืนยันสถานะการเป็นนักเรียน</p>
                            <button class="btn btn-primary" onclick="requestDocument('student-certificate')">ขอเอกสาร</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-bar-graph display-4 text-success mb-3"></i>
                            <h5>ใบแสดงผลการเรียน</h5>
                            <p class="text-muted">เอกสารแสดงเกรดและผลการเรียน</p>
                            <button class="btn btn-success" onclick="requestDocument('transcript')">ขอเอกสาร</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-person display-4 text-warning mb-3"></i>
                            <h5>ใบรับรองความประพฤติ</h5>
                            <p class="text-muted">เอกสารยืนยันความประพฤติของนักเรียน</p>
                            <button class="btn btn-warning" onclick="requestDocument('conduct-certificate')">ขอเอกสาร</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-medical display-4 text-info mb-3"></i>
                            <h5>ใบรับรองสุขภาพ</h5>
                            <p class="text-muted">เอกสารรับรองสุขภาพจากโรงเรียน</p>
                            <button class="btn btn-info" onclick="requestDocument('health-certificate')">ขอเอกสาร</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        showModal('ขอเอกสารออนไลน์', content);
    }

    function requestDocument(docType) {
        showAlert(`คำขอเอกสาร "${docType}" ถูกส่งเรียบร้อยแล้ว`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
    }

    function showTuitionFees() {
        const content = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-center">
                            <h5>ค่าเล่าเรียนภาคเรียนที่ 1</h5>
                            <h2>15,000 บาท</h2>
                            <span class="badge bg-success">ชำระแล้ว</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body text-center">
                            <h5>ค่าเล่าเรียนภาคเรียนที่ 2</h5>
                            <h2>15,000 บาท</h2>
                            <span class="badge bg-warning">รอชำระ</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>รายการ</th>
                            <th>จำนวนเงิน</th>
                            <th>วันที่ครบกำหนด</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ค่าเล่าเรียนภาคเรียนที่ 1</td>
                            <td>15,000 บาท</td>
                            <td>15 มิ.ย. 2567</td>
                            <td><span class="badge bg-success">ชำระแล้ว</span></td>
                        </tr>
                        <tr>
                            <td>ค่าหนังสือเรียน</td>
                            <td>2,500 บาท</td>
                            <td>20 มิ.ย. 2567</td>
                            <td><span class="badge bg-success">ชำระแล้ว</span></td>
                        </tr>
                        <tr>
                            <td>ค่าเล่าเรียนภาคเรียนที่ 2</td>
                            <td>15,000 บาท</td>
                            <td>15 พ.ย. 2567</td>
                            <td><span class="badge bg-warning">รอชำระ</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        showModal('ข้อมูลค่าเล่าเรียน', content);
    }


    function showComplaints() {
        const content = `
            <form id="complaintForm">
                <div class="mb-3">
                    <label for="complaintType" class="form-label">ประเภทการแจ้ง</label>
                    <select class="form-select" id="complaintType" required>
                        <option value="">เลือกประเภท</option>
                        <option value="complaint">ร้องเรียน</option>
                        <option value="suggestion">ข้อเสนอแนะ</option>
                        <option value="problem">แจ้งปัญหา</option>
                        <option value="request">ขอความช่วยเหลือ</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="complaintSubject" class="form-label">หัวข้อ</label>
                    <input type="text" class="form-control" id="complaintSubject" required>
                </div>
                <div class="mb-3">
                    <label for="complaintDetail" class="form-label">รายละเอียด</label>
                    <textarea class="form-control" id="complaintDetail" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="complaintFile" class="form-label">แนบไฟล์ (ถ้ามี)</label>
                    <input type="file" class="form-control" id="complaintFile" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                </div>
            </form>
        `;
        showModal('แจ้งเหตุ/ร้องเรียน', content, true, 'ส่งเรื่อง', function() {
            submitComplaint();
        });
    }

    function submitComplaint() {
        const form = document.getElementById('complaintForm');
        const type = document.getElementById('complaintType').value;
        const subject = document.getElementById('complaintSubject').value;
        const detail = document.getElementById('complaintDetail').value;
        const file = document.getElementById('complaintFile').files[0];

        if (!type || !subject || !detail) {
            showAlert('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
            return;
        }

        if (file && file.size > 5 * 1024 * 1024) {
            showAlert('ไฟล์มีขนาดใหญ่เกิน 5MB', 'warning');
            return;
        }

        showAlert(`ส่งเรื่อง "${subject}" เรียบร้อยแล้ว`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
        form.reset();
    }

    function showActivities() {
        const content = `
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>กิจกรรม</th>
                            <th>รางวัล/ผลงาน</th>
                            <th>วันที่</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${studentData.activities.map(activity => `
                            <tr>
                                <td>${activity.name}</td>
                                <td><span class="badge ${getActivityBadgeClass(activity.award)}">${activity.award}</span></td>
                                <td>${formatDate(activity.date)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <div class="alert alert-success">
                <i class="bi bi-trophy me-2"></i>
                คุณมีผลงานและกิจกรรมที่ดีเยี่ยม! เก็บสะสมต่อไป
            </div>
        `;
        showModal('กิจกรรมและรางวัล', content);
    }

    function showAnnouncements() {
        const content = `
            <div class="list-group">
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">ประกาศเปิดรับสมัครทุนการศึกษา</h5>
                        <small>3 วันที่แล้ว</small>
                    </div>
                    <p class="mb-1">เปิดรับสมัครทุนการศึกษาสำหรับนักเรียนชั้น ม.4-6 ที่มีผลการเรียนดี</p>
                    <small>ฝ่ายบริหารงานทั่วไป</small>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">กำหนดการสอบปลายภาค</h5>
                        <small>1 สัปดาห์ที่แล้ว</small>
                    </div>
                    <p class="mb-1">ประกาศกำหนดการสอบปลายภาคเรียนที่ 1 ปีการศึกษา 2567</p>
                    <small>ฝ่ายวิชาการ</small>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">กิจกรรมวันกีฬาสี</h5>
                        <small>2 สัปดาห์ที่แล้ว</small>
                    </div>
                    <p class="mb-1">เชิญชวนนักเรียนเข้าร่วมกิจกรรมวันกีฬาสีประจำปี</p>
                    <small>ฝ่ายกิจการนักเรียน</small>
                </div>
            </div>
        `;
        showModal('ประกาศสำคัญ', content);
    }


    // Teacher Evaluation Functions
    function showTeacherEvaluation() {
        const content = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-check display-4 text-primary mb-3"></i>
                            <h5>ประเมินครูที่ปรึกษา</h5>
                            <p class="text-muted">ให้คะแนนและข้อเสนอแนะสำหรับครูที่ปรึกษาของคุณ</p>
                            <button class="btn btn-primary" onclick="evaluateAdvisor()">เริ่มประเมิน</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-book-half display-4 text-success mb-3"></i>
                            <h5>ประเมินครูผู้สอน</h5>
                            <p class="text-muted">ให้คะแนนและข้อเสนอแนะสำหรับครูผู้สอนในแต่ละวิชา</p>
                            <button class="btn btn-success" onclick="evaluateSubjectTeacher()">เริ่มประเมิน</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        showModal('ประเมินครู', content);
    }

    function evaluateAdvisor() {
        const advisor = studentData.teachers.advisor;
        const content = `
            <form id="advisorEvaluationForm">
                <div class="mb-3">
                    <label class="form-label">ครูที่ปรึกษา</label>
                    <input type="text" class="form-control" value="${advisor.name}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">คะแนนการให้คำปรึกษา (1-5)</label>
                    <select class="form-select" id="advisorRating" required>
                        <option value="">เลือกคะแนน</option>
                        <option value="1">1 - ปรับปรุง</option>
                        <option value="2">2 - พอใช้</option>
                        <option value="3">3 - ปานกลาง</option>
                        <option value="4">4 - ดี</option>
                        <option value="5">5 - ดีเยี่ยม</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ข้อเสนอแนะ</label>
                    <textarea class="form-control" id="advisorFeedback" rows="5" placeholder="กรุณาให้ข้อเสนอแนะเพื่อการพัฒนา"></textarea>
                </div>
            </form>
        `;
        showModal('ประเมินครูที่ปรึกษา', content, true, 'ส่งการประเมิน', function() {
            const rating = document.getElementById('advisorRating').value;
            const feedback = document.getElementById('advisorFeedback').value;

            if (!rating) {
                showAlert('กรุณาเลือกคะแนนการประเมิน', 'warning');
                return;
            }

            showAlert(`ส่งการประเมินครูที่ปรึกษา "${advisor.name}" เรียบร้อยแล้ว`, 'success');
            bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
            document.getElementById('advisorEvaluationForm').reset();
        });
    }

    function evaluateSubjectTeacher() {
        const content = `
            <form id="subjectTeacherEvaluationForm">
                <div class="mb-3">
                    <label class="form-label">เลือกครูผู้สอน</label>
                    <select class="form-select" id="teacherSelect" required>
                        <option value="">เลือกครู</option>
                        ${studentData.teachers.subjectTeachers.map(teacher => `
                            <option value="${teacher.id}">${teacher.name} - ${teacher.subject}</option>
                        `).join('')}
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">คะแนนการสอน (1-5)</label>
                    <select class="form-select" id="teacherRating" required>
                        <option value="">เลือกคะแนน</option>
                        <option value="1">1 - ปรับปรุง</option>
                        <option value="2">2 - พอใช้</option>
                        <option value="3">3 - ปานกลาง</option>
                        <option value="4">4 - ดี</option>
                        <option value="5">5 - ดีเยี่ยม</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ข้อเสนอแนะ</label>
                    <textarea class="form-control" id="teacherFeedback" rows="5" placeholder="กรุณาให้ข้อเสนอแนะเพื่อการพัฒนา"></textarea>
                </div>
            </form>
        `;
        showModal('ประเมินครูผู้สอน', content, true, 'ส่งการประเมิน', function() {
            const teacherId = document.getElementById('teacherSelect').value;
            const rating = document.getElementById('teacherRating').value;
            const feedback = document.getElementById('teacherFeedback').value;

            if (!teacherId || !rating) {
                showAlert('กรุณาเลือกครูและคะแนนการประเมิน', 'warning');
                return;
            }

            const teacher = studentData.teachers.subjectTeachers.find(t => t.id === teacherId);
            showAlert(`ส่งการประเมินครู "${teacher.name} - ${teacher.subject}" เรียบร้อยแล้ว`, 'success');
            bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
            document.getElementById('subjectTeacherEvaluationForm').reset();
        });
    }

    function showProfileInfo() {
      const content = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="student-avatar mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="uploadProfilePicture()">เปลี่ยนรูปโปรไฟล์</button>
            </div>
            <div class="col-md-8">
                <form id="profileForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" value="สมชาย" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" value="ใจดี" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">เลขประจำตัวนักเรียน</label>
                            <input type="text" class="form-control" value="65001234567" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชั้นเรียน</label>
                            <input type="text" class="form-control" value="ม.4/1" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control" id="profileEmail" value="somchai.jaidee@student.kps.ac.th">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="profilePhone" value="081-xxx-xxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ที่อยู่</label>
                        <textarea class="form-control" id="profileAddress" rows="3">123/45 หมู่ 6 ตำบลกำแพงแสน อำเภอกำแพงแสน จังหวัดนครปฐม 73140</textarea>
                    </div>
                </form>
            </div>
        </div>
      `;
      showModal('ข้อมูลส่วนตัว', content, true, 'บันทึกการเปลี่ยนแปลง', function() {
        const email = document.getElementById('profileEmail').value;
        const phone = document.getElementById('profilePhone').value;
        const address = document.getElementById('profileAddress').value;

        if (!email.includes('@') || !phone.match(/^\d{3}-xxx-xxxx$/)) {
            showAlert('กรุณากรอกอีเมลหรือเบอร์โทรศัพท์ให้ถูกต้อง', 'warning');
            return;
        }

        showAlert('บันทึกข้อมูลเรียบร้อยแล้ว', 'success');
        bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
      });
    }

    function uploadProfilePicture() {
        showAlert('อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success');
    }

    function showChangePassword() {
        const content = `
            <form id="changePasswordForm">
                <div class="mb-3">
                    <label for="currentPassword" class="form-label">รหัสผ่านปัจจุบัน</label>
                    <input type="password" class="form-control" id="currentPassword" required>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" class="form-control" id="newPassword" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" class="form-control" id="confirmPassword" required>
                </div>
            </form>
        `;
        showModal('เปลี่ยนรหัสผ่าน', content, true, 'บันทึก', function() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                showAlert('กรุณากรอกข้อมูลให้ครบถ้วน', 'warning');
                return;
            }

            if (newPassword !== confirmPassword) {
                showAlert('รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน', 'warning');
                return;
            }

            if (newPassword.length < 8) {
                showAlert('รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร', 'warning');
                return;
            }

            showAlert('เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success');
            bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
            document.getElementById('changePasswordForm').reset();
        });
    }

    function showNotifications() {
        const content = `
            <div class="list-group">
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">แจ้งเตือนการชำระค่าเล่าเรียน</h6>
                        <small>วันนี้</small>
                    </div>
                    <p class="mb-1">กรุณาชำระค่าเล่าเรียนภาคเรียนที่ 2 ภายในวันที่ 15 พ.ย. 2567</p>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">กำหนดการสอบปลายภาค</h6>
                        <small>3 วันที่แล้ว</small>
                    </div>
                    <p class="mb-1">สอบปลายภาคเรียนที่ 1 วันที่ 20-25 ธ.ค. 2567</p>
                </div>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">กิจกรรมกีฬาสี</h6>
                        <small>1 สัปดาห์ที่แล้ว</small>
                    </div>
                    <p class="mb-1">เตรียมตัวเข้าร่วมกีฬาสีวันที่ 10 ธ.ค. 2567</p>
                </div>
            </div>
        `;
        showModal('การแจ้งเตือน', content);
    }

    function showPrivacySettings() {
        const content = `
            <form id="privacyForm">
                <div class="mb-3">
                    <label class="form-label">การมองเห็นข้อมูลส่วนตัว</label>
                    <select class="form-select">
                        <option value="public">ทุกคน</option>
                        <option value="teachers" selected>เฉพาะอาจารย์</option>
                        <option value="private">เฉพาะฉัน</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        <label class="form-check-label" for="emailNotifications">
                            รับการแจ้งเตือนทางอีเมล
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="smsNotifications">
                        <label class="form-check-label" for="smsNotifications">
                            รับการแจ้งเตือนทาง SMS
                        </label>
                    </div>
                </div>
            </form>
        `;
        showModal('ตั้งค่าความเป็นส่วนตัว', content, true, 'บันทึก', function() {
            showAlert('บันทึกการตั้งค่าความเป็นส่วนตัวเรียบร้อยแล้ว', 'success');
            bootstrap.Modal.getInstance(document.getElementById('universalModal')).hide();
        });
    }

    // Handle logout function
    function handleLogout() {
        if (confirm('คุณต้องการออกจากระบบหรือไม่?')) {
            // Add your logout logic here
            console.log('Logging out...');
            // Example: window.location.href = '/logout';
            showAlert('กำลังออกจากระบบ...', 'success');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1000);
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', () => {
        showAlert('ยินดีต้อนรับสู่แดชบอร์ดครูงานทะเบียน!', 'success');
    });
    </script>
</body>
</html>