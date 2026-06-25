<?php
// evaluate_complaint.php (หน้าจอกรอกคะแนนประเมินความพึงพอใจ - ฟอนต์ Kanit)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php');

$log_id = isset($_GET['log_id']) ? intval($_GET['log_id']) : 0;
$complaint_id = isset($_GET['complaint_id']) ? intval($_GET['complaint_id']) : 0;
$user_id = intval($_SESSION['user_id']);

// ตรวจสอบข้อมูลความถูกต้องของกิ่งและการล็อกอินป้องกันสิทธิ์ซ้ำซ้อน
$sql = "SELECT l.* FROM `complaint_workflow_logs` l
        INNER JOIN `complaints` c ON l.complaint_id = c.id
        WHERE l.id = ? AND c.user_id = ? AND l.action_type = 'ช่วยเหลือเสร็จสิ้น'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $log_id, $user_id);
$stmt->execute();
$log_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$log_data) {
    die("<div class='container mt-5 alert alert-danger text-center'>ไม่พบรายการประวัติที่ตรงตามเงื่อนไข หรือคุณได้สิทธิ์ประเมินกิ่งนี้ไปแล้วค่ะ</div>");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประเมินความพึงพอใจ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body, button, input, select, textarea, span, h1, h2, h3, h4, h5, h6, label {
            font-family: 'Kanit', sans-serif !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-warning text-dark py-3 fw-bold fs-5">
                    <i class="bi bi-star-fill me-2"></i> แบบประเมินความพึงพอใจต่อการช่วยเหลือ
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <span class="text-muted d-block small">หน่วยงาน/ผู้ดำเนินงานส่วนนี้:</span>
                        <strong class="text-primary fs-5"><?php echo htmlspecialchars($log_data['actor_name'] ?? 'เจ้าหน้าที่'); ?></strong>
                        <small class="text-secondary d-block mt-1">สรุปการช่วยเหลือย่อย: <?php echo htmlspecialchars($log_data['notes']); ?></small>
                    </div>

                    <form action="save_evaluation.php" method="POST">
                        <input type="hidden" name="log_id" value="<?php echo $log_id; ?>">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">

                        <!-- การเลือกคะแนนความพึงพอใจ แบบไอคอนดาวและคำอธิบายครบทุกระดับ -->
                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark d-block mb-4 text-center fs-5">ระดับความพึงพอใจต่อการช่วยเหลือส่วนนี้:</label>
                            <div class="d-flex justify-content-between px-1">
                                <?php 
                                $rating_labels = [
                                    5 => 'ดีเยี่ยม',
                                    4 => 'ดีมาก',
                                    3 => 'ปานกลาง',
                                    2 => 'พอใช้',
                                    1 => 'ควรปรับปรุง'
                                ];

                                for($i = 5; $i >= 1; $i--): 
                                ?>
                                    <div class="form-check form-check-inline text-center mx-0" style="width: 19%;">
                                        <input class="form-check-input float-none d-block mx-auto mb-3" type="radio" 
                                            name="rating" id="rate_<?php echo $i; ?>" value="<?php echo $i; ?>" 
                                            required <?php echo $i === 5 ? 'checked' : ''; ?> 
                                            style="width: 1.4em; height: 1.4em; cursor: pointer;">
                                        
                                        <label class="form-check-label d-block" for="rate_<?php echo $i; ?>" style="cursor: pointer;">
                                            <!-- 🌟 1. เพิ่ม white-space: nowrap และปรับขนาดดาวเพื่อไม่ให้ตกบรรทัด -->
                                            <div class="text-warning mb-2" style="white-space: nowrap; font-size: 0.75rem;">
                                                <?php for($star = 1; $star <= $i; $star++): ?>
                                                    <i class="bi bi-star-fill"></i>
                                                <?php endfor; ?>
                                            </div>
                                            
                                            <!-- 🌟 2. ส่วนแสดงคำอธิบาย -->
                                            <span class="fw-bold d-block text-dark" style="font-size: 0.85rem;">
                                                <?php echo $i; ?> คะแนน
                                            </span>
                                            <span class="text-muted d-block" style="font-size: 0.7rem; white-space: nowrap;">
                                                (<?php echo $rating_labels[$i]; ?>)
                                            </span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">ข้อเสนอแนะหรือความคิดเห็นเพิ่มเติม (ถ้ามี):</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="นักเรียนสามารถพิมพ์ข้อเสนอแนะเพิ่มเติมเพื่อการปรับปรุงระบบงานช่วยเหลือในอนาคตได้ค่ะ"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg rounded-3 fw-bold">
                                <i class="bi bi-cloud-arrow-up me-2"></i>ส่งผลคะแนนประเมิน
                            </button>
                            <a href="complaint_status.php?id=<?php echo $complaint_id; ?>" class="btn btn-outline-secondary btn-sm border-0">ยกเลิกและกลับหน้าเดิม</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>