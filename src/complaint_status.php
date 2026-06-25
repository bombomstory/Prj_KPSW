<?php
// complaint_status.php (ฉบับเพิ่มระบบลิงก์ประเมินความพึงพอใจแยกตาม Branch)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php'); // เรียกใช้ $conn (mysqli)

$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 🔐 1. ตรวจสอบสิทธิ์ผู้ปกครอง
if ($_SESSION['user_type'] == 'parent') {

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

}

if(!isset($student_id)){
    $student_user_id = intval($_SESSION['user_id']);
    $student_id = getStudentID($student_user_id);
}


$user_id = intval($student_id);

// 1. ดึงข้อมูลเรื่องร้องเรียนหลัก
$sql = "SELECT c.*, ct.name AS type_name 
        FROM `complaints` c
        INNER JOIN `complaint_types` ct ON c.complaint_type_id = ct.id
        WHERE c.id = ? AND c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $complaint_id, $user_id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$complaint) {
    die("<div class='container mt-5 alert alert-danger text-center'>ไม่พบข้อมูลรายการ หรือคุณไม่มีสิทธิ์เข้าถึงข้อมูลชุดนี้ค่ะ</div>");
}

// 2. ดึงประวัติ Logs ทั้งหมดมาวาดผังโครงสร้างแตกกิ่ง
$logs = [];
$log_sql = "SELECT * FROM `complaint_workflow_logs` WHERE `complaint_id` = ? ORDER BY `id` ASC"; 
$l_stmt = $conn->prepare($log_sql);
$l_stmt->bind_param("i", $complaint_id);
$l_stmt->execute();
$l_result = $l_stmt->get_result();
while ($row = $l_result->fetch_assoc()) {
    $logs[] = $row;
}
$l_stmt->close();

// 3. 🧠 ลอจิกเจนเนอเรตโค้ดส่งให้ Mermaid.js
$mermaid_code = 'graph LR' . PHP_EOL;
$mermaid_code .= '  Root["นักเรียนแจ้งเรื่อง<br><small>สถานะ: รอพิจารณา</small>"]' . PHP_EOL;

foreach ($logs as $log) {
    $node_id = 'Node_' . $log['id'];
    $actor = htmlspecialchars($log['actor_name'] ?? 'เจ้าหน้าที่ระบบ');
    $action = htmlspecialchars($log['action_type']);
    $forwarded = htmlspecialchars($log['forwarded_to_name'] ?? '');
    
    $node_text = 'ผู้ดำเนินการ: ' . $actor . '<br>การปฏิบัติงาน: ' . $action;
    if (!empty($forwarded) && $action !== 'ช่วยเหลือเสร็จสิ้น') {
        $node_text .= '<br>ส่งต่อไปยัง: ' . $forwarded;
    }

    if ($log['action_type'] === 'ช่วยเหลือเสร็จสิ้น') {
        $mermaid_code .= '  ' . $node_id . '(["ดำเนินการเสร็จสิ้น<br><small>โดย: ' . $actor . '</small>"])' . PHP_EOL;
        $mermaid_code .= '  style ' . $node_id . ' fill:#d1e7dd,stroke:#0f5132,stroke-width:2px' . PHP_EOL;
    } else {
        $mermaid_code .= '  ' . $node_id . '["' . $node_text . '"]' . PHP_EOL;
    }

    if (empty($log['parent_log_id']) || $log['parent_log_id'] == 0) {
        $mermaid_code .= '  Root --> ' . $node_id . PHP_EOL;
    } else {
        $parent_node = 'Node_' . $log['parent_log_id'];
        $mermaid_code .= '  ' . $parent_node . ' --> ' . $node_id . PHP_EOL;
    }
}

// 4. 🌟 [เพิ่มใหม่] ดึงข้อมูลเฉพาะ Branch ที่ยุติเรื่องเสร็จสิ้นแล้ว เพื่อนำมาทำปุ่มประเมินความพึงพอใจ
$completed_branches = [];
$comp_sql = "SELECT l.*, e.rating 
             FROM `complaint_workflow_logs` l
             LEFT JOIN `complaint_evaluations` e ON l.id = e.log_id
             WHERE l.complaint_id = ? AND l.action_type = 'ช่วยเหลือเสร็จสิ้น'
             ORDER BY l.id ASC";
$comp_stmt = $conn->prepare($comp_sql);
$comp_stmt->bind_param("i", $complaint_id);
$comp_stmt->execute();
$comp_result = $comp_stmt->get_result();
while ($row = $comp_result->fetch_assoc()) {
    $completed_branches[] = $row;
}
$comp_stmt->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ติดตามสถานะและ Flow การช่วยเหลือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body, button, span, p, h1, h2, h3, h4, h5, h6, div, table, label {
            font-family: 'Kanit', sans-serif !important;
        }
        .mermaid * { font-family: 'Kanit', sans-serif !important; }
        .flow-container { background-color: #ffffff; border: 1px solid #e3e6f0; border-radius: 0.5rem; overflow-x: auto; padding: 20px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({ startOnLoad: true, theme: 'neutral', securityLevel: 'loose', fontFamily: 'Kanit' });
    </script>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-activity me-2"></i>สถานะข้อร้องเรียนปัจจุบัน</h5>
                    <span class="badge bg-light text-primary px-3 py-2 fs-6">รวมสถานะล่าสุด: <?php echo htmlspecialchars($complaint['status']); ?></span>
                </div>
                <div class="card-body p-4">
                    <small class="text-muted d-block">หัวข้อเรื่องที่แจ้ง:</small>
                    <h4 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($complaint['subject']); ?></h4>
                    <p class="text-secondary bg-light p-3 rounded-3" style="white-space: pre-line;"><?php echo htmlspecialchars($complaint['detail']); ?></p>
                </div>
            </div>

            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3 me-2"></i>ผังเส้นทางการดำเนินงานและการส่งต่อช่วยเหลือ (Workflow Tree)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="flow-container text-center">
                        <div class="mermaid"><?php echo $mermaid_code; ?></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($completed_branches)): ?>
                <div class="card shadow border-0 rounded-3 mb-4 border-start border-success border-4">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-star-half me-2"></i>ประเมินความพึงพอใจต่อการช่วยเหลือ</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small">ระบบตรวจพบว่ามีสายงานการช่วยเหลือที่เสร็จสิ้นภารกิจแล้ว ดังรายการต่อไปนี้ รบกวนนักเรียนช่วยกดให้คะแนนความพึงพอใจเพื่อนำไปพัฒนาสถาบันต่อไปค่ะ</p>
                        <div class="row g-3">
                            <?php foreach ($completed_branches as $br): ?>
                                <div class="col-100">
                                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                                        <div>
                                            <strong class="text-dark d-block">🎯 ผู้สิ้นสุดการดำเนินงาน: <?php echo htmlspecialchars($br['actor_name'] ?? 'เจ้าหน้าที่'); ?></strong>
                                            <small class="text-secondary">บันทึกความช่วยเหลือล่าสุด: <?php echo htmlspecialchars($br['notes']); ?></small>
                                        </div>
                                        <div>
                                            <?php if (empty($br['rating'])): ?>
                                                <a href="evaluate_complaint.php?log_id=<?php echo $br['id']; ?>&complaint_id=<?php echo $complaint_id; ?>" class="btn btn-warning btn-sm fw-bold px-3 text-dark rounded-pill shadow-sm">
                                                    <i class="bi bi-pencil-square me-1"></i> กดประเมินความพึงพอใจ
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
                                                    คะแนนที่ให้: <?php echo intval($br['rating']); ?> / 5 ⭐
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-end">
                <a href="complaint_history.php" class="btn btn-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i>กลับไปหน้ารายการประวัติ
                </a>
            </div>

        </div>
    </div>
</div>
</body>
</html>