
-- สร้างตาราง student_grades
CREATE TABLE `student_grades` (
  `grade_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_classroom_id` INT NOT NULL,
  `subject_name` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` INT NOT NULL,
  `grade` VARCHAR(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit` INT NOT NULL,
  FOREIGN KEY (`student_classroom_id`) REFERENCES `student_classroom`(`student_classroom_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- สร้างตาราง grade_summary
CREATE TABLE `grade_summary` (
  `summary_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_classroom_id` INT NOT NULL,
  `gpa` DECIMAL(3,2) NOT NULL,
  `total_credits` INT NOT NULL,
  FOREIGN KEY (`student_classroom_id`) REFERENCES `student_classroom`(`student_classroom_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลตัวอย่างสำหรับ student_grades
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES
(1, 'คณิตศาสตร์', 85, 'A', 3),
(1, 'ภาษาไทย', 78, 'B+', 2),
(1, 'ภาษาอังกฤษ', 82, 'A-', 3),
(1, 'วิทยาศาสตร์', 88, 'A', 4),
(1, 'สังคมศึกษา', 75, 'B', 2),
(1, 'ศิลปะ', 90, 'A', 1);

-- ข้อมูลตัวอย่างสำหรับ grade_summary
INSERT INTO `grade_summary` (`student_classroom_id`, `gpa`, `total_credits`) VALUES
(1, 3.45, 15);
