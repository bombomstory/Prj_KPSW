<?php
// get_attendance.php - คลังคำนวณสถิติเข้าเรียนแบบบีบอัดรายสัปดาห์ปฏิทินจริง (Idea 2)
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'กรุณาล็อกอินก่อนใช้งานค่ะ']);
    exit();
}

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

$current_term = get_current_term(); // คอนฟิก 1
$current_year = get_current_year(); // คอนฟิก 2568

// ค้นหาห้องเรียนปัจจุบันของนักเรียน
$class_sql = "SELECT classroom_id FROM `student_classrooms` WHERE student_id = ? AND academic_year = ? LIMIT 1";
$class_stmt = $conn->prepare($class_sql);
$class_stmt->bind_param("is", $student_id, $current_year);
$class_stmt->execute();
$class_res = $class_stmt->get_result()->fetch_assoc();
$class_stmt->close();

$classroom_id = $class_res ? $class_res['classroom_id'] : 0;

// เตรียมอาร์เรย์สรุปผลโครงสร้างปฏิทิน 18 สัปดาห์
$calendar_weeks = array_fill(1, 18, 'Unchecked');
$overall_counts = ['present_weeks' => 0, 'absent_weeks' => 0, 'late_weeks' => 0, 'leave_weeks' => 0, 'max_taught_week' => 0];
$subjects_attendance = [];

if ($classroom_id > 0) {
    
    // 🧠 1. คิวรี่จัดกลุ่มเช็กสถานะบีบอัดรายสัปดาห์ปฏิทิน (Calendar Week Rollup)
    $rollup_sql = "SELECT week_number,
                          SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count,
                          SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late_count,
                          SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) AS leave_count,
                          SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count
                   FROM `attendance_records`
                   WHERE student_id = ? AND assignment_id IN (
                       SELECT assignment_id FROM `teaching_assignments` 
                       WHERE classroom_id = ? AND academic_year = ? AND term = ?
                   )
                   GROUP BY week_number
                   ORDER BY week_number ASC";
                   
    $rollup_stmt = $conn->prepare($rollup_sql);
    $rollup_stmt->bind_param("iiss", $student_id, $classroom_id, $current_year, $current_term);
    $rollup_stmt->execute();
    $rollup_res = $rollup_stmt->get_result();
    
    while ($row = $rollup_res->fetch_assoc()) {
        $week = intval($row['week_number']);
        
        if ($week > $overall_counts['max_taught_week']) {
            $overall_counts['max_taught_week'] = $week;
        }
        
        // 🌟 ลอจิกตัดสินแผลประจำสัปดาห์: ขาดแค่ 1 วิชา ถือว่าสัปดาห์นั้น "มีแผลขาดเรียน" ทันที
        if (intval($row['absent_count']) > 0) {
            $calendar_weeks[$week] = 'Absent';
            $overall_counts['absent_weeks']++;
        } elseif (intval($row['late_count']) > 0) {
            $calendar_weeks[$week] = 'Late';
            $overall_counts['late_weeks']++;
        } elseif (intval($row['leave_count']) > 0) {
            $calendar_weeks[$week] = 'Leave';
            $overall_counts['leave_weeks']++;
        } elseif (intval($row['present_count']) > 0) {
            $calendar_weeks[$week] = 'Present';
            $overall_counts['present_weeks']++;
        }
    }
    $rollup_stmt->close();

    // 🧠 2. คิวรี่เก็บสถิติแยกรายวิชาตามปกติ เพื่อนำไปพ่นหลอดกราฟความคืบหน้าด้านล่าง
    $sub_sql = "SELECT ta.assignment_id, sj.subject_code, sj.subject_name,
                       CONCAT(u_t.prefix_name, u_t.first_name, ' ', u_t.last_name) AS teacher_name,
                       COUNT(att.attendance_id) AS total_weeks,
                       SUM(CASE WHEN att.status = 'Present' THEN 1 ELSE 0 END) AS present,
                       SUM(CASE WHEN att.status = 'Absent' THEN 1 ELSE 0 END) AS absent,
                       SUM(CASE WHEN att.status = 'Late' THEN 1 ELSE 0 END) AS late,
                       SUM(CASE WHEN att.status = 'Leave' THEN 1 ELSE 0 END) AS leave_count
                FROM `teaching_assignments` ta
                INNER JOIN `subjects` sj ON ta.subject_id = sj.subject_id
                INNER JOIN `teachers` t ON ta.teacher_id = t.teacher_id
                INNER JOIN `users` u_t ON t.user_id = u_t.user_id
                LEFT JOIN `attendance_records` att ON ta.assignment_id = att.assignment_id AND att.student_id = ?
                WHERE ta.classroom_id = ? AND ta.academic_year = ? AND ta.term = ?
                GROUP BY ta.assignment_id";

    $sub_stmt = $conn->prepare($sub_sql);
    $sub_stmt->bind_param("iiss", $student_id, $classroom_id, $current_year, $current_term);
    $sub_stmt->execute();
    $sub_res = $sub_stmt->get_result();

    while ($row = $sub_res->fetch_assoc()) {
        $p = intval($row['present']);
        $a = intval($row['absent']);
        $lt = intval($row['late']);
        $lv = intval($row['leave_count']);
        $checked_total = $p + $a + $lt + $lv;
        $sub_pct = $checked_total > 0 ? (($p + $lt + $lv) / $checked_total) * 100 : 0;

        $subjects_attendance[] = [
            'subject_code' => $row['subject_code'],
            'subject_name' => $row['subject_name'],
            'teacher_name' => $row['teacher_name'],
            'total_weeks' => $checked_total,
            'present' => $p,
            'absent' => $a,
            'late' => $lt,
            'leave' => $lv,
            'percentage' => round($sub_pct, 1)
        ];
    }
    $sub_stmt->close();
}

// คืนค่ารูปแบบออบเจกต์ลอจิกปฏิทินจริง
echo json_encode([
    'overall' => $overall_counts,
    'calendar' => $calendar_weeks,
    'subjects' => $subjects_attendance
], JSON_UNESCAPED_UNICODE);