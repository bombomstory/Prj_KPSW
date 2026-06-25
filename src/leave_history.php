<?php
session_start();
include('db_connection.php');

$student_id = $_SESSION['user_id'];
$query = "SELECT * FROM leave_requests WHERE student_id = ? ORDER BY leave_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$history = $stmt->get_result();
?>

<div class="container mt-4">
    <h4 class="fw-bold mb-4">ประวัติการลาของฉัน</h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>วันที่ลา</th>
                <th>ประเภท</th>
                <th>เหตุผล</th>
                <th>สถานะที่ปรึกษา</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $history->fetch_assoc()): ?>
            <tr>
                <td><?= $row['leave_date'] ?></td>
                <td><?= $row['leave_type'] ?></td>
                <td><?= $row['leave_reason'] ?></td>
                <td>
                    <span class="badge bg-<?= ($row['advisor_status'] == 'approved') ? 'success' : 'warning' ?>">
                        <?= $row['advisor_status'] ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewDetails(<?= $row['leave_id'] ?>)">ดูรายละเอียด</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>