<?php
session_start();
include('db_connection.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>แดชบอร์ดสำหรับผู้บริหาร - โรงเรียนกำแพงแสนวิทยา</title>

  <link rel="shortcut icon" href="images/favicon.svg" />
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Font Kanit -->
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Kanit', sans-serif;
      background: #f4f7fc;
      padding-top: 70px;
    }
    .navbar-brand {
      font-weight: 600;
    }
    .footer {
      background: rgba(30, 41, 59, 0.95);
      backdrop-filter: blur(10px);
      color: white;
      padding: 2rem 0;
      margin-top: 3rem;
      text-align: center;
    }
    select[multiple] {
      height: auto !important;
      min-height: 120px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="background: linear-gradient(135deg, #6b48ff 0%, #00ddeb 100%);">
    <div class="container">
      <img src="images/KPSWLogo.png" alt="โรงเรียนกำแพงแสนวิทยา" class="fas fa-graduation-cap me-2" width="60">  
      <a class="navbar-brand" href="#">โรงเรียนกำแพงแสนวิทยา</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNavAdmin"
        aria-controls="navbarNavAdmin"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAdmin">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link active" href="#" onclick="showDashboard()">แดชบอร์ด</a></li>
          <li class="nav-item"><a class="nav-link" href="#" onclick="showReports()">รายงาน</a></li>
          <li class="nav-item"><a class="nav-link" href="#" onclick="showSettings()">ตั้งค่าระบบ</a></li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="profileDropdownAdmin"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-person-circle"></i> 
              <?=$_SESSION['first_name'];?> <?=$_SESSION['last_name'];?>
            </a>
            <!--
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownAdmin">
              <li><a class="dropdown-item" href="#" onclick="showProfile()">ข้อมูลส่วนตัว</a></li>
              <li><a class="dropdown-item" href="#" onclick="showAccountSettings()">ตั้งค่าบัญชี</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item text-danger" href="#" onclick="handleLogout()">ออกจากระบบ</a></li>
            </ul>
            -->
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

  <!-- Main Content -->
  <section class="container mt-5 pt-4" id="mainContent">
    <h2 class="mb-4">แดชบอร์ดผู้บริหาร</h2>

    <!-- ตัวอย่างกราฟอื่นๆ -->
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>จำนวนนักเรียน ปีนี้ vs ปีที่แล้ว</h5>
          <canvas id="studentCountChart" height="250"></canvas>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>ผลการเรียนเฉลี่ยรายชั้น ปีนี้ vs ปีที่แล้ว</h5>
          <canvas id="avgGradeChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>แนวโน้มการเข้าเรียนรายเดือน (ปีนี้)</h5>
          <canvas id="monthlyAttendanceChart" height="250"></canvas>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>จำนวนครูและบุคลากรแยกตามฝ่าย/แผนก</h5>
          <canvas id="staffDepartmentChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>สัดส่วนนักเรียนตามชั้น</h5>
          <canvas id="studentDistributionChart" height="250"></canvas>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card shadow-sm p-3">
          <h5>สัดส่วนคะแนนประเมินครู</h5>
          <canvas id="teacherEvalDistributionChart" height="250"></canvas>
        </div>
      </div>
    </div>
    
    <div class="row">
        
        <!-- Spider Chart ครูผู้สอน -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm p-3">
            <h5>ผลการประเมินครูผู้สอนแยกด้าน (Spider Chart)</h5>
            <div class="mb-4">
                <label for="teacherSelectRadar" class="form-label">เลือกครูผู้สอนเพื่อแสดงผลและเปรียบเทียบ</label>
                <select id="teacherSelectRadar" class="form-select" multiple size="4">
                <option value="t1" selected>อ.สมชาย ใจดี</option>
                <option value="t2">อ.สมหญิง แก้วตา</option>
                <option value="t3">อ.มาลี ใจงาม</option>
                <option value="t4">อ.ศิริ สง่า</option>
                </select>
                <small class="text-muted">กด Ctrl (หรือ Cmd บน Mac) เพื่อเลือกหลายครู</small>
            </div>
            <canvas id="teacherEvalRadarChart" height="250"></canvas>
          </div>
        </div>

        <!-- Spider Chart ครูที่ปรึกษา -->
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm p-3">
            <h5>ผลการประเมินครูที่ปรึกษาแยกด้าน (Spider Chart)</h5>
            <div class="mb-4">
                <label for="advisorSelectRadar" class="form-label">เลือกครูที่ปรึกษาเพื่อแสดงผลและเปรียบเทียบ</label>
                <select id="advisorSelectRadar" class="form-select" multiple size="4">
                <option value="a1" selected>อ.สมศรี ใจดี</option>
                <option value="a2">อ.สมหมาย มีสุข</option>
                <option value="a3">อ.สมปอง ใจงาม</option>
                <option value="a4">อ.สมบัติ เกษมสุข</option>
                </select>
                <small class="text-muted">กด Ctrl (หรือ Cmd บน Mac) เพื่อเลือกหลายครูที่ปรึกษา</small>
            </div>
            <canvas id="advisorEvalRadarChart" height="250"></canvas>
          </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-4">
      <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary p-3">
          <h5>ครูและบุคลากร</h5>
          <p class="display-6">85 คน</p>
          <button class="btn btn-light btn-sm" onclick="showStaffDetails()">รายละเอียด</button>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card text-white bg-success p-3">
          <h5>นักเรียนเข้าเรียนวันนี้</h5>
          <p class="display-6">1,120 คน</p>
          <button class="btn btn-light btn-sm" onclick="showAttendanceToday()">รายละเอียด</button>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning p-3">
          <h5>ผลการประเมินครูเฉลี่ย</h5>
          <p class="display-6">4.3 / 5.0</p>
          <button class="btn btn-light btn-sm" onclick="showTeacherEvalReport()">ดูรายงาน</button>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card text-white bg-danger p-3">
          <h5>ค่าใช้จ่ายโรงเรียนเดือนนี้</h5>
          <p class="display-6">1,250,000 บาท</p>
          <button class="btn btn-light btn-sm" onclick="showSchoolExpenses()">รายละเอียด</button>
        </div>
      </div>
    </div>

    <!-- Table: Students with High Absence -->
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card shadow-sm p-3">
          <h5>รายชื่อนักเรียนขาดเรียนเกินเกณฑ์ (มากกว่า 10 วัน)</h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>รหัสนักเรียน</th>
                  <th>ชื่อ-สกุล</th>
                  <th>ชั้นเรียน</th>
                  <th>จำนวนวันที่ขาด</th>
                </tr>
              </thead>
              <tbody id="highAbsenceTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>

  <!-- Modal -->
  <div
    class="modal fade"
    id="adminModal"
    tabindex="-1"
    aria-labelledby="adminModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adminModalLabel">รายละเอียด</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="ปิด"
          ></button>
        </div>
        <div class="modal-body" id="adminModalBody"></div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
          >
            ปิด
          </button>
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
  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // ข้อมูลจำลองสำหรับกราฟต่าง ๆ
    const studentCountData = {
      labels: ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'],
      yearThis: [120, 110, 130, 140, 115, 125],
      yearLast: [115, 108, 128, 135, 110, 120]
    };

    const avgGradeData = {
      labels: ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'],
      yearThis: [3.0, 2.9, 3.1, 3.2, 3.0, 3.1],
      yearLast: [2.8, 2.9, 3.0, 3.1, 2.9, 3.0]
    };

    const studentDistributionData = {
      labels: ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'],
      counts: [120, 110, 130, 140, 115, 125]
    };

    const monthlyAttendanceData = {
      labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
      attendancePercent: [95, 94, 93, 96, 95, 97, 94, 93, 92, 96, 97, 95]
    };

    const staffByDeptData = {
      labels: ['ฝ่ายวิชาการ', 'ฝ่ายกิจการนักเรียน', 'ฝ่ายบริหาร', 'ฝ่ายเทคนิค', 'ฝ่ายสนับสนุน'],
      counts: [30, 20, 15, 10, 10]
    };

    const teacherEvalRatingData = {
      labels: ['ดีเยี่ยม', 'ดี', 'พอใช้', 'ปรับปรุง'],
      counts: [45, 30, 8, 2]
    };

    const highAbsenceStudents = [
      {id: '65001234567', name: 'นายสมชาย ใจดี', class: 'ม.4/1', daysAbsent: 12},
      {id: '65001234568', name: 'นางสาวสมหญิง แก้วตา', class: 'ม.5/2', daysAbsent: 15},
      {id: '65001234569', name: 'นายมานะ ทรงดี', class: 'ม.3/4', daysAbsent: 11},
    ];

    // หัวข้อประเมินครูผู้สอน (6 ด้าน)
    const teacherEvalCriteria = [
      'ความรู้ความสามารถ',
      'การสื่อสาร',
      'การจัดการชั้นเรียน',
      'ความรับผิดชอบ',
      'ทัศนคติและความเป็นมืออาชีพ',
      'ทักษะการสอน'
    ];

    // ข้อมูลคะแนนประเมินครูผู้สอน
    const teacherEvaluations = {
      t1: { name: 'อ.สมชาย ใจดี', scores: [4.5, 4.7, 4.3, 4.6, 4.8, 4.4] },
      t2: { name: 'อ.สมหญิง แก้วตา', scores: [4.2, 4.4, 4.5, 4.1, 4.3, 4.1] },
      t3: { name: 'อ.มาลี ใจงาม', scores: [4.7, 4.8, 4.6, 4.7, 4.9, 4.8] },
      t4: { name: 'อ.ศิริ สง่า', scores: [4.0, 4.1, 3.9, 4.0, 4.2, 3.8] }
    };

    // หัวข้อประเมินครูที่ปรึกษา (5 ด้าน)
    const advisorEvalCriteria = [
      'การดูแลเอาใจใส่นักเรียน',
      'ความสามารถในการให้คำปรึกษา',
      'การสื่อสารกับนักเรียนและผู้ปกครอง',
      'การส่งเสริมพัฒนาการนักเรียน',
      'ความรับผิดชอบ'
    ];

    // ข้อมูลคะแนนประเมินครูที่ปรึกษา
    const advisorEvaluations = {
      a1: { name: 'อ.สมศรี ใจดี', scores: [4.8, 4.7, 4.6, 4.7, 4.9] },
      a2: { name: 'อ.สมหมาย มีสุข', scores: [4.5, 4.3, 4.4, 4.2, 4.3] },
      a3: { name: 'อ.สมปอง ใจงาม', scores: [4.7, 4.6, 4.5, 4.7, 4.8] },
      a4: { name: 'อ.สมบัติ เกษมสุข', scores: [4.0, 4.1, 3.9, 4.0, 4.1] }
    };

    let teacherRadarChart, advisorRadarChart;

    // ฟังก์ชันวาดกราฟใยแมงมุม (Radar) ครูผู้สอน
    function drawTeacherRadarChart(selectedTeachers) {
      if (!selectedTeachers.length) return;

      const colors = {
        t1: 'rgba(107, 72, 255, 0.7)',
        t2: 'rgba(0, 221, 235, 0.7)',
        t3: 'rgba(255, 159, 64, 0.7)',
        t4: 'rgba(255, 99, 132, 0.7)'
      };

      const datasets = selectedTeachers.map(tid => {
        const teacher = teacherEvaluations[tid];
        return {
          label: teacher.name,
          data: teacher.scores,
          fill: true,
          backgroundColor: colors[tid],
          borderColor: colors[tid].replace('0.7', '1'),
          pointBackgroundColor: colors[tid].replace('0.7', '1'),
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: colors[tid].replace('0.7', '1'),
          tension: 0.3
        };
      });

      if (teacherRadarChart) teacherRadarChart.destroy();

      const ctx = document.getElementById('teacherEvalRadarChart').getContext('2d');
      teacherRadarChart = new Chart(ctx, {
        type: 'radar',
        data: {
          labels: teacherEvalCriteria,
          datasets: datasets
        },
        options: {
          responsive: true,
          scales: {
            r: {
              min: 0,
              max: 5,
              ticks: { stepSize: 1, backdropColor: 'rgba(255,255,255,0.8)' },
              pointLabels: { font: { size: 14, weight: 'bold' } }
            }
          },
          plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
        }
      });
    }

    // ฟังก์ชันวาดกราฟใยแมงมุม ครูที่ปรึกษา
    function drawAdvisorRadarChart(selectedAdvisors) {
      if (!selectedAdvisors.length) return;

      const colors = {
        a1: 'rgba(54, 162, 235, 0.7)',
        a2: 'rgba(255, 206, 86, 0.7)',
        a3: 'rgba(75, 192, 192, 0.7)',
        a4: 'rgba(153, 102, 255, 0.7)'
      };

      const datasets = selectedAdvisors.map(aid => {
        const advisor = advisorEvaluations[aid];
        return {
          label: advisor.name,
          data: advisor.scores,
          fill: true,
          backgroundColor: colors[aid],
          borderColor: colors[aid].replace('0.7', '1'),
          pointBackgroundColor: colors[aid].replace('0.7', '1'),
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: colors[aid].replace('0.7', '1'),
          tension: 0.3
        };
      });

      if (advisorRadarChart) advisorRadarChart.destroy();

      const ctx = document.getElementById('advisorEvalRadarChart').getContext('2d');
      advisorRadarChart = new Chart(ctx, {
        type: 'radar',
        data: {
          labels: advisorEvalCriteria,
          datasets: datasets
        },
        options: {
          responsive: true,
          scales: {
            r: {
              min: 0,
              max: 5,
              ticks: { stepSize: 1, backdropColor: 'rgba(255,255,255,0.8)' },
              pointLabels: { font: { size: 14, weight: 'bold' } }
            }
          },
          plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
        }
      });
    }

    // Event listeners for selects
    document.getElementById('teacherSelectRadar').addEventListener('change', function () {
      const selected = Array.from(this.selectedOptions).map(o => o.value);
      drawTeacherRadarChart(selected);
    });

    document.getElementById('advisorSelectRadar').addEventListener('change', function () {
      const selected = Array.from(this.selectedOptions).map(o => o.value);
      drawAdvisorRadarChart(selected);
    });

    // โหลดเริ่มต้น
    document.addEventListener('DOMContentLoaded', () => {
      const teacherSelect = document.getElementById('teacherSelectRadar');
      drawTeacherRadarChart(Array.from(teacherSelect.selectedOptions).map(o => o.value));

      const advisorSelect = document.getElementById('advisorSelectRadar');
      drawAdvisorRadarChart(Array.from(advisorSelect.selectedOptions).map(o => o.value));

      loadOtherCharts();
      loadHighAbsenceTable();
    });

    // ฟังก์ชันวาดกราฟอื่น ๆ
    function loadOtherCharts() {
      // Student Count Chart
      new Chart(document.getElementById('studentCountChart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: studentCountData.labels,
          datasets: [
            { label: 'ปีนี้', data: studentCountData.yearThis, backgroundColor: 'rgba(107, 72, 255, 0.7)' },
            { label: 'ปีที่แล้ว', data: studentCountData.yearLast, backgroundColor: 'rgba(0, 221, 235, 0.7)' }
          ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
      });

      // Average Grade Chart
      new Chart(document.getElementById('avgGradeChart').getContext('2d'), {
        type: 'line',
        data: {
          labels: avgGradeData.labels,
          datasets: [
            { label: 'ปีนี้', data: avgGradeData.yearThis, borderColor: 'rgba(107, 72, 255, 0.9)', backgroundColor: 'rgba(107, 72, 255, 0.2)', fill: true, tension: 0.3 },
            { label: 'ปีที่แล้ว', data: avgGradeData.yearLast, borderColor: 'rgba(0, 221, 235, 0.9)', backgroundColor: 'rgba(0, 221, 235, 0.2)', fill: true, tension: 0.3 }
          ]
        },
        options: { responsive: true, scales: { y: { min: 0, max: 4, ticks: { stepSize: 0.5 } } } }
      });

      // Student Distribution Chart
      new Chart(document.getElementById('studentDistributionChart').getContext('2d'), {
        type: 'pie',
        data: {
          labels: studentDistributionData.labels,
          datasets: [{ data: studentDistributionData.counts, backgroundColor: ['#6b48ff', '#00ddeb', '#ff9a9e', '#fad0c4', '#ffc107', '#ffca2c'] }]
        },
        options: { responsive: true }
      });

      // Monthly Attendance Chart
      new Chart(document.getElementById('monthlyAttendanceChart').getContext('2d'), {
        type: 'line',
        data: {
          labels: monthlyAttendanceData.labels,
          datasets: [{
            label: 'เปอร์เซ็นต์การเข้าเรียน',
            data: monthlyAttendanceData.attendancePercent,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            fill: true,
            tension: 0.3,
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { min: 80, max: 100, ticks: { stepSize: 5, callback: val => val + '%' } }
          }
        }
      });

      // Staff Department Chart
      new Chart(document.getElementById('staffDepartmentChart').getContext('2d'), {
        type: 'bar',
        data: {
          labels: staffByDeptData.labels,
          datasets: [{ label: 'จำนวน (คน)', data: staffByDeptData.counts, backgroundColor: ['#6b48ff', '#00ddeb', '#ff9a9e', '#ffc107', '#28a745'] }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
      });

      // Teacher Eval Distribution Chart
      new Chart(document.getElementById('teacherEvalDistributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: teacherEvalRatingData.labels,
          datasets: [{ data: teacherEvalRatingData.counts, backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545'] }]
        },
        options: { responsive: true }
      });
    }

    // เติมข้อมูลตารางรายชื่อนักเรียนขาดเรียนเกินเกณฑ์
    function loadHighAbsenceTable() {
      const tbody = document.getElementById('highAbsenceTableBody');
      highAbsenceStudents.forEach(student => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${student.id}</td><td>${student.name}</td><td>${student.class}</td><td>${student.daysAbsent} วัน</td>`;
        tbody.appendChild(tr);
      });
    }

    // Modal แสดงรายละเอียดตัวอย่าง
    function showAdminModal(content, title = 'รายละเอียด') {
      document.getElementById('adminModalLabel').textContent = title;
      document.getElementById('adminModalBody').innerHTML = content;
      new bootstrap.Modal(document.getElementById('adminModal')).show();
    }

    // ฟังก์ชันเมนูรายละเอียดต่าง ๆ
    function showStaffDetails() {
      const content = `
        <h5>รายชื่อครูและบุคลากร</h5>
        <ul>
          <li>นายสมชาย ใจดี - ครูคณิตศาสตร์</li>
          <li>นางสาวสมหญิง แก้วตา - ครูภาษาไทย</li>
          <li>นางสาวมาลี ใจงาม - ครูภาษาอังกฤษ</li>
          <li>...</li>
        </ul>
      `;
      showAdminModal(content, 'รายชื่อครูและบุคลากร');
    }

    function showAttendanceToday() {
      const content = `
        <h5>นักเรียนเข้าเรียนวันนี้</h5>
        <p>จำนวนนักเรียน: 1,120 คน จากทั้งหมด 1,200 คน</p>
        <p>ขาดเรียน: 80 คน</p>
      `;
      showAdminModal(content, 'สถานะการเข้าเรียนวันนี้');
    }

    function showTeacherEvalReport() {
      const content = `
        <h5>รายงานผลการประเมินครูเฉลี่ย</h5>
        <p>คะแนนเฉลี่ย: 4.3 / 5.0</p>
        <p>จำนวนครูที่ถูกประเมิน: 85 คน</p>
        <p>สรุป: ครูส่วนใหญ่ได้รับคะแนนดีมาก</p>
      `;
      showAdminModal(content, 'รายงานผลการประเมินครู');
    }

    function showSchoolExpenses() {
      const content = `
        <h5>สรุปค่าใช้จ่ายโรงเรียนเดือนนี้</h5>
        <ul>
          <li>ค่าวัสดุการเรียนการสอน: 450,000 บาท</li>
          <li>ค่าจ้างบุคลากรพิเศษ: 300,000 บาท</li>
          <li>ค่าไฟฟ้าและน้ำประปา: 100,000 บาท</li>
          <li>อื่นๆ: 400,000 บาท</li>
        </ul>
      `;
      showAdminModal(content, 'สรุปค่าใช้จ่ายโรงเรียน');
    }

    // เมนูหลักแสดงเนื้อหาอื่น ๆ
    function showDashboard() {
      document.getElementById('mainContent').style.display = 'block';
    }

    function showReports() {
      const content = `
        <h4>รายงานผลการประเมินครู</h4>
        <p>ข้อมูลและกราฟสรุปผลการประเมินต่าง ๆ ของครู...</p>
        <hr />
        <h4>รายงานผลการเรียนรวม</h4>
        <p>ข้อมูลและสถิติผลการเรียนรวมของนักเรียน...</p>
      `;
      showAdminModal(content, 'รายงาน');
    }

    function showSettings() {
      const content = `
        <form id="systemSettingsForm">
          <div class="mb-3">
            <label for="schoolYear" class="form-label">ปีการศึกษา</label>
            <input type="number" class="form-control" id="schoolYear" value="2568" min="2550" max="2700" />
          </div>
          <div class="mb-3">
            <label for="semester" class="form-label">ภาคเรียน</label>
            <select class="form-select" id="semester">
              <option value="1" selected>ภาคเรียนที่ 1</option>
              <option value="2">ภาคเรียนที่ 2</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">บันทึกการตั้งค่า</button>
        </form>
      `;
      showAdminModal(content, 'ตั้งค่าระบบ');

      document.getElementById('systemSettingsForm').addEventListener('submit', function (e) {
        e.preventDefault();
        alert('บันทึกการตั้งค่าระบบเรียบร้อยแล้ว');
        bootstrap.Modal.getInstance(document.getElementById('adminModal')).hide();
      });
    }

 
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

    // โปรไฟล์ผู้บริหาร
    function showProfile() {
      const content = `
        <h4>โปรไฟล์ผู้บริหาร</h4>
        <p>ชื่อ: นายสมชาย ใจดี</p>
        <p>ตำแหน่ง: ผู้บริหาร</p>
        <p>อีเมล: somchai@kswit.ac.th</p>
      `;
      showAdminModal(content, 'โปรไฟล์');
    }

    // ตั้งค่าบัญชีผู้บริหาร
    function showAccountSettings() {
      const content = `
        <form id="accountSettingsForm">
          <div class="mb-3">
            <label for="email" class="form-label">อีเมล</label>
            <input type="email" class="form-control" id="email" value="somchai@kswit.ac.th" required />
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="tel" class="form-control" id="phone" value="081-xxx-xxxx" required />
          </div>
          <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
        </form>
      `;
      showAdminModal(content, 'ตั้งค่าบัญชี');

      document.getElementById('accountSettingsForm').addEventListener('submit', function (e) {
        e.preventDefault();
        alert('บันทึกข้อมูลบัญชีเรียบร้อยแล้ว');
        bootstrap.Modal.getInstance(document.getElementById('adminModal')).hide();
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
        showAlert('ยินดีต้อนรับสู่แดชบอร์ดผู้บริหาร!', 'success');
    });
  </script>
</body>
</html>
