<?php
session_start();
include('db_connection.php');
include('lib.php');

if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

$student_user_id = $_SESSION['user_id'];
echo "<h2>🔍 ตรวจสอบข้อมูลครูที่ปรึกษา</h2>";
echo "<hr>";

// 1. ตรวจสอบข้อมูลนักเรียน
echo "<h3>1. ข้อมูลนักเรียน</h3>";
$student_sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $student_user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if ($student) {
    echo "<div style='background:#e8f5e8; padding:10px; border-radius:5px;'>";
    echo "✅ พบข้อมูลนักเรียน<br>";
    echo "- student_id: {$student['student_id']}<br>";
    echo "- student_code: {$student['student_code']}<br>";
    echo "- ชื่อ: {$student['first_name']} {$student['last_name']}<br>";
    echo "- ชั้น: {$student['grade_level']}<br>";
    echo "</div>";
    $student_id = $student['student_id'];
} else {
    echo "<div style='background:#ffe8e8; padding:10px; border-radius:5px;'>";
    echo "❌ ไม่พบข้อมูลนักเรียน user_id = $student_user_id";
    echo "</div>";
    exit;
}

echo "<br>";

// 2. ตรวจสอบข้อมูลห้องเรียน
echo "<h3>2. ข้อมูลห้องเรียนของนักเรียน</h3>";
$classroom_sql = "
    SELECT sc.*, c.classroom_name, c.academic_year as class_year, c.department
    FROM student_classrooms sc
    JOIN classrooms c ON sc.classroom_id = c.classroom_id
    WHERE sc.student_id = ?
    ORDER BY sc.academic_year DESC
";
$stmt = $conn->prepare($classroom_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$classrooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($classrooms) {
    echo "<div style='background:#e8f5e8; padding:10px; border-radius:5px;'>";
    echo "✅ พบข้อมูลห้องเรียน " . count($classrooms) . " รายการ<br>";
    foreach ($classrooms as $classroom) {
        echo "- ห้อง: {$classroom['classroom_name']} | ปีการศึกษา: {$classroom['academic_year']} | แผนก: {$classroom['department']}<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background:#ffe8e8; padding:10px; border-radius:5px;'>";
    echo "❌ ไม่พบข้อมูลห้องเรียนของนักเรียน";
    echo "</div>";
}

echo "<br>";

// 3. ตรวจสอบข้อมูล advisor_items
echo "<h3>3. ข้อมูลครูที่ปรึกษาในห้องเรียน</h3>";
if ($classrooms) {
    foreach ($classrooms as $classroom) {
        echo "<h4>ห้อง: {$classroom['classroom_name']} (ID: {$classroom['classroom_id']})</h4>";
        
        $advisor_items_sql = "
            SELECT ai.*, a.advisor_id, a.department as advisor_dept,
                   CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as advisor_name
            FROM advisor_items ai
            JOIN advisors a ON ai.advisor_id = a.advisor_id
            JOIN users u ON a.user_id = u.user_id
            WHERE ai.classroom_id = ?
        ";
        $stmt = $conn->prepare($advisor_items_sql);
        $stmt->bind_param("i", $classroom['classroom_id']);
        $stmt->execute();
        $advisor_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        if ($advisor_items) {
            echo "<div style='background:#e8f5e8; padding:10px; border-radius:5px; margin:10px 0;'>";
            echo "✅ พบครูที่ปรึกษา " . count($advisor_items) . " คน<br>";
            foreach ($advisor_items as $item) {
                echo "- {$item['advisor_name']} (ID: {$item['advisor_id']}) | แผนก: {$item['advisor_dept']}<br>";
            }
            echo "</div>";
        } else {
            echo "<div style='background:#fff3cd; padding:10px; border-radius:5px; margin:10px 0;'>";
            echo "⚠️ ไม่พบครูที่ปรึกษาในห้องนี้";
            echo "</div>";
        }
    }
} else {
    echo "<div style='background:#ffe8e8; padding:10px; border-radius:5px;'>";
    echo "❌ ไม่สามารถตรวจสอบได้เพราะไม่มีข้อมูลห้องเรียน";
    echo "</div>";
}

echo "<br>";

// 4. ตรวจสอบข้อมูลครูที่ปรึกษาทั้งหมดในระบบ
echo "<h3>4. ครูที่ปรึกษาทั้งหมดในระบบ</h3>";
$all_advisors_sql = "
    SELECT a.advisor_id, a.department,
           CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as advisor_name,
           u.profile_picture
    FROM advisors a
    JOIN users u ON a.user_id = u.user_id
    ORDER BY a.advisor_id
";
$all_advisors = $conn->query($all_advisors_sql)->fetch_all(MYSQLI_ASSOC);

if ($all_advisors) {
    echo "<div style='background:#e8f5e8; padding:10px; border-radius:5px;'>";
    echo "✅ พบครูที่ปรึกษาในระบบทั้งหมด " . count($all_advisors) . " คน<br>";
    foreach ($all_advisors as $advisor) {
        echo "- {$advisor['advisor_name']} (ID: {$advisor['advisor_id']}) | แผนก: {$advisor['department']}<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background:#ffe8e8; padding:10px; border-radius:5px;'>";
    echo "❌ ไม่พบครูที่ปรึกษาในระบบ";
    echo "</div>";
}

echo "<br>";

// 5. ตรวจสอบการประเมินที่มีอยู่
echo "<h3>5. การประเมินที่มีอยู่</h3>";
$current_year = get_current_year();
$current_term = get_current_term();

echo "<p><strong>ปีการศึกษาปัจจุบัน:</strong> $current_year, <strong>ภาคเรียน:</strong> $current_term</p>";

$evaluations_sql = "
    SELECT aer.*, CONCAT(u.prefix_name, u.first_name, ' ', u.last_name) as advisor_name
    FROM advisor_evaluation_responses aer
    JOIN advisors a ON aer.advisor_id = a.advisor_id
    JOIN users u ON a.user_id = u.user_id
    WHERE aer.student_id = ?
    ORDER BY aer.created_at DESC
";
$stmt = $conn->prepare($evaluations_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($evaluations) {
    echo "<div style='background:#e8f5e8; padding:10px; border-radius:5px;'>";
    echo "✅ พบการประเมินที่มีอยู่ " . count($evaluations) . " รายการ<br>";
    foreach ($evaluations as $eval) {
        echo "- ครู: {$eval['advisor_name']} | ปี: {$eval['academic_year']} | ภาค: {$eval['semester']} | คะแนน: {$eval['rating']} | ข้อเสนอแนะ: {$eval['comments']} | วันที่: {$eval['created_at']}<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background:#fff3cd; padding:10px; border-radius:5px;'>";
    echo "⚠️ ยังไม่มีการประเมินครูที่ปรึกษา";
    echo "</div>";
}

echo "<br>";

// 6. แนะนำวิธีแก้ไข
echo "<h3>🔧 วิธีแก้ไขปัญหา</h3>";
echo "<div style='background:#f0f8ff; padding:15px; border-radius:5px;'>";

if (empty($classrooms)) {
    echo "<h4 style='color:red;'>⚠️ ปัญหา: ไม่มีข้อมูลห้องเรียน</h4>";
    echo "<p><strong>วิธีแก้:</strong> เพิ่มข้อมูลในตาราง student_classrooms</p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "INSERT INTO student_classrooms (student_id, classroom_id, academic_year, start_date, end_date) 
VALUES ({$student_id}, 1, '{$current_year}', '2024-05-15', '2025-03-31');";
    echo "</pre>";
} else if (empty($advisor_items)) {
    echo "<h4 style='color:orange;'>⚠️ ปัญหา: ไม่มีครูที่ปรึกษาในห้องเรียน</h4>";
    echo "<p><strong>วิธีแก้:</strong> เพิ่มข้อมูลในตาราง advisor_items</p>";
    echo "<pre style='background:#f5f5f5; padding:10px;'>";
    echo "INSERT INTO advisor_items (advisor_id, classroom_id) 
VALUES (1, {$classrooms[0]['classroom_id']});";
    echo "</pre>";
} else {
    echo "<h4 style='color:green;'>✅ ข้อมูลครบถ้วน</h4>";
    echo "<p>ระบบควรทำงานได้ปกติแล้ว</p>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='studentDashboard.php'>← กลับไปที่ Dashboard</a></p>";
?>