<?php
session_start();
// ตรวจสอบสิทธิ์ (เปิดคอมเมนต์เมื่อเชื่อมระบบ Login สมบูรณ์แล้ว)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'registrar') {
//     header('Location: index.php');
//     exit();
// }
include('db_connection.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นำเข้าข้อมูลระบบ - โรงเรียนกำแพงแสนวิทยา</title>
    
    <link rel="shortcut icon" href="images/favicon.svg" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --shadow-primary: 0 10px 25px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Kanit', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }
        .navbar {
            background: var(--primary-gradient);
            box-shadow: var(--shadow-primary);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .page-header {
            background: white;
            padding: 2rem 0;
            margin-top: 60px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .import-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .import-card-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.25rem;
        }
        .instruction-card {
            background: #fff8f1;
            border-left: 5px solid #f59e0b;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #38ef7d;
            background: #f0fdf4;
        }
        .upload-icon {
            font-size: 4rem;
            color: #94a3b8;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .upload-area:hover .upload-icon {
            color: #38ef7d;
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="registarDashboard.php">
                <img src="images/KPSWLogo.png" alt="Logo" width="40" class="me-2 rounded-circle bg-white p-1">
                โรงเรียนกำแพงแสนวิทยา
            </a>
            <div class="d-flex">
                <a href="registarDashboard.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> กลับหน้าหลัก
                </a>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="bi bi-cloud-arrow-up-fill fs-2"></i>
                </div>
                <div>
                    <h2 class="mb-1 fw-bold text-dark">นำเข้าข้อมูล (Import Data)</h2>
                    <p class="text-muted mb-0">นำเข้าไฟล์ผลการเรียน รายชื่อนักเรียน และข้อมูลวิชาเรียนเข้าสู่ระบบ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            
            <div class="col-lg-4 mb-4">
                <div class="instruction-card shadow-sm">
                    <h5 class="fw-bold text-warning-emphasis mb-3">
                        <i class="bi bi-lightbulb-fill me-2"></i>คำแนะนำการนำเข้า
                    </h5>
                    <ol class="text-muted ps-3 mb-0" style="font-size: 0.95rem; line-height: 1.8;">
                        <li>ไฟล์ที่รองรับคือ <strong>.csv</strong> (Comma Separated Values) เท่านั้น</li>
                        <li>กรุณาตรวจสอบให้แน่ใจว่าไฟล์ถูกบันทึกด้วยการเข้ารหัส <strong>UTF-8</strong> เพื่อป้องกันปัญหาภาษาไทยอ่านไม่ออก</li>
                        <li>ระบบจะทำการ <strong>เพิ่มนักเรียน/ครู/วิชา ใหม่ให้อัตโนมัติ</strong> หากไม่พบข้อมูลในระบบ</li>
                        <li>หากอัปโหลดข้อมูลเทอมซ้ำ ระบบจะทำการ <strong>อัปเดต(เขียนทับ)</strong> ข้อมูลเดิมให้อัตโนมัติ</li>
                    </ol>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-arrow-down-fill text-primary me-2"></i>ดาวน์โหลดไฟล์ต้นแบบ</h6>
                        <a href="#" class="btn btn-light w-100 text-start mb-2 border">
                            <i class="bi bi-filetype-csv text-success me-2"></i> ไฟล์ผลการเรียน (Grade_1-1.csv)
                        </a>
                        <a href="#" class="btn btn-light w-100 text-start mb-2 border">
                            <i class="bi bi-filetype-csv text-primary me-2"></i> ไฟล์รายชื่อนักเรียน (Students.csv)
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> <strong>สำเร็จ!</strong> นำเข้าข้อมูลจำนวน <?php echo htmlspecialchars($_GET['count']); ?> รายการเรียบร้อยแล้ว
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>เกิดข้อผิดพลาด!</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="import-card">
                    <div class="import-card-header">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> ฟอร์มนำเข้าข้อมูล
                    </div>
                    <div class="card-body p-4">
                        <form action="import_grades.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ประเภทข้อมูล</label>
                                    <select class="form-select form-select-lg" name="importType" required>
                                        <option value="grades" selected>ผลการเรียน (All-in-one)</option>
                                        <option value="students">เฉพาะรายชื่อนักเรียน</option>
                                        <option value="schedule">เฉพาะตารางสอน</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">ปีการศึกษา</label>
                                    <input type="number" class="form-control form-select-lg" name="academic_year" value="<?php echo date('Y') + 543; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">ภาคเรียน</label>
                                    <select class="form-select form-select-lg" name="term" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">เลือกไฟล์ CSV</label>
                                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <h5 class="fw-bold text-dark">คลิกเพื่อเลือกไฟล์ หรือ ลากไฟล์มาวางที่นี่</h5>
                                    <p class="text-muted mb-0" id="fileNameDisplay">รองรับไฟล์ .csv ขนาดไม่เกิน 10MB</p>
                                    <input type="file" id="fileInput" name="file" class="d-none" accept=".csv" required onchange="updateFileName(this)">
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-light border px-4" onclick="document.getElementById('fileNameDisplay').innerText='รองรับไฟล์ .csv ขนาดไม่เกิน 10MB'">ล้างข้อมูล</button>
                                <button type="submit" name="import" class="btn btn-success px-5 py-2 fw-bold" id="submitBtn">
                                    <i class="bi bi-upload me-2"></i> อัปโหลดและนำเข้า
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                display.innerHTML = `<span class="text-success fw-bold"><i class="bi bi-file-earmark-check-fill me-1"></i> เลือกไฟล์: ${fileName}</span>`;
            } else {
                display.innerText = 'รองรับไฟล์ .csv ขนาดไม่เกิน 10MB';
            }
        }

        // แสดงสถานะ Loading ตอนกด Submit
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>กำลังประมวลผล...';
            btn.classList.add('disabled');
        });
    </script>
</body>
</html>