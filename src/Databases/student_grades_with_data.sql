CREATE TABLE `student_grades` (
  `grade_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `term` TINYINT NOT NULL,
  `academic_year` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` ENUM('4', '3.5', '3', '2.5', '2', '1.5', '1', '0', 'ร.', 'ม.ส.', '-') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE CASCADE,
  INDEX (`student_id`),
  INDEX (`term`, `academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `student_grades` (`student_id`, `subject_id`, `term`, `academic_year`, `grade`) VALUES
(1, 1, 2, 2567, '4'),
(1, 2, 2, 2567, '3.5'),
(1, 3, 2, 2567, '4'),
(1, 4, 2, 2567, '3'),
(1, 5, 2, 2567, '3'),
(1, 6, 2, 2567, '4'),
(1, 7, 2, 2567, '4'),
(1, 8, 2, 2567, '3.5'),
(1, 9, 2, 2567, '4'),
(1, 10, 2, 2567, '3.5');

INSERT INTO `student_grades` (`student_id`, `subject_id`, `term`, `academic_year`, `grade`) VALUES
(2, 33, 2, 2567, '4'),
(2, 34, 2, 2567, '3.5'),
(2, 35, 2, 2567, '3'),
(2, 36, 2, 2567, '3.5'),
(2, 37, 2, 2567, '4'),
(2, 38, 2, 2567, '4'),
(2, 39, 2, 2567, '3.5'),
(2, 40, 2, 2567, '3'),
(2, 41, 2, 2567, '3.5'),
(2, 42, 2, 2567, '4');

CREATE TABLE IF NOT EXISTS grade_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    total_credits INT NOT NULL,
    gpa DECIMAL(4,2) NOT NULL,
    term INT NOT NULL,
    academic_year INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


INSERT INTO grade_summary (student_id, total_credits, gpa, term, academic_year) VALUES (1, 8, 2.19, 2, 2567);
INSERT INTO grade_summary (student_id, total_credits, gpa, term, academic_year) VALUES (2, 8, 2.19, 2, 2567);