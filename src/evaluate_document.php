<?php
// evaluate_document.php (หน้าประเมินคะแนนเอกสาร - ฟอนต์ Kanit)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php');
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = intval($_SESSION['user_id']);

// ป้องกันการแอบเข้ามากรอกคำขอที่ยังไม่เสร็จ
$sql = "SELECT dr.*, dt.name AS doc_name FROM `document_requests` dr
        INNER JOIN `document_types` dt ON dr.document_type_id = dt.id
        WHERE dr.id = ? AND dr.user_id = ? AND dr.status = 'เสร็จสิ้น'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$req_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$req_data) {
    die("<div class='container mt-5 alert alert-danger text-center'>ไม่พบสิทธิ์การประเมินรายการคำขอนี้ค่ะ</div>");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประเมินความพึงพอใจการขอเอกสาร</title>
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
                    <i class="bi bi-star-fill me-2"></i> ประเมินความพึงพอใจการบริการงานเอกสาร
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <small class="text-muted d-block">เอกสารที่ท่านได้รับคือ:</small>
                        <strong class="text-primary fs-5"><?php echo htmlspecialchars($req_data['doc_name']); ?></strong>
                    </div>

                    <form action="save_document_evaluation.php" method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">

                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark d-block mb-4 text-center fs-5">ระดับความพึงพอใจต่อบริการนี้:</label>
                            <div class="d-flex justify-content-between px-1">
                                <?php 
                                $rating_labels = [5 => 'ดีเยี่ยม', 4 => 'ดีมาก', 3 => 'ปานกลาง', 2 => 'พอใช้', 1 => 'ควรปรับปรุง'];
                                for($i = 5; $i >= 1; $i--): 
                                ?>
                                    <div class="form-check form-check-inline text-center mx-0" style="width: 19%;">
                                        <input class="form-check-input float-none d-block mx-auto mb-3" type="radio" 
                                               name="rating" id="rate_<?php echo $i; ?>" value="<?php echo $i; ?>" 
                                               required <?php echo $i === 5 ? 'checked' : ''; ?> 
                                               style="width: 1.4em; height: 1.4em; cursor: pointer;">
                                        
                                        <label class="form-check-label d-block" for="rate_<?php echo $i; ?>" style="cursor: pointer;">
                                            <div class="text-warning mb-2" style="white-space: nowrap; font-size: 0.75rem;">
                                                <?php for($star = 1; $star <= $i; $star++): ?>
                                                    <i class="bi bi-star-fill"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="fw-bold d-block text-dark" style="font-size: 0.85rem;"><?php echo $i; ?> คะแนน</span>
                                            <span class="text-muted d-block" style="font-size: 0.7rem; white-space: nowrap;">(<?php echo $rating_labels[$i]; ?>)</span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">ข้อเสนอแนะเพิ่มเติม:</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="พิมพ์ข้อเสนอแนะเพิ่มเติมได้ค่ะ"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg rounded-3 fw-bold">ส่งผลคะแนนประเมิน</button>
                            <a href="document_status.php?id=<?php echo $request_id; ?>" class="btn btn-outline-secondary btn-sm border-0">กลับหน้าเดิม</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>