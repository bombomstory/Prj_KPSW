<?php
// complaint_action.php (หน้าจัดการสำหรับคุณครูและบุคลากรในการเลือกจัดการ Work Flow)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php');

$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$current_teacher_id = intval($_SESSION['user_id']); // สมมติรหัสเซสชันครูที่ล็อกอินเข้ามา (1-24)

// 1. ดึงรายละเอียดข้อร้องเรียน
$sql = "SELECT c.*, ct.name AS type_name 
        FROM `complaints` c
        INNER JOIN `complaint_types` ct ON c.complaint_type_id = ct.id
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$complaint) {
    die("<div class='container mt-5 alert alert-danger text-center'>ไม่พบข้อมูลรายการร้องเรียนค่ะ</div>");
}

// 2. ดึงรายชื่อคุณครูแยกตามฝ่าย (สำหรับบุคคลภายในโรงเรียน)
$teachers_array = [];
$t_sql = "SELECT t.id, t.name, d.name AS dept_name 
          FROM `teachers` t
          INNER JOIN `departments` d ON t.department_id = d.id
          WHERE t.id != ? 
          ORDER BY d.id ASC, t.name ASC";
$t_stmt = $conn->prepare($t_sql);
$t_stmt->bind_param("i", $current_teacher_id);
$t_stmt->execute();
$t_result = $t_stmt->get_result();
while ($row = $t_result->fetch_assoc()) {
    $teachers_array[$row['dept_name']][] = $row;
}
$t_stmt->close();

// 3. ดึงรายชื่อหน่วยงานภายนอกทั้งหมด
$agencies = [];
$ag_result = $conn->query("SELECT * FROM `external_agencies` ORDER BY id ASC");
if ($ag_result) {
    while ($row = $ag_result->fetch_assoc()) {
        $agencies[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสถานะและส่งต่อความช่วยเหลือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea, span, h1, h2, h3, h4, h5, h6, label {
            font-family: 'Kanit', sans-serif !important;
        }
        .step-card { border-left: 4px solid #0d6efd; }
    </style>
    <style>
        /* 🌟 บังคับฟอนต์ Kanit สำหรับทุกองค์ประกอบ รวมถึง Option ใน Dropdown */
        body, button, input, select, textarea, span, h1, h2, h3, h4, h5, h6, label, option {
            font-family: 'Kanit', sans-serif !important;
        }
        /* ปรับขนาดฟอนต์ใน Select ให้พอดีกับข้อมูลติดต่อที่ยาวขึ้น */
        .form-select option {
            font-size: 0.95rem;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>รายละเอียดเรื่องร้องเรียนจากนักเรียน</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-1 text-muted">ประเภท: <span class="badge bg-info text-dark"><?php echo htmlspecialchars($complaint['type_name']); ?></span></p>
                    <h4 class="fw-bold text-primary"><?php echo htmlspecialchars($complaint['subject']); ?></h4>
                    <p class="text-secondary p-3 bg-light rounded-3 mt-2" style="white-space: pre-line;"><?php echo htmlspecialchars($complaint['detail']); ?></p>
                    <hr>
                    <p class="mb-0 small text-muted">สถานะปัจจุบันในระบบ: <span class="badge bg-secondary"><?php echo htmlspecialchars($complaint['status']); ?></span></p>
                </div>
            </div>

            <div class="card shadow border-0 rounded-3 step-card">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-gear-wide-connected me-2"></i>ส่วนการดำเนินการจัดการช่วยเหลือและส่งต่อเรื่อง</h5>
                </div>
                <div class="card-body p-4">
                    <form action="process_workflow.php" method="POST">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-5 text-dark">กรุณาเลือกรูปแบบการช่วยเหลือและจัดการ:</label>
                            
                            <div class="form-check card p-3 mb-2 border-light shadow-sm">
                                <input class="form-check-input ms-0 me-2" type="radio" name="workflow_option" id="opt_resolve" value="resolve" checked onclick="toggleWorkflowSections()">
                                <label class="form-check-label fw-bold text-success" for="opt_resolve">
                                    <i class="bi bi-check-circle-fill me-1"></i> [Step 2.1 / Step 3] ให้ความช่วยเหลือเสร็จสิ้น (ยุติเรื่องด้วยตนเอง)
                                </label>
                                <small class="text-muted d-block ps-4">เลือกข้อนี้เมื่อท่านสามารถให้คำปรึกษา แนะนำ หรือแก้ไขปัญหาสำเร็จเรียบร้อยแล้วค่ะ</small>
                            </div>

                            <div class="form-check card p-3 mb-2 border-light shadow-sm">
                                <input class="form-check-input ms-0 me-2" type="radio" name="workflow_option" id="opt_internal" value="forward_internal" onclick="toggleWorkflowSections()">
                                <label class="form-check-label fw-bold text-primary" for="opt_internal">
                                    <i class="bi bi-person-fill-up me-1"></i> [Step 2.2 / Step 3] ส่งต่อเรื่องไปยังบุคลากรภายในโรงเรียน (แยกตามฝ่าย)
                                </label>
                                <small class="text-muted d-block ps-4">ส่งต่อไปยังครูหรืออาจารย์ท่านอื่นที่มีส่วนเกี่ยวข้องโดยตรง</small>
                            </div>

                            <div class="form-check card p-3 mb-2 border-light shadow-sm">
                                <input class="form-check-input ms-0 me-2" type="radio" name="workflow_option" id="opt_external" value="forward_external" onclick="toggleWorkflowSections()">
                                <label class="form-check-label fw-bold text-warning-emphasis" for="opt_external">
                                    <i class="bi bi-building-fill-up me-1"></i> [Step 2.2 / Step 3] ส่งต่อเรื่องไปยังหน่วยงานภายนอกโรงเรียน
                                </label>
                                <small class="text-muted d-block ps-4">ประสานงานส่งต่อไปยังองค์กร หน่วยงานปกครอง หรือสาธารณสุขภายนอก</small>
                            </div>
                        </div>

                        <div id="sec_internal" class="mb-4 p-3 bg-light rounded-3 border" style="display:none;">
                            <label for="target_teacher" class="form-label fw-bold text-primary">เลือกบุคลากรภายในที่ต้องการส่งต่อเรื่อง:</label>
                            <select name="target_teacher_id" id="target_teacher" class="form-select">
                                <option value="">--- กรุณาเลือกบุคลากรภายใน ---</option>
                                <?php foreach ($teachers_array as $dept_name => $teachers): ?>
                                    <optgroup label="📂 ฝ่าย: <?php echo htmlspecialchars($dept_name); ?>">
                                        <?php foreach ($teachers as $t): ?>
                                            <option value="<?php echo $t['id']; ?>">คุณครู <?php echo htmlspecialchars($t['name']); ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 🏥 ส่วนย่อยสำหรับ: เลือกหน่วยงานภายนอก (ฉบับแสดงช่องทางติดต่อ) -->
                        <div id="sec_external" class="mb-4 p-3 bg-light rounded-3 border" style="display:none;">
                            <label for="target_agency" class="form-label fw-bold text-warning-emphasis">เลือกหน่วยงานภายนอกที่ต้องการส่งต่อเรื่อง:</label>
                            <select name="target_agency_id" id="target_agency" class="form-select">
                                <option value="">--- กรุณาเลือกหน่วยงานภายนอก ---</option>
                                <?php foreach ($agencies as $ag): ?>
                                    <option value="<?php echo $ag['id']; ?>">
                                        🏢 <?php echo htmlspecialchars($ag['name']); ?> 
                                        <?php 
                                            // 🌟 แสดงข้อมูลติดต่อในวงเล็บถ้ามีข้อมูลค่ะ
                                            $contacts = array_filter([$ag['phone'], $ag['social_contact']]);
                                            if (!empty($contacts)) {
                                                echo " (" . htmlspecialchars(implode(' | ', $contacts)) . ")";
                                            }
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> ระบบจะแสดงเบอร์โทรและช่องทางโซเชียลเพื่อให้ท่านประสานงานเบื้องต้นได้ทันทีค่ะ</div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-dark">บันทึกรายละเอียดเหตุผลความช่วยเหลือ / หมายเหตุการส่งต่อเรื่อง:</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="กรุณาระบุรายละเอียดการให้คำปรึกษา หรือเหตุผลที่ต้องดำเนินการส่งต่อเรื่องเพื่อให้ผู้รับไม้ต่อไปพิจารณาต่อค่ะ" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold">
                                <i class="bi bi-save me-2"></i>บันทึกผลการดำเนินงานและอัปเดตสถานะ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function toggleWorkflowSections() {
    const optResolve = document.getElementById('opt_resolve').checked;
    const optInternal = document.getElementById('opt_internal').checked;
    const optExternal = document.getElementById('opt_external').checked;

    document.getElementById('sec_internal').style.display = optInternal ? 'block' : 'none';
    document.getElementById('sec_external').style.display = optExternal ? 'block' : 'none';
    
    // ตั้งค่า required บังคับเลือกเมื่อแสดงผล
    document.getElementById('target_teacher').required = optInternal;
    document.getElementById('target_agency').required = optExternal;
}
</script>
</body>
</html>