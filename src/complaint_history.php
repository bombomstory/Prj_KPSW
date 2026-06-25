<?php
// complaint_history.php (ฉบับแก้ไขเพิ่ม Type Hinting ปิด Error Linter)
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('db_connection.php'); 

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

$sql = "SELECT `id`, `complaint_type_id`, `subject`, `status`, `created_at` 
        FROM `complaints` 
        WHERE `user_id` = ? 
        ORDER BY `created_at` DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

/**
 * 🌟 เพิ่ม Type Hinting (int) และระบุประเภทการ Return ค่าออกไป (: string)
 */
function getComplaintTypeName(int $type_id): string {
    switch ($type_id) {
        case 1: return 'ร้องเรียน';
        case 2: return 'ข้อเสนอแนะ';
        case 3: return 'แจ้งปัญหา';
        case 4: return 'ขอความช่วยเหลือ';
        default: return 'ทั่วไป';
    }
}

/**
 * 🌟 เพิ่ม Type Hinting (string) และระบุประเภทการ Return ค่าออกไป (: string)
 */
function getStatusBadgeClass(string $status): string {
    switch ($status) {
        case 'รอพิจารณา': return 'bg-secondary';
        case 'กำลังดำเนินการ': return 'bg-warning text-dark';
        case 'ดำเนินการเสร็จสิ้น': return 'bg-success';
        default: return 'bg-danger';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการแจ้งเหตุและร้องเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body, button, input, select, textarea, table, span, h1, h2, h3, h4, h5, h6 {
            font-family: 'Kanit', sans-serif !important;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>ประวัติการแจ้งเหตุ / ร้องเรียน</h3>
                    <p class="text-muted mb-0">ติดตามสถานะและตรวจสอบรายการเรื่องร้องเรียนทั้งหมดของคุณ</p>
                </div>
                <?php
                if ($_SESSION['user_type'] == 'parent') {
                ?>
                <a href="parentDashboard.php" class="btn btn-outline-secondary">
                <?php
                }else{
                ?>
                <a href="studentDashboard.php" class="btn btn-outline-secondary">
                <?php
                }
                ?>
                    <i class="bi bi-arrow-left me-1"></i>กลับสู่แดชบอร์ด
                </a>
            </div>

            <div class="card shadow border-0 rounded-3">
                <div class="card-body p-0">
                    
                    <?php if ($result->num_rows === 0): ?>
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-chat-left-text mb-3 text-secondary" style="font-size: 3.5rem;"></i>
                            <h5 class="fw-bold">ไม่พบประวัติการแจ้งเรื่อง</h5>
                            <p class="small mb-0">คุณยังไม่เคยส่งเรื่องแจ้งเหตุหรือข้อร้องเรียนเข้ามาในระบบค่ะ</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-primary text-nowrap">
                                    <tr>
                                        <th class="ps-4" style="width: 15%;">วันที่แจ้ง</th>
                                        <th style="width: 15%;">ประเภท</th>
                                        <th>หัวข้อเรื่อง</th>
                                        <th class="text-center" style="width: 15%;">สถานะปัจจุบัน</th>
                                        <th class="text-center pe-4" style="width: 15%;">การจัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <small class="text-dark">
                                                    <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo getComplaintTypeName(intval($row['complaint_type_id'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-truncate" style="max-width: 350px;">
                                                    <?php echo htmlspecialchars($row['subject']); ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo getStatusBadgeClass($row['status']); ?> px-2 py-2">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="complaint_status.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                                    <i class="bi bi-search me-1"></i>ติดตามเรื่อง
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
<?php $stmt->close(); ?>