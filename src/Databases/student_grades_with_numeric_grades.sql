
-- ตาราง student_grades (พร้อม term และ academic_year)
DROP TABLE IF EXISTS student_grades;
CREATE TABLE student_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    subject_code VARCHAR(10) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    credit DECIMAL(3,1) NOT NULL,
    grade VARCHAR(2) NOT NULL,
    term TINYINT NOT NULL,
    academic_year YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_grade CHECK (grade IN ('4', '3.5', '3', '2.5', '2', '1.5', '1', '0', 'ร.', 'ม.ส.', '-'))
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตัวอย่างข้อมูล student_grades
INSERT INTO student_grades (student_id, subject_code, subject_name, credit, grade, term, academic_year) VALUES
('65010101', '100101', 'คณิตศาสตร์พื้นฐาน', 3.0, '4', 1, 2568),
('65010101', '100102', 'วิทยาศาสตร์พื้นฐาน', 3.0, '3.5', 1, 2568),
('65010102', '100103', 'ภาษาอังกฤษพื้นฐาน', 3.0, '2.5', 1, 2568),
('65010102', '100104', 'ฟิสิกส์', 2.0, '3', 1, 2568),
('65010103', '100105', 'ชีววิทยา', 2.0, '4', 1, 2568);

-- ตาราง grade_summary (พร้อม term และ academic_year)
DROP TABLE IF EXISTS grade_summary;
CREATE TABLE grade_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    gpa DECIMAL(4,2) NOT NULL,
    total_credit DECIMAL(4,1) NOT NULL,
    term TINYINT NOT NULL,
    academic_year YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตัวอย่างข้อมูล grade_summary
INSERT INTO grade_summary (student_id, gpa, total_credit, term, academic_year) VALUES
('65010101', 3.75, 6.0, 1, 2568),
('65010102', 2.75, 5.0, 1, 2568),
('65010103', 4.00, 2.0, 1, 2568);
