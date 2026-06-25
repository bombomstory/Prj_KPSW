<?php
session_start();
include('db_connection.php');
include('lib.php');

// 🔐 1. ตรวจสอบสิทธิ์ผู้ปกครอง
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'parent') {
    header('Location: login-error.php');
    exit;
}

// 👶 2. ดึง student_id ของลูกจากตาราง parents_items
$parent_user_id = $_SESSION['user_id'];
$student_id = 0;

$sql_child = "SELECT pi.student_id FROM parents_items pi 
              INNER JOIN parents p ON pi.parent_id = p.parent_id 
              WHERE p.user_id = ? LIMIT 1";
$stmt_child = $conn->prepare($sql_child);
$stmt_child->bind_param("i", $parent_user_id);
$stmt_child->execute();
$res_child = $stmt_child->get_result();
if ($row = $res_child->fetch_assoc()) {
    $student_id = $row['student_id'];
}


// 🌟 ตรวจสอบสิทธิ์และดึงข้อมูลยอดค้างชำระค่าเทอมสำหรับแถบแจ้งเตือนแจ้งเหตุ
$student_id = getStudentID($student_id);
$current_term = get_current_term();
$current_year = get_current_year();
$has_unpaid_fees = hasUnpaidFees($student_id, $current_year, $current_term);

// 🌟 ประกาศตัวแปรเป็น Array ว่างรอไว้ก่อนเลยเพื่อป้องกัน Error "Undefined variable"
$complaint_types_array = [];

// ดึงข้อมูลประเภทการแจ้ง และเรียงลำดับตาม id
$type_sql = "SELECT `id`, `name` FROM `complaint_types` ORDER BY `id` ASC";
$type_result = $conn->query($type_sql);

if ($type_result) {
    while ($row = $type_result->fetch_assoc()) {
        $complaint_types_array[] = $row;
    }
}

$doc_types_array = [];
$doc_sql = "SELECT `id`, `name` FROM `document_types` ORDER BY `id` ASC";
$doc_result = $conn->query($doc_sql);
if ($doc_result) {
    while ($row = $doc_result->fetch_assoc()) {
        $doc_types_array[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดนักเรียน - โรงเรียนกำแพงแสนวิทยา</title>

    <link rel="shortcut icon" href="images/favicon.svg" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
       
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
    .star-yellow {
        color: gold;
    }

    .star-rating {
        direction: rtl;
        display: flex;
        justify-content: start;
    }

    .star-rating input[type="radio"] {
        display: none;
    }

    .star-rating label {
        font-size: 1.5rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #f5c518;
    }

    .rating-description {
        font-size: 0.9rem;
        color: #666;
        margin-top: 4px;
    }
    </style>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);
            --success-gradient: linear-gradient(135deg, #28a745 0%, #71dd37 100%);
            --warning-gradient: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        }

        body {
            font-family: 'Kanit';
            background: #f4f7fc;
            padding-top: 70px;
        }

        .navbar {
            background: var(--primary-gradient);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar-brand, .nav-link {
            color: white !important;
            transition: all 0.3s;
        }

        .nav-link:hover {
            transform: translateY(-2px);
        }

        .hero-section {
            background: url('images/banner02_2568.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }

        .hero-overlay {
            background: rgba(0,0,0,0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .dashboard-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .menu-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .menu-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.8rem;
        }

        .menu-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .menu-description {
            font-size: 0.9rem;
            color: #666;
        }

        .quick-access {
            background: #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .quick-access-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .quick-access-item:hover {
            background: #dee2e6;
            transform: translateX(5px);
        }

        .stat-card {
            background: var(--primary-gradient);
            border-radius: 10px;
            padding: 15px;
            color: white;
            text-align: center;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9rem;
        }

        .success { background: var(--success-gradient); }
        .warning { background: var(--warning-gradient); }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .student-avatar {
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .hero-section {
                padding: 60px 0;
            }
        }

        /* Start rating vote */
        .star-rating {
            direction: rtl;
            display: inline-flex;
            gap: 0.25em;
            font-size: 1.5rem;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f5c518;
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
    
        .footer-link {
            color: #9ca3af;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: var(--accent-color);
        }        

        /* Evaluation Status Styles */
        .evaluation-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .evaluation-summary h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .summary-stat {
            display: flex;
            flex-direction: column;
        }

        .summary-stat .number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-stat .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Enhanced Star Rating */
        .star-display {
            display: inline-flex;
            gap: 2px;
            align-items: center;
            margin: 5px 0;
        }

        .star-display .star {
            font-size: 18px;
            color: #fbbf24;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .star-display .star.empty {
            color: #e5e7eb;
        }

        .average-score {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }

        /* Evaluation History */
        .evaluation-history {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .evaluation-item {
            border-left: 4px solid #10b981;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .evaluation-item h6 {
            color: #1f2937;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .evaluation-date {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .criteria-scores {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .criteria-score {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f3f4f6;
            border-radius: 6px;
        }

        .criteria-name {
            font-size: 13px;
            color: #374151;
            flex: 1;
            margin-right: 10px;
        }

        /* Alert Styles for Evaluation */
        .evaluation-alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }

        .evaluation-alert.alert-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
        }

        .evaluation-alert.alert-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            color: white;
        }

        .evaluation-alert.alert-info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .summary-stats {
                flex-direction: column;
                gap: 15px;
            }
            
            .criteria-scores {
                grid-template-columns: 1fr;
            }
            
            .advisor-wrap {
                flex-direction: column;
            }
            
            .criteria-score {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }

        .loading-evaluation {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .loading-evaluation .spinner {
            width: 32px;
            height: 32px;
            margin: 0 auto 15px;
        }

        /* Enhanced modal for evaluation */
        .modal-content.evaluation-modal {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .modal-header.evaluation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .evaluation-progress {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin: 10px 0;
        }

        .evaluation-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        /* Tooltip for star ratings */
        .star-tooltip {
            position: relative;
        }

        .star-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            opacity: 0.9;
        }

        /* Animation for star selection */
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #f59e0b;
            transform: scale(1.1);
            transition: all 0.2s ease;
        }

        /* Completed evaluation badge */
        .completed-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #10b981;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }        
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
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
                        <a class="nav-link" href="#dashboard">
                            <i class="bi bi-house me-1"></i>
                            หน้าหลัก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#grades" onclick="showGrades()">
                            <i class="bi bi-journal-text me-1"></i>
                            ผลการเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#schedule" onclick="showSchedule()">
                            <i class="bi bi-calendar3 me-1"></i>
                            ตารางเรียน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact" onclick="showComplaints()">
                            <i class="bi bi-telephone me-1"></i>
                            แจ้งเหตุ/ร้องเรียน
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>
                            <?=$_SESSION['first_name'];?> <?=$_SESSION['last_name'];?> (ผู้ปกครอง)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#" onclick="showProfileInfo()">
                                <i class="bi bi-person"></i> ข้อมูลส่วนตัว
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showChangePassword()">
                                <i class="bi bi-key"></i> เปลี่ยนรหัสผ่าน
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showNotifications()">
                                <i class="bi bi-bell"></i> การแจ้งเตือน
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showPrivacySettings()">
                                <i class="bi bi-shield-lock"></i> ความเป็นส่วนตัว
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="handleLogout()">
                                <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container position-relative">
            <h1 class="display-4">ยินดีต้อนรับสู่หน้าจอสำหรับผู้ปกครอง</h1>
            <p class="lead">จัดการข้อมูลการเรียน ตรวจสอบผลการเรียน และเข้าถึงบริการต่างๆ ได้ที่นี่</p>
            <a href="#dashboard" class="btn btn-light btn-lg mt-3">เริ่มใช้งาน</a>
        </div>
    </section>

    <section id="dashboard" class="container">
        <div class="dashboard-container fade-in">
            
            <?php if($has_unpaid_fees): ?>
            <div class="alert alert-danger fade-in shadow-sm border-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <strong>แจ้งเตือนสำคัญ!</strong> คุณมียอดค้างชำระค่าเล่าเรียนในภาคเรียนปัจจุบัน กรุณาตรวจสอบและดำเนินการชำระเงิน <a href="#" onclick="showTuitionFees()" class="alert-link text-decoration-underline">คลิกที่นี่เพื่อดูรายละเอียด</a>
                </div>
            </div>
            <?php endif; ?>

            <div class="dashboard-header">
                <h2>เมนูหลักสำหรับนักเรียน</h2>
                <p class="mb-0">เข้าถึงข้อมูลการเรียนและบริการต่างๆ</p>
            </div>
            <div class="menu-grid">
                <div class="menu-card slide-up" onclick="showGrades()">
                    <div class="menu-icon">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <h5 class="menu-title">ดูผลการเรียน</h5>
                    <p class="menu-description">ตรวจสอบผลการเรียน คะแนนสอบ และเกรดเฉลี่ยรายวิชา</p>
                </div>
                <div class="menu-card slide-up" onclick="showSchedule()">
                    <div class="menu-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <h5 class="menu-title">ตารางเรียน</h5>
                    <p class="menu-description">ดูตารางเรียนรายวัน รายสัปดาห์ และกิจกรรมต่างๆ</p>
                </div>
                <div class="menu-card slide-up" onclick="showAttendance()">
                    <div class="menu-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h5 class="menu-title">เช็คการเข้าเรียน</h5>
                    <p class="menu-description">ตรวจสอบสถิติการเข้าเรียน การมาสาย และการขาดเรียน</p>
                </div>
                <div class="menu-card slide-up" onclick="showDocuments()">
                    <div class="menu-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h5 class="menu-title">ขอเอกสาร</h5>
                    <p class="menu-description">ยื่นคำร้องขอเอกสารต่างๆ เช่น ใบรับรอง ใบแสดงผลการเรียน</p>
                </div>
                <div class="menu-card slide-up" onclick="showTuitionFees()">
                    <div class="menu-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h5 class="menu-title">ค่าเล่าเรียน</h5>
                    <p class="menu-description">ตรวจสอบค่าเล่าเรียน ค่าใช้จ่าย และประวัติการชำระเงิน</p>
                </div>
                <div class="menu-card slide-up" onclick="showComplaints()">
                    <div class="menu-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h5 class="menu-title">แจ้งเหตุ/ร้องเรียน</h5>
                    <p class="menu-description">แจ้งปัญหา ข้อเสนอแนะ หรือร้องเรียนต่อฝ่ายที่เกี่ยวข้อง</p>
                </div>
                <div class="menu-card slide-up" onclick="showActivities()">
                    <div class="menu-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h5 class="menu-title">กิจกรรม/รางวัล</h5>
                    <p class="menu-description">ดูกิจกรรมที่เข้าร่วม รางวัลที่ได้รับ และประวัติความสำเร็จ</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5">
        <div class="quick-access fade-in">
            <h4 class="mb-3">การเข้าถึงด่วน</h4>
            <div class="quick-access-item" onclick="showAnnouncements()">
                <i class="bi bi-megaphone me-3 text-primary"></i>
                <span>ดูประกาศล่าสุด</span>
            </div>
            <div class="quick-access-item" onclick="showGrades()">
                <i class="bi bi-book me-3 text-success"></i>
                <span>ดูผลการเรียน</span>
            </div>
            <div class="quick-access-item" onclick="showComplaints()">
                <i class="bi bi-chat-dots me-3 text-warning"></i>
                <span>แจ้งเหตุหรือร้องเรียน</span>
            </div>
        </div>
    </section>

    <section class="container my-5" style="font-family: 'Kanit', sans-serif;">
    
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm mb-4 border border-light-subtle flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="bi bi-sliders text-primary me-2"></i>ช่วงเวลาประเมินวิเคราะห์แดชบอร์ด</h5>
                <p class="text-muted small mb-0">ระบบจะทำการสับเปลี่ยนข้อมูล และสรุปผลเกรดเฉลี่ยสะสมแยกตามหมวดหมู่อัตโนมัติค่ะ</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="metricsScopeSelector" class="small fw-bold text-secondary text-nowrap">ขอบเขตข้อมูล:</label>
                <select id="metricsScopeSelector" class="form-select border-primary-subtle rounded-pill px-3" style="min-width: 260px;" onchange="handleScopeChange(this.value)">
                    <option value="overall" selected>🌟 ภาพรวมสะสมตั้งแต่เข้าเรียน (GPAX)</option>
                </select>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card success shadow-sm border-0">
                    <div class="stat-number" id="metric-gpa">0.00</div>
                    <div class="stat-label" id="label-gpa">เกรดเฉลี่ยรวมสะสม (GPAX)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning shadow-sm border-0">
                    <div class="stat-number" id="metric-attendance">0%</div>
                    <div class="stat-label">การเข้าชั้นเรียนสะสม</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card primary shadow-sm border-0" style="background: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);">
                    <div class="stat-number" id="metric-credits">0</div>
                    <div class="stat-label" id="label-credits">หน่วยกิตรวมสะสม</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card shadow-sm border-0" style="background: linear-gradient(135deg, #FF512F 0%, #DD2476 100%);">
                    <div class="stat-number" id="metric-behavior">0</div>
                    <div class="stat-label" id="label-behavior">คะแนนความประพฤติสะสม</div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4 g-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>แผนภูมิสัดส่วนระดับผลการเรียนสะสม (Grade Distribution)</h6>
                    <div class="d-flex align-items-center justify-content-around flex-wrap gap-3">
                        <div style="width: 190px; height: 190px; position: relative;">
                            <canvas id="gradeDoughnut"></canvas>
                        </div>
                        <div>
                            <ul class="list-unstyled mb-0" id="grade-legend"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-white" style="background: #1e293b;">
                    <h6 class="fw-bold mb-3 text-info"><i class="bi bi-award-fill me-2 text-warning"></i>ภาคผนวกพอร์ตโฟลิโอดิจิทัล</h6>
                    <div class="text-center py-2">
                        <i class="bi bi-trophy display-3 text-warning mb-2 d-block"></i>
                        <p class="small text-info-subtle mb-3">ตรวจสอบประวัติรางวัล และทำการบันทึกเกียรติบัตรเป็นรูปเล่มเอกสารแนบ Portfolio ทันที</p>
                        <button class="btn btn-info text-dark fw-bold btn-sm rounded-pill px-3 py-2" onclick="showActivities()">
                            <i class="bi bi-folder-symlink me-1"></i>เปิดคลังเกียรติบัตรสะสม
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 fade-in">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-range-fill text-danger me-2"></i>สมุดบันทึกประวัติย้อนหลังรายภาคเรียน (Academic Timeline Tabs)</h6>
                        <p class="text-muted small mb-0">คลิกเลือกปุ่มแท็บเทอมด้านล่างเพื่อกางดูสรุปเกรดและพฤติกรรมในอดีตย้อนหลังได้ทุกเทอมเลยค่ะ</p>
                    </div>
                    <ul class="nav nav-tabs border-bottom" id="historyTabHeader" role="tablist"></ul>
                    <div class="tab-content p-3 bg-light-subtle rounded-bottom-4 border border-top-0" id="historyTabContent"></div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="universalModal" tabindex="-1" aria-labelledby="universalModalLabel" aria-hidden="true">
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
    <?php
        $current_term = get_current_term();
        $current_year = get_current_year();
        $last_term = get_last_term();
        $last_year = get_last_year();
        $student_classroom = getStudentClassroom($student_id);
    ?>
        const current_academic = 'ผลการเรียน<?php echo $_SESSION["prefix_name"]; ?><?php echo $_SESSION["first_name"]; ?> <?php echo $_SESSION["last_name"]; ?> ห้อง <?php echo $student_classroom; ?> ภาคเรียนที่ <?php echo $last_term; ?> ปีการศึกษา <?php echo $last_year; ?>';
        
        const studentData = {};

        Promise.all([
            fetch('get_grades.php').then(res => res.json()),
            fetch('get_schedule.php').then(res => res.json()),
            fetch('get_attendance.php').then(res => res.json()),
            fetch('get_activities.php').then(res => res.json()),
            fetch('get_teachers.php').then(res => res.json()),
        ])
        .then(([grades, schedule, attendance, activities, teachers]) => {
            studentData.grades = grades;
            studentData.schedule = schedule;
            studentData.attendance = attendance;
            studentData.activities = activities;
            studentData.teachers = teachers;

            console.log('Student Data:', studentData);
        });

        // Utility functions
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            if (!alertContainer) return;

            const alertId = 'alert-' + Date.now();
            const alertHTML = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-lg border-2" role="alert" style="min-width: 300px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            alertContainer.insertAdjacentHTML('beforeend', alertHTML);

            // 🌟 ถ้าไม่ใช่ประเภทอันตราย (danger) ให้หายไปเองใน 5 วินาที
            // แต่ถ้าเป็น danger (เรื่องค่าเทอม) ให้ค้างไว้จนกว่านักเรียนจะกดปิดเองค่ะ
            if (type !== 'danger') {
                setTimeout(() => {
                    const alertElement = document.getElementById(alertId);
                    if (alertElement) {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
                        bsAlert.close();
                    }
                }, 5000);
            }
        }

        // 🌟 1. ปรับปรุงฟังก์ชัน showModal กลางของระบบให้ฉลาดและเสถียรที่สุด
        function showModal(title, content, hasFooter = false, footerBtnText = '', callback = null) {
            const modalEl = document.getElementById('universalModal');
            if (!modalEl) return;

            // เคลียร์การค้างโฟกัสของปุ่มเดิมก่อนเปลี่ยนเนื้อหา ป้องกันอาการเอ๋อของเบราว์เซอร์ค่ะ
            if (document.activeElement && modalEl.contains(document.activeElement)) {
                document.activeElement.blur();
            }

            // เปลี่ยนเนื้อหาภายในกล่องข้อความทันที
            const modalTitle = modalEl.querySelector('.modal-title');
            const modalBody = modalEl.querySelector('.modal-body');
            if (modalTitle) modalTitle.innerHTML = title;
            if (modalBody) modalBody.innerHTML = content;

            // จัดการส่วนปุ่มกดด้านล่าง (Footer)
            const modalFooter = modalEl.querySelector('.modal-footer');
            if (modalFooter) {
                if (hasFooter) {
                    modalFooter.style.display = 'flex';
                    modalFooter.innerHTML = `
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal" style="font-family: 'Kanit';">ยกเลิก</button>
                        <button type="button" class="btn btn-primary rounded-pill" id="modalSubmitBtn" style="font-family: 'Kanit';">${footerBtnText}</button>
                    `;
                    if (callback) {
                        document.getElementById('modalSubmitBtn').onclick = callback;
                    }
                } else {
                    modalFooter.style.display = 'none';
                }
            }

            // 🔥 พระเอกเด่น: ตรวจสอบว่าโมดอลเปิดแสดงผลอยู่บนหน้าจอแล้วหรือยัง
            const isVisible = modalEl.classList.contains('show') || (modalEl.style.display === 'block');
            
            if (isVisible) {
                // แอบโฟกัสไปที่ตัวกล่องใหญ่เพื่อความปลอดภัยของระบบสากล
                modalEl.focus(); 
                return; // 🎯 ถ้าเปิดอยู่แล้ว ให้เปลี่ยนแค่เนื้อหาข้างในทันที โดยไม่ต้องสั่งเปิดซ้ำให้เบราว์เซอร์แครชค่ะ
            }

            // 💡 หากกล่องยังไม่ได้เปิด ให้สร้างและจำค่าไว้ที่หน่วยความจำ window (เรียกใช้ครั้งเดียวตลอดอายุหน้าเว็บ)
            if (!window.myUniversalModalInstance) {
                window.myUniversalModalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            }
            
            // สั่งเปิดกล่องขึ้นมาตามปกติ
            window.myUniversalModalInstance.show();
        }



        function showLoading(element) {
            element.innerHTML = '<div class="text-center"><div class="spinner"></div><p class="mt-2">กำลังโหลด...</p></div>';
        }

        function getGradeBadgeClass(grade) {
            const gradeMap = {
                '4': 'bg-success',
                '3.5': 'bg-success',
                '3': 'bg-success',
                '2.5': 'bg-info',
                '2': 'bg-info',
                '1.5': 'bg-warning text-dark',
                '1': 'bg-warning text-dark',
                '0': 'bg-danger',
                'ผ.': 'bg-success',
                'มผ.': 'bg-danger',
                'ร': 'bg-secondary',
                'มส.': 'bg-dark text-light'
            };
            return gradeMap[grade] || 'bg-light text-dark';
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
            const gradePointMap = {
                '4': 4.0, '3.5': 3.5, '3': 3.0, '2.5': 2.5,
                '2': 2.0, '1.5': 1.5, '1': 1.0, '0': 0.0
            };

            let totalGradePoints = 0;
            let totalCreditForGPA = 0;
            let totalCreditAll = 0;
            let hasUnevaluated = false;

            studentData.grades.forEach(item => {
                const isEvaluated = Number(item.is_evaluated) > 0;
                const grade = (item.grade || '').toString().trim();
                const credits = Number(item.credits);
                const isNumberGrade = gradePointMap.hasOwnProperty(grade);
                const gradePoint = gradePointMap[grade];
                const countCreditForTotal = (isNumberGrade && grade !== '0') || grade === 'ผ.';

                if (!isEvaluated) {
                    hasUnevaluated = true;
                    return; 
                }

                if (isNumberGrade) {
                    totalGradePoints += gradePoint * credits;
                    totalCreditForGPA += credits;
                }
                if (countCreditForTotal) {
                    totalCreditAll += credits;
                }
            });

            let gpaDisplay = '';
            if (hasUnevaluated) {
                gpaDisplay = '<span class="text-danger"><i class="fa-solid fa-lock me-1"></i> ประเมินไม่ครบ (ระบบซ่อนเกรดเฉลี่ย)</span>';
            } else {
                gpaDisplay = totalCreditForGPA ? (totalGradePoints / totalCreditForGPA).toFixed(2) : '0.00';
            }

            const content = `
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width: 15%;">รหัสวิชา</th>
                                <th>ชื่อวิชา</th>
                                <th style="width: 25%;">เกรด</th>
                                <th style="width: 15%;">หน่วยกิต</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${studentData.grades.map(grade => {
                                const isEvaluated = Number(grade.is_evaluated) > 0;
                                let gradeDisplay = '';

                                if (!isEvaluated) {
                                    // 🌟 ส่งค่า grade.academic_year และ grade.term ผูกติดไปกับฟังก์ชันคลิกประเมินเลยค่ะ
                                    gradeDisplay = `
                                        <div class="text-center">
                                            <span class="badge bg-danger-subtle text-danger p-2 border border-danger-subtle" 
                                                  style="cursor: pointer; font-size: 0.9rem; font-weight: 500;" 
                                                  title="นักเรียนยังไม่ได้ประเมินครูผู้สอน คลิกประเมินเพื่อดูผลการเรียน"
                                                  onclick="evaluateSubjectTeacher('${grade.subject_id}', '${grade.teacher_id}', '${grade.academic_year}', '${grade.term}')">
                                                <i class="fa-solid fa-eye-slash me-1"></i> คลิกประเมินเพื่อดูเกรด
                                            </span>
                                        </div>
                                    `;
                                } else {
                                    gradeDisplay = `
                                        <div class="text-center">
                                            <span class="badge ${getGradeBadgeClass(grade.grade)} fs-6">${grade.grade}</span>
                                        </div>
                                    `;
                                }

                                return `
                                    <tr>
                                        <td class="text-center">${grade.subject_code}</td>
                                        <td>${grade.subject}</td>
                                        <td>${gradeDisplay}</td>
                                        <td class="text-center">${grade.credits}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info shadow-sm mt-3">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    เกรดเฉลี่ยรวม: <strong>${gpaDisplay}</strong> | หน่วยกิตรวม: <strong>${totalCreditAll}</strong>
                </div>
            `;
            showModal(current_academic, content);
        }

        // 🌟 [ปรับปรุงใหม่] ฟังก์ชันแสดงผลตารางเรียนแบบดึงข้อมูล Asynchronous จากไฟล์ get_schedule.php จริง
        function showSchedule() {
            const daysConfig = {
                'Monday': { title: 'วันจันทร์', color: '#f1c40f', text: '#000' },
                'Tuesday': { title: 'วันอังคาร', color: '#e84393', text: '#fff' },
                'Wednesday': { title: 'วันพุธ', color: '#2ecc71', text: '#fff' },
                'Thursday': { title: 'วันพฤหัสบดี', color: '#e67e22', text: '#fff' },
                'Friday': { title: 'วันศุกร์', color: '#3498db', text: '#fff' }
            };

            const scheduleByDay = { 'Monday': [], 'Tuesday': [], 'Wednesday': [], 'Thursday': [], 'Friday': [] };
            
            // 🔎 แตกพาธดึงข้อมูลโครงสร้างออบเจกต์ที่ได้รับมาจากไฟล์ get_schedule.php
            const scheduleResponse = studentData.schedule || {};
            const classroomNameDisplay = scheduleResponse.classroom_name || "ไม่ระบุห้องเรียน";
            const dataRows = scheduleResponse.schedule || [];

            if (Array.isArray(dataRows)) {
                dataRows.forEach(item => {
                    const dayKey = item.day_of_week || item.day;
                    if (scheduleByDay[dayKey]) {
                        scheduleByDay[dayKey].push(item);
                    }
                });
            }

            let tabHeaders = `<ul class="nav nav-pills mb-3 p-1 bg-light rounded-3 d-flex" id="scheduleTabs" role="tablist">`;
            let tabContents = `<div class="tab-content mt-3" id="scheduleTabsContent">`;

            let isFirst = true;
            Object.keys(daysConfig).forEach(dayKey => {
                const config = daysConfig[dayKey];
                const isActive = isFirst ? 'active' : '';
                const isSelected = isFirst ? 'true' : 'false';
                const uniqueTabId = `sched-${dayKey}`;

                tabHeaders += `
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link w-100 ${isActive} fw-bold py-2 schedule-pill-btn" 
                                id="${uniqueTabId}-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#${uniqueTabId}" 
                                type="button" role="tab" 
                                aria-controls="${uniqueTabId}" 
                                aria-selected="${isSelected}"
                                style="--act-bg: ${config.color}; --act-txt: ${config.text}; font-family: 'Kanit';">
                            ${config.title.replace('วัน', '')}
                        </button>
                    </li>
                `;

                const periods = scheduleByDay[dayKey] || [];
                let rowsHtml = '';

                if (periods.length === 0) {
                    rowsHtml = `
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 text-secondary"></i>
                                ไม่มีรายการเรียนการสอนในวันดังกล่าวค่ะ
                            </td>
                        </tr>
                    `;
                } else {
                    periods.forEach(p => {
                        const subjectCode = p.subject_code || '';
                        const subjectName = p.subject_name || p.subject || 'ไม่ระบุวิชา';
                        const roomDisplay = p.room_name && p.building_name ? `${p.room_name} (${p.building_name})` : 'ไม่ระบุห้องเรียน';
                        const teacherName = p.teacher_full_name || p.teacher || 'ไม่ระบุอาจารย์';
                        const periodNum = p.period || '-';

                        rowsHtml += `
                            <tr class="text-center">
                                <td class="fw-bold fs-6 text-dark bg-light-subtle" style="width:12%;">${periodNum}</td>
                                <td class="text-start fw-bold text-primary" style="width:48%;">
                                    ${subjectCode ? `<span class="text-secondary small fw-normal d-block" style="font-size:11px; letter-spacing: 0.5px;">${subjectCode}</span>` : ''}
                                    ${subjectName}
                                </td>
                                <td style="width:20%;"><span class="badge bg-light text-dark border px-2 py-1 fw-normal"><i class="bi bi-door-open text-secondary me-1"></i>${roomDisplay}</span></td>
                                <td class="text-secondary small" style="width:20%;">${teacherName}</td>
                            </tr>
                        `;
                    });
                }

                tabContents += `
                    <div class="tab-pane fade ${isFirst ? 'show active' : ''}" id="${uniqueTabId}" role="tabpanel" aria-labelledby="${uniqueTabId}-tab">
                        <div class="table-responsive rounded-3 border border-light-subtle">
                            <table class="table table-hover align-middle mb-0 text-dark">
                                <thead class="table-light fw-bold text-secondary text-center">
                                    <tr>
                                        <th width="12%">คาบที่</th>
                                        <th width="48%" class="text-start">วิชาเรียน</th>
                                        <th width="20%">ห้อง / อาคารเรียน</th>
                                        <th width="20%">อาจารย์ผู้สอน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rowsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                isFirst = false;
            });

            tabHeaders += `</ul>`;
            tabContents += `</div>`;

            const titleHeader = `
                <div style="font-family: 'Kanit', sans-serif;">
                    <span class="fs-5 fw-bold text-dark d-block"><i class="bi bi-calendar3 text-primary me-2"></i>ตารางเรียนรายสัปดาห์</span>
                    <small class="text-secondary">ห้องเรียนปัจจุบันของคุณ: <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">${classroomNameDisplay}</span></small>
                </div>
            `;

            const styleInjection = `
                <style>
                    .schedule-pill-btn { color: #555 !important; background: transparent !important; border: none; transition: all 0.2s; border-radius: 8px; }
                    .schedule-pill-btn:hover { background-color: rgba(0,0,0,0.05) !important; }
                    .schedule-pill-btn.active { background-color: var(--act-bg) !important; color: var(--act-txt) !important; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
                    .table th { font-size: 0.9rem; padding: 12px !important; background-color: #fdfdfd !important; }
                    .table td { font-size: 0.95rem; padding: 12px !important; }
                </style>
            `;

            showModal(titleHeader, styleInjection + tabHeaders + tabContents);
        }

        // 🌟 [ยกเครื่องใหม่ - ไอเดียที่ 2] ฟังก์ชันพ่นรายงานระบบปฏิทินเช็กชื่อเรียนรายสัปดาห์
        function showAttendance() {
            const attData = studentData.attendance || { overall: { present_weeks:0, absent_weeks:0, late_weeks:0, leave_weeks:0, max_taught_week:0 }, calendar: {}, subjects: [] };
            const { present_weeks, absent_weeks, late_weeks, leave_weeks, max_taught_week } = attData.overall;
            const calendar = attData.calendar || {};

            // 1. สร้างตัวประกอบการ์ดสถิติสรุปความสะอาดของปฏิทิน
            let htmlContent = `
                <div class="row text-center mb-4" style="font-family: 'Kanit', sans-serif;">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="stat-card p-3 rounded-3 text-white" style="background: linear-gradient(135deg, #28a745 0%, #71dd37 100%);">
                            <div class="fs-2 fw-bold">${present_weeks}</div>
                            <div class="small opacity-90">สัปดาห์เรียนปกติ</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="stat-card p-3 rounded-3 text-white" style="background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);">
                            <div class="fs-2 fw-bold">${absent_weeks}</div>
                            <div class="small opacity-90">สัปดาห์ที่ขาดเรียน</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="stat-card p-3 rounded-3 text-white" style="background: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);">
                            <div class="fs-2 fw-bold">${late_weeks}</div>
                            <div class="small opacity-90">สัปดาห์ที่มาสาย</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="stat-card p-3 rounded-3 text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #007bff 100%);">
                            <div class="fs-2 fw-bold">${leave_weeks}</div>
                            <div class="small opacity-90">สัปดาห์ที่ลาเรียน</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary py-2 px-3 rounded-3 mb-4 small d-flex justify-content-between align-items-center" style="font-family: 'Kanit';">
                    <span><i class="bi bi-clock-history me-1"></i> ปัจจุบันภาคเรียนดำเนินไปแล้ว: <strong>${max_taught_week}</strong> / 18 สัปดาห์</span>
                    <span class="badge bg-white text-primary border border-primary-subtle fs-6 px-3">ปีการศึกษา 2568</span>
                </div>

                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-grid-3x3-gap-fill text-secondary me-2"></i>ผังวิเคราะห์สถานะปฏิทินรายสัปดาห์ (Calendar Timeline)</h6>
                <div class="row g-2 mb-4 text-center" style="font-family: 'Kanit', sans-serif;">
            `;

            // วนลูปสร้างกล่องสัปดาห์ที่ 1 ถึง 18 
            for (let w = 1; w <= 18; w++) {
                const status = calendar[w] || 'Unchecked';
                let boxStyle = '';
                let icon = '';
                let titleText = `สัปดาห์ที่ ${w}`;

                if (status === 'Present') {
                    boxStyle = 'background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;';
                    icon = '<i class="bi bi-check-circle-fill text-success d-block small"></i>';
                    titleText += ' (มาเรียนครบทุกวิชา)';
                } else if (status === 'Absent') {
                    boxStyle = 'background-color: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;';
                    icon = '<i class="bi bi-x-circle-fill text-danger d-block small"></i>';
                    titleText += ' (พบสถานะขาดเรียน ≥ 1 วิชา)';
                } else if (status === 'Late') {
                    boxStyle = 'background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e;';
                    icon = '<i class="bi bi-exclamation-circle-fill text-warning d-block small"></i>';
                    titleText += ' (พบสถานะมาสาย)';
                } else if (status === 'Leave') {
                    boxStyle = 'background-color: #f0fdfa; border: 1px solid #99f6e4; color: #115e59;';
                    icon = '<i class="bi bi-info-circle-fill text-info d-block small"></i>';
                    titleText += ' (มีการยื่นลาเรียน)';
                } else {
                    // ยังไม่เปิดเรียน (Unchecked)
                    boxStyle = 'background-color: #f8fafc; border: 1px solid #e2e8f0; color: #94a3b8; opacity: 0.65;';
                    icon = '<i class="bi bi-dash-lg text-muted d-block small"></i>';
                    titleText += ' (ยังไม่มีการเช็กชื่อ)';
                }

                htmlContent += `
                    <div class="col-3 col-sm-2 col-md-2 col-lg-1" title="${titleText}">
                        <div class="p-2 rounded-3 shadow-none text-center" style="${boxStyle} min-height: 56px;">
                            <span class="fw-bold d-block" style="font-size: 11px;">สัปดาห์ ${w}</span>
                            ${icon}
                        </div>
                    </div>
                `;
            }

            htmlContent += `
                </div>
                
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>ความคืบหน้าสถิติเวลาเรียนสะสมแยกรายวิชา</h6>
                <div class="row g-3" style="font-family: 'Kanit', sans-serif;">
            `;

            if (!attData.subjects || attData.subjects.length === 0) {
                htmlContent += `
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-clock-history fs-1 mb-2 d-block text-secondary"></i>ไม่พบวิชาเรียนที่บันทึกข้อมูลเวลาเรียนในเทอมนี้ค่ะ
                    </div>
                `;
            } else {
                attData.subjects.forEach(sub => {
                    const totalWeeksInSemester = 18;
                    const currentTaughtWeeks = sub.total_weeks;
                    
                    const presentPct = currentTaughtWeeks > 0 ? (sub.present / totalWeeksInSemester) * 100 : 0;
                    const absentPct = currentTaughtWeeks > 0 ? (sub.absent / totalWeeksInSemester) * 100 : 0;
                    const latePct = currentTaughtWeeks > 0 ? (sub.late / totalWeeksInSemester) * 100 : 0;
                    const leavePct = currentTaughtWeeks > 0 ? (sub.leave / totalWeeksInSemester) * 100 : 0;
                    const remainingWeeks = totalWeeksInSemester - currentTaughtWeeks;
                    const remainingPct = (remainingWeeks / totalWeeksInSemester) * 100;

                    htmlContent += `
                        <div class="col-12 col-md-6">
                            <div class="card border border-light-subtle shadow-none rounded-3 p-3 bg-white h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="text-secondary small d-block" style="font-size: 11px;">${sub.subject_code}</span>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">${sub.subject_name}</h6>
                                        <small class="text-muted" style="font-size: 11px;"><i class="bi bi-person me-1"></i>อาจารย์: ${sub.teacher_name}</small>
                                    </div>
                                    <span class="badge ${sub.percentage >= 80 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} rounded-pill px-2 py-1" style="font-size: 11px;">
                                        เข้าเรียน ${sub.percentage}%
                                    </span>
                                </div>

                                <div class="progress rounded-pill my-2" style="height: 12px; background-color: #f1f3f5;">
                                    ${sub.present > 0 ? `<div class="progress-bar bg-success" role="progressbar" style="width: ${presentPct}%" title="มาเรียน ${sub.present} คาบ"></div>` : ''}
                                    ${sub.late > 0 ? `<div class="progress-bar bg-warning" role="progressbar" style="width: ${latePct}%" title="มาสาย ${sub.late} คาบ"></div>` : ''}
                                    ${sub.leave > 0 ? `<div class="progress-bar bg-info" role="progressbar" style="width: ${leavePct}%" title="ลาเรียน ${sub.leave} คาบ"></div>` : ''}
                                    ${sub.absent > 0 ? `<div class="progress-bar bg-danger" role="progressbar" style="width: ${absentPct}%" title="ขาดเรียน ${sub.absent} คาบ"></div>` : ''}
                                    ${remainingWeeks > 0 ? `<div class="progress-bar bg-light border-start" role="progressbar" style="width: ${remainingPct}%" title="ยังไม่ถึงคาบสอน ${remainingWeeks} คาบ"></div>` : ''}
                                </div>

                                <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 11px;">
                                    <div>บันทึกข้อมูลแล้ว <strong>${currentTaughtWeeks}</strong> / ${totalWeeksInSemester} สัปดาห์</div>
                                    <div class="text-end">
                                        <span class="text-success me-1">มา ${sub.present}</span>
                                        ${sub.late > 0 ? `<span class="text-warning me-1">สาย ${sub.late}</span>` : ''}
                                        ${sub.absent > 0 ? `<span class="text-danger">ขาด ${sub.absent}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            htmlContent += `</div>`;
            showModal('<i class="bi bi-clock-history text-primary me-2"></i>สถิติเวลาเรียนและการเข้าชั้นเรียนประจำปี', htmlContent);
        }

        // 🌟 ฟังก์ชัน showDocuments() ฉบับเพิ่มปุ่มติดตามสถานะแยกตามประเภท (ฟอนต์ Kanit)
        // 🌟 ส่งค่าจาก PHP Array มาเป็น JavaScript Object
        const documentTypes = <?php echo json_encode($doc_types_array); ?>;

        // 🌟 1. ฟังก์ชันแสดงรายการประเภทเอกสารทั้งหมดให้เลือก
        function showDocuments() {
            const content = `
                <div class="row" style="font-family: 'Kanit', sans-serif;">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-file-earmark-text display-4 text-primary mb-3 d-block"></i>
                                <h5 class="fw-bold mb-2">ใบรับรองการเป็นนักเรียน</h5>
                                <p class="text-muted small mb-4">เอกสารยืนยันสถานะการเป็นนักเรียนปัจจุบัน</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-primary rounded-pill px-3" onclick="requestDocument(3)">
                                        <i class="bi bi-plus-circle me-1"></i>ขอเอกสาร
                                    </button>
                                    <a href="document_history.php" class="btn btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-clock-history me-1"></i>ดูสถานะ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-file-earmark-bar-graph display-4 text-success mb-3 d-block"></i>
                                <h5 class="fw-bold mb-2">ใบแสดงผลการเรียน</h5>
                                <p class="text-muted small mb-4">เอกสารแสดงเกรดและผลการเรียน (Transcript)</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-success text-white rounded-pill px-3" onclick="requestDocument(1)">
                                        <i class="bi bi-plus-circle me-1"></i>ขอเอกสาร
                                    </button>
                                    <a href="document_history.php" class="btn btn-outline-success rounded-pill px-3">
                                        <i class="bi bi-clock-history me-1"></i>ดูสถานะ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-file-earmark-person display-4 text-warning mb-3 d-block"></i>
                                <h5 class="fw-bold mb-2">ใบรับรองความประพฤติ</h5>
                                <p class="text-muted small mb-4">เอกสารยืนยันความประพฤติของนักเรียน</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning text-dark rounded-pill px-3" onclick="requestDocument(4)">
                                        <i class="bi bi-plus-circle me-1"></i>ขอเอกสาร
                                    </button>
                                    <a href="document_history.php" class="btn btn-outline-warning text-dark rounded-pill px-3">
                                        <i class="bi bi-clock-history me-1"></i>ดูสถานะ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-file-earmark-medical display-4 text-info mb-3 d-block"></i>
                                <h5 class="fw-bold mb-2">ใบรับรองสุขภาพ</h5>
                                <p class="text-muted small mb-4">เอกสารรับรองสุขภาพเบื้องต้นจากโรงเรียน</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-info text-white rounded-pill px-3" onclick="requestDocument(2)">
                                        <i class="bi bi-plus-circle me-1"></i>ขอเอกสาร
                                    </button>
                                    <a href="document_history.php" class="btn btn-outline-info rounded-pill px-3">
                                        <i class="bi bi-clock-history me-1"></i>ดูสถานะ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            showModal('ขอเอกสารออนไลน์', content);
        }

        // 🌟 2. ฟังก์ชันเปิดฟอร์มกรอกคำขอ พร้อมเลือกประเภทดีฟอลต์ให้อัตโนมัติ
        function requestDocument(documentTypeId) {
            let options = '<option value="">--- กรุณาเลือกประเภทเอกสาร ---</option>';
            
            // ตรวจสอบว่ามีตัวแปรข้อมูลประเภทเอกสารที่ดึงมาจาก PHP หรือไม่
            if (typeof documentTypes !== 'undefined' && Array.isArray(documentTypes)) {
                documentTypes.forEach(type => {
                    // 🌟 เช็คว่า ID แถวนี้ตรงกับ ID ที่คลิกส่งมาไหม ถ้าตรงให้ใส่คำว่า selected ค้างไว้ทันทีค่ะ
                    const isSelected = (type.id == documentTypeId) ? 'selected' : '';
                    options += `<option value="${type.id}" ${isSelected}>${type.name}</option>`;
                });
            }

            const formContent = `
                <form id="documentRequestForm" method="POST" style="font-family: 'Kanit', sans-serif;">
                    <div class="mb-3">
                        <label for="document_type_id" class="form-label fw-bold">ประเภทเอกสารที่ต้องการ</label>
                        <select name="document_type_id" id="document_type_id" class="form-select" required>
                            ${options}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qty" class="form-label fw-bold">จำนวนที่ต้องการ (ชุด)</label>
                        <input type="number" name="qty" id="qty" class="form-control" value="1" min="1" max="10" required />
                    </div>
                    <div class="mb-3">
                        <label for="purpose" class="form-label fw-bold">วัตถุประสงค์ในการนำไปใช้</label>
                        <textarea name="purpose" id="purpose" class="form-control" rows="3" placeholder="กรุณาระบุวัตถุประสงค์อย่างชัดเจน เช่น ใช้สำหรับศึกษาต่อ, สมัครรับทุนการศึกษา" required></textarea>
                    </div>
                </form>
            `;

            // อัปเดตเนื้อหาโมดอลตัวเดิมเป็นฟอร์มกรอกใบคำขอ
            showModal('กรอกรายละเอียดคำขอเอกสาร', formContent, true, 'ส่งคำขอเอกสาร', () => {
                submitDocumentRequest(); 
            });
        }


        // 🌟 ฟังก์ชันส่งข้อมูล Fetch API ยิงไปที่ไฟล์บันทึกหลังบ้าน
        async function submitDocumentRequest() {
            const form = document.getElementById('documentRequestForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return; 
            }

            const formData = new FormData(form);

            try {
                const response = await fetch('save_document_request.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    // บันทึกสำเร็จ ย้ายผู้ใช้ไปหน้าไทม์ไลน์สถานะทันทีค่ะ
                    window.location.href = 'document_status.php?id=' + result.request_id;
                } else {
                    alert('เกิดข้อผิดพลาด: ' + result.message);
                }
            } catch (error) {
                console.error('Error submitting document request:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อระบบเซิร์ฟเวอร์ค่ะ');
            }
        }

        // 🌟 ฟังก์ชันแสดงค่าเล่าเรียน (ฉบับแก้ไขชื่อฟิลด์จาก amount เป็น fee_amount)
        async function showTuitionFees() {
            try {
                const response = await fetch('get_tuition_fees.php');
                const feesData = await response.json();

                if (feesData && feesData.error) {
                    alert('พบข้อผิดพลาดจากระบบหลังบ้าน:\n' + feesData.error);
                    return;
                }

                // กรณีที่ 1: ถ้าไม่มีข้อมูลอยู่ในตารางเลย (ตารางว่างเปล่า)
                if (!feesData || feesData.length === 0) {
                    const emptyContent = `
                        <div class="text-center p-5 text-muted border border-dashed rounded-3 bg-light my-3 shadow-sm">
                            <i class="bi bi-cash-stack mb-3 text-secondary" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-secondary">ยังไม่มีข้อมูลการเรียกเก็บค่าเล่าเรียน</h5>
                            <p class="small mb-0">ไม่พบข้อมูลการเรียกเก็บเงินหรือใบแจ้งชำระค่าเล่าเรียนของคุณในภาคเรียนปัจจุบันค่ะ</p>
                        </div>
                    `;
                    showModal('ข้อมูลค่าเล่าเรียน', emptyContent);
                    return;
                }

                // กรณีที่ 2: มีข้อมูลในตารางจริง -> ทำการคำนวณแยกยอดและแสดงผล
                let totalAmount = 0;
                let unpaidAmount = 0;
                let hasUnpaid = false;
                
                // 🌟 แก้ไขจุดนี้: เปลี่ยนจาก item.amount เป็น item.fee_amount ค่ะ
                feesData.forEach(item => {
                    const amt = parseFloat(item.fee_amount) || 0;
                    totalAmount += amt;
                    if (parseInt(item.is_paid) === 0) {
                        unpaidAmount += amt;
                        hasUnpaid = true;
                    }
                });

                const termDisplay = feesData[0].term;
                const yearDisplay = feesData[0].academic_year;

                // สร้างตารางรายการค่าใช้จ่ายจากข้อมูลจริง
                let tableRows = '';
                feesData.forEach((item, index) => {
                    const statusBadge = parseInt(item.is_paid) === 1 
                        ? '<span class="badge bg-success">ชำระแล้ว</span>' 
                        : '<span class="badge bg-warning text-dark">รอชำระ</span>';
                    
                    const dueDate = item.due_date ? new Date(item.due_date).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: '2-digit' }) : '-';

                    // 🌟 แก้ไขจุดนี้: เปลี่ยนจาก item.amount เป็น item.fee_amount เช่นกันค่ะ
                    tableRows += `
                        <tr>
                            <td>${index + 1}. ${item.fee_name}</td>
                            <td class="text-end">${(parseFloat(item.fee_amount) || 0).toLocaleString('th-TH', { minimumFractionDigits: 2 })} บาท</td>
                            <td class="text-center">${dueDate}</td>
                            <td class="text-center">${statusBadge}</td>
                        </tr>
                    `;
                });

                const content = `
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card text-white h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <h5>ยอดเรียกเก็บทั้งหมด ภาคเรียนที่ ${termDisplay}/${yearDisplay}</h5>
                                    <h2 class="fw-bold">${totalAmount.toLocaleString('th-TH', { minimumFractionDigits: 2 })} บาท</h2>
                                    <div><span class="badge bg-light text-primary">ข้อมูลทางการ</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card text-white h-100" style="${hasUnpaid ? 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);' : 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);'}">
                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                    <h5>${hasUnpaid ? 'ยอดหนี้ค้างชำระปัจจุบัน' : 'สถานะการเงินในเทอมนี้'}</h5>
                                    <h2 class="fw-bold">${hasUnpaid ? unpaidAmount.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' บาท' : 'ชำระครบถ้วนแล้ว'}</h2>
                                    <div>
                                        ${hasUnpaid ? '<span class="badge bg-danger">โปรดชำระเงิน</span>' : '<span class="badge bg-white text-success">✓ ไม่มีค้างชำระ</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>รายการค่าธรรมเนียมการศึกษา</th>
                                    <th class="text-end" style="width: 20%;">จำนวนเงิน</th>
                                    <th class="text-center" style="width: 20%;">ครบกำหนด</th>
                                    <th class="text-center" style="width: 15%;">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                `;
                
                showModal(`ข้อมูลค่าเล่าเรียน ภาคเรียนที่ ${termDisplay}/${yearDisplay}`, content);

            } catch (error) {
                console.error("Error loading tuition fees:", error);
                alert('เกิดข้อผิดพลาดในการเรียกโหลดข้อมูลค่าเล่าเรียนค่ะ');
            }
        }

        // 🌟 1. ฟังก์ชันแสดงฟอร์มแจ้งเหตุ/ร้องเรียน (มีช่องแนบไฟล์แบบไม่บังคับ required)
        // 🌟 ส่งค่าจาก PHP Array มาเป็น JavaScript Object Array
        const complaintTypes = <?php echo json_encode($complaint_types_array); ?>;

        // 🌟 ฟังก์ชันแสดงฟอร์มแจ้งเหตุ/ร้องเรียน (ฉบับดึงประเภทจากฐานข้อมูล)
        function showComplaints() {
            // 1. วนลูปสร้างตัวเลือก <option> จากฐานข้อมูลตามลำดับ ID
            let typeOptions = '<option value="">เลือกประเภท</option>';
            complaintTypes.forEach(type => {
                typeOptions += `<option value="${type.id}">${type.name}</option>`;
            });

            // 2. นำตัวแปร typeOptions ไปใส่ในแท็ก <select>
            const content = `
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-3 border border-dashed">
                    <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> ต้องการติดตามความคืบหน้าเรื่องเดิมใช่ไหมคะ?</span>
                    <a href="complaint_history.php" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="bi bi-clock-history me-1"></i>ดูประวัติการแจ้งเรื่อง
                    </a>
                </div>

                <form id="complaintForm" enctype="multipart/form-data" method="POST">
                    <div class="mb-3">
                        <label for="complaint_type_id" class="form-label fw-bold">ประเภทการแจ้ง</label>
                        <select name="complaint_type_id" id="complaint_type_id" class="form-select" required>
                            ${typeOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold">หัวข้อเรื่อง</label>
                        <input type="text" name="subject" id="subject" class="form-control" placeholder="กรุณาระบุหัวข้อเรื่อง" required />
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label fw-bold">รายละเอียดเหตุการณ์</label>
                        <textarea name="detail" id="detail" class="form-control" rows="4" placeholder="กรุณากรอกรายละเอียดที่ต้องการแจ้งอย่างชัดเจน" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">แนบไฟล์ภาพหลักฐาน (ถ้ามี - ไม่บังคับค่ะ)</label>
                        <input type="file" name="file" id="file" class="form-control" accept="image/*,application/pdf" />
                    </div>
                </form>
            `;
            
            showModal('แจ้งเหตุ / ร้องเรียน', content, true, 'ส่งเรื่องแจ้งเหตุ', () => {
                submitComplaint(); 
            });
        }

        // 🌟 2. ฟังก์ชัน submitComplaint() สำหรับส่งข้อมูล (ตรวจเช็คเฉพาะฟิลด์ที่บังคับ)
        async function submitComplaint() {
            const form = document.getElementById('complaintForm');

            // 💡 ตัว checkValidity() จะเช็คเฉพาะฟิลด์ที่มี required เท่านั้น (ประเภท, หัวข้อ, รายละเอียด)
            // ส่วนช่องไฟล์ที่ไม่มี required มันจะปล่อยผ่านให้โดยอัตโนมัติเลยค่ะ
            if (!form.checkValidity()) {
                form.reportValidity();
                return; 
            }

            // FormData จะดึงข้อมูลทั้งหมดรวมถึงไฟล์แนบ (ถ้ามี) ส่งไปให้หลังบ้านอัตโนมัติ
            const formData = new FormData(form);

            try {
                const response = await fetch('save_complaint.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    // บันทึกสำเร็จ เปลี่ยนหน้าจอไปยังหน้าไทม์ไลน์สถานะ
                    window.location.href = 'complaint_status.php?id=' + result.complaint_id;
                } else {
                    alert('ไม่สามารถส่งเรื่องได้: ' + result.message);
                }

            } catch (error) {
                console.error('Error submitting complaint:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อระบบเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้งค่ะ');
            }
        }

        
        // 🌟 [เวอร์ชันอัปเกรดเหนือชั้น] ฟังก์ชันแสดงประวัติกิจกรรม พร้อมระบบดูรูปและส่งออกเล่มรวม PDF
        function showActivities() {
            const currentActs = studentData.activities || [];
            let tableRows = '';
            
            if (currentActs.length === 0) {
                tableRows = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-trophy fs-2 d-block mb-2 text-secondary"></i>
                            ยังไม่มีข้อมูลการบันทึกประวัติกิจกรรมหรือรางวัลสะสมในระบบค่ะ
                        </td>
                    </tr>
                `;
            } else {
                currentActs.forEach(act => {
                    let badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                    if (act.award.includes('ชนะเลิศ') || act.award.includes('ทอง')) {
                        badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                    } else if (act.award.includes('เกียรติบัตร') || act.award.includes('เข้าร่วม')) {
                        badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    }

                    const formattedDate = act.date ? formatDate(act.date) : '-';

                    // 🌟 ลอจิกปุ่มเปิดดูรูปใบประกาศรายชิ้นงาน
                    let certButtonHTML = `<span class="text-muted small">- ไม่มีแนบไฟล์ -</span>`;

                    // 1. เพิ่มตัวแปรเก็บรหัส user_id ไว้ที่ส่วนหัวของแท็ก <script> (ถัดจาก current_academic)
                    const currentUserId = <?php echo intval($student_id); ?>;

                    // 2. ปรับปรุงลอจิกปุ่มเปิดดูรูปในฟังก์ชัน showActivities() ให้ชี้ไปที่โฟลเดอร์ใหม่ของพี่ทูล
                    if (act.certificate) {
                        certButtonHTML = `
                            <a href="uploads/portfolio/${currentUserId}/${act.certificate}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size:11px;">
                                <i class="bi bi-image me-1"></i>เปิดดูใบประกาศ
                            </a>
                        `;
                    }

                    tableRows += `
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary rounded-3 px-2 py-1 small fw-bold">
                                    เทอม ${act.term}/${act.year}
                                </span>
                            </td>
                            <td class="fw-bold text-dark" style="font-size: 13px;">
                                ${act.name}
                                <small class="text-muted d-block fw-normal mt-1" style="font-size: 11px;">
                                    <i class="bi bi-person-check me-1"></i>ผู้บันทึก: ${act.recorder}
                                </small>
                            </td>
                            <td class="text-center"><span class="badge ${badgeClass} rounded-pill px-3 py-1 fw-bold" style="font-size: 11.5px;">${act.award}</span></td>
                            <td class="text-center text-secondary small" style="font-size: 12px;">${formattedDate}</td>
                            <td class="text-center">${certButtonHTML}</td>
                        </tr>
                    `;
                });
            }

            // 🌟 เพิ่มปุ่ม "ส่งออกคลังใบประกาศ Portfolio (PDF)" เด่นสง่าไว้ที่ส่วนหัวกล่องข้อความเลย์เอาต์ค่ะ
            const titleHeader = `
                <div class="d-flex justify-content-between align-items-center w-100" style="font-family: 'Kanit';">
                    <div>
                        <span class="fs-5 fw-bold text-dark d-block"><i class="bi bi-trophy text-primary me-2"></i>ประวัติกิจกรรมและรางวัลสะสม</span>
                        <small class="text-secondary">รวบรวมประวัติความสำเร็จทุกปีการศึกษา</small>
                    </div>
                    <div>
                        <a href="print_portfolio.php" target="_blank" class="btn btn-sm btn-danger text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size:12px;">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>พิมพ์ภาคผนวก Portfolio (PDF)
                        </a>
                    </div>
                </div>
            `;

            const content = `
                <div class="table-responsive rounded-3 border border-light-subtle" style="font-family: 'Kanit', sans-serif;">
                    <table class="table table-hover align-middle mb-0 text-dark">
                        <thead class="table-light fw-bold text-secondary text-center" style="font-size:13px;">
                            <tr>
                                <th width="12%">ภาคเรียน</th>
                                <th width="40%" class="text-start">กิจกรรม / โครงการความสำเร็จ</th>
                                <th width="18%">รางวัลที่ได้รับ</th>
                                <th width="15%">วันที่ได้รับ</th>
                                <th width="15%">ไฟล์หลักฐาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            `;
            
            showModal(titleHeader, content);
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

        async function evaluateAdvisor() {
            try {
                const response = await fetch('check_advisor_evaluation.php');
                const data = await response.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                const { criteria, advisors, evaluations, academic_info } = data;

                if (!advisors || advisors.length === 0) {
                    alert("ไม่พบข้อมูลครูที่ปรึกษาในห้องเรียนของคุณ");
                    return;
                }

                const styles = `
                    <style>
                    .advisor-wrap{display:flex;flex-wrap:wrap;gap:12px;margin:8px 0 4px}
                    .advisor-card{
                        position:relative; display:flex; align-items:center; gap:12px;
                        padding:12px 14px; border:2px solid #e3e7ef; border-radius:14px;
                        background:#f8fafc; cursor:pointer; transition:.18s ease;
                        box-shadow:0 2px 6px rgba(0,0,0,.04)
                    }
                    .advisor-card:hover{transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,.08)}
                    .advisor-card.active{border-color:#4f46e5; background:#eef2ff}
                    .advisor-card.evaluated{border-color:#10b981; background:#ecfdf5}
                    .advisor-radio{position:absolute; inset:0; opacity:0}
                    .avatar{
                        width:44px; height:44px; border-radius:50%; object-fit:cover;
                        box-shadow:0 1px 3px rgba(0,0,0,.15)
                    }
                    .advisor-info{display:flex; flex-direction:column}
                    .advisor-name{font-weight:600; color:#1f2937}
                    .advisor-sub{font-size:12px; color:#64748b}
                    .evaluation-status{font-size:11px; font-weight:600; margin-top:2px}
                    .evaluated-status{color:#10b981}
                    .not-evaluated-status{color:#f59e0b}
                    .star{font-family:'Material Icons'; font-size:28px; vertical-align:-6px; cursor:pointer; color:#e5e7eb}
                    .star.gold{color:#f59e0b}
                    .star.readonly{cursor:default}
                    .criterion{margin:14px 0}
                    .criterion > label{font-weight:600; color:#0f172a}
                    .readonly-mode{background-color:#f8f9fa; padding:10px; border-radius:8px; margin-bottom:10px}
                    .readonly-mode .alert{margin:0; padding:8px 12px; font-size:14px}
                    </style>
                    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
                `;

                let advisorCards = `<div class="advisor-wrap">`;
                evaluations.forEach((evalData, i) => {
                    const advisor = evalData.advisor_info;
                    const hasEval = evalData.has_evaluation;
                    const photo = advisor.photo || 'teacher-placeholder.png';
                    const statusClass = hasEval ? 'evaluated' : '';
                    const statusText = hasEval 
                        ? `<div class="evaluation-status evaluated-status">✓ ประเมินแล้ว (${new Date(evalData.evaluation_date).toLocaleDateString('th-TH')})</div>`
                        : `<div class="evaluation-status not-evaluated-status">⚠ ยังไม่ได้ประเมิน</div>`;
                    
                    advisorCards += `
                        <label class="advisor-card ${statusClass} ${i===0 ? 'active':''}" data-card data-eval-index="${i}">
                            <input class="advisor-radio" type="radio" name="advisor_id" value="${advisor.advisor_id}" ${i===0 ? 'checked':''}>
                            <img class="avatar" src="images/${photo}" alt="${advisor.full_name}">
                            <div class="advisor-info">
                                <div class="advisor-name">${advisor.full_name}</div>
                                <div class="advisor-sub">ครูที่ปรึกษา</div>
                                ${statusText}
                            </div>
                        </label>
                    `;
                });
                advisorCards += `</div>`;

                let formHtml = `
                    ${styles}
                    <div class="mb-2" style="font-weight:600;color:#334155">เลือกครูที่ปรึกษา:</div>
                    ${advisorCards}
                    <div id="evaluationForm">
                        ${generateEvaluationForm(criteria, evaluations[0])}
                    </div>
                `;

                showModal('แบบประเมินครูที่ปรึกษา', formHtml, true, 'บันทึกการประเมิน', () => {
                    submitAdvisorEvaluation();
                });
                
                setupAdvisorCardEvents(evaluations, criteria);

            } catch (error) {
                console.error("Error loading advisor evaluation:", error);
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
            }
        }

        function generateEvaluationForm(criteria, evalData) {
            const hasEvaluation = evalData.has_evaluation;
            const isReadonly = hasEvaluation;
            
            let formHtml = '';
            
            if (hasEvaluation) {
                formHtml += `
                    <div class="readonly-mode">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>คุณได้ประเมินครูท่านนี้แล้ว</strong><br>
                            วันที่ประเมิน: ${new Date(evalData.evaluation_date).toLocaleDateString('th-TH', {
                                year: 'numeric', month: 'long', day: 'numeric', 
                                hour: '2-digit', minute: '2-digit'
                            })}
                        </div>
                    </div>
                `;
            }
            
            formHtml += `
                <form id="advisorEvaluationForm">
                    <div class="mb-3">
                        <strong>${isReadonly ? 'คะแนนการประเมินของคุณ:' : 'โปรดเลือกคะแนนในแต่ละหัวข้อ (1-5 ดาว):'}</strong>
                    </div>
            `;

            const ratingLabels = ['1 = ควรปรับปรุง', '2 = พอใช้', '3 = ปานกลาง', '4 = ดี', '5 = ดีมาก'];
            
            criteria.forEach((c, index) => {
                const existingRating = hasEvaluation && evalData.ratings[c.criterion_id] 
                    ? evalData.ratings[c.criterion_id].rating : 0;
                
                formHtml += `
                    <div class="form-group mb-3">
                        <label>${index + 1}. ${c.criterion_name}</label>
                        <div class="star-rating ${isReadonly ? 'readonly' : ''}" data-criterion-id="${c.criterion_id}">
                            ${[5,4,3,2,1].map(i => `
                                <input type="radio" name="rating_${c.criterion_id}" 
                                    id="star${i}_${c.criterion_id}" value="${i}" 
                                    ${existingRating == i ? 'checked' : ''}
                                    ${isReadonly ? 'disabled' : 'required'}>
                                <label for="star${i}_${c.criterion_id}" 
                                    class="star ${existingRating >= i ? 'gold' : ''} ${isReadonly ? 'readonly' : ''}"
                                    title="${ratingLabels[i-1]}">★</label>
                            `).join('')}
                        </div>
                        ${existingRating > 0 ? `<small class="text-muted">คะแนนที่ให้: ${existingRating}/5 (${ratingLabels[existingRating-1]})</small>` : ''}
                    </div>
                `;
            });

            formHtml += `
                <div class="form-group">
                    <label>ข้อเสนอแนะเพิ่มเติม ${isReadonly ? '(ที่คุณเคยให้ไว้)' : '(ถ้ามี)'}:</label>
                    <textarea id="advisorComments" class="form-control" rows="3" 
                            ${isReadonly ? 'readonly' : ''}>${evalData.ratings[5] ? evalData.ratings[5].comments : ''}</textarea>
                </div>
                </form>
            `;

            return formHtml;
        }

        function setupAdvisorCardEvents(evaluations, criteria) {
            document.querySelectorAll('[data-card]').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('[data-card]').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    
                    const radio = card.querySelector('.advisor-radio');
                    if (radio) radio.checked = true;
                    
                    const evalIndex = parseInt(card.dataset.evalIndex);
                    const evalData = evaluations[evalIndex];
                    
                    document.getElementById('evaluationForm').innerHTML = 
                        generateEvaluationForm(criteria, evalData);
                    
                    if (!evalData.has_evaluation) {
                        setupStarRatingEvents();
                    }
                    
                    const actionButton = document.getElementById('modalActionButton');
                    if (evalData.has_evaluation) {
                        actionButton.style.display = 'none';
                    } else {
                        actionButton.style.display = 'inline-block';
                    }
                });
            });

            if (!evaluations[0].has_evaluation) {
                setupStarRatingEvents();
            } else {
                const actionButton = document.getElementById('modalActionButton');
                if (actionButton) actionButton.style.display = 'none';
            }
        }

        function setupStarRatingEvents() {
            document.querySelectorAll('.star-rating:not(.readonly)').forEach(group => {
                const stars = group.querySelectorAll('label.star:not(.readonly)');
                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const input = document.getElementById(star.getAttribute('for'));
                        if (input && !input.disabled) {
                            const value = parseInt(input.value);
                            
                            stars.forEach(s => {
                                const sInput = document.getElementById(s.getAttribute('for'));
                                const sValue = parseInt(sInput.value);
                                if (sValue <= value) {
                                    s.classList.add('gold');
                                } else {
                                    s.classList.remove('gold');
                                }
                            });
                            input.checked = true;
                        }
                    });
                });
            });
        }

        async function submitAdvisorEvaluation() {
            const advisorRadio = document.querySelector('input[name="advisor_id"]:checked');
            if (!advisorRadio) { alert('กรุณาเลือกครูที่ปรึกษา'); return; }
            const advisorId = Number(advisorRadio.value);

            const comment = (document.getElementById('advisorComments')?.value || '').trim();

            const groups = document.querySelectorAll('.star-rating');
            if (!groups.length) { alert('ไม่พบหัวข้อประเมิน'); return; }

            const responses = [];
            let hasMissing = false;

            groups.forEach(group => {
                const cid = Number(group.dataset.criterionId);
                const checked = group.querySelector('input[type="radio"]:checked');
                const score = checked ? Number(checked.value) : 0;
                if (score < 1 || score > 5) hasMissing = true;

                responses.push({
                    criterion_id: cid,
                    rating: score,
                    comments: comment
                });
            });

            if (hasMissing) {
                if (!confirm('มีหัวข้อที่ยังไม่ได้ให้คะแนน (จะถูกบันทึกเป็น 0) ต้องการดำเนินการต่อหรือไม่?')) {
                    return;
                }
            }

            const payload = {
                advisor_id: advisorId,
                academic_year: window.ACAD_YEAR ?? null,
                semester: window.TERM ?? null,
                responses,
                comments: comment,
                created_at: new Date().toISOString().slice(0,19).replace('T',' ')
            };

            try {
                const res = await fetch('submit_advisor_evaluation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const text = await res.text();
                let json;
                try { json = JSON.parse(text); }
                catch(e){ console.error('Server response not JSON:', text); alert('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์'); return; }

                if (!res.ok || !json.success) {
                    console.error('Save failed:', json);
                    alert('บันทึกไม่สำเร็จ: ' + (json.error || 'ไม่ทราบสาเหตุ'));
                    return;
                }

                alert('บันทึกการประเมินเรียบร้อยแล้ว');
                if (typeof closeModal === 'function') closeModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);

            } catch (err) {
                console.error(err);
                alert('เกิดข้อผิดพลาดขณะส่งข้อมูล');
            }
        }

        function closeModal() {
            const modalEl = document.querySelector('.modal.show');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            }
        }

        // 2. ฟังก์ชันเปิดหน้าต่างฟอร์มประเมินครูผู้สอน
        async function evaluateSubjectTeacher(targetSubjectId = null, targetTeacherId = null, passedYear = null, passedTerm = null) {
            try {
                const response = await fetch('check_teacher_evaluation.php');
                const data = await response.json();
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const { criteria, subjects, evaluations, academic_info } = data;

                if (!subjects || subjects.length === 0) {
                    alert("ไม่พบรายวิชาที่เรียนในภาคเรียนก่อนหน้า");
                    return;
                }

                // 🌟 หากส่งค่าปี/เทอมมาจากหน้าเกรดให้ใช้ค่านั้นทันที แต่ถ้าไม่มี (กดจากเมนูตรงๆ) ให้ใช้จากค่า config หลังบ้านแทนค่ะ
                const finalYear = passedYear ? passedYear : academic_info.last_year;
                const finalTerm = passedTerm ? Number(passedTerm) : Number(academic_info.last_term);

                let matchingData = evaluations;
                let isFiltered = false;
                if (targetSubjectId) {
                    matchingData = evaluations.filter(e => e.subject_info.subject_id == targetSubjectId);
                    isFiltered = true;
                }

                let startIndex = -1;
                if (targetTeacherId) {
                    startIndex = matchingData.findIndex(e => e.subject_info.teacher_id == targetTeacherId);
                }
                if (startIndex === -1 && !isFiltered) {
                    startIndex = 0; 
                }
                if (startIndex === -1 && isFiltered && matchingData.length === 1) {
                    startIndex = 0; 
                }

                const styles = `
                    <style>
                    .subject-wrap{display:flex;flex-wrap:wrap;gap:12px;margin:8px 0 12px}
                    .subject-card{
                        position:relative; display:flex; align-items:center; gap:12px;
                        padding:12px 14px; border:2px solid #e3e7ef; border-radius:14px;
                        background:#f8fafc; cursor:pointer; transition:.18s ease;
                        box-shadow:0 2px 6px rgba(0,0,0,.04)
                    }
                    .subject-card:hover{transform:translateY(-1px); box-shadow:0 6px 14px rgba(0,0,0,.08)}
                    .subject-card.active{border-color:#4f46e5; background:#eef2ff}
                    .subject-card.evaluated{border-color:#10b981; background:#ecfdf5}
                    .subject-radio{position:absolute; inset:0; opacity:0}
                    .avatar{ width:44px; height:44px; border-radius:50%; object-fit:cover; box-shadow:0 1px 3px rgba(0,0,0,.15) }
                    .subject-info{display:flex; flex-direction:column; flex:1}
                    .subject-name{font-weight:600; color:#1f2937; font-size:14px}
                    .subject-code{font-size:12px; color:#64748b; margin-top:2px}
                    .teacher-name{font-size:13px; color:#059669; margin-top:4px}
                    .evaluation-status{font-size:11px; font-weight:600; margin-top:2px}
                    .evaluated-status{color:#10b981}
                    .not-evaluated-status{color:#f59e0b}
                    .star{font-family:'Material Icons'; font-size:28px; vertical-align:-6px; cursor:pointer; color:#e5e7eb}
                    .star.gold{color:#f59e0b}
                    .star.readonly{cursor:default}
                    .criterion{margin:14px 0}
                    </style>
                `;

                let subjectCards = `<div class="subject-wrap">`;
                matchingData.forEach((evalData, i) => {
                    const subject = evalData.subject_info;
                    const hasEval = evalData.has_evaluation;
                    const photo = subject.profile_picture || 'teacher-placeholder.png';
                    const statusClass = hasEval ? 'evaluated' : '';
                    const isActive = (i === startIndex) ? 'active' : '';
                    const isChecked = (i === startIndex) ? 'checked' : '';
                    const statusText = hasEval 
                        ? `<div class="evaluation-status evaluated-status">✓ ประเมินแล้ว</div>`
                        : `<div class="evaluation-status not-evaluated-status">⚠ ยังไม่ได้ประเมิน</div>`;
                    
                    subjectCards += `
                        <label class="subject-card ${statusClass} ${isActive}" data-card data-eval-index="${i}">
                            <input class="subject-radio" type="radio" name="subject_teacher" 
                                value="${subject.teacher_id}" 
                                data-subject-id="${subject.subject_id}" ${isChecked}>
                            <img class="avatar" src="images/${photo}" alt="${subject.teacher_name}">
                            <div class="subject-info">
                                <div class="subject-name">${subject.subject_name}</div>
                                <div class="subject-code">${subject.subject_code}</div>
                                <div class="teacher-name">${subject.teacher_name}</div>
                                ${statusText}
                            </div>
                        </label>
                    `;
                });
                subjectCards += `</div>`;

                let initialFormHtml = '';
                if (startIndex >= 0) {
                    initialFormHtml = generateTeacherEvaluationForm(criteria, matchingData[startIndex], finalYear, finalTerm);
                } else {
                    initialFormHtml = `
                        <div class="text-center p-4 text-muted border border-dashed rounded-3 bg-light my-3 shadow-sm">
                            <i class="fa-solid fa-arrow-pointer mb-2 fs-4 text-secondary"></i>
                            <div style="font-size: 14px;">กรุณาคลิกเลือกรายวิชาและครูผู้สอนด้านบนเพื่อเริ่มต้นทำแบบประเมิน</div>
                        </div>
                    `;
                }

                let formHtml = `
                    ${styles}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div style="font-weight:600;color:#334155"><i class="fa-solid fa-chalkboard-user me-1 text-primary"></i> รายวิชาและครูผู้สอน:</div>
                        <button id="btnShowAllSubjects" class="btn btn-sm btn-outline-primary" style="display:${isFiltered ? 'inline-block' : 'none'}; font-size: 12px; padding: 3px 10px; border-radius: 20px;">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> แสดงวิชาทั้งหมด
                        </button>
                    </div>
                    ${subjectCards}
                    <div id="teacherEvaluationForm">${initialFormHtml}</div>
                `;

                showModal('แบบประเมินครูผู้สอน', formHtml, true, 'บันทึกการประเมิน', () => {
                    submitTeacherEvaluation(finalYear, finalTerm); // 🌟 ส่งปี/เทอมที่แน่นอนไปบันทึก
                });
                
                setupTeacherCardEvents(matchingData, criteria, finalYear, finalTerm, startIndex, isFiltered);

            } catch (error) {
                console.error("Error loading teacher evaluation:", error);
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
            } 
        }

        // 3. ฟังก์ชันเรนเดอร์แบบฟอร์มคำถาม
        function generateTeacherEvaluationForm(criteria, evalData, finalYear, finalTerm) {
            const hasEvaluation = evalData.has_evaluation;
            const isReadonly = hasEvaluation;
            let formHtml = '';

            if (hasEvaluation) {
                formHtml += `
                    <div class="readonly-mode">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>คุณได้ประเมินครูท่านนี้แล้ว</strong><br>
                            วันที่ประเมิน: ${new Date(evalData.evaluation_date).toLocaleDateString('th-TH', {
                                year: 'numeric', month: 'long', day: 'numeric', 
                                hour: '2-digit', minute: '2-digit'
                            })}<br>
                            ภาคเรียนที่: ${finalTerm}/${finalYear}
                        </div>
                    </div>
                `;
            } else {
                formHtml += `
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        ประเมินสำหรับภาคเรียนที่ ${finalTerm}/${finalYear}
                    </div>
                `;
            }

            formHtml += `
                <form id="teacherEvaluationFormElement">
                    <div class="mb-3">
                        <strong>${isReadonly ? 'คะแนนการประเมินของคุณ:' : 'โปรดเลือกคะแนนในแต่ละหัวข้อ (1-5 ดาว):'}</strong>
                    </div>
            `;

            const ratingLabels = ['1 = ควรปรับปรุง', '2 = พอใช้', '3 = ปานกลาง', '4 = ดี', '5 = ดีมาก'];

            criteria.forEach((c, index) => {
                const existingRating = hasEvaluation && evalData.ratings[c.criterion_id] 
                    ? evalData.ratings[c.criterion_id].rating : 0;
                
                formHtml += `
                    <div class="form-group mb-3">
                        <label>${index + 1}. ${c.criterion_name}</label>
                        <div class="star-rating ${isReadonly ? 'readonly' : ''}" data-criterion-id="${c.criterion_id}">
                            ${[5,4,3,2,1].map(i => `
                                <input type="radio" name="rating_${c.criterion_id}" 
                                    id="star${i}_${c.criterion_id}" value="${i}" 
                                    ${existingRating == i ? 'checked' : ''}
                                    ${isReadonly ? 'disabled' : 'required'}>
                                <label for="star${i}_${c.criterion_id}" 
                                    class="star ${existingRating >= i ? 'gold' : ''} ${isReadonly ? 'readonly' : ''}"
                                    title="${ratingLabels[i-1]}">★</label>
                            `).join('')}
                        </div>
                        ${existingRating > 0 ? `<small class="text-muted">คะแนนที่ให้: ${existingRating}/5 (${ratingLabels[existingRating-1]})</small>` : ''}
                    </div>
                `;
            });

            const existingComments = hasEvaluation && evalData.ratings[Object.keys(evalData.ratings)[0]] 
                ? evalData.ratings[Object.keys(evalData.ratings)[0]].comments : '';

            formHtml += `
                <div class="form-group">
                    <label>ข้อเสนอแนะเพิ่มเติม ${isReadonly ? '(ที่คุณเคยให้ไว้)' : '(ถ้ามี)'}:</label>
                    <textarea id="teacherComments" class="form-control" rows="3" 
                            ${isReadonly ? 'readonly' : ''}>${existingComments || ''}</textarea>
                </div>
                </form>
            `;

            return formHtml;
        }

        // 4. ฟังก์ชันควบคุมคลิกเลือกการ์ดครู
        function setupTeacherCardEvents(matchingData, criteria, finalYear, finalTerm, startIndex, isFiltered) {
            const actionButton = document.getElementById('modalActionButton');
            
            if (actionButton) {
                actionButton.style.display = 'inline-block';
            }

            function updateCardVisibility() {
                const activeCard = document.querySelector('[data-card].active');
                const cards = document.querySelectorAll('[data-card]');
                const btnShowAll = document.getElementById('btnShowAllSubjects');
                
                if (activeCard && !window.showAllSubjectsMode) {
                    cards.forEach(c => {
                        if (c === activeCard) {
                            c.style.setProperty('display', 'flex', 'important');
                        } else {
                            c.style.setProperty('display', 'none', 'important');
                        }
                    });
                    if (btnShowAll) btnShowAll.style.setProperty('display', 'inline-block', 'important');
                } else {
                    cards.forEach(c => c.style.setProperty('display', 'flex', 'important'));
                    if (btnShowAll) {
                        btnShowAll.style.setProperty('display', isFiltered ? 'inline-block' : 'none', 'important');
                    }
                }
            }

            document.querySelectorAll('[data-card]').forEach(card => {
                card.addEventListener('click', () => {
                    window.showAllSubjectsMode = false;

                    document.querySelectorAll('[data-card]').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    const radio = card.querySelector('.subject-radio');
                    if (radio) radio.checked = true;
                    
                    const evalIndex = parseInt(card.dataset.evalIndex);
                    const evalData = matchingData[evalIndex];
                    
                    document.getElementById('teacherEvaluationForm').innerHTML = 
                        generateTeacherEvaluationForm(criteria, evalData, finalYear, finalTerm);
                    
                    if (!evalData.has_evaluation) {
                        setupStarRatingEvents();
                    }
                    
                    if (actionButton) {
                        actionButton.style.display = evalData.has_evaluation ? 'none' : 'inline-block';
                    }

                    updateCardVisibility();
                });
            });

            const btnShowAll = document.getElementById('btnShowAllSubjects');
            if (btnShowAll) {
                btnShowAll.onclick = (e) => {
                    e.preventDefault();
                    evaluateSubjectTeacher();
                };
            }

            if (startIndex >= 0) {
                if (matchingData[startIndex].has_evaluation) {
                    if (actionButton) actionButton.style.display = 'none';
                } else {
                    setupStarRatingEvents();
                    if (actionButton) actionButton.style.display = 'inline-block';
                }
                window.showAllSubjectsMode = true;
            } else {
                window.showAllSubjectsMode = true;
            }
            updateCardVisibility();
        }

        // 5. ฟังก์ชันยิง API ไปบันทึกข้อมูล (รับค่าปีและเทอมที่ถูกต้องร้อยเปอร์เซ็นต์มาใช้งาน)
        async function submitTeacherEvaluation(finalYear, finalTerm) {
            const teacherRadio = document.querySelector('input[name="subject_teacher"]:checked');
            if (!teacherRadio) {
                alert('กรุณาเลือกวิชาและครูผู้สอนก่อนทำการบันทึกการประเมินค่ะ');
                return;
            }
            const teacherId = Number(teacherRadio.value);
            const subjectId = Number(teacherRadio.dataset.subjectId);
            const comment = (document.getElementById('teacherComments')?.value || '').trim();

            const groups = document.querySelectorAll('.star-rating:not(.readonly)');
            if (!groups.length) {
                alert('ไม่พบหัวข้อประเมิน');
                return;
            }

            const responses = [];
            let hasMissing = false;

            groups.forEach(group => {
                const cid = Number(group.dataset.criterionId);
                const checked = group.querySelector('input[type="radio"]:checked');
                const score = checked ? Number(checked.value) : 0;
                if (score < 1 || score > 5) hasMissing = true;

                responses.push({
                    criterion_id: cid,
                    rating: score,
                    comments: comment
                });
            });

            if (hasMissing) {
                if (!confirm('มีหัวข้อที่ยังไม่ได้ให้คะแนน ต้องการดำเนินการต่อหรือไม่?')) {
                    return;
                }
            }

            const payload = {
                teacher_id: teacherId,
                subject_id: subjectId,
                academic_year: finalYear, // 🌟 ส่งปีการศึกษาที่ผูกกับตัวเกรดไปบันทึก
                semester: finalTerm,       // 🌟 ส่งเทอมที่ผูกกับตัวเกรดไปบันทึก
                responses,
                comments: comment
            };

            try {
                const res = await fetch('submit_teacher_evaluation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const text = await res.text();
                let json;
                try {
                    json = JSON.parse(text);
                } catch(e) {
                    console.error('Server response not JSON:', text);
                    alert('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์');
                    return;
                }

                if (!res.ok || !json.success) {
                    console.error('Save failed:', json);
                    alert('บันทึกไม่สำเร็จ: ' + (json.error || 'ไม่ทราบสาเหตุ'));
                    return;
                }

                alert('บันทึกการประเมินเรียบร้อยแล้วค่ะ');
                if (typeof closeModal === 'function') closeModal();
                setTimeout(() => {
                    location.reload(); // รีโหลดหน้าใหม่เพื่อให้เกรดเปิดแสดงผลจริง
                }, 1000);

            } catch (err) {
                console.error(err);
                alert('เกิดข้อผิดพลาดขณะส่งข้อมูล');
            }
        }

        function uploadProfilePicture() {
            showAlert('อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว', 'success');
        }

        // 🌟 ฟังก์ชันแสดงกล่องข้อมูลส่วนตัวนักเรียน และฟอร์มแก้ไขข้อมูล/อัปโหลดรูปภาพ
        async function showProfileInfo() {
            try {
                // ดึงข้อมูลส่วนตัวแบบเรียลไทม์จากหลังบ้าน
                const response = await fetch('get_profile.php');
                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // หากไม่มีรูปโปรไฟล์ ให้ใช้รูป Placeholder ตั้งต้น
                const photo = data.profile_picture ? data.profile_picture : 'teacher-placeholder.png';
                
                const content = `
                    <form id="profileForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center border-end mb-3">
                                <div class="position-relative d-inline-block mb-3 mt-2">
                                    <img id="profilePreview" src="images/${photo}" 
                                         class="rounded-circle img-thumbnail shadow-sm" 
                                         style="width: 140px; height: 140px; object-fit: cover;">
                                    
                                    <label for="profile_pic" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center" 
                                           style="width: 34px; height: 34px; cursor: pointer; border: 2px solid white;">
                                        <i class="fa-solid fa-camera"></i>
                                    </label>
                                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display: none;" onchange="previewProfileImage(this)">
                                </div>
                                <div class="text-muted small"><i class="fa-solid fa-circle-info me-1"></i> คลิกไอคอนกล้องเพื่อเลือกรูปใหม่</div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted small fw-bold">คำนำหน้า</label>
                                        <input type="text" class="form-control bg-light" value="${data.prefix_name || ''}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted small fw-bold">ชื่อ</label>
                                        <input type="text" class="form-control bg-light" value="${data.first_name || ''}" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted small fw-bold">นามสกุล</label>
                                        <input type="text" class="form-control bg-light" value="${data.last_name || ''}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold text-primary">เบอร์โทรศัพท์ (แก้ไขได้)</label>
                                        <input type="tel" class="form-control border-primary-subtle" id="edit_phone" name="phone" value="${data.phone || ''}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold text-primary">อีเมล (แก้ไขได้)</label>
                                        <input type="email" class="form-control border-primary-subtle" id="edit_email" name="email" value="${data.email || ''}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-bold text-primary">ที่อยู่ปัจจุบัน (แก้ไขได้)</label>
                                        <textarea class="form-control border-primary-subtle" id="edit_address" name="address" rows="3" required>${data.address || ''}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                `;

                // เปิดกล่อง Modal ด้วยฟังก์ชันกลางของระบบ
                showModal('<i class="fa-solid fa-user-gear me-2"></i> ข้อมูลส่วนตัวของนักเรียน', content, true, 'บันทึกการแก้ไข', () => {
                    saveProfileInfo();
                });

            } catch (error) {
                console.error("Error loading profile:", error);
                alert('เกิดข้อผิดพลาดในการเรียกโหลดข้อมูลส่วนตัวค่ะ');
            }
        }

        // 🌟 ฟังก์ชันทำ Live Preview แสดงภาพตัวอย่างทันทีเมื่อนักเรียนเลือกรูปใหม่
        function previewProfileImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 🌟 ฟังก์ชันรวบรวมไฟล์และข้อมูลยิงไปบันทึกที่ฝั่งหลังบ้านด้วย FormData API
        async function saveProfileInfo() {
            const form = document.getElementById('profileForm');
            
            // ตรวจสอบ Validation เบื้องต้น
            if (!document.getElementById('edit_phone').value || !document.getElementById('edit_email').value || !document.getElementById('edit_address').value) {
                alert('กรุณากรอกข้อมูลเบอร์โทรศัพท์ อีเมล และที่อยู่ให้ครบถ้วนก่อนบันทึกค่ะ');
                return;
            }

            const formData = new FormData(form);

            try {
                const res = await fetch('update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();

                if (json.success) {
                    alert('บันทึกข้อมูลส่วนตัวและอัปเดตรูปโปรไฟล์เรียบร้อยแล้วค่ะ');
                    if (typeof closeModal === 'function') closeModal();
                    setTimeout(() => {
                        location.reload(); // รีโหลดหน้าจอเพื่อให้รูปโปรไฟล์ที่เมนูด้านบนเปลี่ยนตามทันที
                    }, 500);
                } else {
                    alert('บันทึกข้อมูลไม่สำเร็จ: ' + (json.error || 'ไม่ทราบสาเหตุ'));
                }
            } catch (error) {
                console.error("Error saving profile:", error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์ฐานข้อมูลค่ะ');
            }
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

        function handleLogout() {
            if (confirm('คุณต้องการออกจากระบบหรือไม่?')) {
                console.log('Logging out...');
                showAlert('กำลังออกจากระบบ...', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1000);
            }
        }

        // 📊 ประกาศอินสแตนซ์กราฟไว้ส่วนหัวเพื่อป้องกันอาการกราฟซ้อน
        window.myDashboardChartInstance = null;

        function loadDashboardMetrics(urlParams = '') {
            // 🔍 จัดการตัดแต่งพารามิเตอร์ URL ให้สะอาด ป้องกันเครื่องหมาย & ซ้ำซ้อน
            let cleanParams = urlParams;
            if (cleanParams.startsWith('?')) {
                cleanParams = cleanParams.substring(1);
            }
            
            // ยิง Fetch ดึงข้อมูลจริงจากหลังบ้านที่พี่ทูลเพิ่งคิวรี่เจอ
            fetch('get_dashboard_metrics.php?' + cleanParams)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // =========================================================
                    // 📊 1. ดีดตัวเลขสถิติหลักขึ้นโชว์บนการ์ด 4 ใบ
                    // =========================================================
                    if(data.gpa !== undefined && document.getElementById('metric-gpa')) {
                        document.getElementById('metric-gpa').innerText = data.gpa.toFixed(2);
                    }
                    if(data.attendance_pct !== undefined && document.getElementById('metric-attendance')) {
                        document.getElementById('metric-attendance').innerText = data.attendance_pct + '%';
                    }
                    if(data.total_credits !== undefined && document.getElementById('metric-credits')) {
                        document.getElementById('metric-credits').innerText = data.total_credits;
                    }
                    if(data.behavior_score !== undefined && document.getElementById('metric-behavior')) {
                        document.getElementById('metric-behavior').innerText = data.behavior_score;
                    }

                    // 🎛️ ปรับเปลี่ยนข้อความป้ายกำกับการ์ดตามโหมด
                    if (data.mode === 'semester') {
                        if(document.getElementById('label-gpa')) document.getElementById('label-gpa').innerText = 'เกรดเฉลี่ยประจำภาคเรียน';
                        if(document.getElementById('label-credits')) document.getElementById('label-credits').innerText = 'หน่วยกิตประจำภาคเรียน';
                    } else {
                        if(document.getElementById('label-gpa')) document.getElementById('label-gpa').innerText = 'เกรดเฉลี่ยรวมสะสม (GPAX)';
                        if(document.getElementById('label-credits')) document.getElementById('label-credits').innerText = 'หน่วยกิตสะสมรวมทั้งหมด';
                    }

                    // =========================================================
                    // 🎛️ 2. วนลูปสร้างรายการในกล่อง Dropdown (ทำเฉพาะตอนโหลดครั้งแรก)
                    // =========================================================
                    const selector = document.getElementById('metricsScopeSelector');
                    if (selector && selector.options.length === 1 && Array.isArray(data.timeline)) {
                        data.timeline.forEach(sem => {
                            const opt = document.createElement('option');
                            opt.value = `mode=semester&year=${sem.academic_year}&term=${sem.term}`;
                            opt.innerText = `📅 ภาคเรียนที่ ${sem.term} / ปีการศึกษา ${sem.academic_year}`;
                            selector.appendChild(opt);
                        });
                    }

                    // =========================================================
                    // 🎨 3. ขับเคลื่อนการวาดกราฟวงกลมโดนัท (id="gradeDoughnut")
                    // =========================================================
                    const ctx = document.getElementById('gradeDoughnut');
                    if (ctx && data.grade_distribution) {
                        // ทุบกราฟเก่าทิ้งก่อนเพื่อป้องกัน Error ซ้อนทับ
                        if (window.myDashboardChartInstance) {
                            window.myDashboardChartInstance.destroy();
                        }

                        const labels = Object.keys(data.grade_distribution);
                        const counts = Object.values(data.grade_distribution);

                        // คลังชุดสีพรีเมียมคุมโทนสลับแท่งกราฟสากล
                        const colorPalette = {
                            '4': '#1cc88a', '3.5': '#4e73df', '3': '#36b9cc', 
                            '2.5': '#f6c23e', '2': '#fd7e14', '1.5': '#6f42c1', 
                            '1': '#e74a3b', '0': '#858796'
                        };
                        const backgroundColors = labels.map(g => colorPalette[g] || '#858796');

                        // เริ่มวาดตัวกราฟวงแหวนสไตล์โปรแกรมเมอร์มือฉมัง
                        window.myDashboardChartInstance = new Chart(ctx.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: labels.map(g => `เกรด ${g}`),
                                datasets: [{
                                    data: counts,
                                    backgroundColor: backgroundColors,
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: { cutout: '75%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                        });

                        // =========================================================
                        // ✨ 4. พ่นคำอธิบายและจำนวนวิชาลงใน <ul id="grade-legend">
                        // =========================================================
                        const legendContainer = document.getElementById('grade-legend');
                        if (legendContainer) {
                            const labelNames = {
                                '4': 'ระดับดีเยี่ยม (เกรด 4)', '3.5': 'ระดับดีมาก (เกรด 3.5)', '3': 'ระดับดี (เกรด 3)',
                                '2.5': 'ระดับค่อนข้างดี (เกรด 2.5)', '2': 'ระดับปานกลาง (เกget_dเกรด 2)', '1.5': 'ระดับพอใช้ (เกรด 1.5)'
                            };

                            legendContainer.innerHTML = labels.map((grade, i) => `
                                <li class="mb-2 small d-flex align-items-center text-dark" style="font-size:13px; font-family:'Kanit';">
                                    <span class="me-2" style="width:11px; height:11px; background:${backgroundColors[i]}; border-radius:50%; display:inline-block;"></span>
                                    ${labelNames[grade] || 'เกรด ' + grade}: <strong class="ms-1">${counts[i]} วิชา</strong>
                                </li>
                            `).join('');
                        }
                    }

                    // =========================================================
                    // 📅 5. วนลูปเจนปุ่มแท็บประวัติย้อนหลัง Timeline ด้านล่างออโต้
                    // =========================================================
                    const historyHeader = document.getElementById('historyTabHeader');
                    const historyContent = document.getElementById('historyTabContent');

                    if (historyHeader && historyContent && historyHeader.children.length === 0 && Array.isArray(data.history)) {
                        let headersHtml = ''; let contentsHtml = '';

                        if (data.history.length === 0) {
                            headersHtml = `<li class="nav-item"><button class="nav-link active">ไม่มีประวัติ</button></li>`;
                            contentsHtml = `<div class="text-center text-muted py-4 small">ไม่พบประวัติผลงานย้อนหลังในระบบค่ะ</div>`;
                        } else {
                            data.history.forEach((h, index) => {
                                const tabId = `hist-term-${h.term}-${h.academic_year}`;
                                const isActive = index === data.history.length - 1 ? 'active' : ''; 
                                const isShowActive = index === data.history.length - 1 ? 'show active' : '';

                                headersHtml += `
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link ${isActive} fw-bold text-secondary" id="${tabId}-tab" 
                                                data-bs-toggle="tab" data-bs-target="#${tabId}" 
                                                type="button" role="tab" style="font-size: 13px; font-family:'Kanit';">
                                            ภาคเรียน ${h.term}/${h.academic_year}
                                        </button>
                                    </li>`;

                                contentsHtml += `
                                    <div class="tab-pane fade ${isShowActive}" id="${tabId}" role="tabpanel">
                                        <div class="row g-3 text-center mt-1">
                                            <div class="col-md-4"><div class="p-3 rounded-3 border bg-white shadow-xs"><span class="text-secondary small d-block mb-1">เกรดเฉลี่ย (GPA)</span><strong class="fs-4 text-success">${h.gpa.toFixed(2)}</strong></div></div>
                                            <div class="col-md-4"><div class="p-3 rounded-3 border bg-white shadow-xs"><span class="text-secondary small d-block mb-1">หน่วยกิตที่เรียนผ่าน</span><strong class="fs-4 text-primary">${h.credits} หน่วยกิต</strong></div></div>
                                            <div class="col-md-4"><div class="p-3 rounded-3 border bg-white shadow-xs"><span class="text-secondary small d-block mb-1">สถิติเข้าชั้นเรียน</span><strong class="fs-4 text-warning">${h.attendance_pct}%</strong></div></div>
                                        </div>
                                    </div>`;
                            });
                        }
                        historyHeader.innerHTML = headersHtml;
                        historyContent.innerHTML = contentsHtml;
                    }
                }
            })
            .catch(err => console.error("Front-end Render Error:", err));
        }

        function handleScopeChange(selectedValue) {
            if (selectedValue === 'overall') { 
                loadDashboardMetrics('?mode=overall'); 
            } else { 
                loadDashboardMetrics('?' + selectedValue); 
            }
        }

        // ผูกระบบรันงานอัตโนมัติ
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardMetrics('?mode=overall');
            
            // 1. แสดงข้อความต้อนรับปกติ
            showAlert('ยินดีต้อนรับสู่แดชบอร์ดนักเรียน!', 'success');
            
            // 🌟 2. เพิ่มเติม: หากมียอดค้างชำระ ให้เด้ง Alert สีแดงเตือนทันทีหลัง Login สำเร็จ
            <?php if($has_unpaid_fees): ?>
                setTimeout(() => {
                    showAlert('<i class="bi bi-exclamation-octagon-fill me-2"></i> <strong>แจ้งเตือน:</strong> ตรวจพบยอดค้างชำระค่าเทอม! กรุณาตรวจสอบที่เมนูค่าเล่าเรียนค่ะ', 'danger');
                }, 800); // หน่วงเวลาเล็กน้อยเพื่อให้ดูเหมือนเป็นการแจ้งเตือนใหม่ที่เด้งขึ้นมาค่ะ
            <?php endif; ?>
            
            // 3. ตรวจสอบ Deep Link จากหน้าอื่น
            const urlParams = new URLSearchParams(window.location.search);
            const subjectId = urlParams.get('subject_id');
            if (subjectId) {
                evaluateSubjectTeacher(subjectId);
            }
        });
    </script>

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
                        <p><i class="bi bi-envelope me-2"></i><a href='mailto:manager@kpsw.ac.th'>อีเมลล์ผู้อำนวยการ: manager@kpsw.ac.th</a></p>

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
</body>
</html>