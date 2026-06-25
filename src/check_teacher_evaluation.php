<?php
session_start();
include('db_connection.php');
include('lib.php');

// เปลี่ยนแปลงเพื่อให้อ่านภาษาไทยใน JSON ได้ง่ายขึ้นตอน Debug ค่ะ
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}

try {
    // 🌟 จุดแก้ไขที่ 1: แปลง user_id ให้เป็น student_id ผ่านฟังก์ชันของระบบก่อนนำไปคิวรี่
    $student_id = getStudentID($_SESSION['user_id']); 
    
    $current_year = get_current_year();
    $current_term = get_current_term();
    $last_year = get_last_year();
    $last_term = get_last_term();

    // 1. ดึงเกณฑ์การประเมินครูผู้สอน
    $sql = "
    SELECT criterion_id, criterion_name, category, status
    FROM evaluation_criteria
    WHERE category = 'Teacher' AND status = 'active'
    ORDER BY criterion_id
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        throw new Exception('Query failed: ' . mysqli_error($conn));
    }

    $criteria = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $criteria[] = $row;
    }

    // 2. ดึงรายวิชาที่นักเรียนเรียนในภาคเรียนก่อนหน้า พร้อมข้อมูลครูผู้สอน
    $sql = "
        SELECT DISTINCT
            sg.subject_id,
            s.subject_code,
            s.subject_name,
            t.teacher_id,
            CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as teacher_name,
            u.profile_picture,
            t.department
        FROM student_grades sg
        INNER JOIN subjects s ON sg.subject_id = s.subject_id
        INNER JOIN weekly_schedule ws ON sg.subject_id = ws.subject_id 
            AND sg.student_id = ws.student_id
        INNER JOIN teachers t ON ws.teacher_id = t.teacher_id
        INNER JOIN users u ON t.user_id = u.user_id
        WHERE sg.student_id = ? 
            AND sg.academic_year = ?
            AND sg.term = ?
        ORDER BY s.subject_code
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        throw new Exception('Prepare failed: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iss', $student_id, $last_year, $last_term);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $subjects = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }

    mysqli_stmt_close($stmt);

    if (empty($subjects)) {
        echo json_encode([
            'error' => 'ไม่พบรายวิชาที่เรียนในภาคเรียนที่ ' . $last_term . '/' . $last_year,
            'criteria' => $criteria,
            'subjects' => [],
            'evaluations' => [],
            'academic_info' => [
                'current_year' => $current_year,
                'current_term' => $current_term,
                'last_year' => $last_year,
                'last_term' => $last_term
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3. ตรวจสอบการประเมินที่มีอยู่แล้ว
    $evaluations = [];

    foreach ($subjects as $subject) {
        $teacher_id = $subject['teacher_id'];
        $subject_id = $subject['subject_id']; // 🌟 ดึงรหัสวิชาออกมาร่วมตรวจสอบด้วยค่ะ
        
        // 🌟 จุดแก้ไขที่ 2: เพิ่ม "AND subject_id = ?" เพื่อเช็คแยกขาดเป็นระดับรายวิชา
        $sql = "
            SELECT 
                response_id,
                criterion_id,
                rating,
                comments,
                created_at
            FROM teacher_evaluation_responses
            WHERE student_id = ?
                AND teacher_id = ?
                AND subject_id = ?  -- 👈 เช็คเจาะจงเฉพาะวิชานี้เท่านั้น
                AND academic_year = ?
                AND semester = ?
        ";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . mysqli_error($conn));
        }
        
        // 🌟 จุดแก้ไขที่ 3: เปลี่ยนมาผูกตัวแปรแบบ 'iiiss' หรือให้ตรงตาม Schema จริงของตารางอาจารย์ค่ะ
        mysqli_stmt_bind_param($stmt, 'iiiss', $student_id, $teacher_id, $subject_id, $last_year, $last_term);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $existing_ratings = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_ratings[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        
        $has_evaluation = !empty($existing_ratings);
        $ratings = [];
        $evaluation_date = null;
        
        if ($has_evaluation) {
            foreach ($existing_ratings as $rating) {
                $ratings[$rating['criterion_id']] = [
                    'rating' => $rating['rating'],
                    'comments' => $rating['comments']
                ];
                $evaluation_date = $rating['created_at'];
            }
        }
        
        $evaluations[] = [
            'subject_info' => $subject,
            'has_evaluation' => $has_evaluation,
            'ratings' => $ratings,
            'evaluation_date' => $evaluation_date
        ];
    }

    echo json_encode([
        'success' => true,
        'criteria' => $criteria,
        'subjects' => $subjects,
        'evaluations' => $evaluations,
        'academic_info' => [
            'current_year' => $current_year,
            'current_term' => $current_term,
            'last_year' => $last_year,
            'last_term' => $last_term
        ]
    ], JSON_UNESCAPED_UNICODE); // ใส่ JSON_UNESCAPED_UNICODE เพื่อให้ฝั่งหน้าบ้านรับภาษาไทยได้สมบูรณ์ค่ะ

} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>