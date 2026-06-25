-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Sep 20, 2025 at 11:47 AM
-- Server version: 5.7.44
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kpswacth_reg`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`kpswacth_reg`@`localhost` PROCEDURE `CleanExpiredTokens` ()   BEGIN
    DELETE FROM password_resets WHERE expires_at < NOW();
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `advisors`
--

CREATE TABLE `advisors` (
  `advisor_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `advisors`
--

INSERT INTO `advisors` (`advisor_id`, `user_id`, `department`) VALUES
(1, 5, 'คณิตศาสตร์'),
(2, 6, 'ภาษาไทย');

-- --------------------------------------------------------

--
-- Table structure for table `advisor_evaluation_responses`
--

CREATE TABLE `advisor_evaluation_responses` (
  `response_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `advisor_id` int(11) NOT NULL,
  `academic_year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `criterion_id` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่ประเมิน',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advisor_evaluation_responses`
--

INSERT INTO `advisor_evaluation_responses` (`response_id`, `student_id`, `advisor_id`, `academic_year`, `semester`, `criterion_id`, `rating`, `comments`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2567, 2, 1, 3.0, NULL, '2025-08-11 14:31:40', '2025-08-11 14:31:40'),
(2, 1, 2, 2567, 2, 2, 3.0, NULL, '2025-08-11 14:31:40', '2025-08-11 14:31:40'),
(3, 1, 2, 2567, 2, 3, 3.0, NULL, '2025-08-11 14:31:40', '2025-08-11 14:31:40'),
(4, 1, 2, 2567, 2, 4, 3.0, NULL, '2025-08-11 14:31:40', '2025-08-11 14:31:40'),
(5, 1, 2, 2567, 2, 5, 2.0, NULL, '2025-08-11 14:31:40', '2025-08-11 14:31:40'),
(6, 1, 1, 2567, 2, 1, 5.0, NULL, '2025-09-20 10:57:12', '2025-09-20 10:57:12'),
(7, 1, 1, 2567, 2, 2, 4.0, NULL, '2025-09-20 10:57:12', '2025-09-20 10:57:12'),
(8, 1, 1, 2567, 2, 3, 4.0, NULL, '2025-09-20 10:57:12', '2025-09-20 10:57:12'),
(9, 1, 1, 2567, 2, 4, 4.0, NULL, '2025-09-20 10:57:12', '2025-09-20 10:57:12'),
(10, 1, 1, 2567, 2, 5, 5.0, NULL, '2025-09-20 10:57:12', '2025-09-20 10:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `advisor_items`
--

CREATE TABLE `advisor_items` (
  `advisor_item_id` int(11) NOT NULL,
  `advisor_id` int(11) DEFAULT NULL,
  `classroom_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `advisor_items`
--

INSERT INTO `advisor_items` (`advisor_item_id`, `advisor_id`, `classroom_id`) VALUES
(1, 1, 1),
(3, 1, 3),
(2, 2, 1),
(4, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `building_id` int(11) NOT NULL,
  `building_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `CEOs`
--

CREATE TABLE `CEOs` (
  `ceo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `office_email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `CEOs`
--

INSERT INTO `CEOs` (`ceo_id`, `user_id`, `title`, `office_phone`, `office_email`, `created_at`, `updated_at`) VALUES
(1, 8, 'ผู้อำนวยการโรงเรียน', '034-281-100', 'director@kpsw.ac.th', '2025-06-03 08:26:17', '2025-06-03 08:26:17'),
(2, 3, 'รองผู้อำนวยการ', '034-281-101', 'vp@kpsw.ac.th', '2025-06-03 08:26:17', '2025-06-03 08:26:17');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `classroom_id` int(11) NOT NULL,
  `classroom_name` varchar(50) NOT NULL,
  `academic_year` varchar(10) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`classroom_id`, `classroom_name`, `academic_year`, `department`) VALUES
(1, 'ม.1/1', '2567', 'วิทยาศาสตร์'),
(2, 'ม.2/2', '2567', 'ภาษาอังกฤษ'),
(3, 'ม.3/3', '2567', 'คณิตศาสตร์'),
(4, 'ม.4/1', '2567', 'วิทยาศาสตร์'),
(5, 'ม.5/2', '2567', 'ภาษาอังกฤษ'),
(6, 'ม.6/3', '2567', 'คณิตศาสตร์');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `complaint_type_id` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('รอพิจารณา','กำลังดำเนินการ','ดำเนินการเสร็จแล้ว') COLLATE utf8mb4_unicode_ci DEFAULT 'รอพิจารณา'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_types`
--

CREATE TABLE `complaint_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_types`
--

INSERT INTO `complaint_types` (`id`, `name`) VALUES
(1, 'ร้องเรียน'),
(2, 'ข้อเสนอแนะ'),
(3, 'แจ้งปัญหา'),
(4, 'ขอความช่วยเหลือ');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_criteria`
--

CREATE TABLE `evaluation_criteria` (
  `criterion_id` int(11) NOT NULL,
  `criterion_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Teacher','Advisor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_criteria`
--

INSERT INTO `evaluation_criteria` (`criterion_id`, `criterion_name`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, 'การดูแลเอาใจใส่นักเรียน', 'Advisor', 'active', '2025-07-13 14:17:02', '2025-07-13 14:17:02'),
(2, 'ความสามารถในการให้คำปรึกษา', 'Advisor', 'active', '2025-07-13 14:17:02', '2025-07-13 14:17:02'),
(3, 'การสื่อสารกับนักเรียนและผู้ปกครอง', 'Advisor', 'active', '2025-07-13 14:17:02', '2025-07-13 14:17:02'),
(4, 'การส่งเสริมพัฒนาการนักเรียน', 'Advisor', 'active', '2025-07-13 14:17:02', '2025-07-13 14:17:02'),
(5, 'ความรับผิดชอบ', 'Advisor', 'active', '2025-07-13 14:17:02', '2025-07-13 14:17:02'),
(6, 'ความรู้ความสามารถ', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10'),
(7, 'การสื่อสาร', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10'),
(8, 'การจัดการชั้นเรียน', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10'),
(9, 'ความรับผิดชอบ', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10'),
(10, 'ทัศนคติและความเป็นมืออาชีพ', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10'),
(11, 'ทักษะการสอน', 'Teacher', 'active', '2025-07-13 14:21:10', '2025-07-13 14:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `grade_summary`
--

CREATE TABLE `grade_summary` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_credits` int(11) NOT NULL,
  `gpa` decimal(4,2) NOT NULL,
  `term` int(11) NOT NULL,
  `academic_year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grade_summary`
--

INSERT INTO `grade_summary` (`id`, `student_id`, `total_credits`, `gpa`, `term`, `academic_year`, `created_at`) VALUES
(1, 1, 8, 2.19, 2, 2567, '2025-07-27 16:30:13'),
(2, 2, 8, 2.19, 2, 2567, '2025-07-27 16:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `learning_areas`
--

CREATE TABLE `learning_areas` (
  `area_id` int(11) NOT NULL,
  `area_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_areas`
--

INSERT INTO `learning_areas` (`area_id`, `area_name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ภาษาไทย', 'กลุ่มสาระการเรียนรู้ด้านภาษาไทย', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(2, 'คณิตศาสตร์', 'กลุ่มสาระการเรียนรู้ด้านคณิตศาสตร์', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(3, 'วิทยาศาสตร์', 'กลุ่มสาระการเรียนรู้ด้านวิทยาศาสตร์', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(4, 'สังคมศึกษา ศาสนา และวัฒนธรรม', 'กลุ่มสาระการเรียนรู้ด้านสังคมศึกษา, ศาสนา และวัฒนธรรม', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(5, 'สุขศึกษาและพลศึกษา', 'กลุ่มสาระการเรียนรู้ด้านสุขศึกษาและพลศึกษา', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(6, 'ศิลปะ', 'กลุ่มสาระการเรียนรู้ด้านศิลปะ', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(7, 'การงานอาชีพ', 'กลุ่มสาระการเรียนรู้ด้านการงานอาชีพ', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(8, 'ภาษาอังกฤษ', 'กลุ่มสาระการเรียนรู้ด้านภาษาอังกฤษ', 'active', '2025-07-13 15:00:34', '2025-07-13 15:00:34'),
(9, 'กิจกรรมพัฒนาผู้เรียน', 'กลุ่มกิจกรรมพัฒนาผู้เรียน/กิจกรรมเพิ่มเติม', 'active', '2025-07-13 16:17:41', '2025-07-13 16:17:41');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`parent_id`, `user_id`) VALUES
(1, 3),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `parents_items`
--

CREATE TABLE `parents_items` (
  `parent_item_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `relationship_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `parents_items`
--

INSERT INTO `parents_items` (`parent_item_id`, `parent_id`, `student_id`, `relationship_id`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrars`
--

CREATE TABLE `registrars` (
  `registrar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `registrars`
--

INSERT INTO `registrars` (`registrar_id`, `user_id`, `position`, `office_phone`, `created_at`, `updated_at`) VALUES
(1, 7, 'เจ้าหน้าที่ทะเบียน', '034-281-500', '2025-06-03 08:24:26', '2025-06-03 09:41:54'),
(2, 8, 'หัวหน้าฝ่ายทะเบียน', '034-281-501', '2025-06-03 08:24:26', '2025-06-03 09:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE `relationship` (
  `relationship_id` int(11) NOT NULL,
  `relationship_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `relationship`
--

INSERT INTO `relationship` (`relationship_id`, `relationship_name`) VALUES
(1, 'พ่อ'),
(2, 'แม่'),
(3, 'ตา'),
(4, 'ยาย'),
(5, 'ปู่'),
(6, 'ย่า'),
(7, 'ลุง'),
(8, 'ป้า'),
(9, 'น้า'),
(10, 'อา');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `building_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `site_config`
--

CREATE TABLE `site_config` (
  `config_id` int(11) NOT NULL,
  `config_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_config`
--

INSERT INTO `site_config` (`config_id`, `config_name`, `config_value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'site_title', 'โรงเรียนกำแพงแสนวิทยา', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(2, 'maintenance_mode', 'OFF', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(3, 'homepage_banner', 'welcome.jpg', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(4, 'contact_email', 'info@kpsw.ac.th', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(5, 'enable_registration', 'true', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(6, 'system_version', '1.0.5', 'active', '2025-07-27 14:55:29', '2025-07-27 14:55:29'),
(7, 'current_acadyear', '2568', 'active', '2025-07-27 14:55:29', '2025-08-02 20:42:24'),
(8, 'current_term', '1', 'active', '2025-07-27 14:55:29', '2025-08-02 20:42:28'),
(9, 'last_acadyear', '2567', 'active', '2025-08-02 20:42:07', '2025-08-02 20:42:07'),
(10, 'last_term', '2', 'active', '2025-08-02 20:42:07', '2025-08-02 20:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_code` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `grade_level` enum('ม.1','ม.2','ม.3','ม.4','ม.5','ม.6') NOT NULL,
  `gpax` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `grade_level`, `gpax`) VALUES
(1, 1, 'S001', 'น้องสมชาย', 'ใจดี', 'ม.1', 3.25),
(2, 2, 'S002', 'น้องสมหญิง', 'แก้วตา', 'ม.2', 3.50);

-- --------------------------------------------------------

--
-- Table structure for table `student_classrooms`
--

CREATE TABLE `student_classrooms` (
  `student_classroom_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `classroom_id` int(11) DEFAULT NULL,
  `academic_year` varchar(10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_classrooms`
--

INSERT INTO `student_classrooms` (`student_classroom_id`, `student_id`, `classroom_id`, `academic_year`, `start_date`, `end_date`) VALUES
(1, 1, 1, '2567', '2024-05-15', '2025-03-31'),
(2, 2, 2, '2567', '2024-05-15', '2025-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `term` tinyint(4) NOT NULL,
  `academic_year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` enum('4','3.5','3','2.5','2','1.5','1','0','ร.','มส.','ผ.','มผ.') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_grades`
--

INSERT INTO `student_grades` (`grade_id`, `student_id`, `subject_id`, `term`, `academic_year`, `grade`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '2567', '4', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(2, 1, 2, 2, '2567', '3.5', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(3, 1, 3, 2, '2567', '2.5', '2025-07-28 00:08:56', '2025-07-28 01:10:09'),
(4, 1, 4, 2, '2567', '1.5', '2025-07-28 00:08:56', '2025-07-28 01:10:19'),
(5, 1, 5, 2, '2567', '3', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(6, 1, 6, 2, '2567', '4', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(7, 1, 7, 2, '2567', '4', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(8, 1, 8, 2, '2567', '3.5', '2025-07-28 00:08:56', '2025-07-28 00:08:56'),
(9, 1, 9, 2, '2567', 'ผ.', '2025-07-28 00:08:56', '2025-07-28 01:11:25'),
(10, 1, 10, 2, '2567', 'มผ.', '2025-07-28 00:08:56', '2025-07-28 01:11:34'),
(11, 2, 33, 2, '2567', '4', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(12, 2, 34, 2, '2567', '3.5', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(13, 2, 35, 2, '2567', '3', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(14, 2, 36, 2, '2567', '3.5', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(15, 2, 37, 2, '2567', '4', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(16, 2, 38, 2, '2567', '4', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(17, 2, 39, 2, '2567', '3.5', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(18, 2, 40, 2, '2567', '3', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(19, 2, 41, 2, '2567', '3.5', '2025-07-28 00:10:01', '2025-07-28 00:10:01'),
(20, 2, 42, 2, '2567', '4', '2025-07-28 00:10:01', '2025-07-28 00:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits` decimal(3,1) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `grade_level` enum('ม.1','ม.2','ม.3','ม.4','ม.5','ม.6') COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credits`, `description`, `grade_level`, `area_id`, `created_at`, `updated_at`) VALUES
(1, 'ท21101', 'ภาษาไทย 1', 3.0, 'วิชาภาษาไทยพื้นฐาน', 'ม.1', 1, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(2, 'ค21101', 'คณิตศาสตร์ 1', 3.0, 'วิชาคณิตศาสตร์พื้นฐาน', 'ม.1', 2, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(3, 'ว21101', 'วิทยาศาสตร์ 1', 3.0, 'วิชาวิทยาศาสตร์พื้นฐาน', 'ม.1', 3, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(4, 'ส21101', 'สังคมศึกษา 1', 3.0, 'วิชาสังคมศึกษา 1', 'ม.1', 4, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(5, 'ส21102', 'สังคมศึกษา 2', 3.0, 'วิชาสังคมศึกษา 2', 'ม.1', 4, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(6, 'พ21101', 'พลศึกษา 1', 2.0, 'วิชาพลศึกษาเบื้องต้น', 'ม.1', 5, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(7, 'พ21103', 'พลศึกษา 3', 2.0, 'วิชาพลศึกษาระดับสูง', 'ม.1', 5, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(8, 'ศ21101', 'ศิลปะ 1', 2.0, 'วิชาศิลปะพื้นฐาน', 'ม.1', 6, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(9, 'ว21106', 'เทคโนโลยีและวิทยาการคำนวณ 1', 2.0, 'วิชาเทคโนโลยีสารสนเทศและวิทยาการคำนวณ 1', 'ม.1', 7, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(10, 'อ21101', 'ภาษาอังกฤษ 1', 3.0, 'วิชาภาษาอังกฤษพื้นฐาน', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(11, 'ง21261', 'อาหารว่างและเครื่องดื่ม 1', 2.0, 'วิชาอาหารว่างและเครื่องดื่ม', 'ม.1', 7, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(12, 'ง20265', 'งานปลูกไม้ดอกไม้ประดับ', 2.0, 'วิชางานปลูกไม้ดอกไม้ประดับ', 'ม.1', 7, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(13, 'ง21265', 'วัสดุงานช่าง', 2.0, 'วิชาวัสดุงานช่าง', 'ม.1', 7, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(14, 'ก20201', 'ภาษาเกาหลี 1', 3.0, 'วิชาภาษาเกาหลีเบื้องต้น', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(15, 'จ20219', 'ภาษาจีนพื้นฐาน 1', 3.0, 'วิชาภาษาจีนพื้นฐาน', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(16, 'ท20207', 'พัฒนาทักษะการอ่านและการเขียน 1', 2.0, 'วิชาพัฒนาทักษะการอ่านและการเขียน', 'ม.1', 1, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(17, 'ค20213', 'คณิตศาสตร์เสริมศักยภาพ 1', 3.0, 'วิชาคณิตศาสตร์เสริมศักยภาพ', 'ม.1', 2, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(18, 'ว20207', 'เสริมทักษะกระบวนการวิทยาศาสตร์', 3.0, 'วิชาเสริมทักษะกระบวนการวิทยาศาสตร์', 'ม.1', 3, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(19, 'ว20239', 'การสร้างและการจัดการเอกสารดิจิทัล', 2.0, 'วิชาการสร้างและการจัดการเอกสารดิจิทัล', 'ม.1', 3, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(20, 'ง21270', 'การออกแบบ 1', 2.0, 'วิชาการออกแบบ 1', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(21, 'อ20213', 'ภาษาอังกฤษฟัง-พูด 1', 3.0, 'วิชาภาษาอังกฤษฟัง-พูด', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(22, 'อ20220', 'ภาษาอังกฤษอ่าน-เขียน 1', 3.0, 'วิชาภาษาอังกฤษอ่าน-เขียน', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(23, 'ว21224', 'คอมพิวเตอร์พื้นฐาน', 2.0, 'วิชาคอมพิวเตอร์พื้นฐาน', 'ม.1', 7, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(24, 'ค20219', 'คณิตศาสตร์ส่งเสริมความเป็นเลิศ 1', 3.0, 'วิชาคณิตศาสตร์ส่งเสริมความเป็นเลิศ', 'ม.1', 2, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(25, 'ว20231', 'ชีววิทยาพื้นฐานและปฏิบัติการ 1', 3.0, 'วิชาชีววิทยาพื้นฐาน', 'ม.1', 3, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(26, 'อ20217', 'การอ่านเพื่อความเข้าใจ 1', 2.0, 'วิชาการอ่านเพื่อความเข้าใจ', 'ม.1', 8, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(27, 'ว20224', 'เสริมทักษะกระบวนการวิทยาศาสตร์ 2', 3.0, 'วิชาเสริมทักษะกระบวนการวิทยาศาสตร์ 2', 'ม.1', 3, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(28, 'ก20941', 'แนะแนว', 1.0, 'วิชาการแนะแนว', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(29, 'ก20951', 'ชุมนุม', 1.0, 'วิชาชุมนุม', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(30, 'ก20991', 'ลูกเสือ-เนตรนารี', 1.0, 'วิชาลูกเสือ-เนตรนารี', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(31, 'ก20981', 'เพื่อสังคมและสาธารณประโยชน์', 1.0, 'วิชาเพื่อสังคมและสาธารณประโยชน์', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(32, 'ก21901', 'กิจกรรมเสริมทักษะวิชาการ', 1.0, 'กิจกรรมเสริมทักษะวิชาการ', 'ม.1', 9, '2025-07-13 16:33:45', '2025-07-13 16:33:45'),
(33, 'ท22101', 'ภาษาไทย 2', 3.0, 'วิชาภาษาไทย 2', 'ม.2', 1, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(34, 'ค22101', 'คณิตศาสตร์ 2', 3.0, 'วิชาคณิตศาสตร์ 2', 'ม.2', 2, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(35, 'ว22101', 'วิทยาศาสตร์ 2', 3.0, 'วิชาวิทยาศาสตร์ 2', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(36, 'ส22101', 'สังคมศึกษา 1', 3.0, 'วิชาสังคมศึกษา 1', 'ม.2', 4, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(37, 'ส22102', 'สังคมศึกษา 2', 3.0, 'วิชาสังคมศึกษา 2', 'ม.2', 4, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(38, 'พ22101', 'พลศึกษา 2', 2.0, 'วิชาพลศึกษา 2', 'ม.2', 5, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(39, 'พ22103', 'พลศึกษา 3', 2.0, 'วิชาพลศึกษา 3', 'ม.2', 5, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(40, 'ศ22101', 'ศิลปะ 2', 2.0, 'วิชาศิลปะ 2', 'ม.2', 6, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(41, 'อ22101', 'ภาษาอังกฤษ 2', 3.0, 'วิชาภาษาอังกฤษ 2', 'ม.2', 8, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(42, 'ว22105', 'เทคโนโลยีและวิทยาการคำนวณ 2', 2.0, 'วิชาเทคโนโลยีและวิทยาการคำนวณ 2', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(43, 'ง20278', 'งานดอกไม้ประดิษฐ์', 2.0, 'วิชางานดอกไม้ประดิษฐ์', 'ม.2', 7, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(44, 'ง20209', 'งานปลูกผักปลอดสารพิษ', 2.0, 'วิชางานปลูกผักปลอดสารพิษ', 'ม.2', 7, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(45, 'ง20286', 'การเขียนแบบ 2', 2.0, 'วิชาการเขียนแบบ 2', 'ม.2', 7, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(46, 'ง22269', 'งานธุรกิจ 1', 2.0, 'วิชางานธุรกิจ 1', 'ม.2', 7, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(47, 'ศ20215', 'ศิลปะเพื่อการแสดง 3', 2.0, 'วิชาศิลปะเพื่อการแสดง 3', 'ม.2', 6, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(48, 'ส20287', 'แบกเป้ท่องไทย', 1.0, 'วิชาการท่องเที่ยวในประเทศ', 'ม.2', 4, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(49, 'ท20217', 'พัฒนาทักษะการอ่านและเขียน 3', 2.0, 'วิชาพัฒนาทักษะการอ่านและการเขียน 3', 'ม.2', 1, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(50, 'ก20203', 'ภาษาเกาหลี 3', 3.0, 'วิชาภาษาเกาหลี 3', 'ม.2', 8, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(51, 'จ20203', 'ภาษาจีน 3', 3.0, 'วิชาภาษาจีน 3', 'ม.2', 8, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(52, 'อ20207', 'ภาษาอังกฤษในชีวิตประจำวัน 1', 3.0, 'วิชาภาษาอังกฤษในชีวิตประจำวัน', 'ม.2', 8, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(53, 'ค20215', 'คณิตศาสตร์เสริมศักยภาพ 3', 3.0, 'วิชาคณิตศาสตร์เสริมศักยภาพ 3', 'ม.2', 2, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(54, 'ว20240', 'STEAM 1', 2.0, 'วิชา STEAM 1', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(55, 'ว20233', 'เคมีพื้นฐานและปฏิบัติการ 1', 3.0, 'วิชาเคมีพื้นฐานและปฏิบัติการ 1', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(56, 'ค20221', 'คณิตศาสตร์สำหรับวิทยาศาสตร์', 3.0, 'วิชาคณิตศาสตร์สำหรับวิทยาศาสตร์', 'ม.2', 2, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(57, 'ว20219', 'สะเต็มศึกษา 1', 2.0, 'วิชาสะเต็มศึกษา 1', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(58, 'I20201', 'การศึกษาค้นคว้าและสร้างองค์ความรู้', 2.0, 'วิชาการศึกษาค้นคว้าและสร้างองค์ความรู้', 'ม.2', 3, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(59, 'ก20943', 'แนะแนว', 1.0, 'วิชาการแนะแนว', 'ม.2', 9, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(60, 'ก20953', 'ชุมนุม', 1.0, 'วิชาชุมนุม', 'ม.2', 9, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(61, 'ก20993', 'ลูกเสือ-เนตรนารี', 1.0, 'วิชาลูกเสือ-เนตรนารี', 'ม.2', 9, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(62, 'ก20983', 'เพื่อสังคมและสาธารณประโยชน์', 1.0, 'วิชาเพื่อสังคมและสาธารณประโยชน์', 'ม.2', 9, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(63, 'ก22901', 'กิจกรรมเสริมทักษะวิชาการ', 1.0, 'กิจกรรมเสริมทักษะวิชาการ', 'ม.2', 9, '2025-07-13 16:55:15', '2025-07-13 16:55:15'),
(64, 'ท23101', 'ภาษาไทย 3', 3.0, 'วิชาภาษาไทย 3', 'ม.3', 1, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(65, 'ค23101', 'คณิตศาสตร์ 3', 3.0, 'วิชาคณิตศาสตร์ 3', 'ม.3', 2, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(66, 'ว23101', 'วิทยาศาสตร์ 3', 3.0, 'วิชาวิทยาศาสตร์ 3', 'ม.3', 3, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(67, 'ก20945', 'แนะแนว', 1.0, 'วิชาการแนะแนว', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(68, 'ก20955', 'ชุมนุม', 1.0, 'วิชาชุมนุม', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(69, 'ก20965', 'ลูกเสือ', 1.0, 'วิชาลูกเสือ', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(70, 'ก20975', 'ยุวกาชาด', 1.0, 'วิชายุวกาชาด', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(71, 'ก20985', 'เพื่อสังคมและสาธารณประโยชน์', 1.0, 'วิชาเพื่อสังคมและสาธารณประโยชน์', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(72, 'ก23901', 'กิจกรรมเสริมทักษะวิชาการ', 1.0, 'กิจกรรมเสริมทักษะวิชาการ', 'ม.3', 9, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(73, 'ส23101', 'สังคมศึกษา 3', 3.0, 'วิชาสังคมศึกษา 3', 'ม.3', 4, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(74, 'ส23102', 'สังคมศึกษา 4', 3.0, 'วิชาสังคมศึกษา 4', 'ม.3', 4, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(75, 'พ23101', 'พลศึกษา 4', 2.0, 'วิชาพลศึกษา 4', 'ม.3', 5, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(76, 'พ23103', 'พลศึกษา 5', 2.0, 'วิชาพลศึกษา 5', 'ม.3', 5, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(77, 'ศ23101', 'ศิลปะ 3', 2.0, 'วิชาศิลปะ 3', 'ม.3', 6, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(78, 'อ23101', 'ภาษาอังกฤษ 3', 3.0, 'วิชาภาษาอังกฤษ 3', 'ม.3', 8, '2025-07-14 05:15:16', '2025-07-14 05:15:16'),
(79, 'ง20293', 'ช่างร้อยมาลัย', 2.0, 'วิชาช่างร้อยมาลัย', 'ม.3', 7, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(80, 'ง23265', 'ไม้กระถางเพื่อการค้า 1', 2.0, 'วิชาไม้กระถางเพื่อการค้า 1', 'ม.3', 7, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(81, 'ง20281', 'ช่างปูน 1', 2.0, 'วิชาช่างปูน 1', 'ม.3', 7, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(82, 'พ20207', 'การปฐมพยาบาล', 1.0, 'วิชาการปฐมพยาบาล', 'ม.3', 5, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(83, 'ศ20219', 'ดนตรีเพื่อการแสดง 4', 2.0, 'วิชาดนตรีเพื่อการแสดง 4', 'ม.3', 6, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(84, 'ท20219', 'พัฒนาทักษะการอ่านและเขียน 5', 2.0, 'วิชาพัฒนาทักษะการอ่านและเขียน 5', 'ม.3', 8, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(85, 'อ20215', 'ภาษาอังกฤษเพื่อการศึกษาต่อ 1', 3.0, 'วิชาภาษาอังกฤษเพื่อการศึกษาต่อ 1', 'ม.3', 8, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(86, 'จ20205', 'ภาษาจีน 5', 3.0, 'วิชาภาษาจีน 5', 'ม.3', 8, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(87, 'จ20217', 'ภาษาจีนในชีวิตประจำวัน 1', 3.0, 'วิชาภาษาจีนในชีวิตประจำวัน 1', 'ม.3', 8, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(88, 'ค20217', 'คณิตศาสตร์เสริมศักยภาพ 5', 3.0, 'วิชาคณิตศาสตร์เสริมศักยภาพ 5', 'ม.3', 2, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(89, 'ว20205', 'เริ่มต้นกับโครงงานวิทยาศาสตร์', 2.0, 'วิชาเริ่มต้นกับโครงงานวิทยาศาสตร์', 'ม.3', 3, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(90, 'ว20228', 'วิทยาศาสตร์คำนวณ', 2.0, 'วิชาวิทยาศาสตร์คำนวณ', 'ม.3', 3, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(91, 'อ20211', 'ภาษาอังกฤษรอบรู้ 1', 3.0, 'วิชาภาษาอังกฤษรอบรู้ 1', 'ม.3', 8, '2025-07-14 05:26:45', '2025-07-14 05:26:45'),
(92, 'ส20288', 'การป้องกันทุจริต 1', 1.0, 'วิชาการป้องกันทุจริต 1', 'ม.3', 4, '2025-07-14 05:26:45', '2025-07-14 05:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `subjects` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `department`, `subjects`) VALUES
(1, 6, 'วิทยาศาสตร์', 'วิทยาศาสตร์');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_classrooms`
--

CREATE TABLE `teacher_classrooms` (
  `teacher_classroom_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `classroom_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher_classrooms`
--

INSERT INTO `teacher_classrooms` (`teacher_classroom_id`, `teacher_id`, `classroom_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_evaluation_responses`
--

CREATE TABLE `teacher_evaluation_responses` (
  `response_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `criterion_id` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `prefix_name` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `prefix_name`, `first_name`, `last_name`, `email`, `phone`, `profile_picture`, `status`, `created_at`, `updated_at`) VALUES
(1, 'student', '$2y$10$EDGEPZwJ0F3SsfyAsUMEa.Z50c9aMEUFiqJK1mwBT9Ls4T9mg53aC', 'เด็กชาย', 'สมชาย', 'ใจดี', 'bombomstory@gmail.com', '0812345678', 'user0002.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:35:04'),
(2, 'somying', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'เด็กหญิง', 'สมหญิง', 'แก้วตา', 'somying_parent@example.com', '0812345679', 'user0001.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:34:46'),
(3, 'pikul', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'นาง', 'พิกุล', 'สมสุข', 'pikul_parent@example.com', '0812345670', 'user0001.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:34:46'),
(4, 'parent', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'นาย', 'ชาติชาย', 'ใจดี', 'somchai_teacher@example.com', '0812345671', 'user0002.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:35:13'),
(5, 'advisor', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'คุณครู', 'ที่ปรึกษา', 'โรงเรียนกำแพงแสนวิทยา', 'somying_teacher@example.com', '0812345672', 'teacher0005.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:36:00'),
(6, 'teacher', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'คุณครู', 'ผู้สอน', 'โรงเรียนกำแพงแสนวิทยา', 'student1@example.com', '0812345673', 'teacher0004.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:35:56'),
(7, 'staff', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'คุณครู', 'งานทะเบียน', 'โรงเรียนกำแพงแสนวิทยา', 'student2@example.com', '0812345674', 'user0001.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:34:46'),
(8, 'admin', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'คุณครู', 'ผู้อำนวยการ', 'โรงเรียนกำแพงแสนวิทยา', 'admin@example.com', '0812345675', 'user0001.jpg', 'active', '2025-06-02 12:16:49', '2025-08-11 05:34:46');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_schedule`
--

CREATE TABLE `weekly_schedule` (
  `schedule_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `day_of_week` enum('จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์') COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_number` tinyint(4) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advisors`
--
ALTER TABLE `advisors`
  ADD PRIMARY KEY (`advisor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `advisor_evaluation_responses`
--
ALTER TABLE `advisor_evaluation_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `advisor_id` (`advisor_id`),
  ADD KEY `criterion_id` (`criterion_id`);

--
-- Indexes for table `advisor_items`
--
ALTER TABLE `advisor_items`
  ADD PRIMARY KEY (`advisor_item_id`),
  ADD UNIQUE KEY `advisor_id` (`advisor_id`,`classroom_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`building_id`);

--
-- Indexes for table `CEOs`
--
ALTER TABLE `CEOs`
  ADD PRIMARY KEY (`ceo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`classroom_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `complaint_type_id` (`complaint_type_id`);

--
-- Indexes for table `complaint_types`
--
ALTER TABLE `complaint_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  ADD PRIMARY KEY (`criterion_id`);

--
-- Indexes for table `grade_summary`
--
ALTER TABLE `grade_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_areas`
--
ALTER TABLE `learning_areas`
  ADD PRIMARY KEY (`area_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`parent_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parents_items`
--
ALTER TABLE `parents_items`
  ADD PRIMARY KEY (`parent_item_id`),
  ADD UNIQUE KEY `parent_id` (`parent_id`,`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `relationship_id` (`relationship_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `registrars`
--
ALTER TABLE `registrars`
  ADD PRIMARY KEY (`registrar_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `relationship`
--
ALTER TABLE `relationship`
  ADD PRIMARY KEY (`relationship_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `config_name` (`config_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_classrooms`
--
ALTER TABLE `student_classrooms`
  ADD PRIMARY KEY (`student_classroom_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `term` (`term`,`academic_year`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_classrooms`
--
ALTER TABLE `teacher_classrooms`
  ADD PRIMARY KEY (`teacher_classroom_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `classroom_id` (`classroom_id`);

--
-- Indexes for table `teacher_evaluation_responses`
--
ALTER TABLE `teacher_evaluation_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `criterion_id` (`criterion_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weekly_schedule`
--
ALTER TABLE `weekly_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `room_id` (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advisors`
--
ALTER TABLE `advisors`
  MODIFY `advisor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `advisor_evaluation_responses`
--
ALTER TABLE `advisor_evaluation_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `advisor_items`
--
ALTER TABLE `advisor_items`
  MODIFY `advisor_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `building_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `CEOs`
--
ALTER TABLE `CEOs`
  MODIFY `ceo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `classroom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_types`
--
ALTER TABLE `complaint_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  MODIFY `criterion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `grade_summary`
--
ALTER TABLE `grade_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `learning_areas`
--
ALTER TABLE `learning_areas`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `parents_items`
--
ALTER TABLE `parents_items`
  MODIFY `parent_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrars`
--
ALTER TABLE `registrars`
  MODIFY `registrar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `relationship`
--
ALTER TABLE `relationship`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_config`
--
ALTER TABLE `site_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_classrooms`
--
ALTER TABLE `student_classrooms`
  MODIFY `student_classroom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher_classrooms`
--
ALTER TABLE `teacher_classrooms`
  MODIFY `teacher_classroom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_evaluation_responses`
--
ALTER TABLE `teacher_evaluation_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `weekly_schedule`
--
ALTER TABLE `weekly_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisors`
--
ALTER TABLE `advisors`
  ADD CONSTRAINT `advisors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `advisor_evaluation_responses`
--
ALTER TABLE `advisor_evaluation_responses`
  ADD CONSTRAINT `advisor_evaluation_responses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `advisor_evaluation_responses_ibfk_2` FOREIGN KEY (`advisor_id`) REFERENCES `advisors` (`advisor_id`),
  ADD CONSTRAINT `advisor_evaluation_responses_ibfk_3` FOREIGN KEY (`criterion_id`) REFERENCES `evaluation_criteria` (`criterion_id`);

--
-- Constraints for table `advisor_items`
--
ALTER TABLE `advisor_items`
  ADD CONSTRAINT `advisor_items_ibfk_1` FOREIGN KEY (`advisor_id`) REFERENCES `advisors` (`advisor_id`),
  ADD CONSTRAINT `advisor_items_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`);

--
-- Constraints for table `CEOs`
--
ALTER TABLE `CEOs`
  ADD CONSTRAINT `CEOs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`complaint_type_id`) REFERENCES `complaint_types` (`id`);

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `parents_items`
--
ALTER TABLE `parents_items`
  ADD CONSTRAINT `parents_items_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`parent_id`),
  ADD CONSTRAINT `parents_items_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `parents_items_ibfk_3` FOREIGN KEY (`relationship_id`) REFERENCES `relationship` (`relationship_id`);

--
-- Constraints for table `registrars`
--
ALTER TABLE `registrars`
  ADD CONSTRAINT `registrars_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`building_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `student_classrooms`
--
ALTER TABLE `student_classrooms`
  ADD CONSTRAINT `student_classrooms_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_classrooms_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`);

--
-- Constraints for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD CONSTRAINT `student_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `learning_areas` (`area_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teacher_classrooms`
--
ALTER TABLE `teacher_classrooms`
  ADD CONSTRAINT `teacher_classrooms_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `teacher_classrooms_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`classroom_id`);

--
-- Constraints for table `teacher_evaluation_responses`
--
ALTER TABLE `teacher_evaluation_responses`
  ADD CONSTRAINT `teacher_evaluation_responses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `teacher_evaluation_responses_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `teacher_evaluation_responses_ibfk_3` FOREIGN KEY (`criterion_id`) REFERENCES `evaluation_criteria` (`criterion_id`);

--
-- Constraints for table `weekly_schedule`
--
ALTER TABLE `weekly_schedule`
  ADD CONSTRAINT `weekly_schedule_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `weekly_schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `weekly_schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `weekly_schedule_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`kpswacth_reg`@`localhost` EVENT `clean_expired_tokens` ON SCHEDULE EVERY 1 HOUR STARTS '2025-07-06 16:19:26' ON COMPLETION NOT PRESERVE ENABLE DO CALL CleanExpiredTokens()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
