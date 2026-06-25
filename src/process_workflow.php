<?php
// process_workflow.php (ไฟล์รับเรื่องประมวลผลเวิร์กโฟลว์ด้วย mysqli)
session_start();
if (!isset($_SESSION['user_id'])) {
    die("ไม่มีสิทธิ์การเข้าถึงระบบค่ะ");
}

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id    = intval($_POST['complaint_id']);
    $workflow_option = isset($_POST['workflow_option']) ? $_POST['workflow_option'] : 'resolve';
    $notes           = trim($_POST['notes']);
    $actor_id        = intval($_SESSION['user_id']); // รหัสคุณครูคนปัจจุบัน

    // ตั้งค่าตัวแปรอัปเดตกระบวนการเริ่มต้น
    $new_status = 'ดำเนินการเสร็จสิ้น';
    $current_role = 'none';
    $current_assignee_id = null;
    $forwarded_to_name = 'เสร็จสิ้นสิ้นสุดกระบวนการช่วยเหลือ';
    $action_type = 'ช่วยเหลือเสร็จสิ้น';

    // ลอจิกจำแนกเงื่อนไขตาม Step 2.1, 2.2 และ Step 3
    if ($workflow_option === 'forward_internal') {
        $target_teacher_id = intval($_POST['target_teacher_id']);
        
        // ดึงชื่อครูปรับเข้า Log Timeline
        $t_res = $conn->query("SELECT name FROM teachers WHERE id = $target_teacher_id");
        $t_data = $t_res->fetch_assoc();
        
        $new_status = 'ส่งต่อบุคคลภายใน';
        $current_role = 'internal';
        $current_assignee_id = $target_teacher_id;
        $forwarded_to_name = 'คุณครู ' . $t_data['name'];
        $action_type = 'ส่งต่อภายใน';

    } else if ($workflow_option === 'forward_external') {
        $target_agency_id = intval($_POST['target_agency_id']);
        
        // ดึงชื่อหน่วยงานภายนอกเข้า Log Timeline
        $ag_res = $conn->query("SELECT name FROM external_agencies WHERE id = $target_agency_id");
        $ag_data = $ag_res->fetch_assoc();

        $new_status = 'ส่งต่อหน่วยงานภายนอก';
        $current_role = 'external';
        $current_assignee_id = $target_agency_id;
        $forwarded_to_name = $ag_data['name'];
        $action_type = 'ส่งต่อภายนอก';
    }

    // เริ่มการทำ Transaction ป้องกันข้อมูลเพี้ยน
    $conn->begin_transaction();

    try {
        // 1. อัปเดตสถานะและบทบาทผู้รับไม้ต่อในตาราง complaints หลัก
        $up_sql = "UPDATE `complaints` SET `status` = ?, `current_role` = ?, `current_assignee_id` = ?, `staff_comment` = ? WHERE `id` = ?";
        $up_stmt = $conn->prepare($up_sql);
        $up_stmt->bind_param("ssisi", $new_status, $current_role, $current_assignee_id, $notes, $complaint_id);
        $up_stmt->execute();
        $up_stmt->close();

        // 2. บันทึกประวัติลงตาราง Timeline Log
        $log_sql = "INSERT INTO `complaint_workflow_logs` (`complaint_id`, `actor_id`, `action_type`, `notes`, `forwarded_to_name`) VALUES (?, ?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iisss", $complaint_id, $actor_id, $action_type, $notes, $forwarded_to_name);
        $log_stmt->execute();
        $log_stmt->close();

        // ยืนยันข้อมูลสำเร็จ
        $conn->commit();
        
        echo "<script>
                alert('อัปเดตและดำเนินกระบวนการส่งต่อ Work Flow สำเร็จเรียบร้อยแล้วค่ะ');
                window.location.href = 'complaint_history.php'; 
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "เกิดข้อผิดพลาดทางระบบคิวรี่: " . $e->getMessage();
    }
}
?>