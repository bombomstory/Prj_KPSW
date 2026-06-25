<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โรงเรียนกำแพงแสนวิทยา - ระบบงานทะเบียนนักเรียน</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: #1a237e;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: white;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }
        
        .dropdown-item {
            font-weight: 300;
            padding: 0.5rem 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #303f9f, #1a237e);
            color: white;
            padding: 4rem 0;
            border-radius: 0 0 1rem 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
        }
        
        .hero-img {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #303f9f;
        }
        
        .card-title {
            font-weight: 500;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }
        
        .card-text {
            font-weight: 300;
            color: #6c757d;
        }
        
        .btn-primary {
            background-color: #303f9f;
            border-color: #303f9f;
            border-radius: 0.5rem;
            padding: 0.5rem 1.5rem;
            font-weight: 400;
        }
        
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #1a237e;
            border-color: #1a237e;
        }
        
        .footer {
            background-color: #1a237e;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .footer h5 {
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .footer-links {
            list-style: none;
            padding-left: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: opacity 0.3s;
        }
        
        .social-icons a:hover {
            opacity: 0.8;
        }
        
        .copyright {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .active-menu {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 0.25rem;
        }
        
        .dashboard-stats {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background-color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .stats-card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #303f9f;
        }
        
        .stats-card-value {
            font-size: 2rem;
            font-weight: 600;
            color: #303f9f;
            margin-bottom: 0.5rem;
        }
        
        .stats-card-title {
            font-size: 1rem;
            font-weight: 400;
            color: #6c757d;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .hero-section {
                padding: 3rem 0;
            }
            
            .hero-img {
                margin-top: 2rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .navbar {
                padding: 0.5rem;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .footer {
                text-align: center;
            }
            
            .footer-section {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="ms-2"><img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" width="40px"> โรงเรียนกำแพงแสนวิทยา</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active-menu" href="#"><i class="fas fa-home me-1"></i> หน้าหลัก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user me-1"></i> ข้อมูลส่วนตัว</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-book me-1"></i> ผลการเรียน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-calendar-alt me-1"></i> ตารางเรียน</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="teacherEvalDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-comment-dots me-1"></i> ประเมินครู
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="teacherEvalDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-friends me-1"></i> ประเมินครูที่ปรึกษา</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chalkboard-teacher me-1"></i> ประเมินครูผู้สอน</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-clipboard-list me-1"></i> กิจกรรม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell me-1"></i> แจ้งเตือน</a>
                    </li>
                </ul>
                <div class="ms-3 d-flex align-items-center">
                    <div class="dropdown">
                        <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> นักเรียน
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-1"></i> ตั้งค่า</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="fw-bold mb-4">ยินดีต้อนรับสู่ระบบงานทะเบียนนักเรียน</h1>
                    <p class="lead mb-4">ระบบงานทะเบียนออนไลน์สำหรับนักเรียนโรงเรียนกำแพงแสนวิทยา เข้าถึงข้อมูลการศึกษาของคุณได้ทุกที่ทุกเวลา</p>
                    <button class="btn btn-light btn-lg">เริ่มต้นใช้งาน <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
                <div class="col-lg-6">
                    <img src="images/hero-img.png" width="350px" alt="Student Dashboard" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Stats -->
    <section class="dashboard-stats">
        <div class="container">
            <h2 class="text-center mb-4">แดชบอร์ดของฉัน</h2>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stats-card-value">3.75</div>
                        <div class="stats-card-title">เกรดเฉลี่ยสะสม</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stats-card-value">95%</div>
                        <div class="stats-card-title">การเข้าเรียน</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stats-card-value">5</div>
                        <div class="stats-card-title">กิจกรรมที่ต้องทำ</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-card-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="stats-card-value">3</div>
                        <div class="stats-card-title">รางวัลที่ได้รับ</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Features -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">บริการสำหรับนักเรียน</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="card-title">ข้อมูลส่วนตัว</h3>
                        <p class="card-text">จัดการข้อมูลประวัติส่วนตัว ที่อยู่ และข้อมูลผู้ปกครอง</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="card-title">ผลการเรียน</h3>
                        <p class="card-text">ตรวจสอบผลการเรียน เกรดเฉลี่ย และประวัติการศึกษา</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="card-title">ตารางเรียน</h3>
                        <p class="card-text">ดูตารางเรียนประจำวัน ตารางสอบ และกิจกรรมโรงเรียน</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <h3 class="card-title">ประเมินครู</h3>
                        <p class="card-text">ร่วมประเมินครูที่ปรึกษาและครูผู้สอนเพื่อพัฒนาคุณภาพการเรียนการสอน</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="card-title">กิจกรรม</h3>
                        <p class="card-text">ลงทะเบียนและติดตามกิจกรรมพัฒนาผู้เรียนและชมรมต่างๆ</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 p-4">
                        <div class="card-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="card-title">แจ้งเตือน</h3>
                        <p class="card-text">รับการแจ้งเตือนสำคัญเกี่ยวกับเรื่องเรียน กิจกรรม และประกาศจากโรงเรียน</p>
                        <a href="#" class="btn btn-primary mt-3">เข้าสู่เมนู</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">กำหนดการสำคัญ</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white p-3 rounded me-3 text-center">
                                    <div class="fw-bold">15</div>
                                    <div>พ.ค.</div>
                                </div>
                                <h5 class="mb-0">สอบกลางภาค</h5>
                            </div>
                            <p class="card-text small">สอบกลางภาคเรียนที่ 1 ประจำปีการศึกษา 2025</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white p-3 rounded me-3 text-center">
                                    <div class="fw-bold">20</div>
                                    <div>พ.ค.</div>
                                </div>
                                <h5 class="mb-0">กิจกรรมวันวิทยาศาสตร์</h5>
                            </div>
                            <p class="card-text small">การแข่งขันโครงงานวิทยาศาสตร์ประจำปี</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white p-3 rounded me-3 text-center">
                                    <div class="fw-bold">25</div>
                                    <div>พ.ค.</div>
                                </div>
                                <h5 class="mb-0">กีฬาสี</h5>
                            </div>
                            <p class="card-text small">กิจกรรมกีฬาสีประจำปีของโรงเรียน</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white p-3 rounded me-3 text-center">
                                    <div class="fw-bold">1</div>
                                    <div>มิ.ย.</div>
                                </div>
                                <h5 class="mb-0">ปิดรับสมัครชมรม</h5>
                            </div>
                            <p class="card-text small">วันสุดท้ายของการลงทะเบียนชมรมประจำปี</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 footer-section">
                    <h5><img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" width="40px"> โรงเรียนกำแพงแสนวิทยา</h5>
                    <p>เลขที่ 123 หมู่ 4 ตำบลกำแพงแสน<br>อำเภอกำแพงแสน จังหวัดนครปฐม 73140</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 footer-section">
                    <h5>ลิงก์ด่วน</h5>
                    <ul class="footer-links">
                        <li><a href="#">หน้าหลัก</a></li>
                        <li><a href="#">ข้อมูลส่วนตัว</a></li>
                        <li><a href="#">ผลการเรียน</a></li>
                        <li><a href="#">ตารางเรียน</a></li>
                        <li><a href="#">ประเมินครู</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 footer-section">
                    <h5>บริการนักเรียน</h5>
                    <ul class="footer-links">
                        <li><a href="#">คู่มือนักเรียน</a></li>
                        <li><a href="#">ปฏิทินการศึกษา</a></li>
                        <li><a href="#">ดาวน์โหลดเอกสาร</a></li>
                        <li><a href="#">ช่องทางติดต่อครู</a></li>
                        <li><a href="#">คำถามที่พบบ่อย</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 footer-section">
                    <h5>ติดต่อเรา</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-phone me-2"></i> 034-123456</li>
                        <li><i class="fas fa-envelope me-2"></i> info@kpws.ac.th</li>
                        <li><i class="fas fa-clock me-2"></i> จันทร์-ศุกร์: 8:00 - 16:30</li>
                        <li><a href="#"><i class="fas fa-question-circle me-2"></i> ฝ่ายช่วยเหลือ</a></li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="row">
                <div class="col-md-6">
                    <p class="copyright">© 2025 โรงเรียนกำแพงแสนวิทยา. สงวนลิขสิทธิ์.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="copyright">ออกแบบและพัฒนาโดย ฝ่ายเทคโนโลยีสารสนเทศ</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>