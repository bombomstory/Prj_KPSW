<?php
include('db_connection.php');
header('Content-Type: text/html; charset=utf-8');
// ตั้งค่าข้อมูลทดสอบ
$student_id = 1;  // เปลี่ยนตาม student_id ที่ต้องการ
$teacher_id = 1;  // เปลี่ยนตาม teacher_id ที่มี
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูล Weekly Schedule</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .section { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔧 แก้ไขข้อมูล Weekly Schedule</h1>
<?php

// 1. ดึงวิชาที่นักเรียนเรียน
echo '<div class="section">';
echo '<h3>ขั้นตอนที่ 1: ตรวจสอบวิชาที่นักเรียนเรียน</h3>';

$sql = "SELECT DISTINCT sg.subject_id, s.subject_code, s.subject_name
        FROM student_grades sg
        INNER JOIN subjects s ON sg.subject_id = s.subject_id
        WHERE sg.student_id = $student_id 
          AND sg.academic_year = '2567' 
          AND sg.term = 2";

$result = mysqli_query($conn, $sql);
$subjects = [];

echo '<table class="table table-striped">';
echo '<thead><tr><th>Subject ID</th><th>รหัสวิชา</th><th>ชื่อวิชา</th></tr></thead><tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row['subject_id'];
    echo "<tr>";
    echo "<td>{$row['subject_id']}</td>";
    echo "<td>{$row['subject_code']}</td>";
    echo "<td>{$row['subject_name']}</td>";
    echo "</tr>";
}

echo '</tbody></table>';
echo '<p>พบ ' . count($subjects) . ' วิชา</p>';
echo '</div>';

// 2. เพิ่มข้อมูลเข้า weekly_schedule
if (!empty($subjects)) {
    echo '<div class="section">';
    echo '<h3>ขั้นตอนที่ 2: เพิ่มข้อมูลเข้า weekly_schedule</h3>';
    
    $days = ['จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์'];
    $inserted = 0;
    $errors = [];
    
    foreach ($subjects as $index => $subject_id) {
        $day = $days[$index % count($days)];
        $period = ($index % 8) + 1;
        
        // เช็คว่ามีข้อมูลอยู่แล้วหรือไม่
        $check_sql = "SELECT schedule_id FROM weekly_schedule 
                     WHERE student_id = $student_id 
                       AND subject_id = $subject_id 
                       AND teacher_id = $teacher_id";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) == 0) {
            $insert_sql = "INSERT INTO weekly_schedule 
                          (student_id, subject_id, teacher_id, day_of_week, period_number, room_id)
                          VALUES ($student_id, $subject_id, $teacher_id, '$day', $period, 1)";
            
            if (mysqli_query($conn, $insert_sql)) {
                $inserted++;
                echo "<p class='text-success'>✓ เพิ่มวิชา subject_id=$subject_id ($day คาบ $period)</p>";
            } else {
                $errors[] = "✗ ไม่สามารถเพิ่มวิชา subject_id=$subject_id: " . mysqli_error($conn);
            }
        } else {
            echo "<p class='text-info'>- วิชา subject_id=$subject_id มีในระบบแล้ว</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><strong>สรุป:</strong> เพิ่มข้อมูลสำเร็จ $inserted รายการ</p>";
    
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Error:</strong><ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul></div>';
    }
    
    echo '</div>';
}

// 3. แสดงข้อมูลปัจจุบัน
echo '<div class="section">';
echo '<h3>ขั้นตอนที่ 3: ข้อมูล weekly_schedule ปัจจุบัน</h3>';

$sql = "SELECT ws.*, s.subject_code, s.subject_name
        FROM weekly_schedule ws
        INNER JOIN subjects s ON ws.subject_id = s.subject_id
        WHERE ws.student_id = $student_id";

$result = mysqli_query($conn, $sql);

echo '<table class="table table-hover">';
echo '<thead class="table-light"><tr><th>ID</th><th>รหัสวิชา</th><th>ชื่อวิชา</th><th>ครู ID</th><th>วัน</th><th>คาบ</th></tr></thead><tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['schedule_id']}</td>";
    echo "<td>{$row['subject_code']}</td>";
    echo "<td>{$row['subject_name']}</td>";
    echo "<td>{$row['teacher_id']}</td>";
    echo "<td>{$row['day_of_week']}</td>";
    echo "<td>{$row['period_number']}</td>";
    echo "</tr>";
}

echo '</tbody></table>';
echo '</div>';

// 4. ทดสอบ Query หลัก
echo '<div class="section">';
echo '<h3>ขั้นตอนที่ 4: ทดสอบ Query หลัก</h3>';

$sql = "
    SELECT DISTINCT
        sg.subject_id,
        s.subject_code,
        s.subject_name,
        t.teacher_id,
        CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as teacher_name,
        t.department
    FROM student_grades sg
    INNER JOIN subjects s ON sg.subject_id = s.subject_id
    INNER JOIN weekly_schedule ws ON sg.subject_id = ws.subject_id 
        AND sg.student_id = ws.student_id
    INNER JOIN teachers t ON ws.teacher_id = t.teacher_id
    INNER JOIN users u ON t.user_id = u.user_id
    WHERE sg.student_id = $student_id
        AND sg.academic_year = '2567'
        AND sg.term = 2
    ORDER BY s.subject_code
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo '<div class="alert alert-danger">Query Error: ' . mysqli_error($conn) . '</div>';
} else {
    $count = mysqli_num_rows($result);
    echo "<p><strong>ผลลัพธ์:</strong> พบ $count วิชา</p>";
    
    if ($count > 0) {
        echo '<table class="table table-success">';
        echo '<thead><tr><th>รหัสวิชา</th><th>ชื่อวิชา</th><th>ครูผู้สอน</th><th>ภาควิชา</th></tr></thead><tbody>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['subject_code']}</td>";
            echo "<td>{$row['subject_name']}</td>";
            echo "<td>{$row['teacher_name']}</td>";
            echo "<td>{$row['department']}</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table>';
        echo '<div class="alert alert-success">';
        echo '<strong>✓ สำเร็จ!</strong> ตอนนี้ระบบควรทำงานได้แล้ว';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">';
        echo '<strong>⚠️ ยังไม่พบข้อมูล</strong><br>';
        echo 'ลองรีเฟรชหน้านี้อีกครั้ง หรือตรวจสอบข้อมูลใน student_grades';
        echo '</div>';
    }
}

echo '</div>';

?>
<div class="section">
        <h3>Next Steps</h3>
        <ol>
            <li>ตรวจสอบว่าข้อมูลครบถ้วนแล้ว</li>
            <li>ลองเปิด <a href="test_teacher_evaluation.php" class="btn btn-primary btn-sm">test_teacher_evaluation.php</a></li>
            <li>ทดสอบ API: <a href="debug_teacher_evaluation.php" class="btn btn-info btn-sm">debug_teacher_evaluation.php</a></li>
            <li>เปิดระบบจริง: <a href="dashboard.php" class="btn btn-success btn-sm">dashboard.php</a></li>
        </ol>
    </div>

</div>
</body>
</html>