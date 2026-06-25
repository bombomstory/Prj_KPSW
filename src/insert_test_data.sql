
-- ============================================
-- SQL สำหรับสร้างข้อมูลทดสอบระบบประเมินครูผู้สอน
-- ============================================
-- 1. เพิ่มเกณฑ์การประเมินครูผู้สอน (ถ้ายังไม่มี)
INSERT IGNORE INTO evaluation_criteria (criterion_id, criterion_name, category, status) VALUES
(6, 'ความรู้ความสามารถ', 'Teacher', 'active'),
(7, 'การสื่อสาร', 'Teacher', 'active'),
(8, 'การจัดการชั้นเรียน', 'Teacher', 'active'),
(9, 'ความรับผิดชอบ', 'Teacher', 'active'),
(10, 'ทัศนคติและความเป็นมืออาชีพ', 'Teacher', 'active'),
(11, 'ทักษะการสอน', 'Teacher', 'active');
-- 2. เพิ่มข้อมูล weekly_schedule (ตารางเรียน)
-- เชื่อมโยง นักเรียน - วิชา - ครู
INSERT IGNORE INTO weekly_schedule
(student_id, subject_id, teacher_id, day_of_week, period_number, room_id)
VALUES
-- นักเรียน ID 1
(1, 1, 1, 'จันทร์', 1, 1),
(1, 2, 1, 'จันทร์', 2, 1),
(1, 3, 1, 'จันทร์', 3, 1),
(1, 4, 1, 'อังคาร', 1, 1),
(1, 5, 1, 'อังคาร', 2, 1),
-- นักเรียน ID 2
(2, 33, 1, 'จันทร์', 1, 1),
(2, 34, 1, 'จันทร์', 2, 1),
(2, 35, 1, 'จันทร์', 3, 1);
-- 3. ตรวจสอบข้อมูลที่มีอยู่
SELECT 'Check 1: Evaluation Criteria (Teacher)' as step;
SELECT criterion_id, criterion_name, category, status
FROM evaluation_criteria
WHERE category = 'Teacher'
ORDER BY criterion_id;
SELECT 'Check 2: Students' as step;
SELECT student_id, student_code, first_name, last_name, grade_level
FROM students
LIMIT 5;
SELECT 'Check 3: Teachers' as step;
SELECT t.teacher_id, u.first_name, u.last_name, t.department
FROM teachers t
INNER JOIN users u ON t.user_id = u.user_id
LIMIT 5;
SELECT 'Check 4: Student Grades (Last Term)' as step;
SELECT sg.student_id, sg.subject_id, s.subject_name, sg.academic_year, sg.term, sg.grade
FROM student_grades sg
INNER JOIN subjects s ON sg.subject_id = s.subject_id
WHERE sg.student_id = 1
AND sg.academic_year = '2567'
AND sg.term = 2
LIMIT 10;
SELECT 'Check 5: Weekly Schedule' as step;
SELECT ws.student_id, ws.subject_id, s.subject_name, ws.teacher_id, ws.day_of_week, ws.period_number
FROM weekly_schedule ws
INNER JOIN subjects s ON ws.subject_id = s.subject_id
WHERE ws.student_id = 1
LIMIT 10;
SELECT 'Check 6: Complete Query Test' as step;
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
WHERE sg.student_id = 1
AND sg.academic_year = '2567'
AND sg.term = 2
ORDER BY s.subject_code;