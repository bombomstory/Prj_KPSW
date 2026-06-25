<?php
// เชื่อมต่อฐานข้อมูล
include('db_connection.php'); 
header('Content-Type: application/json; charset=utf-8');

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 1;

$response = [
    'success' => true,
    'grade_distribution' => [],
    'history' => [],
    'gpa' => 0,
    'total_credits' => 0,
    'attendance_pct' => 95 // ค่าตั้งต้นสถิติเข้าเรียน
];

// 1. ดึงข้อมูลสัดส่วนเกรดทั้งหมด (Grade Distribution)
$sql_dist = "SELECT grade, COUNT(*) as count FROM student_grades WHERE student_id = ? GROUP BY grade";
$stmt_dist = $conn->prepare($sql_dist);
$stmt_dist->bind_param("i", $student_id);
$stmt_dist->execute();
$res_dist = $stmt_dist->get_result();

$dist = [];
while ($row = $res_dist->fetch_assoc()) {
    $dist[$row['grade']] = $row['count'];
}
$response['grade_distribution'] = $dist;

// 2. ดึงข้อมูล GPA รวมสะสม และหน่วยกิตรวม (GPAX)
$sql_gpax = "SELECT SUM(sg.grade * sj.credits) as total_grade_points, SUM(sj.credits) as total_credits 
             FROM student_grades sg 
             INNER JOIN subjects sj ON sg.subject_id = sj.subject_id 
             WHERE sg.student_id = ?";
$stmt_gpax = $conn->prepare($sql_gpax);
$stmt_gpax->bind_param("i", $student_id);
$stmt_gpax->execute();
$res_gpax = $stmt_gpax->get_result()->fetch_assoc();

if ($res_gpax['total_credits'] > 0) {
    $response['gpa'] = round($res_gpax['total_grade_points'] / $res_gpax['total_credits'], 2);
    $response['total_credits'] = $res_gpax['total_credits'];
}

// 3. ดึงประวัติรายภาคเรียน (Timeline & History)
$sql_hist = "SELECT sg.academic_year, sg.term, SUM(sg.grade * sj.credits) as pts, SUM(sj.credits) as credits 
             FROM student_grades sg 
             INNER JOIN subjects sj ON sg.subject_id = sj.subject_id 
             WHERE sg.student_id = ? 
             GROUP BY sg.academic_year, sg.term 
             ORDER BY sg.academic_year ASC, sg.term ASC";
$stmt_hist = $conn->prepare($sql_hist);
$stmt_hist->bind_param("i", $student_id);
$stmt_hist->execute();
$res_hist = $stmt_hist->get_result();

while ($row = $res_hist->fetch_assoc()) {
    $response['history'][] = [
        'academic_year' => $row['academic_year'],
        'term' => $row['term'],
        'gpa' => round($row['pts'] / $row['credits'], 2),
        'credits' => $row['credits'],
        'attendance_pct' => 95
    ];
}

echo json_encode($response);
?>