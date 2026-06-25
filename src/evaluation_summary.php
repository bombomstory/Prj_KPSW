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
    $current_year = get_current_year();
    $current_term = get_current_term();
    
    // ดึงข้อมูลการประเมินครูที่ปรึกษาทั้งหมด
    $sql = "
        SELECT 
            aer.advisor_id,
            CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as advisor_name,
            u.profile_picture,
            aer.criterion_id,
            ec.criterion_name,
            aer.rating,
            aer.comments,
            aer.created_at,
            aer.updated_at
        FROM advisor_evaluation_responses aer
        JOIN students s ON aer.student_id = s.student_id
        JOIN advisors a ON aer.advisor_id = a.advisor_id
        JOIN users u ON a.user_id = u.user_id
        JOIN evaluation_criteria ec ON aer.criterion_id = ec.criterion_id
        WHERE s.user_id = ?
          AND aer.academic_year = ?
          AND aer.semester = ?
          AND ec.category = 'Advisor'
        ORDER BY aer.advisor_id, aer.criterion_id
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $current_year, $current_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $evaluations = $result->fetch_all(MYSQLI_ASSOC);
    
    // จัดกลุ่มข้อมูลตามครูที่ปรึกษา
    $grouped_evaluations = [];
    $total_advisors = 0;
    $total_evaluations = 0;
    $overall_average = 0;
    $total_ratings = 0;
    $rating_count = 0;
    
    foreach ($evaluations as $eval) {
        $advisor_id = $eval['advisor_id'];
        
        if (!isset($grouped_evaluations[$advisor_id])) {
            $grouped_evaluations[$advisor_id] = [
                'advisor_name' => $eval['advisor_name'],
                'profile_picture' => $eval['profile_picture'],
                'evaluations' => [],
                'average_rating' => 0,
                'total_rating' => 0,
                'evaluation_count' => 0,
                'evaluation_date' => $eval['created_at']
            ];
            $total_advisors++;
        }
        
        $grouped_evaluations[$advisor_id]['evaluations'][] = [
            'criterion_name' => $eval['criterion_name'],
            'rating' => $eval['rating'],
            'comments' => $eval['comments']
        ];
        
        $grouped_evaluations[$advisor_id]['total_rating'] += $eval['rating'];
        $grouped_evaluations[$advisor_id]['evaluation_count']++;
        
        $total_ratings += $eval['rating'];
        $rating_count++;
        $total_evaluations++;
    }
    
    // คำนวณค่าเฉลี่ยสำหรับแต่ละครู
    foreach ($grouped_evaluations as $advisor_id => &$advisor_data) {
        if ($advisor_data['evaluation_count'] > 0) {
            $advisor_data['average_rating'] = round($advisor_data['total_rating'] / $advisor_data['evaluation_count'], 1);
        }
    }
    
    // คำนวณค่าเฉลี่ยรวม
    if ($rating_count > 0) {
        $overall_average = round($total_ratings / $rating_count, 1);
    }
    
    // ดึงข้อมูลครูที่ปรึกษาทั้งหมดที่ควรประเมิน
    $advisor_sql = "
        SELECT DISTINCT 
            a.advisor_id,
            CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as full_name
        FROM students s
        JOIN student_classrooms sc ON s.student_id = sc.student_id
        JOIN advisor_items ai ON sc.classroom_id = ai.classroom_id  
        JOIN advisors a ON ai.advisor_id = a.advisor_id
        JOIN users u ON a.user_id = u.user_id
        WHERE s.user_id = ? AND sc.academic_year = ?
    ";
    
    $advisor_stmt = $conn->prepare($advisor_sql);
    $advisor_stmt->bind_param("is", $student_id, $current_year);
    $advisor_stmt->execute();
    $advisor_result = $advisor_stmt->get_result();
    $all_advisors = $advisor_result->fetch_all(MYSQLI_ASSOC);
    
    $total_advisors_should_evaluate = count($all_advisors);
    $completed_evaluations = count($grouped_evaluations);
    $pending_evaluations = $total_advisors_should_evaluate - $completed_evaluations;
    
    // สถิติการประเมิน
    $evaluation_stats = [
        'total_advisors' => $total_advisors_should_evaluate,
        'completed_evaluations' => $completed_evaluations,
        'pending_evaluations' => $pending_evaluations,
        'completion_percentage' => $total_advisors_should_evaluate > 0 ? 
            round(($completed_evaluations / $total_advisors_should_evaluate) * 100, 1) : 0,
        'overall_average' => $overall_average,
        'total_ratings_given' => $rating_count
    ];
    
    // การแจกแจงคะแนน
    $rating_distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach ($evaluations as $eval) {
        if (isset($rating_distribution[$eval['rating']])) {
            $rating_distribution[$eval['rating']]++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $evaluation_stats,
        'evaluations' => array_values($grouped_evaluations),
        'rating_distribution' => $rating_distribution,
        'academic_info' => [
            'year' => $current_year,
            'term' => $current_term
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>