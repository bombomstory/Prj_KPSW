<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบทะเบียนนักเรียน - โรงเรียนกำแพงแสนวิทยา</title>

    <link rel="shortcut icon" href="images/favicon.svg" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Kanit';
        }
        
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }
        
        body {
            line-height: 1.6;
        }
        
        /* Header Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 400;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--accent-color) !important;
            transform: translateY(-2px);
        }
        
        .btn-login {
            background: var(--accent-color);
            border: none;
            color: white;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>');
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        /* Features Section */
        .features-section {
            padding: 4rem 0;
            background: var(--light-color);
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        /* Quick Access Section */
        .quick-access {
            padding: 4rem 0;
            background: white;
        }
        
        .access-card {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }
        
        .access-card:hover {
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem 0;
        }
        
        .footer-content {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .footer-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
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
        
        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 1rem;
            text-align: center;
            color: #9ca3af;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .feature-card {
                margin-bottom: 2rem;
            }
        }
        
        /* Animations */
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
        
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="60">    
<!--                 <i class="fas fa-graduation-cap me-2"></i> -->
                โรงเรียนกำแพงแสนวิทยา
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">เกี่ยวกับโรงเรียน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">บริการ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#news">ข่าวสาร</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">ติดต่อ</a>
                    </li>
                </ul>
                <button class="btn btn-login" onclick="window.location.href='login.php';">
                    <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content animate-fadeInUp">
                        <h1 class="hero-title">ระบบทะเบียนนักเรียน</h1>
                        <p class="hero-subtitle">โรงเรียนกำแพงแสนวิทยา - ระบบจัดการข้อมูลนักเรียนที่ทันสมัย สะดวก และปลอดภัย</p>
                        <div class="d-flex flex-wrap gap-3">
                            <button class="btn btn-light btn-lg" onclick="window.location.href='login.php?ut=student';">
                                <i class="fas fa-user-graduate me-2"></i>สำหรับนักเรียน
                            </button>
                            <button class="btn btn-outline-light btn-lg" onclick="window.location.href='login.php?ut=teacher';">
                                <i class="fas fa-chalkboard-teacher me-2"></i>สำหรับครู
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="images/hero-img.png" alt="Student Registration System" class="img-fluid rounded-3 shadow-lg" width="500" height="400">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="services" class="features-section">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-dark mb-3">บริการของเรา</h2>
                    <p class="lead text-muted">ระบบจัดการข้อมูลนักเรียนที่ครบครันและใช้งานง่าย</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="feature-title">ลงทะเบียนนักเรียน</h3>
                        <p class="text-muted">ระบบลงทะเบียนนักเรียนใหม่ที่สะดวกและรวดเร็ว พร้อมการตรวจสอบข้อมูลอัตโนมัติ</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">ตรวจสอบผลการเรียน</h3>
                        <p class="text-muted">ดูผลการเรียน คะแนนสอบ และสถิติการเรียนแบบออนไลน์ทุกที่ทุกเวลา</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="feature-title">ตารางเรียน</h3>
                        <p class="text-muted">ระบบตารางเรียนดิจิทัล พร้อมการแจ้งเตือนและการอัพเดทข้อมูลแบบเรียลไทม์</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="feature-title">เอกสารและใบรับรอง</h3>
                        <p class="text-muted">ขอเอกสารต่างๆ เช่น ใบรับรอง ใบแสดงผลการเรียน ผ่านระบบออนไลน์</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">ข่าวสารและประกาศ</h3>
                        <p class="text-muted">รับข่าวสารและประกาศจากโรงเรียนแบบเรียลไทม์ พร้อมการแจ้งเตือน</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">ช่วยเหลือและสนับสนุน</h3>
                        <p class="text-muted">ทีมสนับสนุนพร้อมให้ความช่วยเหลือตลอด 24 ชั่วโมง</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Quick Access Section -->
    <section class="quick-access">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-dark mb-3">เข้าใช้งานด่วน</h2>
                    <p class="lead text-muted">เลือกประเภทผู้ใช้งานเพื่อเข้าสู่ระบบ</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=student';">
                        <i class="fas fa-user-graduate fa-3x mb-3"></i>
                        <h4>นักเรียน</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับนักเรียน</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=parent';" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h4>ผู้ปกครอง</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับผู้ปกครอง</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=advisor';" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">
                        <i class="fas fa-user-friends fa-3x mb-3"></i>
                        <h4>ครูที่ปรึกษา</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับครูที่ปรึกษา</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=teacher';" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                        <h4>ครูผู้สอน</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับครูผู้สอน</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=registrar';" style="background: linear-gradient(135deg, #059669, #047857);">
                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                        <h4>งานทะเบียน</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับงานทะเบียน</p>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <a href="#" class="access-card" onclick="window.location.href='login.php?ut=admin';" style="background: linear-gradient(135deg, #7c3aed, #5b21b6);">
                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                        <h4>ผู้บริหาร</h4>
                        <p class="mb-0">เข้าสู่ระบบสำหรับผู้บริหาร</p>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a name="contact">
                        <h5><img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="40"> โรงเรียนกำแพงแสนวิทยา</h5>
                        </a>
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
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5 class="footer-title">เมนูหลัก</h5>
                        <a href="#home" class="footer-link">หน้าแรก</a>
                        <a href="#about" class="footer-link">เกี่ยวกับโรงเรียน</a>
                        <a href="#services" class="footer-link">บริการ</a>
                        <a href="#news" class="footer-link">ข่าวสาร</a>
                        <a href="#contact" class="footer-link">ติดต่อ</a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title">บริการ</h5>
                        <a href="#" class="footer-link">ลงทะเบียนนักเรียน</a>
                        <a href="#" class="footer-link">ตรวจสอบผลการเรียน</a>
                        <a href="#" class="footer-link">ตารางเรียน</a>
                        <a href="#" class="footer-link">เอกสารและใบรับรอง</a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5 class="footer-title">ติดต่อเรา</h5>
                        <p class="footer-link"><i class="fas fa-map-marker-alt me-2"></i>123 ถ.กำแพงแสน อ.กำแพงแสน จ.นครปฐม</p>
                        <p class="footer-link"><i class="fas fa-phone me-2"></i>Tel.034-351047, Fax 034-351264</p>
                        <p class="footer-link"><i class="fas fa-envelope me-2"></i><a href='mailto:support@kpsw.ac.th'>support@kpsw.ac.th</a></p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2568 โรงเรียนกำแพงแสนวิทยา. สงวนลิขสิทธิ์ทุกประการ.</p>
            </div>
        </div>
    </footer>
    
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">เข้าสู่ระบบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">ประเภทผู้ใช้งาน</label>
                            <select class="form-select">
                                <option>นักเรียน</option>
                                <option>ครูผู้สอน</option>
                                <option>ผู้ปกครอง</option>
                                <option>ผู้บริหาร</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รหัสประจำตัว/อีเมล</label>
                            <input type="text" class="form-control" placeholder="กรอกรหัสประจำตัวหรืออีเมล">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control" placeholder="กรอกรหัสผ่าน">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">เข้าสู่ระบบ</button>
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">ลืมรหัสผ่าน?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for navigation links
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
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.feature-card, .access-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>