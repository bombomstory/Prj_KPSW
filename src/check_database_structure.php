<?php
include('db_connection.php');
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตรวจสอบโครงสร้างฐานข้อมูล</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .table-section { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-name { 
            color: #007bff; 
            font-weight: bold; 
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .status-ok { color: green; }
        .status-missing { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4"><i class="bi bi-database"></i> ตรวจสอบโครงสร้างฐานข้อมูล</h1>
<?php
// รายชื่อตารางที่ต้องมี
$required_tables = [
    'evaluation_criteria',
    'students',
    'teachers',
    'users',
    'subjects',
    'student_grades',
    'weekly_schedule',
    'teacher_evaluation_responses',
    'advisor_evaluation_responses'
];

echo '<div class="table-section">';
echo '<h3>ตรวจสอบตารางที่จำเป็น</h3>';
echo '<table class="table table-striped">';
echo '<thead><tr><th>ตาราง</th><th>สถานะ</th><th>จำนวนแถว</th></tr></thead><tbody>';

foreach ($required_tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    $exists = mysqli_num_rows($result) > 0;
    
    if ($exists) {
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM `$table`");
        $count = mysqli_fetch_assoc($count_result)['count'];
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td><span class='status-ok'>✓ มี</span></td>";
        echo "<td>$count แถว</td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td><span class='status-missing'>✗ ไม่มี</span></td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
}

echo '</tbody></table>';
echo '</div>';

// ตรวจสอบโครงสร้างแต่ละตาราง
$tables_to_check = [
    'teacher_evaluation_responses' => [
        'response_id', 'student_id', 'teacher_id', 'academic_year', 
        'semester', 'criterion_id', 'rating', 'comments', 'created_at'
    ],
    'evaluation_criteria' => [
        'criterion_id', 'criterion_name', 'category', 'status'
    ],
    'weekly_schedule' => [
        'schedule_id', 'student_id', 'subject_id', 'teacher_id', 
        'day_of_week', 'period_number', 'room_id'
    ]
];

foreach ($tables_to_check as $table => $required_columns) {
    echo '<div class="table-section">';
    echo "<div class='table-name'>ตาราง: $table</div>";
    
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p class='status-missing'>ตารางนี้ไม่มีในฐานข้อมูล</p>";
        echo '</div>';
        continue;
    }
    
    $columns_result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
    $existing_columns = [];
    
    echo '<table class="table table-sm">';
    echo '<thead><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead><tbody>';
    
    while ($col = mysqli_fetch_assoc($columns_result)) {
        $existing_columns[] = $col['Field'];
        $in_required = in_array($col['Field'], $required_columns);
        $row_class = $in_required ? 'table-success' : '';
        
        echo "<tr class='$row_class'>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    
    echo '</tbody></table>';
    
    // เช็คว่ามี column ที่ขาดหรือไม่
    $missing_columns = array_diff($required_columns, $existing_columns);
    if (!empty($missing_columns)) {
        echo '<div class="alert alert-warning">';
        echo '<strong>Column ที่ขาดหายไป:</strong> ' . implode(', ', $missing_columns);
        echo '</div>';
    }
    
    echo '</div>';
}

// ตัวอย่างข้อมูล
echo '<div class="table-section">';
echo '<h3>ตัวอย่างข้อมูลในตาราง weekly_schedule</h3>';
$sample = mysqli_query($conn, "
    SELECT ws.*, s.subject_name, u.first_name, u.last_name
    FROM weekly_schedule ws
    LEFT JOIN subjects s ON ws.subject_id = s.subject_id
    LEFT JOIN teachers t ON ws.teacher_id = t.teacher_id
    LEFT JOIN users u ON t.user_id = u.user_id
    LIMIT 10
");

if (mysqli_num_rows($sample) > 0) {
    echo '<table class="table table-striped">';
    echo '<thead><tr><th>Student ID</th><th>Subject</th><th>Teacher</th><th>Day</th><th>Period</th></tr></thead><tbody>';
    while ($row = mysqli_fetch_assoc($sample)) {
        echo "<tr>";
        echo "<td>{$row['student_id']}</td>";
        echo "<td>{$row['subject_name']}</td>";
        echo "<td>{$row['first_name']} {$row['last_name']}</td>";
        echo "<td>{$row['day_of_week']}</td>";
        echo "<td>{$row['period_number']}</td>";
        echo "</tr>";
    }
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-warning">ไม่มีข้อมูลในตาราง weekly_schedule</div>';
}
echo '</div>';

?>
</div>
</body>
</html>
