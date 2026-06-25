<?php
    session_start();
    include('db_connection.php');
    include('lib.php');
    // เปิดการแสดง Error ทั้งหมด
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header('Content-Type: application/json; charset=utf-8');
    // ============================================
    // ตั้งค่า SESSION สำหรับทดสอบ
    // ============================================
    $_SESSION['user_id'] = 1;
    $_SESSION['student_id'] = 1;
    $debug_info = [];
    $errors = [];
    try {
        // เช็ค Session
        $debug_info['session_check'] = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'student_id' => $_SESSION['student_id'] ?? null
        ];
        if (!isset($_SESSION['user_id'])) {
        $errors[] = 'ไม่พบ user_id ใน session';
    }

    // ดึงข้อมูลพื้นฐาน
    $student_id = $_SESSION['student_id'];
    $current_year = get_current_year();
    $current_term = get_current_term();
    $last_year = get_last_year();
    $last_term = get_last_term();

    $debug_info['academic_info'] = [
        'current_year' => $current_year,
        'current_term' => $current_term,
        'last_year' => $last_year,
        'last_term' => $last_term
    ];

    // 1. ดึงเกณฑ์การประเมิน
    $sql = "
        SELECT criterion_id, criterion_name, category, status
        FROM evaluation_criteria
        WHERE category = 'Teacher' AND status = 'active'
        ORDER BY criterion_id
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        $errors[] = 'Query criteria failed: ' . mysqli_error($conn);
        throw new Exception('Query criteria failed');
    }

    $criteria = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $criteria[] = $row;
    }

    $debug_info['criteria_count'] = count($criteria);

    if (empty($criteria)) {
        $errors[] = 'ไม่พบเกณฑ์การประเมินครูผู้สอน';
    }

    // 2. ดึงรายวิชาและครูผู้สอน
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

    $debug_info['query_subjects'] = [
        'student_id' => $student_id,
        'academic_year' => $last_year,
        'term' => $last_term
    ];

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        $errors[] = 'Prepare subjects failed: ' . mysqli_error($conn);
        throw new Exception('Prepare failed');
    }

    mysqli_stmt_bind_param($stmt, 'iss', $student_id, $last_year, $last_term);

    if (!mysqli_stmt_execute($stmt)) {
        $errors[] = 'Execute subjects failed: ' . mysqli_stmt_error($stmt);
        throw new Exception('Execute failed');
    }

    $result = mysqli_stmt_get_result($stmt);
    $subjects = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }

    mysqli_stmt_close($stmt);

    $debug_info['subjects_count'] = count($subjects);

    if (empty($subjects)) {
        $errors[] = "ไม่พบรายวิชาที่เรียนในภาคเรียนที่ {$last_term}/{$last_year}";
        
        // ตรวจสอบเพิ่มเติม
        $check_grades = mysqli_query($conn, "SELECT COUNT(*) as c FROM student_grades WHERE student_id = $student_id");
        $check_schedule = mysqli_query($conn, "SELECT COUNT(*) as c FROM weekly_schedule WHERE student_id = $student_id");
        
        $debug_info['additional_checks'] = [
            'student_grades_count' => mysqli_fetch_assoc($check_grades)['c'],
            'weekly_schedule_count' => mysqli_fetch_assoc($check_schedule)['c']
        ];
    }

    // 3. ตรวจสอบการประเมินที่มีอยู่
    $evaluations = [];

    foreach ($subjects as $subject) {
        $teacher_id = $subject['teacher_id'];
        
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
                AND academic_year = ?
                AND semester = ?
        ";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            $errors[] = 'Prepare evaluation check failed: ' . mysqli_error($conn);
            continue;
        }
        
        mysqli_stmt_bind_param($stmt, 'iiii', $student_id, $teacher_id, $last_year, $last_term);
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

    // สร้าง Response
    $response = [
        'success' => empty($errors),
        'debug_info' => $debug_info,
        'errors' => $errors,
        'criteria' => $criteria,
        'subjects' => $subjects,
        'evaluations' => $evaluations,
        'academic_info' => [
            'current_year' => $current_year,
            'current_term' => $current_term,
            'last_year' => $last_year,
            'last_term' => $last_term
        ]
    ];

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug_info' => $debug_info,
        'errors' => $errors,
        'trace' => $e->getTraceAsString()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
?>
