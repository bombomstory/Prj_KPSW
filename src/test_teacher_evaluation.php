<?php
    session_start();
    include('db_connection.php');
    include('lib.php');// ============================================
    // ตั้งค่า SESSION สำหรับทดสอบ
    // ============================================
    $_SESSION['user_id'] = 1;        // ใส่ user_id ของนักเรียนที่ต้องการทดสอบ
    $_SESSION['student_id'] = 1;     // ใส่ student_id ของนักเรียนที่ต้องการทดสอบ
    $_SESSION['first_name'] = 'นักเรียนทดสอบ';
    $_SESSION['last_name'] = 'ระบบ';?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ทดสอบระบบประเมินครูผู้สอน</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <style>
        body { 
            padding: 20px; 
            font-family: 'Kanit', sans-serif;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-title {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
    </head>
    <body>
        <div class="container">
            <h1 class="text-center mb-4">
                <i class="bi bi-bug"></i> ทดสอบระบบประเมินครูผู้สอน
            </h1>
     
<!-- ส่วนที่ 1: ตรวจสอบ Session -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-1-circle"></i> ตรวจสอบ Session</h3>
        <?php
        echo "<p><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? '<span class="text-danger">ไม่พบ</span>') . "</p>";
        echo "<p><strong>Student ID:</strong> " . ($_SESSION['student_id'] ?? '<span class="text-danger">ไม่พบ</span>') . "</p>";
        echo "<p><strong>ชื่อ:</strong> " . ($_SESSION['first_name'] ?? '') . " " . ($_SESSION['last_name'] ?? '') . "</p>";
        
        if (isset($_SESSION['user_id']) && isset($_SESSION['student_id'])) {
            echo '<span class="status-badge status-success">✓ Session พร้อมใช้งาน</span>';
        } else {
            echo '<span class="status-badge status-error">✗ Session ไม่สมบูรณ์</span>';
        }
        ?>
    </div>

    <!-- ส่วนที่ 2: ตรวจสอบการเชื่อมต่อฐานข้อมูล -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-2-circle"></i> ตรวจสอบการเชื่อมต่อฐานข้อมูล</h3>
        <?php
        if ($conn && mysqli_ping($conn)) {
            echo '<span class="status-badge status-success">✓ เชื่อมต่อฐานข้อมูลสำเร็จ</span>';
            echo "<p class=\"debug-info mt-2\">Database: " . mysqli_get_host_info($conn) . "</p>";
        } else {
            echo '<span class="status-badge status-error">✗ เชื่อมต่อฐานข้อมูลล้มเหลว</span>';
            echo "<p class=\"text-danger\">Error: " . mysqli_connect_error() . "</p>";
        }
        ?>
    </div>

    <!-- ส่วนที่ 3: ตรวจสอบฟังก์ชันใน lib.php -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-3-circle"></i> ตรวจสอบฟังก์ชันจาก lib.php</h3>
        <?php
        try {
            $current_year = get_current_year();
            $current_term = get_current_term();
            $last_year = get_last_year();
            $last_term = get_last_term();
            
            echo '<span class="status-badge status-success">✓ ฟังก์ชันทำงานปกติ</span>';
            echo "<table class='table table-sm mt-3'>";
            echo "<tr><td><strong>ปีการศึกษาปัจจุบัน:</strong></td><td>$current_year</td></tr>";
            echo "<tr><td><strong>ภาคเรียนปัจจุบัน:</strong></td><td>$current_term</td></tr>";
            echo "<tr><td><strong>ปีการศึกษาก่อนหน้า:</strong></td><td>$last_year</td></tr>";
            echo "<tr><td><strong>ภาคเรียนก่อนหน้า:</strong></td><td>$last_term</td></tr>";
            echo "</table>";
        } catch (Exception $e) {
            echo '<span class="status-badge status-error">✗ ฟังก์ชันมีปัญหา</span>';
            echo "<p class=\"text-danger mt-2\">Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- ส่วนที่ 4: ตรวจสอบเกณฑ์การประเมิน -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-4-circle"></i> ตรวจสอบเกณฑ์การประเมิน (Teacher)</h3>
        <?php
        $sql = "SELECT * FROM evaluation_criteria WHERE category = 'Teacher' AND status = 'active' ORDER BY criterion_id";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $count = mysqli_num_rows($result);
            echo '<span class="status-badge status-success">✓ พบเกณฑ์การประเมิน ' . $count . ' รายการ</span>';
            
            if ($count > 0) {
                echo "<table class='table table-striped mt-3'>";
                echo "<thead><tr><th>ID</th><th>ชื่อเกณฑ์</th><th>สถานะ</th></tr></thead><tbody>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>{$row['criterion_id']}</td>";
                    echo "<td>{$row['criterion_name']}</td>";
                    echo "<td><span class='badge bg-success'>{$row['status']}</span></td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo '<div class="alert alert-warning mt-3">ไม่พบเกณฑ์การประเมินครูผู้สอน กรุณาเพิ่มข้อมูลในตาราง evaluation_criteria</div>';
            }
        } else {
            echo '<span class="status-badge status-error">✗ Query ล้มเหลว</span>';
            echo "<p class=\"text-danger mt-2\">Error: " . mysqli_error($conn) . "</p>";
        }
        ?>
    </div>

    <!-- ส่วนที่ 5: ตรวจสอบข้อมูลนักเรียน -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-5-circle"></i> ตรวจสอบข้อมูลนักเรียน</h3>
        <?php
        $student_id = $_SESSION['student_id'];
        $sql = "SELECT s.*, u.username, u.email 
                FROM students s 
                INNER JOIN users u ON s.user_id = u.user_id 
                WHERE s.student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo '<span class="status-badge status-success">✓ พบข้อมูลนักเรียน</span>';
            echo "<table class='table table-sm mt-3'>";
            echo "<tr><td><strong>รหัสนักเรียน:</strong></td><td>{$row['student_code']}</td></tr>";
            echo "<tr><td><strong>ชื่อ-นามสกุล:</strong></td><td>{$row['first_name']} {$row['last_name']}</td></tr>";
            echo "<tr><td><strong>ระดับชั้น:</strong></td><td>{$row['grade_level']}</td></tr>";
            echo "<tr><td><strong>Username:</strong></td><td>{$row['username']}</td></tr>";
            echo "</table>";
        } else {
            echo '<span class="status-badge status-error">✗ ไม่พบข้อมูลนักเรียน</span>';
        }
        mysqli_stmt_close($stmt);
        ?>
    </div>

    <!-- ส่วนที่ 6: ตรวจสอบตาราง weekly_schedule -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-6-circle"></i> ตรวจสอบตาราง weekly_schedule</h3>
        <?php
        $student_id = $_SESSION['student_id'];
        $sql = "SELECT COUNT(*) as count FROM weekly_schedule WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
        
        if ($count > 0) {
            echo '<span class="status-badge status-success">✓ พบข้อมูล ' . $count . ' รายการ</span>';
            
            // แสดงตัวอย่างข้อมูล
            $sql2 = "SELECT ws.*, s.subject_name, s.subject_code 
                    FROM weekly_schedule ws 
                    INNER JOIN subjects s ON ws.subject_id = s.subject_id 
                    WHERE ws.student_id = ? 
                    LIMIT 5";
            $stmt2 = mysqli_prepare($conn, $sql2);
            mysqli_stmt_bind_param($stmt2, 'i', $student_id);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            
            echo "<table class='table table-striped mt-3'>";
            echo "<thead><tr><th>วิชา</th><th>รหัสวิชา</th><th>วัน</th><th>คาบ</th></tr></thead><tbody>";
            while ($row2 = mysqli_fetch_assoc($result2)) {
                echo "<tr>";
                echo "<td>{$row2['subject_name']}</td>";
                echo "<td>{$row2['subject_code']}</td>";
                echo "<td>{$row2['day_of_week']}</td>";
                echo "<td>{$row2['period_number']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            mysqli_stmt_close($stmt2);
        } else {
            echo '<span class="status-badge status-error">✗ ไม่พบข้อมูล weekly_schedule</span>';
            echo '<div class="alert alert-danger mt-3">';
            echo '<strong>ปัญหา:</strong> ตาราง weekly_schedule ไม่มีข้อมูลของนักเรียนคนนี้<br>';
            echo 'กรุณาเพิ่มข้อมูลตารางเรียนก่อนใช้งานระบบประเมิน';
            echo '</div>';
        }
        mysqli_stmt_close($stmt);
        ?>
    </div>

    <!-- ส่วนที่ 7: ตรวจสอบรายวิชาที่เรียน -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-7-circle"></i> ตรวจสอบรายวิชาที่เรียนในภาคก่อนหน้า</h3>
        <?php
        $student_id = $_SESSION['student_id'];
        $last_year = get_last_year();
        $last_term = get_last_term();
        
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
        mysqli_stmt_bind_param($stmt, 'iss', $student_id, $last_year, $last_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $count = mysqli_num_rows($result);
        
        if ($count > 0) {
            echo '<span class="status-badge status-success">✓ พบ ' . $count . ' วิชา</span>';
            echo "<p class='debug-info mt-2'>ภาคเรียนที่ $last_term/$last_year</p>";
            
            echo "<table class='table table-hover mt-3'>";
            echo "<thead class='table-light'><tr><th>รหัสวิชา</th><th>ชื่อวิชา</th><th>ครูผู้สอน</th><th>ภาควิชา</th></tr></thead><tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['subject_code']}</td>";
                echo "<td>{$row['subject_name']}</td>";
                echo "<td><i class='bi bi-person'></i> {$row['teacher_name']}</td>";
                echo "<td>{$row['department']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo '<span class="status-badge status-error">✗ ไม่พบรายวิชา</span>';
            echo '<div class="alert alert-danger mt-3">';
            echo "<strong>ปัญหา:</strong> ไม่พบรายวิชาที่เรียนในภาคเรียนที่ $last_term/$last_year<br><br>";
            echo '<strong>สาเหตุที่เป็นไปได้:</strong><ul>';
            echo '<li>ไม่มีข้อมูลใน student_grades สำหรับภาคเรียนนี้</li>';
            echo '<li>ไม่มีข้อมูลใน weekly_schedule เชื่อมโยงนักเรียน-วิชา-ครู</li>';
            echo '<li>ค่า academic_year หรือ term ไม่ตรงกัน</li>';
            echo '</ul>';
            echo '</div>';
        }
        mysqli_stmt_close($stmt);
        ?>
    </div>

    <!-- ส่วนที่ 8: ทดสอบ API check_teacher_evaluation.php -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-8-circle"></i> ทดสอบ API: check_teacher_evaluation.php</h3>
        <button class="btn btn-primary" onclick="testCheckAPI()">
            <i class="bi bi-play-circle"></i> ทดสอบ API
        </button>
        <div id="api-result" class="mt-3"></div>
    </div>

    <!-- ส่วนที่ 9: ทดสอบการบันทึก -->
    <div class="test-section">
        <h3 class="test-title"><i class="bi bi-9-circle"></i> ทดสอบฟังก์ชันประเมิน</h3>
        <button class="btn btn-success" onclick="openEvaluation()">
            <i class="bi bi-star"></i> เปิดฟอร์มประเมิน
        </button>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    async function testCheckAPI() {
        const resultDiv = document.getElementById('api-result');
        resultDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        
        try {
            const response = await fetch('check_teacher_evaluation.php');
            const text = await response.text();
            
            // ลองแปลง JSON
            let json;
            try {
                json = JSON.parse(text);
            } catch (e) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Response ไม่ใช่ JSON</strong><br>
                        <pre>${text}</pre>
                    </div>
                `;
                return;
            }
            
            // แสดงผลลัพธ์
            if (json.error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <strong>⚠️ API คืนค่า Error:</strong><br>
                        ${json.error}
                    </div>
                `;
            } else if (json.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <strong>✓ API ทำงานสำเร็จ</strong><br>
                        <ul class="mt-2 mb-0">
                            <li>เกณฑ์การประเมิน: ${json.criteria.length} รายการ</li>
                            <li>รายวิชา: ${json.subjects.length} วิชา</li>
                            <li>สถานะการประเมิน: ${json.evaluations.length} รายการ</li>
                        </ul>
                    </div>
                    <details class="mt-2">
                        <summary style="cursor:pointer" class="text-primary">คลิกดู JSON Response</summary>
                        <pre class="mt-2">${JSON.stringify(json, null, 2)}</pre>
                    </details>
                `;
            }
            
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ เกิดข้อผิดพลาด:</strong><br>
                    ${error.message}
                </div>
            `;
        }
    }
    
    function openEvaluation() {
        window.location.href = 'studentDashboard.php';
    }
</script>
</body>
</html>
