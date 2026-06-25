<?php
session_start();
include('db_connection.php');
include('lib.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}

try {
    $student_id = $_SESSION['user_id'];
    $current_year = get_last_year();
    $current_term = get_last_term();
    
    // Debug: ตรวจสอบข้อมูลนักเรียน
    $debug_sql = "SELECT student_id, student_code, first_name, last_name FROM students WHERE user_id = ?";
    $debug_stmt = $conn->prepare($debug_sql);
    $debug_stmt->bind_param("i", $student_id);
    $debug_stmt->execute();
    $student_info = $debug_stmt->get_result()->fetch_assoc();
    
    if (!$student_info) {
        echo json_encode(['error' => 'ไม่พบข้อมูลนักเรียนในระบบ']);
        exit;
    }
    
    $actual_student_id = $student_info['student_id'];
    
    // ลองหาครูที่ปรึกษาด้วยวิธีที่ยืดหยุ่นมากขึ้น
    // วิธีที่ 1: ตามปีการศึกษาปัจจุบัน
    $advisor_sql = "
        SELECT DISTINCT 
            a.advisor_id,
            CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as full_name,
            u.profile_picture as photo,
            a.department,
            c.classroom_name,
            sc.academic_year
        FROM students s
        JOIN student_classrooms sc ON s.student_id = sc.student_id
        JOIN classrooms c ON sc.classroom_id = c.classroom_id
        JOIN advisor_items ai ON c.classroom_id = ai.classroom_id  
        JOIN advisors a ON ai.advisor_id = a.advisor_id
        JOIN users u ON a.user_id = u.user_id
        WHERE s.user_id = ? AND sc.academic_year = ?
    ";
    
    $stmt = $conn->prepare($advisor_sql);
    $stmt->bind_param("is", $student_id, $current_year);
    $stmt->execute();
    $advisor_result = $stmt->get_result();
    $advisors = $advisor_result->fetch_all(MYSQLI_ASSOC);
    
    // ถ้าไม่พบ ลองหาจากปีล่าสุด
    if (empty($advisors)) {
        $advisor_sql2 = "
            SELECT DISTINCT 
                a.advisor_id,
                CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as full_name,
                u.profile_picture as photo,
                a.department,
                c.classroom_name,
                sc.academic_year
            FROM students s
            JOIN student_classrooms sc ON s.student_id = sc.student_id
            JOIN classrooms c ON sc.classroom_id = c.classroom_id
            JOIN advisor_items ai ON c.classroom_id = ai.classroom_id  
            JOIN advisors a ON ai.advisor_id = a.advisor_id
            JOIN users u ON a.user_id = u.user_id
            WHERE s.user_id = ?
            ORDER BY sc.academic_year DESC
            LIMIT 5
        ";
        
        $stmt2 = $conn->prepare($advisor_sql2);
        $stmt2->bind_param("i", $student_id);
        $stmt2->execute();
        $advisor_result = $stmt2->get_result();
        $advisors = $advisor_result->fetch_all(MYSQLI_ASSOC);
    }
    
    // ถ้ายังไม่พบ ลองหาครูที่ปรึกษาทั้งหมดในระบบ (สำหรับ test)
    if (empty($advisors)) {
        $advisor_sql3 = "
            SELECT DISTINCT 
                a.advisor_id,
                CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as full_name,
                u.profile_picture as photo,
                a.department
            FROM advisors a
            JOIN users u ON a.user_id = u.user_id
            LIMIT 5
        ";
        
        $advisor_result = $conn->query($advisor_sql3);
        $advisors = $advisor_result->fetch_all(MYSQLI_ASSOC);
    }
    
    if (empty($advisors)) {
        // Debug info
        $debug_info = [
            'student_info' => $student_info,
            'current_year' => $current_year,
            'current_term' => $current_term,
            'query_used' => $advisor_sql
        ];
        
        echo json_encode([
            'error' => 'ไม่พบข้อมูลครูที่ปรึกษา', 
            'debug' => $debug_info
        ]);
        exit;
    }
    
    // ดึงหัวข้อการประเมินสำหรับครูที่ปรึกษา
    $criteria_sql = "
        SELECT criterion_id, criterion_name 
        FROM evaluation_criteria 
        WHERE category = 'Advisor' AND status = 'active'
        ORDER BY criterion_id
    ";
    $criteria_result = $conn->query($criteria_sql);
    $criteria = $criteria_result->fetch_all(MYSQLI_ASSOC);
    
    // ตรวจสอบการประเมินที่มีอยู่แล้วสำหรับแต่ละครูที่ปรึกษา
    $evaluation_data = [];
    
    foreach ($advisors as $advisor) {
        $advisor_id = $advisor['advisor_id'];
        
        // ตรวจสอบการประเมินที่มีอยู่
        $eval_sql = "
            SELECT aer.advisor_id, aer.criterion_id, aer.rating, aer.comments,
                   aer.created_at, aer.updated_at
            FROM advisor_evaluation_responses aer
            WHERE aer.student_id = (SELECT student_id FROM students WHERE user_id = ?)
              AND aer.advisor_id = ?
              AND aer.academic_year = ?
              AND aer.semester = ?
        ";
        
        $eval_stmt = $conn->prepare($eval_sql);
        $eval_stmt->bind_param("iiii", $student_id, $advisor_id, $current_year, $current_term);
        $eval_stmt->execute();
        $eval_result = $eval_stmt->get_result();
        $existing_evaluations = $eval_result->fetch_all(MYSQLI_ASSOC);
        
        $advisor_evaluation = [
            'advisor_info' => $advisor,
            'has_evaluation' => !empty($existing_evaluations),
            'evaluation_date' => null,
            'ratings' => [],
            'comments' => []
        ];
        
        if (!empty($existing_evaluations)) {
            // มีการประเมินแล้ว - จัดเก็บคะแนนตามหัวข้อ
            $advisor_evaluation['evaluation_date'] = $existing_evaluations[0]['created_at'];
            
            foreach ($existing_evaluations as $eval) {
                $advisor_evaluation['ratings'][$eval['criterion_id']] = [
                    'rating' => $eval['rating'],
                    'comments' => $eval['comments']
                ];
            }
        }
        
        $evaluation_data[] = $advisor_evaluation;
    }
    
    echo json_encode([
        'success' => true,
        'criteria' => $criteria,
        'advisors' => $advisors,
        'evaluations' => $evaluation_data,
        'academic_info' => [
            'year' => $current_year,
            'term' => $current_term
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>