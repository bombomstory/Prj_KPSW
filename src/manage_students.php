<?php
session_start();
include('db_connection.php');

// 1. รับค่าจากฟอร์มค้นหา (ถ้ามี)
$search = trim($_GET['search'] ?? '');
$grade  = $_GET['grade'] ?? '';
$room   = $_GET['room'] ?? '';
$status = $_GET['status'] ?? '';

// 2. สร้างคำสั่ง SQL เริ่มต้น
$sql = "SELECT s.*, u.profile_picture, u.prefix_name, u.first_name, u.last_name, 
        (SELECT c.classroom_name FROM student_classrooms sc 
         JOIN classrooms c ON sc.classroom_id = c.classroom_id 
         WHERE sc.student_id = s.student_id 
         ORDER BY sc.academic_year DESC LIMIT 1) as current_room
        FROM students s 
        JOIN users u ON s.user_id = u.user_id 
        WHERE 1=1 ";

$params = [];
$types = "";

// 3. เพิ่มเงื่อนไข WHERE ตามสิ่งที่เลือกมากรอง
if ($search !== '') {
    $sql .= " AND (s.student_code LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?) ";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($grade !== '') {
    $sql .= " AND s.grade_level = ? ";
    $params[] = $grade;
    $types .= "s";
}

if ($status !== '') {
    $sql .= " AND s.enrollment_status = ? ";
    $params[] = $status;
    $types .= "s";
}

// 4. เงื่อนไขสำหรับห้องเรียน (ใช้ HAVING เพราะ current_room ถูกสร้างจำลองขึ้นมาใน SELECT)
if ($room !== '') {
    $sql .= " HAVING current_room LIKE ? ";
    $room_param = "%/{$room}";
    $params[] = $room_param;
    $types .= "s";
}

$sql .= " ORDER BY s.student_code ASC LIMIT 100"; 

// 5. ประมวลผลคำสั่ง SQL
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$students = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลนักเรียน - โรงเรียนกำแพงแสนวิทยา</title>
    <link rel="shortcut icon" href="images/favicon.svg" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f8fafc; }
        .navbar { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .page-header { background: white; padding: 2rem 0; margin-top: 60px; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .table-hover tbody tr:hover { background-color: #f1f5f9; transition: 0.2s; }
        .badge-status { font-weight: 500; padding: 0.5em 0.8em; border-radius: 8px; }
        .avatar-sm { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
        
        /* Custom Tabs for Modal */
        .nav-tabs .nav-link { color: #64748b; font-weight: 500; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #2563eb; border-bottom: 3px solid #2563eb; background: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="registarDashboard.php">
                <i class="bi bi-arrow-left-circle me-2"></i> กลับหน้าแดชบอร์ด
            </a>
        </div>
    </nav>

    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>ระบบจัดการข้อมูลนักเรียน</h2>
                    <p class="text-muted mb-0">ค้นหา เพิ่ม แก้ไข และติดตามสถานะนักเรียนทั้งหมด</p>
                </div>
                <button class="btn btn-primary btn-lg rounded-pill shadow-sm" onclick="showAddStudentModal()">
                    <i class="bi bi-person-plus-fill me-2"></i>ลงทะเบียนนักเรียนใหม่
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 py-4">
        
        <div class="card mb-4">
            <div class="card-body">
                <form action="manage_students.php" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted">ค้นหานักเรียน</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="ชื่อ, นามสกุล หรือ รหัส" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted">ระดับชั้น</label>
                        <select name="grade" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="ม.1" <?php if($grade == 'ม.1') echo 'selected'; ?>>ม.1</option>
                            <option value="ม.2" <?php if($grade == 'ม.2') echo 'selected'; ?>>ม.2</option>
                            <option value="ม.3" <?php if($grade == 'ม.3') echo 'selected'; ?>>ม.3</option>
                            <option value="ม.4" <?php if($grade == 'ม.4') echo 'selected'; ?>>ม.4</option>
                            <option value="ม.5" <?php if($grade == 'ม.5') echo 'selected'; ?>>ม.5</option>
                            <option value="ม.6" <?php if($grade == 'ม.6') echo 'selected'; ?>>ม.6</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted">ห้องเรียน</label>
                        <select name="room" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="1" <?php if($room == '1') echo 'selected'; ?>>ห้อง 1</option>
                            <option value="2" <?php if($room == '2') echo 'selected'; ?>>ห้อง 2</option>
                            <option value="3" <?php if($room == '3') echo 'selected'; ?>>ห้อง 3</option>
                            <option value="4" <?php if($room == '4') echo 'selected'; ?>>ห้อง 4</option>
                            <option value="5" <?php if($room == '5') echo 'selected'; ?>>ห้อง 5</option>
                            <option value="6" <?php if($room == '6') echo 'selected'; ?>>ห้อง 6</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="กำลังศึกษา" <?php if($status == 'กำลังศึกษา') echo 'selected'; ?>>กำลังศึกษา</option>
                            <option value="สำเร็จการศึกษา" <?php if($status == 'สำเร็จการศึกษา') echo 'selected'; ?>>สำเร็จการศึกษา</option>
                            <option value="ลาออก" <?php if($status == 'ลาออก') echo 'selected'; ?>>ลาออก</option>
                            <option value="พักการเรียน" <?php if($status == 'พักการเรียน') echo 'selected'; ?>>พักการเรียน</option>
                            <option value="ย้ายสถานศึกษา" <?php if($status == 'ย้ายสถานศึกษา') echo 'selected'; ?>>ย้ายสถานศึกษา</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1"><i class="bi bi-funnel me-1"></i> กรองข้อมูล</button>
                        <a href="manage_students.php" class="btn btn-light border" title="ล้างการค้นหา"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">รหัสนักเรียน</th>
                                <th class="py-3">ข้อมูลนักเรียน</th>
                                <th class="py-3 text-center">ระดับชั้น/ห้อง</th>
                                <th class="py-3 text-center">เกรดเฉลี่ย (GPAX)</th>
                                <th class="py-3 text-center">สถานะ</th>
                                <th class="px-4 py-3 text-end">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($students)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-search fs-1 d-block mb-3 text-secondary"></i> 
                                    <h5 class="fw-bold">ไม่พบข้อมูลนักเรียนที่ค้นหา</h5>
                                    <p>ลองปรับเปลี่ยนเงื่อนไขการค้นหาดูอีกครั้งนะคะ</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($students as $stu): ?>
                                <tr>
                                    <td class="px-4 fw-bold text-primary">#<?php echo htmlspecialchars($stu['student_code']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($stu['profile_picture'])): ?>
                                                <img src="images/<?php echo htmlspecialchars($stu['profile_picture']); ?>" class="avatar-sm me-3 shadow-sm border">
                                            <?php else: ?>
                                                <div class="avatar-sm me-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle text-primary border border-primary-subtle">
                                                    <i class="bi bi-person-fill fs-5"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars(($stu['prefix_name'] ?? '').($stu['first_name'] ?? '').' '.($stu['last_name'] ?? '')); ?>
                                                </div>
                                                <small class="text-muted">ID: <?php echo htmlspecialchars($stu['citizen_id'] ?? '-'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark bg-opacity-25 border border-info px-2 py-1">
                                            <?php echo htmlspecialchars($stu['current_room'] ?? $stu['grade_level'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        <?php echo number_format($stu['gpax'] ?? 0, 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $status_val = $stu['enrollment_status'] ?? 'กำลังศึกษา';
                                            $badgeClass = 'bg-success';
                                            if($status_val == 'สำเร็จการศึกษา') $badgeClass = 'bg-primary';
                                            if($status_val == 'ลาออก' || $status_val == 'ย้ายสถานศึกษา') $badgeClass = 'bg-danger';
                                            if($status_val == 'พักการเรียน') $badgeClass = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> badge-status"><?php echo htmlspecialchars($status_val); ?></span>
                                    </td>
                                    <td class="px-4 text-end">
                                        <button class="btn btn-sm btn-light border text-primary me-1" onclick="viewStudent(<?php echo $stu['student_id']; ?>)" title="ดูรายละเอียด">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border text-warning me-1" onclick="editStudent(<?php echo $stu['student_id']; ?>)" title="แก้ไขข้อมูล">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border text-danger" title="เปลี่ยนสถานะ">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-between align-items-center">
                <small class="text-muted">แสดงผลลัพธ์ทั้งหมด <span class="fw-bold text-dark"><?php echo count($students); ?></span> รายการ</small>
            </div>
        </div>
    </div>

    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="studentModalTitle">ลงทะเบียนนักเรียนใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-4" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">1. ข้อมูลส่วนตัว</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab">2. ข้อมูลการเรียน</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="parent-tab" data-bs-toggle="tab" data-bs-target="#parent" type="button" role="tab">3. ข้อมูลผู้ปกครอง</button>
                        </li>
                    </ul>

                    <form id="studentForm">
                        <input type="hidden" name="student_id" id="edit_student_id" value="">

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">เลขประจำตัวนักเรียน *</label>
                                        <input type="text" class="form-control" name="student_code" id="form_student_code" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold">เลขประจำตัวประชาชน (13 หลัก)</label>
                                        <input type="text" class="form-control" name="citizen_id" id="form_citizen_id" maxlength="13">
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">คำนำหน้า</label>
                                        <select class="form-select" name="prefix_name" id="form_prefix_name">
                                            <option value="ด.ช.">ด.ช.</option>
                                            <option value="ด.ญ.">ด.ญ.</option>
                                            <option value="นาย">นาย</option>
                                            <option value="นางสาว">นางสาว</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">ชื่อ *</label>
                                        <input type="text" class="form-control" name="first_name" id="form_first_name" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold">นามสกุล *</label>
                                        <input type="text" class="form-control" name="last_name" id="form_last_name" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">วันเดือนปีเกิด</label>
                                        <input type="date" class="form-control" name="birth_date" id="form_birth_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">กรุ๊ปเลือด</label>
                                        <select class="form-select" name="blood_type" id="form_blood_type">
                                            <option value="">เลือกกรุ๊ปเลือด</option>
                                            <option value="A">A</option><option value="B">B</option>
                                            <option value="AB">AB</option><option value="O">O</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">ที่อยู่ปัจจุบัน</label>
                                        <textarea class="form-control" name="address" id="form_address" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="academic" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ระดับชั้นที่เข้าศึกษา *</label>
                                        <select class="form-select" name="grade_level" id="form_grade_level" required>
                                            <option value="ม.1">มัธยมศึกษาปีที่ 1</option>
                                            <option value="ม.2">มัธยมศึกษาปีที่ 2</option>
                                            <option value="ม.3">มัธยมศึกษาปีที่ 3</option>
                                            <option value="ม.4">มัธยมศึกษาปีที่ 4</option>
                                            <option value="ม.5">มัธยมศึกษาปีที่ 5</option>
                                            <option value="ม.6">มัธยมศึกษาปีที่ 6</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ห้องเรียนปัจจุบัน</label>
                                        <select class="form-select" name="classroom_id" id="form_classroom_id">
                                            <option value="">-- ไม่ระบุ / จัดการภายหลัง --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">สถานะนักเรียน</label>
                                        <select class="form-select bg-light" name="enrollment_status" id="form_enrollment_status">
                                            <option value="กำลังศึกษา">กำลังศึกษา (Active)</option>
                                            <option value="สำเร็จการศึกษา">สำเร็จการศึกษา</option>
                                            <option value="ลาออก">ลาออก</option>
                                            <option value="พักการเรียน">พักการเรียน</option>
                                            <option value="ย้ายสถานศึกษา">ย้ายสถานศึกษา</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="parent" role="tabpanel">
                                <div class="alert alert-warning py-2 mb-3">
                                    <i class="bi bi-info-circle-fill me-1"></i> สามารถกรอกภายหลังได้
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ชื่อ-สกุล ผู้ปกครอง</label>
                                        <input type="text" class="form-control" name="parent_name" id="form_parent_name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ความสัมพันธ์</label>
                                        <select class="form-select" name="parent_relation" id="form_parent_relation">
                                            <option value="บิดา">บิดา</option>
                                            <option value="มารดา">มารดา</option>
                                            <option value="ญาติ">ญาติ (ปู่/ย่า/ตา/ยาย/ลุง/ป้า/น้า/อา)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อด่วน</label>
                                        <input type="text" class="form-control" name="parent_phone" id="form_parent_phone" maxlength="10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveStudent()">บันทึกข้อมูล</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const studentModal = new bootstrap.Modal(document.getElementById('studentModal'));
        
        // ส่งต่อข้อมูลนักเรียนจาก PHP มายัง JavaScript แบบ JSON เพื่อให้ค้นหาได้ทันที
        const studentsData = <?php echo json_encode($students, JSON_UNESCAPED_UNICODE); ?>;

        function showAddStudentModal() {
            document.getElementById('studentForm').reset();
            document.getElementById('edit_student_id').value = ''; // เคลียร์ ID
            document.getElementById('studentModalTitle').innerText = 'ลงทะเบียนนักเรียนใหม่';
            
            // กลับไปที่ Tab 1 เสมอเวลาเปิดฟอร์ม
            const firstTab = new bootstrap.Tab(document.getElementById('personal-tab'));
            firstTab.show();
            studentModal.show();
        }

        function editStudent(id) {
            // ค้นหาข้อมูลนักเรียนจาก array JSON 
            const student = studentsData.find(s => s.student_id == id);
            
            if(student) {
                document.getElementById('studentModalTitle').innerText = 'แก้ไขข้อมูลนักเรียน';
                
                // นำข้อมูลลงฟอร์ม
                document.getElementById('edit_student_id').value = student.student_id;
                document.getElementById('form_student_code').value = student.student_code || '';
                document.getElementById('form_citizen_id').value = student.citizen_id || '';
                document.getElementById('form_prefix_name').value = student.prefix_name || 'ด.ช.';
                document.getElementById('form_first_name').value = student.first_name || '';
                document.getElementById('form_last_name').value = student.last_name || '';
                document.getElementById('form_birth_date').value = student.birth_date || '';
                document.getElementById('form_blood_type').value = student.blood_type || '';
                document.getElementById('form_address').value = student.address || '';
                document.getElementById('form_grade_level').value = student.grade_level || 'ม.1';
                
                // สำหรับ status ถ้าเป็น null ให้ค่าตั้งต้นเป็น 'กำลังศึกษา'
                document.getElementById('form_enrollment_status').value = student.enrollment_status || 'กำลังศึกษา';
                
                // ในส่วนผู้ปกครองจะปล่อยว่างไว้ก่อน หรือถ้ามีข้อมูลใน array ก็สามารถดึงมาใส่ได้เช่นกัน
                document.getElementById('form_parent_name').value = student.parent_name || '';
                document.getElementById('form_parent_relation').value = student.parent_relation || 'บิดา';
                document.getElementById('form_parent_phone').value = student.parent_phone || '';

                // เลื่อนให้กลับมาแสดงที่ Tab แรกเสมอ
                const firstTab = new bootstrap.Tab(document.getElementById('personal-tab'));
                firstTab.show();
                
                studentModal.show();
            } else {
                alert("เกิดข้อผิดพลาด ไม่พบข้อมูลนักเรียนคนนี้");
            }
        }

        function viewStudent(id) {
            // TODO: สร้างหน้ารายละเอียดแยก (Profile) หรือ Modal โชว์ข้อมูลแบบ Read-only
            alert("กำลังเปิดดูประวัติและผลการเรียนของนักเรียน ID: " + id);
        }

        function saveStudent() {
            const form = document.getElementById('studentForm');
            if(!form.reportValidity()) {
                return;
            }
            
            // เช็คว่าเป็นการ Add ใหม่หรือ Update โดยดูจากค่า edit_student_id
            const studentId = document.getElementById('edit_student_id').value;
            const modeText = studentId ? 'แก้ไขข้อมูลนักเรียน' : 'ลงทะเบียนนักเรียนใหม่';

            // TODO: ส่งข้อมูลไปเซฟที่หลังบ้าน (เช่น api_save_student.php)
            alert('จำลองการ' + modeText + 'สำเร็จ! \n(ระบบจะส่งไปบันทึกที่ฐานข้อมูลในขั้นต่อไป)');
            studentModal.hide();
        }
    </script>
</body>
</html>