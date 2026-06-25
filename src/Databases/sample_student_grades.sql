
-- Sample Data for student_grades
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'คณิตศาสตร์', 85, 'A', 3);
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'ภาษาไทย', 78, 'B+', 2);
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'ภาษาอังกฤษ', 82, 'A-', 3);
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'วิทยาศาสตร์', 88, 'A', 4);
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'สังคมศึกษา', 75, 'B', 2);
INSERT INTO `student_grades` (`student_classroom_id`, `subject_name`, `score`, `grade`, `credit`) VALUES (1, 'ศิลปะ', 90, 'A', 1);

-- Sample Data for grade_summary
INSERT INTO `grade_summary` (`student_classroom_id`, `total_credit`, `gpa`) VALUES (1, 15, 3.45);