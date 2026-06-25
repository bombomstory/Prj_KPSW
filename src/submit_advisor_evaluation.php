<?php
    session_start();
    include('db_connection.php');
    include('lib.php');

    header('Content-Type: application/json');

    // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $student_id = $_SESSION['user_id'];

    // รับข้อมูล JSON ที่ส่งมา
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['advisor_id'], $data['responses'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required data.']);
        exit;
    }

/*     if (!$data || !isset($data['advisor_id'], $data['academic_year'], $data['semester'], $data['responses'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required data.']);
        exit;
    } */

    $advisor_id = intval($data['advisor_id']);
    $config = getLastAcademicYearAndTerm($conn);
    $academic_year = $config['academic_year'];
    $semester = $config['term'];
    // $academic_year = $data['academic_year'];
    // $semester = intval($data['term']);
    $responses = $data['responses']; // array of { criterion_id, rating, comments }

    // เตรียม statement ล่วงหน้า
    $stmt = $conn->prepare("INSERT INTO advisor_evaluation_responses 
        (student_id, advisor_id, academic_year, semester, criterion_id, rating, comments)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // วนลูปบันทึกแต่ละข้อ
    foreach ($responses as $res) {
        $criterion_id = intval($res['criterion_id']);
        $rating = floatval($res['rating']);
        $comments = $res['comments'] ?? null;

        $stmt->bind_param("iisiiis", $student_id, $advisor_id, $academic_year, $semester, $criterion_id, $rating, $comments);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
            exit;
        }
    }

    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Evaluation submitted successfully.']);
?>
