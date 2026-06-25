<?php
// print_portfolio.php - ระบบคอมไพล์สรุปรูปเล่มภาคผนวกใบประกาศ Portfolio สำหรับพิมพ์/เซดเป็น PDF
session_start();
include('db_connection.php');
include('lib.php');

if (!isset($_SESSION['user_id'])) {
    die("กรุณาล็อกอินเข้าสู่ระบบก่อนค่ะ");
}

$student_user_id = intval($_SESSION['user_id']);
$student_id = getStudentID($student_user_id);
$student_classroom = getStudentClassroom($student_user_id);

// 🔍 ดึงประวัติกิจกรรมและรูปใบประกาศทั้งหมดของนักเรียน
$activities = [];
$sql = "SELECT * FROM `student_activities` WHERE `student_id` = ? ORDER BY `academic_year` DESC, `term` DESC, `activity_date` DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $activities[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คลังใบประกาศเกียรติบัตรแนบ Portfolio - <?=$_SESSION['first_name'];?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f1f3f5; color: #333; margin: 0; padding: 0; }
        .no-print-banner { background: #343a40; color: white; padding: 15px; text-align: center; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .btn-print { background: #dc3545; color: white; border: none; padding: 8px 20px; font-weight: bold; border-radius: 20px; cursor: pointer; transition: 0.2s; margin-left: 10px; font-family: 'Kanit'; }
        .btn-print:hover { background: #bd2130; }
        
        /* จัดขนาดเลย์เอาต์หน้ากระดาษ A4 ในโหมด Preview หน้าเว็บ */
        .page { width: 210mm; min-height: 297mm; padding: 20mm; margin: 15px auto; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; position: relative; }
        .cover-title { text-align: center; margin-top: 50mm; margin-bottom: 20mm; color: #1a252f; }
        .student-meta { text-align: center; font-size: 18px; line-height: 1.8; margin-bottom: 40mm; }
        .table-summary { widtH: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-summary th, .table-summary td { border: 1px solid #dee2e6; padding: 12px; text-align: left; font-size: 14px; }
        .table-summary th { background-color: #f8f9fa; color: #495057; }
        
        /* หน้าพ่นรูปใบประกาศ */
        .cert-container { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; }
        .cert-img { max-width: 100%; max-height: 190mm; object-fit: contain; border: 4px double #ccc; padding: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .cert-footer { margin-top: 15px; font-size: 13px; color: #6c757d; }

        /* ==========================================
           🌟 พระเอกเด่น: คำสั่งตัดระเบียบสำหรับพิมพ์ออก PDF จริง
           ========================================== */
        @media print {
            .no-print-banner { display: none !important; }
            body { background: white; }
            .page { width: auto; height: auto; margin: 0; padding: 15mm; box-shadow: none; page-break-after: always; }
            .cert-img { max-height: 230mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-banner">
        🖥️ <strong>ระบบคัดกรอง Portfolio เอกสารแนบอัตโนมัติ:</strong> คุณสามารถตรวจสอบรูปเล่มก่อนสั่งพิมพ์ หรือบันทึกเป็นไฟล์ PDF ได้ทันทีค่ะ
        <button class="btn-print" onclick="window.print()"><i class="bi bi-printer"></i> คลิกเพื่อบันทึกเป็น PDF / พิมพ์เอกสาร</button>
    </div>

    <div class="page">
        <div class="cover-title">
            <img src="images/KPSWLogo.png" width="100" style="margin-bottom: 20px;"><br>
            <h1 style="font-size: 28px; margin: 0;">เอกสารภาคผนวกแนบแฟ้มสะสมผลงาน</h1>
            <h3 style="font-weight: 400; color: #6c757d; margin-top: 5px;">คลังเก็บรวบรวมหลักฐานใบประกาศเกียรติบัตรออนไลน์</h3>
        </div>
        
        <div class="student-meta">
            <strong>ผู้ให้ข้อมูลความสำเร็จ:</strong> <?=$_SESSION['prefix_name'];?><?=$_SESSION['first_name'];?> <?=$_SESSION['last_name'];?><br>
            <strong>ระดับชั้นเรียน:</strong> ห้องเรียน <?=$student_classroom;?><br>
            <strong>สถานศึกษา:</strong> โรงเรียนกำแพงแสนวิทยา จังหวัดนครปฐม
        </div>

        <h3 style="border-bottom: 2px solid #333; padding-bottom: 8px;">📊 ตารางดัชนีสรุปรายการเกียรติบัตร (รวมทั้งหมด <?=count($activities);?> รายการ)</h3>
        <table class="table-summary">
            <thead>
                <tr>
                    <th width="12%" style="text-align: center;">ลำดับ</th>
                    <th width="18%" style="text-align: center;">ปีการศึกษา</th>
                    <th>รายการกิจกรรม / การแข่งขันความสำเร็จ</th>
                    <th width="25%">รางวัลที่ได้รับ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($activities)): ?>
                    <tr><td colspan="4" style="text-align: center; color: #999;">ไม่พบประวัติผลงานในฐานข้อมูลค่ะ</td></tr>
                <?php else: $i=1; foreach($activities as $act): ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?=$i++;?></td>
                        <td style="text-align: center;">ภาคเรียนที่ <?=$act['term'];?>/<?=$act['academic_year'];?></td>
                        <td><strong><?=$act['activity_name'];?></strong></td>
                        <td><span style="color:#059669; font-weight:600;"><?=$act['award_name'];?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
    $index = 1;
    foreach($activities as $act): 
        // เช็กถ้ามีการแนบไฟล์รูปจริง ให้ทำลายกระดาษขึ้นหน้าใหม่โชว์รูปประกาศค่ะ
        if(!empty($act['certificate_file'])):
    ?>
        <div class="page">
            <div class="cert-container">
                <h3 style="margin-bottom: 20px; color: #2c3e50; width: 100%; border-bottom: 1px dashed #ccc; padding-bottom: 10px;">
                    📄 หลักฐานชิ้นที่ <?=$index++;?>: <?=$act['activity_name'];?>
                </h3>
                
                <img class="cert-img" src="uploads/portfolio/<?=$student_user_id;?>/<?=$act['certificate_file'];?>" alt="ใบประกาศ">
                
                <div class="cert-footer">
                    รางวัล: <strong><?=$act['award_name'];?></strong> | 
                    วันที่ได้รับ: <?=date('d/m/Y', strtotime($act['activity_date']));?> | 
                    รับรองความถูกต้องโดยระบบทะเบียนดิจิทัลโรงเรียนกำแพงแสนวิทยา
                </div>
            </div>
        </div>
    <?php 
        endif;
    endforeach; 
    ?>

</body>
</html>