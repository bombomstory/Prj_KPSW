<?php
// document_status.php (เพิ่มกล่องดาวน์โหลดไฟล์ PDF และตรวจสอบบันทึกเวลาล็อกประวัติ)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php');
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;



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

// 1. ดึงข้อมูลคำขอหลัก (เพิ่มฟิลด์ตรวจสอบสิทธิ์ดาวน์โหลด)
$sql = "SELECT dr.*, dt.name AS doc_name 
        FROM `document_requests` dr
        INNER JOIN `document_types` dt ON dr.document_type_id = dt.id
        WHERE dr.id = ? AND dr.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$request) {
    die("<div class='container mt-5 alert alert-danger text-center'>ไม่พบข้อมูลคำขอเอกสารค่ะ</div>");
}

// [ดึงข้อมูลประวัติ Logs และประเมินผลเหมือนโค้ดชุดเดิมของพี่ทูล...]
$logs = [];
$log_sql = "SELECT * FROM `document_request_logs` WHERE `request_id` = ? ORDER BY `id` ASC";
$l_stmt = $conn->prepare($log_sql);
$l_stmt->bind_param("i", $request_id);
$l_stmt->execute();
$l_result = $l_stmt->get_result();
while ($row = $l_result->fetch_assoc()) { $logs[] = $row; }
$l_stmt->close();

$eval_sql = "SELECT rating FROM `document_request_evaluations` WHERE `request_id` = ?";
$ev_stmt = $conn->prepare($eval_sql);
$ev_stmt->bind_param("i", $request_id);
$ev_stmt->execute();
$eval_data = $ev_stmt->get_result()->fetch_assoc();
$ev_stmt->close();

// สร้างผังโครงสร้างแนวตรงขยับตามประวัติจริงด้วย Mermaid.js
$mermaid_code = 'graph LR' . PHP_EOL;
$mermaid_code .= '  Root["📝 ยื่นคำขอเรียบร้อย"]' . PHP_EOL;
$prev_node = 'Root';
foreach ($logs as $log) {
    $node_id = 'Node_' . $log['id'];
    $node_text = 'ผู้ดำเนินการ: ' . htmlspecialchars($log['actor_name']) . '<br>ขั้นตอน: ' . htmlspecialchars($log['action_type']);
    if ($log['action_type'] === 'เสร็จสิ้น') {
        $mermaid_code .= '  ' . $node_id . '(["🏁 รับเอกสารเสร็จสมบูรณ์"])' . PHP_EOL;
        $mermaid_code .= '  style ' . $node_id . ' fill:#d1e7dd,stroke:#0f5132,stroke-width:2px' . PHP_EOL;
    } else {
        $mermaid_code .= '  ' . $node_id . '["' . $node_text . '"]' . PHP_EOL;
    }
    $mermaid_code .= '  ' . $prev_node . ' --> ' . $node_id . PHP_EOL;
    $prev_node = $node_id;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ติดตามสถานะคำขอเอกสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body, button, span, p, h1, h2, h3, h4, h5, h6, div, table { font-family: 'Kanit', sans-serif !important; }
        .mermaid * { font-family: 'Kanit', sans-serif !important; }
        .flow-container { background-color: #ffffff; border: 1px solid #e3e6f0; border-radius: 0.5rem; overflow-x: auto; padding: 20px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>mermaid.initialize({ startOnLoad: true, theme: 'neutral', securityLevel: 'loose', fontFamily: 'Kanit' });</script>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>ข้อมูลคำขอเอกสาร</h5>
                </div>
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark"><?php echo htmlspecialchars($request['doc_name']); ?></h4>
                    <p class="text-secondary mb-0">วัตถุประสงค์: <?php echo htmlspecialchars($request['purpose']); ?> | จำนวน: <?php echo intval($request['qty']); ?> ชุด</p>
                </div>
            </div>

            <div class="card shadow border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-2 me-2"></i>เส้นทางการดำเนินงานจัดทำเอกสาร (Document Workflow)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="flow-container text-center"><div class="mermaid"><?php echo $mermaid_code; ?></div></div>
                </div>
            </div>

            <?php if ($request['status'] === 'เสร็จสิ้น'): ?>
                <div class="card shadow border-0 rounded-3 mb-4 border-start border-primary border-4 bg-white">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h5 class="fw-bold text-primary mb-1"><i class="bi bi-file-earmark-pdf-fill me-2"></i>เอกสารอิเล็กทรอนิกส์พร้อมดาวน์โหลด (E-Document)</h5>
                                <p class="text-secondary small mb-2">เจ้าหน้าที่งานทะเบียนได้อัปโหลดไฟล์ฉบับดิจิทัลเข้าระบบเรียบร้อยแล้วค่ะ</p>
                                
                                <div class="mt-2">
                                    <?php if ($request['is_downloaded'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill small">
                                            <i class="bi bi-check-circle-fill me-1"></i> เคยดาวน์โหลดแล้วเมื่อ: <?php echo date('d/m/Y H:i', strtotime($request['downloaded_at'])); ?> น.
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill small">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i> ยังไม่เคยดาวน์โหลดเอกสารชุดนี้
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <a href="download_document.php?id=<?php echo $request_id; ?>" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-cloud-arrow-down-fill me-2"></i>ดาวน์โหลดไฟล์ PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0 rounded-3 mb-4 border-start border-success border-4">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-success mb-1"><i class="bi bi-star-fill me-2"></i>ประเมินความพึงพอใจต่อการบริการ</h5>
                            <small class="text-muted">คำขอเอกสารนี้เสร็จสมบูรณ์แล้ว ช่วยประเมินให้คะแนนงานบริการของเจ้าหน้าที่หน่อยนะคะ</small>
                        </div>
                        <div>
                            <?php if (!$eval_data): ?>
                                <a href="evaluate_document.php?id=<?php echo $request_id; ?>" class="btn btn-warning fw-bold text-dark rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i> กดประเมินความพึงพอใจ
                                </a>
                            <?php else: ?>
                                <span class="badge bg-success px-3 py-2 rounded-pill fs-6">ประเมินแล้ว: <?php echo intval($eval_data['rating']); ?> / 5 ⭐</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-end">
                <a href="document_history.php" class="btn btn-secondary rounded-pill px-4">กลับไปหน้ารายการ</a>
            </div>

        </div>
    </div>
</div>
</body>
</html>