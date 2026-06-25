<?php
session_start();
include('db_connection.php');

// ฟังก์ชันสำหรับหั่น "คำนำหน้า", "ชื่อ", "นามสกุล" ออกจากข้อความยาวๆ
function splitThaiName($fullname) {
    // 1. ลบตัวเลขและจุดด้านหน้าออก (เช่น "1.นางสาวสุชาดา" -> "นางสาวสุชาดา")
    $fullNameStr = trim($fullname);
    $fullNameStr = preg_replace('/^[0-9]+\./u', '', $fullNameStr); 
    $fullNameStr = trim($fullNameStr);
    
    // 2. แยกด้วยช่องว่าง
    $parts = preg_split('/\s+/u', $fullNameStr);
    $firstNamePart = $parts[0] ?? '';
    $lastName = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';
    
    $prefix = '';
    $firstName = $firstNamePart;
    
    // 3. ดึงคำนำหน้าชื่อออก
    $prefixes = ['นาย', 'นางสาว', 'นาง', 'ด.ช.', 'ด.ญ.', 'ว่าที่ร้อยตรี', 'ว่าที่ร.ต.'];
    foreach ($prefixes as $p) {
        if (mb_strpos($firstNamePart, $p, 0, 'UTF-8') === 0) {
            $prefix = $p;
            $firstName = mb_substr($firstNamePart, mb_strlen($p, 'UTF-8'), null, 'UTF-8');
            break;
        }
    }
    
    return [
        'prefix' => $prefix,
        'first_name' => $firstName,
        'last_name' => $lastName
    ];
}

if(isset($_POST["import"])){
    $filename = $_FILES["file"]["tmp_name"];
    
    // รับค่าจากฟอร์มเพื่อใช้เป็นค่า Default หากใน Excel ช่องนั้นว่าง
    $form_academic_year = $_POST["academic_year"] ?? '2567';
    $form_term = $_POST["term"] ?? '1';

    if($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        
        // ข้ามบรรทัดแรก (Header ของ CSV)
        fgetcsv($file, 10000, ","); 

        $success_count = 0;
        $error_count = 0;

        // เริ่มวนลูปอ่านข้อมูลทีละบรรทัด
        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
            
            // --- ดึงข้อมูลวิชา ---
            $subject_code = trim($getData[1]); 
            $subject_name = trim($getData[2]);
            $credits = ($getData[3] !== '') ? floatval($getData[3]) : 0;
            
            // --- ดึงข้อมูลห้องและปี/เทอม ---
            $classroom_name = trim($getData[5]); 
            $academic_year = !empty($getData[10]) ? trim($getData[10]) : $form_academic_year;
            $term = !empty($getData[11]) ? trim($getData[11]) : $form_term;

            // --- ดึงข้อมูลครูผู้สอน ---
            $teacher_raw = trim($getData[6]); 
            
            // --- ดึงข้อมูลนักเรียน ---
            $student_code = trim($getData[7]);
            $student_fullname = trim($getData[8]); 

            // --- ดึงข้อมูลคะแนน --- (ถ้าไม่มีค่า ให้เป็น null แทนค่า 0 ป้องกันเกรดเพี้ยน)
            $score_pre   = ($getData[12] !== '') ? floatval($getData[12]) : null;
            $score_mid   = ($getData[13] !== '') ? floatval($getData[13]) : null;
            $score_post  = ($getData[14] !== '') ? floatval($getData[14]) : null;
            $score_final = ($getData[15] !== '') ? floatval($getData[15]) : null;
            $score_total = ($getData[16] !== '') ? floatval($getData[16]) : null;
            $grade       = trim($getData[23]);
            
            $q_grade = trim($getData[34]);
            $l_grade = trim($getData[40]);
            $attend_percent = ($getData[41] !== '') ? floatval($getData[41]) : null;
            $late = ($getData[42] !== '') ? intval($getData[42]) : 0;
            $leave_sick = ($getData[43] !== '') ? intval($getData[43]) : 0;
            $leave_personal = ($getData[44] !== '') ? intval($getData[44]) : 0;
            $absent = ($getData[45] !== '') ? intval($getData[45]) : 0;

            // ข้ามแถวที่รหัสวิชาหรือรหัสนักเรียนว่าง (ป้องกันบรรทัดว่างท้ายไฟล์)
            if(empty($subject_code) || empty($student_code)) {
                continue;
            }

            // ==========================================
            // STEP 1: จัดการวิชา (ค้นหา หรือ สร้างใหม่)
            // ==========================================
            $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_code = ?");
            $stmt->bind_param("s", $subject_code);
            $stmt->execute();
            $sub_res = $stmt->get_result();
            if($sub_res->num_rows > 0) {
                $subject_id = $sub_res->fetch_assoc()['subject_id'];
            } else {
                // ดึงชั้นปีจากชื่อห้อง เช่น "ม.1/1" -> "ม.1"
                $grade_level = 'ม.1';
                if (preg_match('/^(ม\.[1-6])/u', $classroom_name, $m)) {
                    $grade_level = $m[1];
                }
                $default_area_id = 1; // ใส่ค่าตั้งต้นให้ผ่านเงื่อนไข NOT NULL
                $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, credits, grade_level, area_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsi", $subject_code, $subject_name, $credits, $grade_level, $default_area_id);
                $stmt->execute();
                $subject_id = $stmt->insert_id;
            }

            // ==========================================
            // STEP 2: จัดการห้องเรียน
            // ==========================================
            $stmt = $conn->prepare("SELECT classroom_id FROM classrooms WHERE classroom_name = ? AND academic_year = ?");
            $stmt->bind_param("ss", $classroom_name, $academic_year);
            $stmt->execute();
            $class_res = $stmt->get_result();
            if($class_res->num_rows > 0) {
                $classroom_id = $class_res->fetch_assoc()['classroom_id'];
            } else {
                $default_dept = '-';
                $stmt = $conn->prepare("INSERT INTO classrooms (classroom_name, academic_year, department) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $classroom_name, $academic_year, $default_dept);
                $stmt->execute();
                $classroom_id = $stmt->insert_id;
            }

            // ==========================================
            // STEP 3: จัดการครูผู้สอน
            // ==========================================
            $teacherData = splitThaiName($teacher_raw);
            $t_first = $teacherData['first_name'];
            $t_last = $teacherData['last_name'];
            $t_prefix = $teacherData['prefix'];
            
            $stmt = $conn->prepare("
                SELECT t.teacher_id 
                FROM teachers t 
                JOIN users u ON t.user_id = u.user_id 
                WHERE u.first_name = ? AND u.last_name = ?
            ");
            $stmt->bind_param("ss", $t_first, $t_last);
            $stmt->execute();
            $teach_res = $stmt->get_result();
            if($teach_res->num_rows > 0) {
                $teacher_id = $teach_res->fetch_assoc()['teacher_id'];
            } else {
                // เช็คก่อนว่ามี user ชื่อ-สกุลนี้ไหม (ป้องกัน Error Duplicate Username)
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE first_name = ? AND last_name = ?");
                $stmt->bind_param("ss", $t_first, $t_last);
                $stmt->execute();
                $u_res = $stmt->get_result();
                if ($u_res->num_rows > 0) {
                    $t_user_id = $u_res->fetch_assoc()['user_id'];
                } else {
                    // สร้างบัญชีผู้ใช้ใหม่ให้ครู
                    $t_username = 't' . time() . rand(100, 999);
                    $t_password = password_hash('12345678', PASSWORD_BCRYPT);
                    $t_email = $t_username . '@kpsw.ac.th';
                    
                    $stmt = $conn->prepare("INSERT INTO users (username, password, prefix_name, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $t_username, $t_password, $t_prefix, $t_first, $t_last, $t_email);
                    $stmt->execute();
                    $t_user_id = $stmt->insert_id;
                }

                // ผูกเข้าตารางครู
                $stmt = $conn->prepare("INSERT INTO teachers (user_id) VALUES (?)");
                $stmt->bind_param("i", $t_user_id);
                $stmt->execute();
                $teacher_id = $stmt->insert_id;
            }

            // ==========================================
            // STEP 4: จัดการนักเรียน (ตาราง users และ students)
            // ==========================================
            $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_code = ?");
            $stmt->bind_param("s", $student_code);
            $stmt->execute();
            $stu_res = $stmt->get_result();
            if($stu_res->num_rows > 0) {
                $student_id = $stu_res->fetch_assoc()['student_id'];
            } else {
                $studentData = splitThaiName($student_fullname);
                $s_username = $student_code;
                
                // --- เพิ่มการเช็ค User ป้องกัน Error ทิ้งค้าง (Duplicate Entry) ---
                $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
                $stmt->bind_param("s", $s_username);
                $stmt->execute();
                $u_res = $stmt->get_result();
                
                if ($u_res->num_rows > 0) {
                    // ถ้าเคยสร้างค้างไว้ ดึง user_id มาใช้ต่อได้เลย
                    $s_user_id = $u_res->fetch_assoc()['user_id'];
                } else {
                    // ถ้ายังไม่เคยสร้าง ให้สร้างใหม่
                    $s_password = password_hash($student_code, PASSWORD_BCRYPT);
                    $s_email = $student_code . '@student.kpsw.ac.th';
                    
                    $stmt = $conn->prepare("INSERT INTO users (username, password, prefix_name, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $s_username, $s_password, $studentData['prefix'], $studentData['first_name'], $studentData['last_name'], $s_email);
                    $stmt->execute();
                    $s_user_id = $stmt->insert_id;
                }

                // หาชั้นปีจากชื่อห้อง (เช่น ม.1/1 -> ม.1)
                $grade_level = 'ม.1';
                if (preg_match('/^(ม\.[1-6])/u', $classroom_name, $m)) {
                    $grade_level = $m[1];
                }

                // บันทึกข้อมูลส่วนตัวเด็กลงตาราง students 
                $stmt = $conn->prepare("INSERT INTO students (user_id, student_code, first_name, last_name, grade_level) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $s_user_id, $student_code, $studentData['first_name'], $studentData['last_name'], $grade_level);
                $stmt->execute();
                $student_id = $stmt->insert_id;
            }

            // ==========================================
            // STEP 4.5: นำนักเรียนเข้าห้องเรียนของเทอมนั้น (student_classrooms)
            // ==========================================
            $stmt = $conn->prepare("SELECT student_classroom_id FROM student_classrooms WHERE student_id = ? AND classroom_id = ? AND academic_year = ?");
            $stmt->bind_param("iis", $student_id, $classroom_id, $academic_year);
            $stmt->execute();
            if($stmt->get_result()->num_rows == 0) {
                // ถ้ายังไม่เคยบันทึกว่าเด็กคนนี้อยู่ห้องนี้ในปีนี้ ให้บันทึกลงไป
                $stmt = $conn->prepare("INSERT INTO student_classrooms (student_id, classroom_id, academic_year) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $student_id, $classroom_id, $academic_year);
                $stmt->execute();
            }

            // ==========================================
            // STEP 5: สร้างภาระงานสอนให้ครู (สำหรับหน้าประเมินปิดตา)
            // ==========================================
            $stmt = $conn->prepare("INSERT IGNORE INTO teaching_assignments (subject_id, teacher_id, classroom_id, academic_year, term) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisi", $subject_id, $teacher_id, $classroom_id, $academic_year, $term);
            $stmt->execute();

            // ==========================================
            // STEP 6: บันทึกผลการเรียนลงตาราง
            // ==========================================
            $sql = "INSERT INTO student_grade_details 
                    (student_id, subject_id, academic_year, term, score_pre_midterm, score_midterm, score_post_midterm, score_final, score_total, grade, q_grade, l_grade, attendance_percent, late, leave_sick, leave_personal, absent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    score_pre_midterm=VALUES(score_pre_midterm), score_midterm=VALUES(score_midterm),
                    score_post_midterm=VALUES(score_post_midterm), score_final=VALUES(score_final),
                    score_total=VALUES(score_total), grade=VALUES(grade), q_grade=VALUES(q_grade), 
                    l_grade=VALUES(l_grade), attendance_percent=VALUES(attendance_percent), 
                    late=VALUES(late), leave_sick=VALUES(leave_sick), leave_personal=VALUES(leave_personal), absent=VALUES(absent)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisiiddddsssddiii", 
                $student_id, $subject_id, $academic_year, $term, 
                $score_pre, $score_mid, $score_post, $score_final, $score_total, 
                $grade, $q_grade, $l_grade, $attend_percent, 
                $late, $leave_sick, $leave_personal, $absent
            );
            
            if($stmt->execute()){
                $success_count++;
            } else {
                $error_count++;
            }
        }
        fclose($file);

        // นำเข้าเสร็จสิ้น ส่งกลับไปหน้า admin_import.php พร้อมแจ้งเตือนสีเขียว
        header("Location: admin_import.php?status=success&count=" . $success_count);
        exit();
    } else {
        header("Location: admin_import.php?status=error&msg=ไฟล์ว่างเปล่า");
        exit();
    }
} else {
    header("Location: admin_import.php");
    exit();
}
?>