<?php
    session_start();
    include('db_connection.php');
    include('lib.php');

    $category = $_GET['category'] ?? 'Advisor';
    $user_id = $_SESSION['user_id'] ?? null;

    // ตรวจสอบว่ามี user_id หรือไม่
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id in session.']);
        exit;
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

    // ใช้ฟังก์ชันเพื่อหาค่า classroom_id
    $classroom_id = getStudentClassroomIdByUserId($student_id);

    if (!$classroom_id) {
        http_response_code(404);
        echo json_encode(['error' => 'Classroom not found for user.']);
        exit;
    }

    // ดึงรายการเกณฑ์การประเมิน
    $stmtCriteria = $conn->prepare("SELECT criterion_id, criterion_name FROM evaluation_criteria WHERE category = ? and status = 'Active'");
    $stmtCriteria->bind_param("s", $category);
    $stmtCriteria->execute();
    $resultCriteria = $stmtCriteria->get_result();

    $criteria = [];
    while ($row = $resultCriteria->fetch_assoc()) {
        $criteria[] = $row;
    }

    // ดึงรายชื่อครูที่ปรึกษาจาก advisor_items และ users
    $stmtAdvisors = $conn->prepare("SELECT a.advisor_id, u.prefix_name, u.first_name, u.last_name, u.profile_picture 
        FROM advisor_items ai
        JOIN advisors a ON ai.advisor_id = a.advisor_id
        JOIN users u ON a.user_id = u.user_id
        WHERE ai.classroom_id = ?");
    $stmtAdvisors->bind_param("i", $classroom_id);
    $stmtAdvisors->execute();
    $resultAdvisors = $stmtAdvisors->get_result();

    $advisors = [];
    while ($row = $resultAdvisors->fetch_assoc()) {
        $full = trim($row['prefix_name'] . $row['first_name'] . ' ' . $row['last_name']);
        $photo = !empty($row['profile_picture']) ? $row['profile_picture'] : 'images/teacher-placeholder.png';
        $advisors[] = [
            'advisor_id' => (int)$row['advisor_id'],
            'full_name'  => $full,
            'photo'      => $photo
        ];
    }

    // รวมข้อมูลทั้งหมด
    $response = [
        'criteria' => $criteria,
        'advisors' => $advisors
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
?>
