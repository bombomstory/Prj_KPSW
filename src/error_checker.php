<?php
// เปิดการแสดง Error ทั้งหมด
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
session_start();
// ตั้งค่า Session สำหรับทดสอบ
$_SESSION['user_id'] = 1;
$_SESSION['student_id'] = 1;
$_SESSION['first_name'] = 'นักเรียน';
$_SESSION['last_name'] = 'ทดสอบ';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Checker - ระบบประเมินครูผู้สอน</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            text-align: center;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .test-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 0.9em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .status-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .status-error {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        .status-warning {
            background: linear-gradient(135deg, #f09819 0%, #edde5d 100%);
            color: #333;
        }
        .status-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin: 15px 0;
            max-height: 400px;
            border-left: 4px solid #007acc;
        }
        .error-message {
            background: #fee;
            border-left: 4px solid #dc3545;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #721c24;
        }
        .success-message {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #155724;
        }
        .info-message {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #0c5460;
        }
        .warning-message {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            color: #856404;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .details-table tr:hover {
            background: #f5f5f5;
        }
        .icon-check { color: #28a745; }
        .icon-error { color: #dc3545; }
        .icon-warning { color: #ffc107; }
        .icon-info { color: #17a2b8; }

        .progress-bar-custom {
        height: 30px;
        border-radius: 15px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn-test {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-test:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .collapsible {
        cursor: pointer;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        margin: 10px 0;
    }
    
    .collapsible:hover {
        background: #e9ecef;
    }
    
    .collapsible-content {
        display: none;
        padding: 15px;
        border-left: 3px solid #007bff;
        margin-left: 10px;
    }
    
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
</head>
<body>
    <div class="error-container">
        <!-- Header -->
        <div class="header-section">
            <h1><i class="fas fa-bug"></i> Error Checker</h1>
            <p class="text-muted">ระบบตรวจสอบข้อผิดพลาดสำหรับการประเมินครูผู้สอน</p>
            <div class="mt-3">
                <span class="badge bg-primary">Student ID: <?= $_SESSION['student_id'] ?></span>
                <span class="badge bg-info">User ID: <?= $_SESSION['user_id'] ?></span>
                <span class="badge bg-success">Session Active</span>
            </div>
        </div>

        <?php
    $total_tests = 0;
    $passed_tests = 0;
    $failed_tests = 0;
    $warnings = 0;

    // ฟังก์ชันช่วยเหลือ
    function renderStatusBadge($status, $message) {
        $icons = [
            'success' => 'fa-check-circle',
            'error' => 'fa-times-circle',
            'warning' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle'
        ];
        $icon = $icons[$status] ?? 'fa-question-circle';
        return "<span class='status-badge status-{$status}'><i class='fas {$icon}'></i> {$message}</span>";
    }

    function renderMessage($type, $message) {
        return "<div class='{$type}-message'><i class='fas fa-" . 
               ($type === 'error' ? 'times' : ($type === 'success' ? 'check' : ($type === 'warning' ? 'exclamation-triangle' : 'info'))) . 
               "-circle'></i> {$message}</div>";
    }
    ?>

    <!-- Test 1: ตรวจสอบไฟล์ที่จำเป็น -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-file-code"></i> Test 1: ตรวจสอบไฟล์ที่จำเป็น
            </div>
            <?php
            $required_files = [
                'db_connection.php' => 'ไฟล์เชื่อมต่อฐานข้อมูล',
                'lib.php' => 'ไฟล์ฟังก์ชันช่วยเหลือ',
                'check_teacher_evaluation.php' => 'API ตรวจสอบการประเมิน',
                'submit_teacher_evaluation.php' => 'API บันทึกการประเมิน',
                'studentDashboard.php' => 'หน้าแดชบอร์ด'
            ];
            
            $all_files_exist = true;
            foreach ($required_files as $file => $desc) {
                if (!file_exists($file)) {
                    $all_files_exist = false;
                    break;
                }
            }
            $total_tests++;
            if ($all_files_exist) {
                $passed_tests++;
                echo renderStatusBadge('success', 'ผ่าน');
            } else {
                $failed_tests++;
                echo renderStatusBadge('error', 'ไม่ผ่าน');
            }
            ?>
        </div>
        
        <table class="details-table">
            <thead>
                <tr>
                    <th>ไฟล์</th>
                    <th>คำอธิบาย</th>
                    <th>สถานะ</th>
                    <th>ขนาด</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($required_files as $file => $desc): ?>
                <tr>
                    <td><code><?= $file ?></code></td>
                    <td><?= $desc ?></td>
                    <td>
                        <?php if (file_exists($file)): ?>
                            <i class="fas fa-check-circle icon-check"></i> มีไฟล์
                        <?php else: ?>
                            <i class="fas fa-times-circle icon-error"></i> ไม่พบไฟล์
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        if (file_exists($file)) {
                            echo number_format(filesize($file) / 1024, 2) . ' KB';
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Test 2: ตรวจสอบการเชื่อมต่อฐานข้อมูล -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-database"></i> Test 2: ตรวจสอบการเชื่อมต่อฐานข้อมูล
            </div>
            <?php
            $total_tests++;
            $db_error = null;
            $conn = null;
            
            try {
                if (file_exists('db_connection.php')) {
                    include('db_connection.php');
                    
                    if ($conn && mysqli_ping($conn)) {
                        $passed_tests++;
                        echo renderStatusBadge('success', 'เชื่อมต่อสำเร็จ');
                    } else {
                        $failed_tests++;
                        $db_error = mysqli_connect_error();
                        echo renderStatusBadge('error', 'เชื่อมต่อล้มเหลว');
                    }
                } else {
                    $failed_tests++;
                    echo renderStatusBadge('error', 'ไม่พบไฟล์ db_connection.php');
                }
            } catch (Exception $e) {
                $failed_tests++;
                $db_error = $e->getMessage();
                echo renderStatusBadge('error', 'เกิดข้อผิดพลาด');
            }
            ?>
        </div>
        
        <?php if ($conn && mysqli_ping($conn)): ?>
            <?php
            $server_info = mysqli_get_server_info($conn);
            $host_info = mysqli_get_host_info($conn);
            $db_name = mysqli_query($conn, "SELECT DATABASE() as db");
            $db_name_result = mysqli_fetch_assoc($db_name);
            ?>
            <?= renderMessage('success', 'เชื่อมต่อฐานข้อมูลสำเร็จ') ?>
            <table class="details-table">
                <tr>
                    <th>Server Version</th>
                    <td><?= $server_info ?></td>
                </tr>
                <tr>
                    <th>Host Info</th>
                    <td><?= $host_info ?></td>
                </tr>
                <tr>
                    <th>Database</th>
                    <td><?= $db_name_result['db'] ?></td>
                </tr>
                <tr>
                    <th>Character Set</th>
                    <td><?= mysqli_character_set_name($conn) ?></td>
                </tr>
            </table>
        <?php else: ?>
            <?= renderMessage('error', 'ไม่สามารถเชื่อมต่อฐานข้อมูล') ?>
            <?php if ($db_error): ?>
                <div class="error-message">
                    <strong>Error:</strong> <?= htmlspecialchars($db_error) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Test 3: ตรวจสอบฟังก์ชันใน lib.php -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-code"></i> Test 3: ตรวจสอบฟังก์ชันใน lib.php
            </div>
            <?php
            $total_tests++;
            $lib_error = null;
            $lib_functions_work = false;
            
            try {
                if (file_exists('lib.php')) {
                    include_once('lib.php');
                    
                    $required_functions = [
                        'get_current_year',
                        'get_current_term',
                        'get_last_year',
                        'get_last_term'
                    ];
                    
                    $all_functions_exist = true;
                    foreach ($required_functions as $func) {
                        if (!function_exists($func)) {
                            $all_functions_exist = false;
                            break;
                        }
                    }
                    
                    if ($all_functions_exist) {
                        $current_year = get_current_year();
                        $current_term = get_current_term();
                        $last_year = get_last_year();
                        $last_term = get_last_term();
                        
                        if ($current_year && $current_term && $last_year && $last_term) {
                            $passed_tests++;
                            $lib_functions_work = true;
                            echo renderStatusBadge('success', 'ทำงานปกติ');
                        } else {
                            $failed_tests++;
                            echo renderStatusBadge('error', 'ฟังก์ชันคืนค่าไม่ถูกต้อง');
                        }
                    } else {
                        $failed_tests++;
                        echo renderStatusBadge('error', 'ฟังก์ชันไม่ครบ');
                    }
                } else {
                    $failed_tests++;
                    echo renderStatusBadge('error', 'ไม่พบไฟล์ lib.php');
                }
            } catch (Exception $e) {
                $failed_tests++;
                $lib_error = $e->getMessage();
                echo renderStatusBadge('error', 'เกิดข้อผิดพลาด');
            }
            ?>
        </div>
        
        <?php if ($lib_functions_work): ?>
            <?= renderMessage('success', 'ฟังก์ชันทำงานถูกต้อง') ?>
            <table class="details-table">
                <tr>
                    <th>ฟังก์ชัน</th>
                    <th>ค่าที่คืน</th>
                    <th>สถานะ</th>
                </tr>
                <tr>
                    <td><code>get_current_year()</code></td>
                    <td><strong><?= $current_year ?></strong></td>
                    <td><i class="fas fa-check-circle icon-check"></i></td>
                </tr>
                <tr>
                    <td><code>get_current_term()</code></td>
                    <td><strong><?= $current_term ?></strong></td>
                    <td><i class="fas fa-check-circle icon-check"></i></td>
                </tr>
                <tr>
                    <td><code>get_last_year()</code></td>
                    <td><strong><?= $last_year ?></strong></td>
                    <td><i class="fas fa-check-circle icon-check"></i></td>
                </tr>
                <tr>
                    <td><code>get_last_term()</code></td>
                    <td><strong><?= $last_term ?></strong></td>
                    <td><i class="fas fa-check-circle icon-check"></i></td>
                </tr>
            </table>
        <?php else: ?>
            <?= renderMessage('error', 'ฟังก์ชันมีปัญหา') ?>
            <?php if ($lib_error): ?>
                <div class="error-message">
                    <strong>Error:</strong> <?= htmlspecialchars($lib_error) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($conn && mysqli_ping($conn)): ?>
    
    <!-- Test 4: ตรวจสอบตารางฐานข้อมูล -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-table"></i> Test 4: ตรวจสอบตารางฐานข้อมูล
            </div>
            <?php
            $total_tests++;
            $required_tables = [
                'evaluation_criteria' => 'เกณฑ์การประเมิน',
                'teacher_evaluation_responses' => 'คำตอบการประเมินครู',
                'students' => 'ข้อมูลนักเรียน',
                'teachers' => 'ข้อมูลครู',
                'subjects' => 'รายวิชา',
                'student_grades' => 'เกรดนักเรียน',
                'weekly_schedule' => 'ตารางเรียน',
                'users' => 'ผู้ใช้งาน'
            ];
            
            $all_tables_exist = true;
            $table_info = [];
            
            foreach ($required_tables as $table => $desc) {
                $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
                $exists = mysqli_num_rows($result) > 0;
                
                if (!$exists) {
                    $all_tables_exist = false;
                }
                
                $count = 0;
                if ($exists) {
                    $count_result = mysqli_query($conn, "SELECT COUNT(*) as c FROM `$table`");
                    if ($count_result) {
                        $count = mysqli_fetch_assoc($count_result)['c'];
                    }
                }
                
                $table_info[$table] = [
                    'exists' => $exists,
                    'count' => $count,
                    'desc' => $desc
                ];
            }
            
            if ($all_tables_exist) {
                $passed_tests++;
                echo renderStatusBadge('success', 'ตารางครบถ้วน');
            } else {
                $failed_tests++;
                echo renderStatusBadge('error', 'ตารางไม่ครบ');
            }
            ?>
        </div>
        
        <table class="details-table">
            <thead>
                <tr>
                    <th>ตาราง</th>
                    <th>คำอธิบาย</th>
                    <th>สถานะ</th>
                    <th>จำนวนแถว</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($table_info as $table => $info): ?>
                <tr>
                    <td><code><?= $table ?></code></td>
                    <td><?= $info['desc'] ?></td>
                    <td>
                        <?php if ($info['exists']): ?>
                            <i class="fas fa-check-circle icon-check"></i> มี
                        <?php else: ?>
                            <i class="fas fa-times-circle icon-error"></i> ไม่มี
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($info['exists']): ?>
                            <span class="badge <?= $info['count'] > 0 ? 'bg-success' : 'bg-warning' ?>">
                                <?= number_format($info['count']) ?> แถว
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php
        // เตือนถ้าตารางว่าง
        $empty_tables = [];
        foreach ($table_info as $table => $info) {
            if ($info['exists'] && $info['count'] == 0 && $table !== 'teacher_evaluation_responses') {
                $empty_tables[] = $table;
            }
        }
        
        if (!empty($empty_tables)):
            $warnings++;
        ?>
            <?= renderMessage('warning', 'มีตารางที่ไม่มีข้อมูล: ' . implode(', ', $empty_tables)) ?>
        <?php endif; ?>
    </div>

    <!-- Test 5: ตรวจสอบเกณฑ์การประเมิน -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-clipboard-list"></i> Test 5: ตรวจสอบเกณฑ์การประเมินครูผู้สอน
            </div>
            <?php
            $total_tests++;
            $sql = "SELECT * FROM evaluation_criteria WHERE category = 'Teacher' AND status = 'active'";
            $result = mysqli_query($conn, $sql);
            $criteria_count = 0;
            $criteria = [];
            
            if ($result) {
                $criteria_count = mysqli_num_rows($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    $criteria[] = $row;
                }
            }
            
            if ($criteria_count > 0) {
                $passed_tests++;
                echo renderStatusBadge('success', "พบ {$criteria_count} เกณฑ์");
            } else {
                $failed_tests++;
                echo renderStatusBadge('error', 'ไม่พบเกณฑ์');
            }
            ?>
        </div>
        
        <?php if ($criteria_count > 0): ?>
            <?= renderMessage('success', "พบเกณฑ์การประเมินครูผู้สอน {$criteria_count} รายการ") ?>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อเกณฑ์</th>
                        <th>ประเภท</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteria as $c): ?>
                    <tr>
                        <td><?= $c['criterion_id'] ?></td>
                        <td><?= htmlspecialchars($c['criterion_name']) ?></td>
                        <td><span class="badge bg-info"><?= $c['category'] ?></span></td>
                        <td><span class="badge bg-success"><?= $c['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <?= renderMessage('error', 'ไม่พบเกณฑ์การประเมินครูผู้สอน') ?>
            <div class="warning-message">
                <strong>วิธีแก้ไข:</strong> รัน SQL ต่อไปนี้เพื่อเพิ่มเกณฑ์การประเมิน:
                <div class="code-block">INSERT INTO evaluation_criteria (criterion_id, criterion_name, category, status) VALUES(6, 'ความรู้ความสามารถ', 'Teacher', 'active'),
(7, 'การสื่อสาร', 'Teacher', 'active'),
(8, 'การจัดการชั้นเรียน', 'Teacher', 'active'),
(9, 'ความรับผิดชอบ', 'Teacher', 'active'),
(10, 'ทัศนคติและความเป็นมืออาชีพ', 'Teacher', 'active');</div>
</div>
<?php endif; ?>
</div>
<!-- Test 6: ตรวจสอบข้อมูล weekly_schedule -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-calendar-alt"></i> Test 6: ตรวจสอบตาราง weekly_schedule
            </div>
            <?php
            $total_tests++;
            $student_id = $_SESSION['student_id'];
            $sql = "SELECT COUNT(*) as c FROM weekly_schedule WHERE student_id = $student_id";
            $result = mysqli_query($conn, $sql);
            $schedule_count = 0;
            
            if ($result) {
                $schedule_count = mysqli_fetch_assoc($result)['c'];
            }
            
            if ($schedule_count > 0) {
                $passed_tests++;
                echo renderStatusBadge('success', "พบ {$schedule_count} รายการ");
            } else {
                $failed_tests++;
                echo renderStatusBadge('error', 'ไม่พบข้อมูล');
            }
            ?>
        </div>
        
        <?php if ($schedule_count > 0): ?>
            <?= renderMessage('success', "พบข้อมูลตารางเรียนของนักเรียน ID {$student_id} จำนวน {$schedule_count} รายการ") ?>
            <?php
            $sql = "SELECT ws.*, s.subject_code, s.subject_name 
                    FROM weekly_schedule ws
                    INNER JOIN subjects s ON ws.subject_id = s.subject_id
                    WHERE ws.student_id = $student_id
                    LIMIT 10";
            $result = mysqli_query($conn, $sql);
?>
<table class="details-table">
<thead>
<tr>
<th>รหัสวิชา</th>
<th>ชื่อวิชา</th>
<th>Teacher ID</th>
<th>วัน</th>
<th>คาบ</th>
</tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $row['subject_code'] ?></td>
<td><?= htmlspecialchars($row['subject_name']) ?></td>
<td><?= $row['teacher_id'] ?></td>
<td><?= $row['day_of_week'] ?></td>
<td><?= $row['period_number'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<?= renderMessage('error', 'ไม่พบข้อมูลตาราง weekly_schedule สำหรับนักเรียนคนนี้') ?>
<div class="warning-message">
<strong>วิธีแก้ไข:</strong> ใช้ไฟล์ <code>fix_weekly_schedule.php</code> เพื่อเพิ่มข้อมูลอัตโนมัติ
<br>หรือรัน SQL เพื่อเพิ่มข้อมูลตารางเรียน
</div>
<?php endif; ?>
</div>
<!-- Test 7: ทดสอบ Query หลัก -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-search"></i> Test 7: ทดสอบ Query ดึงรายวิชาและครูผู้สอน
            </div>
            <?php
            $total_tests++;
            $student_id = $_SESSION['student_id'];
            
            if (function_exists('get_last_year') && function_exists('get_last_term')) {
                $last_year = get_last_year();
                $last_term = get_last_term();
            } else {
                $last_year = '2567';
                $last_term = '2';
            }
            
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
                WHERE sg.student_id = $student_id
                    AND sg.academic_year = '$last_year'
                    AND sg.term = $last_term
                ORDER BY s.subject_code
            ";
            
            $result = mysqli_query($conn, $sql);
            $query_error = null;
            $subjects_found = 0;
            $subjects_data = [];
            
            if ($result) {
                $subjects_found = mysqli_num_rows($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    $subjects_data[] = $row;
                }
            } else {
                $query_error = mysqli_error($conn);
            }
            
            if ($subjects_found > 0) {
                $passed_tests++;
                echo renderStatusBadge('success', "พบ {$subjects_found} วิชา");
            } else {
                $failed_tests++;
                echo renderStatusBadge('error', 'ไม่พบรายวิชา');
            }
            ?>
        </div>
        
        <div class="info-message">
            <strong>Query Parameters:</strong><br>
            Student ID: <?= $student_id ?><br>
            Academic Year: <?= $last_year ?><br>
            Term: <?= $last_term ?>
        </div>
        
        <?php if ($query_error): ?>
            <?= renderMessage('error', 'Query ล้มเหลว') ?>
            <div class="error-message">
                <strong>MySQL Error:</strong><br>
                <?= htmlspecialchars($query_error) ?>
            </div>
            <div class="collapsible" onclick="toggleCollapse('query-sql')">
                <i class="fas fa-code"></i> คลิกเพื่อดู SQL Query
            </div>
            <div id="query-sql" class="collapsible-content">
                <div class="code-block"><?= htmlspecialchars($sql) ?></div>
            </div>
        <?php elseif ($subjects_found > 0): ?>
            <?= renderMessage('success', "พบรายวิชาที่นักเรียนเรียนในภาคเรียนที่ {$last_term}/{$last_year} จำนวน {$subjects_found} วิชา") ?>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>รหัสวิชา</th>
                        <th>ชื่อวิชา</th>
                        <th>ครูผู้สอน</th>
                        <th>ภาควิชา</th>
                        <th>รูปโปรไฟล์</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects_data as $row): ?>
                    <tr>
                        <td><strong><?= $row['subject_code'] ?></strong></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td>
                            <?php if ($row['profile_picture']): ?>
                                <i class="fas fa-check-circle icon-check"></i> <?= $row['profile_picture'] ?>
                            <?php else: ?>
                                <i class="fas fa-times-circle icon-error"></i> ไม่มี
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <?= renderMessage('error', "ไม่พบรายวิชาที่เรียนในภาคเรียนที่ {$last_term}/{$last_year}") ?>
            
            <!-- แสดงข้อมูลสำหรับ Debug -->
            <div class="collapsible" onclick="toggleCollapse('debug-info')">
                <i class="fas fa-bug"></i> คลิกเพื่อดูข้อมูล Debug
            </div>
            <div id="debug-info" class="collapsible-content">
                <?php
                // ตรวจสอบแต่ละตาราง
                $debug_queries = [
                    'student_grades' => "SELECT COUNT(*) as c FROM student_grades WHERE student_id = $student_id",
                    'student_grades (term)' => "SELECT COUNT(*) as c FROM student_grades WHERE student_id = $student_id AND academic_year = '$last_year' AND term = $last_term",
                    'weekly_schedule' => "SELECT COUNT(*) as c FROM weekly_schedule WHERE student_id = $student_id",
                    'teachers' => "SELECT COUNT(*) as c FROM teachers",
                    'subjects' => "SELECT COUNT(*) as c FROM subjects"
                ];
                
                echo '<table class="details-table">';
                echo '<thead><tr><th>ตาราง</th><th>จำนวนแถว</th><th>หมายเหตุ</th></tr></thead><tbody>';
                
                foreach ($debug_queries as $label => $query) {
                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $count = mysqli_fetch_assoc($result)['c'];
                        $note = $count > 0 ? '<i class="fas fa-check-circle icon-check"></i>' : '<i class="fas fa-exclamation-triangle icon-warning"></i> ว่างเปล่า';
                        echo "<tr><td>{$label}</td><td>{$count}</td><td>{$note}</td></tr>";
                    }
                }
                
                echo '</tbody></table>';
                ?>
            </div>
            
            <div class="warning-message">
                <strong>สาเหตุที่เป็นไปได้:</strong>
                <ul>
                    <li>ไม่มีข้อมูลใน <code>student_grades</code> สำหรับภาคเรียนที่ <?= $last_term ?>/<?= $last_year ?></li>
                    <li>ไม่มีข้อมูลใน <code>weekly_schedule</code> เชื่อมโยงนักเรียน-วิชา-ครู</li>
                    <li>ค่า <code>academic_year</code> หรือ <code>term</code> ไม่ตรงกัน</li>
                </ul>
                <strong>วิธีแก้ไข:</strong> ใช้ไฟล์ <code>fix_weekly_schedule.php</code> หรือ <code>insert_test_data.sql</code>
            </div>
        <?php endif; ?>
    </div>

    <!-- Test 8: ทดสอบ API check_teacher_evaluation.php -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-plug"></i> Test 8: ทดสอบ API check_teacher_evaluation.php
            </div>
            <button class="btn btn-primary btn-test" onclick="testCheckAPI()">
                <i class="fas fa-play"></i> ทดสอบ API
            </button>
        </div>
        
        <div id="api-test-result">
            <div class="info-message">
                <i class="fas fa-info-circle"></i> คลิกปุ่ม "ทดสอบ API" เพื่อเริ่มการทดสอบ
            </div>
        </div>
    </div>

    <!-- Test 9: ทดสอบ API submit_teacher_evaluation.php -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-save"></i> Test 9: ทดสอบ API submit_teacher_evaluation.php
            </div>
            <button class="btn btn-success btn-test" onclick="testSubmitAPI()">
                <i class="fas fa-flask"></i> ทดสอบการบันทึก
            </button>
        </div>
        
        <div id="submit-test-result">
            <div class="info-message">
                <i class="fas fa-info-circle"></i> คลิกปุ่ม "ทดสอบการบันทึก" เพื่อทดสอบ (ไม่บันทึกจริง)
            </div>
        </div>
    </div>

    <?php endif; // End if $conn ?>

    <!-- สรุปผลการทดสอบ -->
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-chart-pie"></i> สรุปผลการทดสอบ
            </div>
        </div>
        
        <?php
        $pass_percentage = $total_tests > 0 ? ($passed_tests / $total_tests) * 100 : 0;
        $status_color = $pass_percentage >= 80 ? 'success' : ($pass_percentage >= 50 ? 'warning' : 'danger');
        ?>
        
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $total_tests ?></h3>
                        <p class="text-muted mb-0">ทดสอบทั้งหมด</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3><?= $passed_tests ?></h3>
                        <p class="mb-0">ผ่าน</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h3><?= $failed_tests ?></h3>
                        <p class="mb-0">ไม่ผ่าน</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning">
                    <div class="card-body">
                        <h3><?= $warnings ?></h3>
                        <p class="mb-0">คำเตือน</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="progress mb-3" style="height: 30px;">
            <div class="progress-bar bg-<?= $status_color ?>" role="progressbar" 
                 style="width: <?= $pass_percentage ?>%">
                <?= number_format($pass_percentage, 1) ?>%
            </div>
        </div>
        
        <?php if ($pass_percentage >= 80): ?>
            <?= renderMessage('success', 'ระบบพร้อมใช้งาน! ผ่านการทดสอบมากกว่า 80%') ?>
            <div class="action-buttons">
                <a href="studentDashboard.php" class="btn btn-success btn-test">
                    <i class="fas fa-rocket"></i> ไปยังแดชบอร์ด
                </a>
            </div>
        <?php elseif ($pass_percentage >= 50): ?>
            <?= renderMessage('warning', 'ระบบใช้งานได้บางส่วน แต่ควรแก้ไขปัญหาที่พบ') ?>
            <div class="action-buttons">
                <a href="fix_weekly_schedule.php" class="btn btn-warning btn-test">
                    <i class="fas fa-wrench"></i> แก้ไขปัญหา
                </a>
                <a href="studentDashboard.php" class="btn btn-secondary btn-test">
                    <i class="fas fa-arrow-right"></i> ลองใช้งานดู
                </a>
            </div>
        <?php else: ?>
            <?= renderMessage('error', 'ระบบยังไม่พร้อมใช้งาน กรุณาแก้ไขปัญหาที่พบ') ?>
            <div class="action-buttons">
                <a href="check_database_structure.php" class="btn btn-info btn-test">
                    <i class="fas fa-database"></i> ตรวจสอบฐานข้อมูล
                </a>
                <a href="fix_weekly_schedule.php" class="btn btn-warning btn-test">
                    <i class="fas fa-wrench"></i> แก้ไขข้อมูล
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Error Log -->
    <?php if (file_exists(__DIR__ . '/error_log.txt')): ?>
    <div class="test-card">
        <div class="test-header">
            <div class="test-title">
                <i class="fas fa-file-alt"></i> Error Log (10 บรรทัดล่าสุด)
            </div>
        </div>
        
        <div class="code-block">
            <?php
$log_file = __DIR__ . '/error_log.txt';
$lines = file($log_file);
$last_lines = array_slice($lines, -10);
echo htmlspecialchars(implode('', $last_lines));
?>
</div>
    </div>
    <?php endif; ?>

</div>

<script>
    function toggleCollapse(id) {
        const element = document.getElementById(id);
        if (element.style.display === 'none' || element.style.display === '') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }
    
    async function testCheckAPI() {
        const resultDiv = document.getElementById('api-test-result');
        resultDiv.innerHTML = '<div class="spinner"></div><p class="text-center">กำลังทดสอบ API...</p>';
        
        try {
            const response = await fetch('check_teacher_evaluation.php');
            const text = await response.text();
            
            // ลอง parse JSON
            let json;
            let parseError = null;
            
            try {
                json = JSON.parse(text);
            } catch (e) {
                parseError = e.message;
            }
            
            if (parseError) {
                // Response ไม่ใช่ JSON
                resultDiv.innerHTML = `
                    <div class="error-message">
                        <strong><i class="fas fa-times-circle"></i> Response ไม่ใช่ JSON</strong><br>
                        Parse Error: ${parseError}
                    </div>
                    <div class="collapsible" onclick="toggleCollapse('raw-response')">
                        <i class="fas fa-code"></i> คลิกเพื่อดู Raw Response
                    </div>
                    <div id="raw-response" class="collapsible-content">
                        <div class="code-block">${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    </div>
                `;
            } else if (json.error) {
                // API คืนค่า Error
                resultDiv.innerHTML = `
                    <div class="warning-message">
                        <strong><i class="fas fa-exclamation-triangle"></i> API คืนค่า Error:</strong><br>
                        ${json.error}
                    </div>
                    <div class="collapsible" onclick="toggleCollapse('json-response')">
                        <i class="fas fa-code"></i> คลิกเพื่อดู JSON Response
                    </div>
                    <div id="json-response" class="collapsible-content">
                        <pre class="code-block">${JSON.stringify(json, null, 2)}</pre>
                    </div>
                `;
            } else if (json.success) {
                // API ทำงานสำเร็จ
                resultDiv.innerHTML = `
                    <div class="success-message">
                        <strong><i class="fas fa-check-circle"></i> API ทำงานสำเร็จ!</strong>
                    </div>
                    <table class="details-table">
                        <tr><th>เกณฑ์การประเมิน</th><td>${json.criteria.length} รายการ</td></tr>
                        <tr><th>รายวิชา</th><td>${json.subjects.length} วิชา</td></tr>
                        <tr><th>สถานะการประเมิน</th><td>${json.evaluations.length} รายการ</td></tr>
                        <tr><th>ปีการศึกษา</th><td>${json.academic_info.last_year}</td></tr>
                        <tr><th>ภาคเรียน</th><td>${json.academic_info.last_term}</td></tr>
                    </table>
                    <div class="collapsible" onclick="toggleCollapse('json-success')">
                        <i class="fas fa-code"></i> คลิกเพื่อดู JSON Response
                    </div>
                    <div id="json-success" class="collapsible-content">
                        <pre class="code-block">${JSON.stringify(json, null, 2)}</pre>
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="error-message">
                    <strong><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาด:</strong><br>
                    ${error.message}
                </div>
            `;
        }
    }
    
    async function testSubmitAPI() {
        const resultDiv = document.getElementById('submit-test-result');
        resultDiv.innerHTML = '<div class="spinner"></div><p class="text-center">กำลังทดสอบ API...</p>';
        
        // สร้าง payload ทดสอบ
        const testPayload = {
            teacher_id: 1,
            subject_id: 1,
            academic_year: '2567',
            semester: '2',
            responses: [
                { criterion_id: 6, rating: 5, comments: 'ทดสอบระบบ' },
                { criterion_id: 7, rating: 4, comments: null }
            ],
            comments: 'นี่คือการทดสอบระบบ'
        };
        
        try {
            const response = await fetch('submit_teacher_evaluation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(testPayload)
            });
            
            const text = await response.text();
            let json;
            
            try {
                json = JSON.parse(text);
            } catch (e) {
                resultDiv.innerHTML = `
                    <div class="error-message">
                        <strong><i class="fas fa-times-circle"></i> Response ไม่ใช่ JSON</strong><br>
                        ${text.substring(0, 500)}
                    </div>
                `;
                return;
            }
            
            if (json.success) {
                resultDiv.innerHTML = `
                    <div class="success-message">
                        <strong><i class="fas fa-check-circle"></i> API ทำงานได้!</strong><br>
                        ${json.message}
                    </div>
                    <div class="info-message">
                        <strong>หมายเหตุ:</strong> นี่เป็นการทดสอบ API ข้อมูลอาจถูกบันทึกจริง<br>
                        กรุณาตรวจสอบตาราง <code>teacher_evaluation_responses</code>
                    </div>
                    <pre class="code-block">${JSON.stringify(json, null, 2)}</pre>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="warning-message">
                        <strong><i class="fas fa-exclamation-triangle"></i> API คืนค่า Error:</strong><br>
                        ${json.error || 'Unknown error'}
                    </div>
                    <pre class="code-block">${JSON.stringify(json, null, 2)}</pre>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="error-message">
                    <strong><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาด:</strong><br>
                    ${error.message}
                </div>
            `;
        }
    }
</script>
</body>
</html>