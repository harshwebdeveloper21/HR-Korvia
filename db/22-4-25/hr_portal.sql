-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 08:21 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_detail`
--

CREATE TABLE `account_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `acc_number` int(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `acc_in_name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `branch_code` varchar(255) NOT NULL,
  `created_by` int(111) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_detail`
--

INSERT INTO `account_detail` (`id`, `user_id`, `acc_number`, `bank_name`, `ifsc_code`, `acc_in_name`, `branch_name`, `branch_code`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 2147483647, 'HDFC', 'BOB1243', 'shital pipaliya', 'asdf', '123ABCD', 1, '2025-04-17 06:27:02', '2025-04-17 06:27:02'),
(2, 3, 2147483647, 'HDFC', 'BOB1243', 'hirva kalsariya', 'Ved Road', '123ABCD', 1, '2025-04-18 03:42:54', '2025-04-18 03:42:54'),
(8, 4, 1234567890, 'bob', '123abc', 'sneha makvana', 'ved road', '123sdf', 1, '2025-04-20 23:57:49', '2025-04-20 23:57:49'),
(9, 16, 2147483647, 'bob', 'BOB1243', 'spnaloka', 'Ved Road', '123er', 1, '2025-04-21 00:24:23', '2025-04-21 00:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `meal_break` time DEFAULT '00:30:00',
  `work_hours` time DEFAULT NULL,
  `overtime` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('absent','present') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `check_in_time`, `check_out_time`, `meal_break`, `work_hours`, `overtime`, `created_at`, `updated_at`, `status`) VALUES
(1, 2, '2025-02-20', '13:20:04', '13:20:31', '00:30:00', '00:00:27', NULL, '2025-02-20 07:50:04', '2025-02-20 07:50:31', 'present'),
(2, 3, '2025-02-21', '04:56:14', '05:01:01', '00:30:00', '00:04:47', NULL, '2025-02-20 23:26:14', '2025-02-20 23:31:01', 'present'),
(3, 2, '2025-03-20', '12:27:11', '12:27:48', '00:30:00', '00:00:37', NULL, '2025-03-20 06:57:11', '2025-03-20 06:57:48', 'present'),
(4, 4, '2025-03-26', '07:20:47', '07:20:54', '00:30:00', '00:00:07', NULL, '2025-03-26 01:50:47', '2025-03-26 01:50:54', 'present'),
(5, 2, '2025-03-29', '07:08:49', '07:09:27', '00:30:00', '00:00:38', NULL, '2025-03-29 01:38:49', '2025-03-29 01:39:27', 'present'),
(6, 2, '2025-03-29', '07:09:47', NULL, '00:30:00', NULL, NULL, '2025-03-29 01:39:47', '2025-03-29 01:39:47', 'present'),
(7, 4, '2025-03-29', '07:14:41', '07:15:04', '00:30:00', '00:00:23', NULL, '2025-03-29 01:44:41', '2025-03-29 01:45:04', 'present'),
(8, 4, '2025-03-29', '07:19:51', '07:22:51', '00:30:00', '00:03:00', NULL, '2025-03-29 01:49:51', '2025-03-29 01:52:51', 'present'),
(9, 4, '2025-03-29', '07:23:38', '07:23:43', '00:30:00', '00:00:05', NULL, '2025-03-29 01:53:38', '2025-03-29 01:53:43', 'present'),
(10, 2, '2025-04-07', '05:28:28', '05:28:46', '00:30:00', '00:00:18', NULL, '2025-04-06 23:58:28', '2025-04-06 23:58:46', 'present'),
(11, 2, '2025-04-07', '06:03:13', NULL, '00:30:00', NULL, NULL, '2025-04-07 00:33:13', '2025-04-07 00:33:13', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `id` int(11) NOT NULL,
  `candidate_name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_date` date NOT NULL,
  `phone_number` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'applied',
  `notes` varchar(255) DEFAULT NULL,
  `resume` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`id`, `candidate_name`, `email`, `job_id`, `job_date`, `phone_number`, `status`, `notes`, `resume`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'aaa', 'aaa@gmail.com', 1, '2025-03-26', 2147483647, 'applied', '', 'uploads/resumes/1742967064_d5c4e2c2ea0d7f857cf7.pdf', '2025-03-26 00:01:04', '2025-03-26 00:01:04', 1),
(2, 'xyz', 'xyz@gmail.com', 2, '2025-03-26', 2147483647, 'applied', '', 'uploads/resumes/1742969798_b1ca4835838b12ecc65b.pdf', '2025-03-26 00:46:38', '2025-03-26 00:46:38', 1),
(3, 'Jessamine Meyer', 'tocynuso@mailinator.com', 2, '2025-03-27', 1234567890, 'applied', '', 'uploads/resumes/1743069827_aea940ec074b31fbc20d.pdf', '2025-03-27 04:33:47', '2025-03-27 04:35:51', 1),
(4, 'bcdd', 'bcda@gmail.com', 10, '2025-03-28', 2147483647, 'applied', '', 'uploads/resumes/1743155862_ffc1f7fcbf4f79ee2f91.pdf', '2025-03-28 04:27:42', '2025-03-28 04:27:42', 1),
(5, 'candiate20', 'candidate20@gmail.com', 5, '2025-04-15', 2147483647, 'applied', 'dfd', 'uploads/resumes/1744714507_9dca34c68b777eacaef3.pdf', '2025-04-15 05:25:24', '2025-04-15 05:25:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `files` varchar(255) DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `sender_id`, `receiver_id`, `message`, `files`, `sent_at`) VALUES
(1, 1, 2, 'dadad', NULL, '2025-04-07 10:33:35'),
(2, 1, 2, 'file_example_MP4_480_1_5MG.mp4', '[\"1744022193_634c66f2e097cc494e50.mp4\"]', '2025-04-07 10:36:33'),
(3, 2, 1, 'hi', NULL, '2025-04-07 10:37:08'),
(4, 2, 3, 'heloo', NULL, '2025-04-07 11:20:23'),
(5, 2, 1, 'good morning', NULL, '2025-04-08 04:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `country_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `city_name`, `country_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'surat', 1, 1, '2025-02-20 07:44:30', '2025-02-20 07:44:30'),
(23, 'mahuva', 31, 1, '2025-03-26 23:57:12', '2025-03-26 23:57:12'),
(25, 'mahuva', 39, 1, '2025-03-27 06:40:32', '2025-03-27 06:40:32'),
(26, 'haridvar', 41, 1, '2025-03-27 06:44:54', '2025-03-27 06:44:54'),
(27, 'nan', 43, 1, '2025-03-27 06:54:06', '2025-03-27 06:54:06'),
(28, 'navati', 44, 1, '2025-03-27 06:56:52', '2025-03-27 06:56:52'),
(29, 'klklkl', 45, 1, '2025-03-27 07:24:52', '2025-03-27 07:24:52'),
(30, 'dfdfdfgdgdfgfgf', 46, 1, '2025-03-27 22:53:55', '2025-03-27 22:53:55'),
(31, 'kalakata', 47, 1, '2025-04-06 23:37:37', '2025-04-06 23:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `task_id`, `comment`, `created_at`) VALUES
(1, 1, 2, 'hi', '2025-04-18 15:52:55'),
(2, 1, 2, 'hi', '2025-04-18 15:53:28'),
(3, 1, 2, 'hi', '2025-04-18 15:53:29'),
(4, 1, 2, 'hi', '2025-04-18 15:54:38'),
(5, 1, 2, 'hello', '2025-04-18 16:03:03'),
(6, 1, 2, 'good morning\n', '2025-04-18 16:04:50'),
(7, 1, 2, 'hello', '2025-04-18 16:05:47'),
(8, 1, 2, 'helllo', '2025-04-18 16:13:57'),
(9, 1, 2, 'helllo', '2025-04-18 16:13:58'),
(10, 1, 2, 'helllo', '2025-04-18 16:13:59'),
(11, 1, 2, 'fhfdhfdhdhfg', '2025-04-18 16:14:42'),
(12, 1, 2, 'zxdsadcsafcsd', '2025-04-18 16:14:57'),
(13, 1, 2, 'drfdfdf', '2025-04-18 16:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `company_logo`
--

CREATE TABLE `company_logo` (
  `id` int(11) NOT NULL,
  `logo_img` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_address` varchar(255) DEFAULT NULL,
  `company_phone` varchar(20) DEFAULT NULL,
  `company_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_logo`
--

INSERT INTO `company_logo` (`id`, `logo_img`, `company_name`, `created_by`, `created_at`, `updated_at`, `company_address`, `company_phone`, `company_email`) VALUES
(1, '1745231761_e353d5a049ee788395c6.jpg', 'ABCD Developers & Technolab', 1, '2025-04-21 10:56:51', '2025-04-21 10:56:51', 'A-5001 Ascon Palaza \r\nAdajan, Surat', '1234567890', 'fablead@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country_name`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'india', 1, '2025-02-20 07:44:06', '2025-03-28 06:27:36'),
(31, 'nepal', 1, '2025-03-26 23:56:47', '2025-03-26 23:56:47'),
(33, 'pdpdpd', 0, '2025-03-27 05:31:47', '2025-03-27 05:31:47'),
(34, 'AsS', 0, '2025-03-27 05:32:11', '2025-03-27 05:32:11'),
(35, 'nepal', 0, '2025-03-27 05:35:57', '2025-03-27 05:35:57'),
(37, 'sxadas', 1, '2025-03-27 05:50:33', '2025-03-27 05:50:33'),
(38, 'ddd', 1, '2025-03-27 05:50:49', '2025-03-27 05:50:49'),
(39, 'dddd', 1, '2025-03-27 06:37:30', '2025-03-27 06:37:30'),
(40, 'os', 1, '2025-03-27 06:41:58', '2025-03-27 06:41:58'),
(41, 'madhpadesh', 1, '2025-03-27 06:44:40', '2025-03-27 06:44:40'),
(42, 'asasassa', 1, '2025-03-27 06:50:57', '2025-03-27 06:50:57'),
(43, 'maldiv', 1, '2025-03-27 06:53:57', '2025-03-27 06:53:57'),
(44, 'Uttarpradesh', 1, '2025-03-27 06:56:14', '2025-03-27 06:56:14'),
(45, 'xyz', 1, '2025-03-27 07:24:35', '2025-03-27 07:24:35'),
(47, 'nepal', 1, '2025-04-06 23:37:17', '2025-04-06 23:37:17');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_name`, `created_at`, `updated_at`) VALUES
(1, 'Engineering', '2025-02-20 07:46:26', '2025-02-20 07:46:26'),
(12, 'Designing', '2025-03-26 23:49:43', '2025-03-26 23:49:43'),
(13, 'Mechanical', '2025-03-26 23:57:58', '2025-03-26 23:57:58'),
(14, 'Mnagament', '2025-03-27 01:06:40', '2025-03-27 01:06:40'),
(15, 'civil management', '2025-03-27 01:10:47', '2025-03-27 01:10:47'),
(16, 'pppp', '2025-03-27 01:14:42', '2025-03-27 01:14:42'),
(18, 'fdgdfhdfhfghfg', '2025-03-27 04:56:42', '2025-03-27 04:56:42'),
(19, 'dfdgdfgfgfhghjjmhhkm', '2025-03-27 04:57:03', '2025-03-27 04:57:03'),
(20, 'axsaadxs', '2025-03-27 04:59:40', '2025-03-27 04:59:40'),
(22, 'dsdsdsfcsfv', '2025-03-27 05:05:43', '2025-03-27 05:05:43'),
(23, 'ppppppppppppppppppppppp', '2025-03-27 05:21:09', '2025-03-27 05:21:09'),
(24, 'SA', '2025-03-27 05:53:42', '2025-03-27 05:53:42'),
(25, 'oooooooooo', '2025-03-27 06:09:42', '2025-03-27 06:09:42'),
(26, 'Accountant', '2025-03-27 06:11:36', '2025-03-27 06:11:36'),
(27, 'website', '2025-03-27 06:14:45', '2025-03-27 06:14:45'),
(28, 'developers', '2025-03-27 06:46:10', '2025-03-27 06:46:10'),
(29, 'department5', '2025-03-27 06:57:25', '2025-03-27 06:57:25'),
(30, 'ffffff', '2025-03-27 07:25:48', '2025-03-27 07:25:48'),
(31, 'sdsdfasfcascf', '2025-03-27 22:53:10', '2025-03-27 22:53:10'),
(33, 'sasas', '2025-03-28 02:32:03', '2025-03-28 02:32:03'),
(34, 'asasa', '2025-03-28 02:43:47', '2025-03-28 02:43:47'),
(35, 'developers', '2025-03-31 01:49:19', '2025-03-31 01:49:19'),
(36, 'developers', '2025-04-06 23:37:49', '2025-04-06 23:37:49'),
(37, 'sasawer', '2025-04-18 06:12:50', '2025-04-18 06:12:50');

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `designation_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`id`, `department_id`, `designation_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'developers', '2025-02-20 07:46:48', '2025-02-20 07:46:48'),
(5, 13, 'designer', '2025-03-26 23:58:20', '2025-03-26 23:58:20'),
(6, 13, 'codeignater', '2025-03-27 03:38:04', '2025-03-27 03:38:04'),
(7, 13, 'codeignater', '2025-03-27 03:38:06', '2025-03-27 03:38:06'),
(8, 13, 'codeignater', '2025-03-27 03:38:13', '2025-03-27 03:38:13'),
(10, 1, 'egineering', '2025-03-27 03:48:06', '2025-03-27 03:48:06'),
(11, 1, 'coding', '2025-03-27 03:48:16', '2025-03-27 03:48:16'),
(12, 12, 'coding', '2025-03-27 03:49:35', '2025-03-27 03:49:35'),
(14, 23, 'egineering', '2025-03-27 05:21:26', '2025-03-27 05:21:26'),
(15, 29, 'flutter', '2025-03-27 06:58:03', '2025-03-27 06:58:03'),
(16, 30, 'developers22', '2025-03-27 07:26:01', '2025-03-27 07:26:01'),
(17, 36, 'egineering', '2025-04-06 23:38:22', '2025-04-06 23:38:22'),
(18, 36, 'yhtfghjgfjfg', '2025-04-06 23:38:54', '2025-04-06 23:38:54');

-- --------------------------------------------------------

--
-- Table structure for table `employee_of_month_certificates`
--

CREATE TABLE `employee_of_month_certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `month_year` varchar(7) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_of_month_certificates`
--

INSERT INTO `employee_of_month_certificates` (`id`, `user_id`, `template_id`, `month_year`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 4, 3, '2025-04', '2025-04-15 12:42:47', 1, '2025-04-15 12:42:47'),
(2, 22, 5, '2025-04', '2025-04-15 12:44:44', 1, '2025-04-15 12:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `empreport`
--

CREATE TABLE `empreport` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `joining_date` int(11) NOT NULL,
  `designation_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_of_month`
--

CREATE TABLE `emp_of_month` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `emp_image` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_of_month`
--

INSERT INTO `emp_of_month` (`id`, `title`, `content`, `emp_image`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'Employee Of The Month 1', '<div style=\"font-family: Arial, sans-serif;\">\r\n<div style=\"text-align: center;\">\r\n<h2 style=\"color: #000000; margin-bottom: 10px;\">Employee of the Month</h2>\r\n</div>\r\n<p style=\"font-size: 16px;\">Dear <strong><!--?= $employee[\'name\']; ?--></strong>, {{username}}</p>\r\n<p style=\"font-size: 14px;\">We are pleased to inform you that you have been selected as the <strong>Employee of the Month</strong> for your outstanding contributions to the team and company during the month of <strong><!--?= date(\'F, Y\'); ?--></strong>.</p>\r\n<p style=\"font-size: 14px;\">Your exceptional performance in the following areas has truly set you apart:</p>\r\n<ul>\r\n<li><strong>Goals Achieved: </strong>{{goals_achieved}}<!--?= $performance[\'goals_achieved\']; ?--></li>\r\n<li><strong>Team Work: </strong>{{team_work}}</li>\r\n<li><strong>Management Skills: </strong>{{management}}<!--?= $performance[\'management\']; ?--></li>\r\n<li><strong>Presentation Skills: </strong>{{presentation_skill}}<!--?= $performance[\'presentation_skill\']; ?--></li>\r\n<li><strong>Behavior:&nbsp;</strong><!--?= $performance[\'behaviour\']; ?-->{{behaviour}}</li>\r\n<li><strong>Rating:&nbsp;</strong><!--?= $performance[\'behaviour\']; ?-->{{rating}}</li>\r\n</ul>\r\n<ul style=\"font-size: 14px; list-style-type: none; padding-left: 0;\">\r\n<li>We appreciate your hard work and dedication. Your ability to exceed expectations is a great example for all of us, and we are proud to have you as part of our team.</li>\r\n</ul>\r\n<p style=\"font-size: 14px;\">As a token of our appreciation, you will receive the following:</p>\r\n<ul style=\"font-size: 14px; list-style-type: none; padding-left: 0;\">\r\n<li>A certificate of recognition</li>\r\n<li>Special mention in the upcoming team meeting</li>\r\n</ul>\r\n<p style=\"font-size: 14px;\">Once again, congratulations on your well-deserved recognition. Keep up the great work!</p>\r\n<p style=\"font-size: 14px;\">Sincerely,<br><strong>{{created_by}}</strong><br>{{company_name}}</p>\r\n<p style=\"font-size: 14px;\"><strong>Date: {{today_date}}</strong></p>\r\n<p style=\"font-size: 14px;\">&nbsp;</p>\r\n</div>\r\n<div style=\"width: 20%; border-top: 1px solid rgb(0, 0, 0); font-size: 14px; padding-top: 5px;\">HR Department</div>', '1744437784_62bc2e9fca62dd65afaf.webp', 1, '2025-04-12 00:33:04', '2025-04-15 07:35:28'),
(4, 'emp of Month 2', '<h2 class=\"\" style=\"text-align: center;\" data-start=\"74\" data-end=\"174\">Employee of the Month</h2>\r\n<p class=\"\" data-start=\"268\" data-end=\"291\">Dear {{username}},</p>\r\n<p class=\"\" data-start=\"293\" data-end=\"519\">I am pleased to announce that you have been selected as the <strong data-start=\"353\" data-end=\"378\">Employee of the Month</strong> for <strong data-start=\"383\" data-end=\"399\">{{today_month}}</strong>. This recognition is a direct reflection of your hard work, dedication, and exceptional performance during this period.</p>\r\n<p class=\"\" data-start=\"521\" data-end=\"740\">Your commitment to excellence and positive attitude have made a significant impact on our team and company. We are truly grateful for your contributions and the value you bring to your role as <strong data-start=\"714\" data-end=\"739\">{{designation_name}}</strong>.</p>\r\n<p class=\"\" data-start=\"742\" data-end=\"807\">As a token of our appreciation, you will receive the following:</p>\r\n<ul>\r\n<li><strong>Goals Achieved:&nbsp;</strong>{{goals_achieved}}</li>\r\n<li><strong>Team Work:&nbsp;</strong>{{team_work}}</li>\r\n<li><strong>Management Skills:&nbsp;</strong>{{management}}</li>\r\n<li><strong>Presentation Skills:&nbsp;</strong>{{presentation_skill}}</li>\r\n<li><strong>Behavior:&nbsp;</strong>{{behaviour}}</li>\r\n<li><strong>Rating:&nbsp;</strong>{{rating}}</li>\r\n</ul>\r\n<p class=\"\" data-start=\"988\" data-end=\"1188\">Once again, thank you for your outstanding efforts and for going above and beyond in your work. Your contributions do not go unnoticed, and we are incredibly fortunate to have you as part of our team.</p>\r\n<p class=\"\" data-start=\"1190\" data-end=\"1272\">Please continue to lead by example, and know that your dedication inspires us all.</p>\r\n<p class=\"\" data-start=\"1274\" data-end=\"1330\">Congratulations again on this well-deserved recognition.</p>\r\n<p class=\"\" data-start=\"1332\" data-end=\"1400\">Sincerely,<br data-start=\"1342\" data-end=\"1345\"><strong data-start=\"1345\" data-end=\"1360\">{{created_by}}</strong><br data-start=\"1379\" data-end=\"1382\"><strong data-start=\"1382\" data-end=\"1400\">{{company_name}}</strong></p>\r\n<p class=\"\" data-start=\"1332\" data-end=\"1400\">&nbsp;</p>\r\n<div style=\"width: 20%; border-top: 1px solid rgb(0, 0, 0); font-size: 14px; padding-top: 5px;\">HR Department</div>', '1744437846_167b46ed7320b217d8a4.webp', 1, '2025-04-12 00:34:06', '2025-04-15 05:15:05'),
(5, 'Emp of month', '<h1 style=\"font-size: 32px; color: #000000; margin-bottom: 5px; text-align: center;\">Certificate of Excellence</h1>\r\n<h2 style=\"font-size: 24px; margin-bottom: 15px; text-align: center;\">Employee of the Month</h2>\r\n<p style=\"font-size: 26px; font-weight: bold; color: rgb(51, 51, 51); margin-bottom: 5px; text-align: center;\">{{username}}</p>\r\n<p style=\"font-size: 18px; margin: 20px 40px;\">This certificate is proudly awarded to <strong>{{username}}</strong> in recognition of exceptional performance and dedication during the month of <strong>{{today_month}}</strong>.</p>\r\n<p style=\"font-size: 16px; margin-top: 30px;\">&nbsp; &nbsp; &nbsp; &nbsp; Awarded on: <strong>{{today_date}}</strong></p>\r\n<div style=\"margin-top: 60px; display: flex; justify-content: space-around;\">\r\n<div style=\"width: 40%; border-top: 1px solid rgb(0, 0, 0); font-size: 14px; padding-top: 5px; text-align: center;\">HR Department</div>\r\n</div>', '1744623610_02635978f1209e0cf0e3.jpg', 1, '2025-04-14 04:10:10', '2025-04-14 04:43:33'),
(6, 'New Emp  of Month', '<p class=\"\" style=\"text-align: center;\" data-start=\"137\" data-end=\"197\"><strong data-start=\"137\" data-end=\"165\">&nbsp;EMPLOYEE OF THE MONTH</strong><br data-start=\"165\" data-end=\"168\"><strong data-start=\"168\" data-end=\"197\">Certificate of Excellence</strong></p>\r\n<p class=\"\" data-start=\"199\" data-end=\"239\">This certificate is proudly presented to</p>\r\n<p class=\"\" data-start=\"199\" data-end=\"239\">Dear<strong data-start=\"241\" data-end=\"260\"> {{username}},</strong></p>\r\n<p class=\"\" data-start=\"262\" data-end=\"443\">In recognition of your outstanding performance, dedication, and contributions to the success of our team. Your hard work and commitment to excellence have made a significant impact.</p>\r\n<p class=\"\" data-start=\"445\" data-end=\"470\"><strong data-start=\"445\" data-end=\"470\">Month: {{today_month}}</strong></p>\r\n<p class=\"\" data-start=\"472\" data-end=\"575\">We sincerely appreciate your efforts and are honored to recognize you as our <strong data-start=\"549\" data-end=\"574\">Employee of the Month</strong>.</p>\r\n<p class=\"\" data-start=\"580\" data-end=\"664\"><em data-start=\"580\" data-end=\"595\">Presented on:</em> {{today_date}}<br data-start=\"615\" data-end=\"618\"><em data-start=\"618\" data-end=\"633\">Presented by:</em> {{company_name}}</p>\r\n<p class=\"\" data-start=\"580\" data-end=\"664\">{{created_by}}<br data-start=\"723\" data-end=\"726\">{{company_name}}</p>\r\n<p class=\"\" data-start=\"580\" data-end=\"664\">&nbsp;</p>\r\n<div style=\"width: 20%; border-top: 1px solid rgb(0, 0, 0); font-size: 14px; padding-top: 5px;\">HR Department</div>', '1744626980_ca36332a4e19938f8e73.webp', 1, '2025-04-14 05:06:20', '2025-04-15 05:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `exprience`
--

CREATE TABLE `exprience` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `template_id` int(11) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exprience`
--

INSERT INTO `exprience` (`id`, `employee_id`, `from_date`, `to_date`, `template_id`, `generated_by`, `created_at`) VALUES
(1, 2, '2025-03-07', '2025-04-25', 5, 1, '2025-04-15 11:45:52'),
(2, 0, '0000-00-00', '0000-00-00', 0, 1, '2025-04-15 12:41:48'),
(3, 3, '2025-02-25', '2025-04-30', 4, 1, '2025-04-16 04:32:18'),
(4, 4, '2025-02-28', '2025-04-30', 3, 1, '2025-04-16 05:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `exprience_letter_templetes`
--

CREATE TABLE `exprience_letter_templetes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `template_img` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exprience_letter_templetes`
--

INSERT INTO `exprience_letter_templetes` (`id`, `title`, `content`, `template_img`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'exparinece12', '<h2 class=\"text-align-justify\" data-node-id=\"35a2c706-cd77-44d5-a902-a276177d455c\">{{company_name}}</h2>\r\n<p class=\"text-align-justify\" data-node-id=\"35a2c706-cd77-44d5-a902-a276177d455c\">{{company_address}}&nbsp;&nbsp;<br>{{company_email}}</p>\r\n<p class=\"text-align-justify\" data-node-id=\"35a2c706-cd77-44d5-a902-a276177d455c\">{{current_date}}</p>\r\n<p class=\"text-align-justify\" data-node-id=\"2766c83d-f113-4c4e-914c-509a9b3a14ec\">{{employee_name}}<br>{{designation}}&nbsp;&nbsp;<br>{{department}}</p>\r\n<p class=\"text-align-center\" style=\"text-align: center;\" data-node-id=\"2766c83d-f113-4c4e-914c-509a9b3a14ec\">TO WHOM IT MAY CONCERN</p>\r\n<p><strong>Subject: Work Experience Certificate</strong></p>\r\n<p>This is to certify that {{employee_name}} has worked as a {{designation}} with {{company_name}} from {{joining_date}} to {{current_date}}.</p>\r\n<p>During his/her/their tenure, {{employee_name}}&rsquo;s services were found to be exceptional. He/she/they has shown strong technical skills, and problem-solving abilities and has been dedicated towards his/her/their work throughout.&nbsp;</p>\r\n<p>{{employee_name}} has been an asset to the {{department}} team and has played a significant role in completing successful projects.&nbsp;</p>\r\n<p>{{company_name}} wishes {{employee_name}} the best in his/her/their future endeavors.&nbsp;</p>\r\n<p>Sincerely, &nbsp;&nbsp;<br>{{created_by}} &nbsp;<br><br></p>\r\n<p class=\"text-align-justify\" data-node-id=\"ccbe78da-480b-4268-8094-ef2069b0f239\">&nbsp;</p>', '1744367650_f062e7aa2143e3864638.jpg', 1, '2025-04-11 10:34:10', '2025-04-12 08:34:38'),
(3, 'exprienceletter12', '<p>&lt;p&gt;This is to certify that &lt;strong&gt;{{employee_name}}&lt;/strong&gt; has worked as a &lt;strong&gt;{{designation}}&lt;/strong&gt; in our organization since &lt;strong&gt;{{joining_date}}&lt;/strong&gt;.&lt;/p&gt;<br>&lt;p&gt;Issued by &lt;strong&gt;{{company_name}}&lt;/strong&gt; on &lt;strong&gt;{{current_date}}&lt;/strong&gt;.&lt;/p&gt;</p>', '1744434413_4df4aec3c9874b4417f0.png', 1, '2025-04-12 05:06:53', '2025-04-12 05:06:53'),
(4, 'templet2', '<h1 class=\"text-align-center\" style=\"text-align: center;\" data-node-id=\"5f242647-7b13-47a4-9257-3fe53cce947c\" data-pm-slice=\"1 1 []\">Experience Letter</h1>\r\n<p class=\"text-align-justify\" data-node-id=\"f40967bc-5f11-408c-b514-9f5a06bb7cd9\">{{current_date}}</p>\r\n<p class=\"text-align-justify\" data-node-id=\"f40967bc-5f11-408c-b514-9f5a06bb7cd9\">{{created_by}}</p>\r\n<p class=\"text-align-justify\" data-node-id=\"f40967bc-5f11-408c-b514-9f5a06bb7cd9\"><strong>{{company_name}}</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"c7f2e0b8-bd8f-44a9-a337-4b9e90c7d2b3\">Dear {{employee_name}},</p>\r\n<p class=\"text-align-justify\" data-node-id=\"81bcd5e3-4940-4cb4-9cdb-f0776f16e3b3\">It is with great pleasure that I write this letter in confirmation of {{employee_name}} working with our esteemed organization {{company_name}} for the period spanning from {{joining_date}} to {{current_date}}. During his tenure, he held the position of Data Analyst with dedication and professionalism.</p>\r\n<p class=\"text-align-justify\" data-node-id=\"e0491f9a-6e20-4b37-8d87-ead66c7b8228\">As a Data Analyst, {{employee_name}}\' main tasks included analyzing data, creating reports, and assisting in strategic decision-making processes. He contributed significantly to our business with his analytical skills, attention to detail, and innovative solutions to tackle business complexities. His performance and work ethic were commendable, he consistently showed enthusiasm and initiative in his roles and responsibilities.</p>\r\n<p class=\"text-align-justify\" data-node-id=\"609e0c42-f330-4a76-bb7a-f219bb273661\">We appreciate his contributions to our team and confirm that his relationship with {{company_name}} ended on good terms. We wish him all the best in his future endeavors.</p>\r\n<p class=\"text-align-justify\" data-node-id=\"db10514d-a379-4517-8028-c58a9f5b95bf\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"4a653d5d-f7d9-483c-a863-29a6cf293709\">Best regards,</p>\r\n<p class=\"text-align-justify\" data-node-id=\"4a653d5d-f7d9-483c-a863-29a6cf293709\">{{company_name}}</p>', '1744617549_4ca09182ee0373a3a7b1.png', 1, '2025-04-14 07:59:09', '2025-04-14 10:27:56'),
(5, 'templet5', '<p class=\"mb-0\">{{company_name}}</p>\r\n<p class=\"mb-0\">{{employee_name}}</p>\r\n<p>{{address_1}}</p>\r\n<p>To whom it might pertain anywhere.</p>\r\n<p><strong>Subject: Work experience certificate</strong></p>\r\n<p>This certifies that {{employee_name}} has been working from {{joining_date}} to {{leaving_date}} as a {{designation}}.</p>\r\n<p><strong>{{employee_name}}</strong>&nbsp;proved extraordinary in the tasks allocated throughout this period. He/she/they possessed great technical prowess and problem-solving capacity. His/her/their commitment to the task was outstanding; their contributions to the business&rsquo;s success have been remarkable.</p>\r\n<p>The {{department}} team has dramatically benefited from {{employee_name}}, who has also completed effective initiatives.</p>\r\n<p><strong>{{company_name}}</strong> hopes {{employee_name}} will succeed in the following projects.</p>\r\n<p class=\"mb-0\">Sincerely,</p>\r\n<p class=\"mb-0\"><strong>{{created_by}}</strong></p>\r\n<p><strong>Employee Signature: ____________________&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;HR Signature: ____________________</strong></p>\r\n<p><strong>Date: {{current_date}}</strong></p>', '1744629675_809d614766ef6430d8d9.jpg', 1, '2025-04-14 11:21:15', '2025-04-15 12:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(200) NOT NULL,
  `schedule_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`id`, `candidate_id`, `job_id`, `description`, `status`, `schedule_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '', 'completed', '2025-03-29 04:06:00', 1, '2025-03-26 00:06:24', '2025-03-26 00:06:57'),
(2, 4, 10, '', 'completed', '2025-04-02 15:28:00', 1, '2025-03-28 04:29:06', '2025-03-28 04:30:10'),
(3, 4, 10, '', 'completed', '2025-04-02 15:30:00', 1, '2025-03-28 04:30:39', '2025-03-28 04:47:00'),
(4, 4, 10, '', 'completed', '2025-04-05 15:49:00', 1, '2025-03-28 04:49:44', '2025-03-28 04:49:56'),
(5, 2, 2, '', 'completed', '2025-04-02 16:17:00', 1, '2025-03-28 05:17:29', '2025-03-28 05:17:54'),
(6, 3, 2, 'Elit a do molestiae', 'scheduled', '2025-04-02 03:39:00', 1, '2025-03-28 05:25:51', '2025-03-28 05:25:51'),
(7, 5, 5, 'ggffg', 'completed', '2025-04-25 16:26:00', 1, '2025-04-15 05:26:21', '2025-04-15 05:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `job_title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `status` enum('open','close') NOT NULL,
  `addresses_id` int(11) NOT NULL,
  `locations_id` int(11) NOT NULL,
  `age` varchar(20) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `job_type` enum('full','part') NOT NULL,
  `experience` int(11) NOT NULL,
  `salary_range` varchar(20) NOT NULL,
  `post_date` date NOT NULL,
  `close_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_title`, `description`, `department_id`, `status`, `addresses_id`, `locations_id`, `age`, `gender`, `job_type`, `experience`, `salary_range`, `post_date`, `close_date`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Designing', 'Page layouts look better with something in each section. Web page designers, content writers, and layout artists use lorem ipsum, also known as placeholder copy, to distinguish which areas on a page will hold advertisements, editorials, and filler before the final written content and website designs receive client approval.', 1, 'open', 8, 0, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-26', '2025-03-29', '2025-03-25 23:58:44', '2025-03-25 23:58:44', 1),
(2, 'Mechanical', 'Page layouts look better with something in each section. Web page designers, content writers, and layout artists use lorem ipsum, also known as placeholder copy, to distinguish which areas on a page will hold advertisements, editorials, and filler before the final written content and website designs receive client approval.', 1, 'open', 10, 0, '23-56', 'female', 'full', 5, '19500-76500', '2025-03-26', '2025-03-31', '2025-03-26 00:04:45', '2025-03-26 00:04:45', 1),
(3, 'Designing', '', 13, 'open', 3, 0, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-27', '2025-04-04', '2025-03-27 00:32:07', '2025-03-27 00:32:07', 1),
(4, 'Designing', '', 12, 'open', 10, 0, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-27', '2025-04-05', '2025-03-27 00:36:26', '2025-03-27 00:36:26', 1),
(5, 'Designing', '', 13, 'open', 8, 0, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-27', '2025-04-05', '2025-03-27 00:44:14', '2025-03-27 00:44:14', 1),
(6, 'Designing', '', 12, 'open', 3, 0, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-27', '2025-04-05', '2025-03-27 00:54:46', '2025-03-27 00:54:46', 1),
(7, 'Animi excepturi in ', 'Quae voluptatem Quo', 13, 'open', 18, 10, '18-65', 'female', 'part', 5, '1000-100000', '2025-03-27', '2025-04-05', '2025-03-27 02:06:15', '2025-03-27 02:06:15', 1),
(8, 'Rerum cum repellendu', 'Quas aliquip odio is', 12, 'open', 18, 10, '18-65', 'male', 'part', 56, '1000-100000', '2025-03-27', '1978-03-17', '2025-03-27 02:23:27', '2025-03-27 02:23:27', 1),
(9, 'Rem velit deserunt d', 'Pariatur Placeat p', 25, 'open', 30, 12, '18-65', 'male', 'part', 5, '15500-75500', '2025-03-28', '2025-03-29', '2025-03-28 01:10:06', '2025-03-28 01:10:06', 1),
(10, 'Testing', '', 33, 'open', 25, 10, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-28', '2025-03-31', '2025-03-28 02:33:21', '2025-03-28 02:33:21', 1),
(11, 'In quia modi repelle', 'Provident cupidatat', 34, 'open', 37, 11, '18-65', 'female', 'part', 5, '1000-100000', '2025-03-28', '2025-04-01', '2025-03-28 02:44:34', '2025-03-28 02:44:34', 1),
(13, 'Designing', '', 35, 'open', 40, 11, '18-65', 'female', 'full', 5, '1000-100000', '2025-03-31', '2025-04-04', '2025-03-31 01:50:17', '2025-03-31 01:51:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `job_location`
--

CREATE TABLE `job_location` (
  `location_id` int(11) NOT NULL,
  `job_location` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_location`
--

INSERT INTO `job_location` (`location_id`, `job_location`, `created_at`, `updated_at`) VALUES
(10, 'Surat', '2025-03-12 04:23:22', '2025-03-13 06:00:02'),
(11, 'Pune', '2025-03-12 04:23:50', '2025-03-13 05:42:21'),
(12, 'Vadodara', '2025-03-12 04:35:19', '2025-03-12 04:35:19'),
(13, 'Ahmadabad', '2025-03-28 01:12:13', '2025-03-28 01:12:13');

-- --------------------------------------------------------

--
-- Table structure for table `job_location_addresses`
--

CREATE TABLE `job_location_addresses` (
  `address_id` int(11) NOT NULL,
  `locations_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city_id` int(11) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country_id` int(11) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_location_addresses`
--

INSERT INTO `job_location_addresses` (`address_id`, `locations_id`, `address`, `city_id`, `state`, `country_id`, `postal_code`, `created_at`, `updated_at`) VALUES
(17, 10, 'katargam', 1, 'gujarat', 31, '234567', '2025-03-27 07:30:59', '2025-03-27 07:30:59'),
(18, 10, '85-kadodara', 1, 'gujarat', 1, '234567', '2025-03-27 07:35:37', '2025-03-27 07:35:37'),
(19, 10, 'asasadad', 1, 'gujarat', 41, '2345678', '2025-03-28 00:00:26', '2025-03-28 05:30:26'),
(20, 10, 'dfdsgfgdf', 25, 'gujarat', 41, '2345678', '2025-03-28 00:07:29', '2025-03-28 05:37:29'),
(21, 10, '', 0, '', 0, '', '2025-03-28 00:07:56', '2025-03-28 05:37:56'),
(22, 11, 'szdcsafd', 23, 'szzxzX', 40, '2345678', '2025-03-28 00:12:39', '2025-03-28 05:42:39'),
(24, 10, 'laldarvaja baliyam bilding', 1, 'gujarat', 31, '2345678', '2025-03-28 00:45:20', '2025-03-28 06:15:20'),
(25, 10, 'adsad', 23, 'dasasd', 1, '2345678', '2025-03-28 00:51:12', '2025-03-28 06:21:12'),
(26, 10, 'wdewrderfewr', 1, 'gujarat', 31, '656567', '2025-03-28 00:51:36', '2025-03-28 06:21:36'),
(27, 10, '', 0, '', 0, '', '2025-03-28 01:00:59', '2025-03-28 06:30:59'),
(28, 11, 'adsfdsfdsa', 1, 'dfgdfg', 39, '2345678', '2025-03-28 01:01:15', '2025-03-28 06:31:15'),
(30, 12, 'kadodtar katargam', 1, 'gujarat', 1, '234567', '2025-03-28 01:09:03', '2025-03-28 06:39:03'),
(31, 13, 'SsASas', 1, 'gujarat', 43, '123456', '2025-03-28 06:55:10', '2025-03-28 06:55:10'),
(35, 10, 'aBSA', 1, 'gujarat', 43, '656567', '2025-03-28 02:34:46', '2025-03-28 08:04:46'),
(36, 10, 'sfdfdfdfdfgdg', 1, 'gujarat', 43, '2345678', '2025-03-28 02:40:59', '2025-03-28 08:10:59'),
(37, 11, 'klkl', 23, 'assad', 43, '2345678', '2025-03-28 02:44:13', '2025-03-28 08:14:13'),
(38, 10, 'jsk', 1, 'gujarat', 33, '234567', '2025-03-28 03:33:04', '2025-03-28 09:03:04'),
(40, 11, 'vv', 1, 'gujarat', 42, '234567', '2025-03-31 01:49:56', '2025-03-31 07:19:56'),
(41, 11, 'pppp', 1, 'gujarat', 45, '2345678', '2025-03-31 01:51:07', '2025-03-31 07:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `no_of_day` int(11) NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `leave_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `firstname`, `start_date`, `end_date`, `no_of_day`, `reason`, `created_at`, `leave_id`, `updated_at`, `created_by`, `user_id`, `status`) VALUES
(1, '', '2025-04-08', '2025-04-12', 5, 'hi', '2025-04-09 00:57:36', 2, '2025-04-09 00:57:53', 1, 2, 'approved'),
(2, '', '2025-05-14', '2025-05-15', 2, 'hh', '2025-04-09 04:49:57', 3, '2025-04-11 00:15:10', 1, 3, 'approved'),
(3, '', '2025-05-13', '2025-05-15', 3, 'hhh', '2025-04-09 23:13:36', 3, '2025-04-09 23:14:01', 1, 2, 'approved'),
(4, '', '2025-05-27', '2025-05-30', 4, 'sdsd', '2025-04-16 06:09:15', 3, '2025-04-16 06:10:39', 1, 2, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `leave_type`
--

CREATE TABLE `leave_type` (
  `id` int(11) NOT NULL,
  `leave_type` varchar(100) NOT NULL,
  `number_of_leaves` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_type`
--

INSERT INTO `leave_type` (`id`, `leave_type`, `number_of_leaves`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 'Vacation Leave', NULL, '2025-02-20 07:51:50', 1, '2025-02-20 07:51:50'),
(2, 'Sick Leave', 4, '2025-03-25 00:27:42', 1, '2025-04-08 03:58:55'),
(3, 'Paid Leave', 6, '2025-03-25 00:28:02', 1, '2025-04-08 03:58:31'),
(4, 'Casual Leave', NULL, '2025-03-26 01:16:49', 1, '2025-03-26 01:16:49'),
(6, 'paid leave', 5, '2025-04-08 03:49:18', 1, '2025-04-08 03:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `recipient_id`, `data`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '{\"username\":\"Kaden Mullins\",\"type\":\"employee\"}', 0, '2025-04-08 01:21:32', '2025-04-08 01:21:32'),
(2, 1, 3, '{\"username\":\"Kaden Mullins\",\"type\":\"employee\"}', 0, '2025-04-08 01:21:32', '2025-04-08 01:21:32'),
(3, 1, 17, '{\"username\":\"Kaden Mullins\",\"type\":\"employee\"}', 0, '2025-04-08 01:21:32', '2025-04-08 01:21:32'),
(4, 1, 1, '{\"username\":\"Fulton Ortiz\",\"type\":\"employee\"}', 0, '2025-04-08 01:38:27', '2025-04-08 01:38:27'),
(5, 1, 3, '{\"username\":\"Fulton Ortiz\",\"type\":\"employee\"}', 0, '2025-04-08 01:38:27', '2025-04-08 01:38:27'),
(6, 1, 17, '{\"username\":\"Fulton Ortiz\",\"type\":\"employee\"}', 0, '2025-04-08 01:38:27', '2025-04-08 01:38:27'),
(7, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Fulton Ortiz\",\"user_id\":\"24\",\"message\":\"Payroll processed for Fulton Ortiz on May 09, 2025\"}', 0, '2025-04-08 01:53:06', '2025-04-08 01:53:06'),
(8, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Fulton Ortiz\",\"user_id\":\"24\",\"message\":\"Payroll processed for Fulton Ortiz on May 09, 2025\"}', 0, '2025-04-08 01:53:06', '2025-04-08 01:53:06'),
(9, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Fulton Ortiz\",\"user_id\":\"24\",\"message\":\"Payroll processed for Fulton Ortiz on May 09, 2025\"}', 0, '2025-04-08 01:53:06', '2025-04-08 01:53:06'),
(10, 1, 24, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Fulton Ortiz\",\"user_id\":\"24\",\"message\":\"Payroll processed for Fulton Ortiz on May 09, 2025\"}', 0, '2025-04-08 01:53:06', '2025-04-08 01:53:06'),
(11, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:15', '2025-04-08 02:16:15'),
(12, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:15', '2025-04-08 02:16:15'),
(13, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:15', '2025-04-08 02:16:15'),
(14, 1, 23, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:15', '2025-04-08 02:16:15'),
(15, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 1, '2025-04-08 02:16:40', '2025-04-08 02:22:09'),
(16, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:40', '2025-04-08 02:16:40'),
(17, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:40', '2025-04-08 02:16:40'),
(18, 1, 23, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"Kaden Mullins\",\"user_id\":\"23\",\"message\":\"Payroll processed for Kaden Mullins on Apr 26, 2025\"}', 0, '2025-04-08 02:16:40', '2025-04-08 02:16:40'),
(19, 23, 1, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:16:37', '2025-04-08 04:16:37'),
(20, 23, 3, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:16:37', '2025-04-08 04:16:37'),
(21, 23, 17, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:16:37', '2025-04-08 04:16:37'),
(22, 23, 23, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:16:37', '2025-04-08 04:16:37'),
(23, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 04:27:41', '2025-04-08 04:27:41'),
(24, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 04:27:41', '2025-04-08 04:27:41'),
(25, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 04:27:41', '2025-04-08 04:27:41'),
(26, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 04:27:41', '2025-04-08 04:27:41'),
(27, 23, 1, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:55:16', '2025-04-08 04:55:16'),
(28, 23, 3, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:55:16', '2025-04-08 04:55:16'),
(29, 23, 17, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:55:16', '2025-04-08 04:55:16'),
(30, 23, 23, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 04:55:16', '2025-04-08 04:55:16'),
(31, 23, 1, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 05:02:38', '2025-04-08 05:02:38'),
(32, 23, 3, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 05:02:38', '2025-04-08 05:02:38'),
(33, 23, 17, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 05:02:38', '2025-04-08 05:02:38'),
(34, 23, 23, '{\"username\":\"Kaden Mullins\",\"type\":\"leave\"}', 0, '2025-04-08 05:02:38', '2025-04-08 05:02:38'),
(35, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:05:35', '2025-04-08 05:05:35'),
(36, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:05:35', '2025-04-08 05:05:35'),
(37, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:05:35', '2025-04-08 05:05:35'),
(38, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:05:35', '2025-04-08 05:05:35'),
(39, 22, 1, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:38:22', '2025-04-08 05:38:22'),
(40, 22, 3, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:38:22', '2025-04-08 05:38:22'),
(41, 22, 17, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:38:22', '2025-04-08 05:38:22'),
(42, 22, 22, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 05:38:22', '2025-04-08 05:38:22'),
(43, 22, 1, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 06:24:58', '2025-04-08 06:24:58'),
(44, 22, 3, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 06:24:58', '2025-04-08 06:24:58'),
(45, 22, 17, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 06:24:58', '2025-04-08 06:24:58'),
(46, 22, 22, '{\"username\":\"srk patel\",\"type\":\"leave\"}', 0, '2025-04-08 06:24:58', '2025-04-08 06:24:58'),
(47, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 23, 2025\"}', 0, '2025-04-08 06:33:01', '2025-04-08 06:33:01'),
(48, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 23, 2025\"}', 0, '2025-04-08 06:33:01', '2025-04-08 06:33:01'),
(49, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 23, 2025\"}', 0, '2025-04-08 06:33:01', '2025-04-08 06:33:01'),
(50, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 23, 2025\"}', 0, '2025-04-08 06:33:01', '2025-04-08 06:33:01'),
(51, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 24, 2025\"}', 0, '2025-04-08 07:19:15', '2025-04-08 07:19:15'),
(52, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 24, 2025\"}', 0, '2025-04-08 07:19:15', '2025-04-08 07:19:15'),
(53, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 24, 2025\"}', 0, '2025-04-08 07:19:15', '2025-04-08 07:19:15'),
(54, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital patel\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital patel on Apr 24, 2025\"}', 0, '2025-04-08 07:19:15', '2025-04-08 07:19:15'),
(55, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"srk patel\",\"user_id\":\"22\",\"message\":\"Payroll processed for srk patel on May 09, 2025\"}', 0, '2025-04-08 07:24:15', '2025-04-08 07:24:15'),
(56, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"srk patel\",\"user_id\":\"22\",\"message\":\"Payroll processed for srk patel on May 09, 2025\"}', 0, '2025-04-08 07:24:15', '2025-04-08 07:24:15'),
(57, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"srk patel\",\"user_id\":\"22\",\"message\":\"Payroll processed for srk patel on May 09, 2025\"}', 0, '2025-04-08 07:24:15', '2025-04-08 07:24:15'),
(58, 1, 22, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"srk patel\",\"user_id\":\"22\",\"message\":\"Payroll processed for srk patel on May 09, 2025\"}', 0, '2025-04-08 07:24:15', '2025-04-08 07:24:15'),
(59, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 23:13:13', '2025-04-08 23:13:13'),
(60, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 23:13:13', '2025-04-08 23:13:13'),
(61, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 23:13:13', '2025-04-08 23:13:13'),
(62, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-08 23:13:13', '2025-04-08 23:13:13'),
(63, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:24:43', '2025-04-09 00:24:43'),
(64, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:24:43', '2025-04-09 00:24:43'),
(65, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:24:43', '2025-04-09 00:24:43'),
(66, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:24:43', '2025-04-09 00:24:43'),
(67, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:57:36', '2025-04-09 00:57:36'),
(68, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:57:36', '2025-04-09 00:57:36'),
(69, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:57:36', '2025-04-09 00:57:36'),
(70, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 00:57:36', '2025-04-09 00:57:36'),
(71, 3, 1, '{\"username\":\"hirva kalsariya\",\"type\":\"leave\"}', 0, '2025-04-09 04:49:57', '2025-04-09 04:49:57'),
(72, 3, 3, '{\"username\":\"hirva kalsariya\",\"type\":\"leave\"}', 0, '2025-04-09 04:49:57', '2025-04-09 04:49:57'),
(73, 3, 17, '{\"username\":\"hirva kalsariya\",\"type\":\"leave\"}', 0, '2025-04-09 04:49:57', '2025-04-09 04:49:57'),
(74, 2, 1, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 23:13:36', '2025-04-09 23:13:36'),
(75, 2, 3, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 23:13:36', '2025-04-09 23:13:36'),
(76, 2, 17, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 23:13:36', '2025-04-09 23:13:36'),
(77, 2, 2, '{\"username\":\"shital patel\",\"type\":\"leave\"}', 0, '2025-04-09 23:13:36', '2025-04-09 23:13:36'),
(78, 1, 1, '{\"type\":\"performance\",\"username\":\"komal\",\"employee\":\"shital patel\",\"message\":\"Performance reviewed for shital patel\",\"user_id\":\"2\"}', 0, '2025-04-11 02:40:37', '2025-04-11 02:40:37'),
(79, 1, 2, '{\"type\":\"performance\",\"username\":\"komal\",\"employee\":\"shital patel\",\"message\":\"Performance reviewed for shital patel\",\"user_id\":\"2\"}', 0, '2025-04-11 02:40:37', '2025-04-11 02:40:37'),
(80, 1, 3, '{\"type\":\"performance\",\"username\":\"komal\",\"employee\":\"shital patel\",\"message\":\"Performance reviewed for shital patel\",\"user_id\":\"2\"}', 0, '2025-04-11 02:40:37', '2025-04-11 02:40:37'),
(81, 1, 17, '{\"type\":\"performance\",\"username\":\"komal\",\"employee\":\"shital patel\",\"message\":\"Performance reviewed for shital patel\",\"user_id\":\"2\"}', 0, '2025-04-11 02:40:37', '2025-04-11 02:40:37'),
(82, 1, 1, '{\"username\":\"candiate20\",\"type\":\"candidate\",\"message\":\"New candidate applied: candiate20\"}', 0, '2025-04-15 05:25:24', '2025-04-15 05:25:24'),
(83, 1, 3, '{\"username\":\"candiate20\",\"type\":\"candidate\",\"message\":\"New candidate applied: candiate20\"}', 0, '2025-04-15 05:25:24', '2025-04-15 05:25:24'),
(84, 1, 17, '{\"username\":\"candiate20\",\"type\":\"candidate\",\"message\":\"New candidate applied: candiate20\"}', 0, '2025-04-15 05:25:24', '2025-04-15 05:25:24'),
(85, 1, 25, '{\"username\":\"candiate20\",\"type\":\"candidate\",\"message\":\"New candidate applied: candiate20\"}', 0, '2025-04-15 05:25:24', '2025-04-15 05:25:24'),
(86, 1, 1, '{\"message\":\"New interview scheduled for candidate ID: 5\",\"type\":\"interview\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:26:21', '2025-04-15 05:26:21'),
(87, 1, 3, '{\"message\":\"New interview scheduled for candidate ID: 5\",\"type\":\"interview\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:26:21', '2025-04-15 05:26:21'),
(88, 1, 17, '{\"message\":\"New interview scheduled for candidate ID: 5\",\"type\":\"interview\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:26:21', '2025-04-15 05:26:21'),
(89, 1, 1, '{\"message\":\"Onboarding started for candiate20\",\"type\":\"onboarding\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:32:27', '2025-04-15 05:32:27'),
(90, 1, 3, '{\"message\":\"Onboarding started for candiate20\",\"type\":\"onboarding\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:32:27', '2025-04-15 05:32:27'),
(91, 1, 17, '{\"message\":\"Onboarding started for candiate20\",\"type\":\"onboarding\",\"username\":\"komal\",\"candidate_id\":\"5\",\"candidate_name\":\"candiate20\"}', 0, '2025-04-15 05:32:27', '2025-04-15 05:32:27'),
(92, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 30, 2025\"}', 0, '2025-04-16 00:36:55', '2025-04-16 00:36:55'),
(93, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 30, 2025\"}', 0, '2025-04-16 00:36:55', '2025-04-16 00:36:55'),
(94, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 30, 2025\"}', 0, '2025-04-16 00:36:55', '2025-04-16 00:36:55'),
(95, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 30, 2025\"}', 0, '2025-04-16 01:22:48', '2025-04-16 01:22:48'),
(96, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 30, 2025\"}', 0, '2025-04-16 01:22:48', '2025-04-16 01:22:48'),
(97, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 30, 2025\"}', 0, '2025-04-16 01:22:48', '2025-04-16 01:22:48'),
(98, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 30, 2025\"}', 0, '2025-04-16 01:22:48', '2025-04-16 01:22:48'),
(99, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 26, 2025\"}', 0, '2025-04-16 02:40:46', '2025-04-16 02:40:46'),
(100, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 26, 2025\"}', 0, '2025-04-16 02:40:46', '2025-04-16 02:40:46'),
(101, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 26, 2025\"}', 0, '2025-04-16 02:40:46', '2025-04-16 02:40:46'),
(102, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Apr 26, 2025\"}', 0, '2025-04-16 02:40:46', '2025-04-16 02:40:46'),
(103, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-16 04:55:08', '2025-04-16 04:55:08'),
(104, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-16 04:55:08', '2025-04-16 04:55:08'),
(105, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-16 04:55:08', '2025-04-16 04:55:08'),
(106, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-16 04:55:08', '2025-04-16 04:55:08'),
(107, 2, 1, '{\"username\":\"shital\",\"type\":\"leave\"}', 0, '2025-04-16 06:09:15', '2025-04-16 06:09:15'),
(108, 2, 3, '{\"username\":\"shital\",\"type\":\"leave\"}', 0, '2025-04-16 06:09:15', '2025-04-16 06:09:15'),
(109, 2, 17, '{\"username\":\"shital\",\"type\":\"leave\"}', 0, '2025-04-16 06:09:15', '2025-04-16 06:09:15'),
(110, 2, 2, '{\"username\":\"shital\",\"type\":\"leave\"}', 0, '2025-04-16 06:09:15', '2025-04-16 06:09:15'),
(111, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-17 05:35:56', '2025-04-17 05:35:56'),
(112, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-17 05:35:56', '2025-04-17 05:35:56'),
(113, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-17 05:35:56', '2025-04-17 05:35:56'),
(114, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 30, 2025\"}', 0, '2025-04-17 05:35:56', '2025-04-17 05:35:56'),
(115, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Jan 31, 2025\"}', 0, '2025-04-17 07:01:10', '2025-04-17 07:01:10'),
(116, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Jan 31, 2025\"}', 0, '2025-04-17 07:01:10', '2025-04-17 07:01:10'),
(117, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Jan 31, 2025\"}', 0, '2025-04-17 07:01:10', '2025-04-17 07:01:10'),
(118, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on Jan 31, 2025\"}', 0, '2025-04-17 07:01:10', '2025-04-17 07:01:10'),
(119, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 31, 2025\"}', 0, '2025-04-17 07:03:23', '2025-04-17 07:03:23'),
(120, 1, 2, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 31, 2025\"}', 0, '2025-04-17 07:03:23', '2025-04-17 07:03:23'),
(121, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 31, 2025\"}', 0, '2025-04-17 07:03:23', '2025-04-17 07:03:23'),
(122, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"shital\",\"user_id\":\"2\",\"message\":\"Payroll processed for shital on May 31, 2025\"}', 0, '2025-04-17 07:03:23', '2025-04-17 07:03:23'),
(123, 3, 1, '{\"type\":\"task\",\"username\":\"hirva kalsariya\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"New task assigned to sneha makvana\"}', 0, '2025-04-18 01:50:17', '2025-04-18 01:50:17'),
(124, 3, 3, '{\"type\":\"task\",\"username\":\"hirva kalsariya\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"New task assigned to sneha makvana\"}', 0, '2025-04-18 01:50:17', '2025-04-18 01:50:17'),
(125, 3, 4, '{\"type\":\"task\",\"username\":\"hirva kalsariya\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"New task assigned to sneha makvana\"}', 0, '2025-04-18 01:50:17', '2025-04-18 01:50:17'),
(126, 3, 17, '{\"type\":\"task\",\"username\":\"hirva kalsariya\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"New task assigned to sneha makvana\"}', 0, '2025-04-18 01:50:17', '2025-04-18 01:50:17'),
(127, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 24, 2025\"}', 0, '2025-04-18 03:45:16', '2025-04-18 03:45:16'),
(128, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 24, 2025\"}', 0, '2025-04-18 03:45:16', '2025-04-18 03:45:16'),
(129, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"hirva kalsariya\",\"user_id\":\"3\",\"message\":\"Payroll processed for hirva kalsariya on Apr 24, 2025\"}', 0, '2025-04-18 03:45:16', '2025-04-18 03:45:16'),
(130, 1, 1, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"Payroll processed for sneha makvana on Apr 24, 2025\"}', 0, '2025-04-21 00:05:31', '2025-04-21 00:05:31'),
(131, 1, 3, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"Payroll processed for sneha makvana on Apr 24, 2025\"}', 0, '2025-04-21 00:05:31', '2025-04-21 00:05:31'),
(132, 1, 4, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"Payroll processed for sneha makvana on Apr 24, 2025\"}', 0, '2025-04-21 00:05:31', '2025-04-21 00:05:31'),
(133, 1, 17, '{\"type\":\"payroll\",\"username\":\"komal\",\"employee\":\"sneha makvana\",\"user_id\":\"4\",\"message\":\"Payroll processed for sneha makvana on Apr 24, 2025\"}', 0, '2025-04-21 00:05:31', '2025-04-21 00:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `offer_letter_templates`
--

CREATE TABLE `offer_letter_templates` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `template_img` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_letter_templates`
--

INSERT INTO `offer_letter_templates` (`id`, `title`, `content`, `created_by`, `template_img`, `created_at`, `updated_at`) VALUES
(1, 'exparinece12', '<h1 id=\"ivjz\">Work Experience Verification Letter</h1>\r\n<p>[Your Name]</p>\r\n<p>HR Manager</p>\r\n<p>[Your Company Name]</p>', 1, '1744372310_0b26fa3fd0b05107d0de.png', '2025-04-09 04:59:43', '2025-04-11 11:53:04'),
(2, 'exprience4', '<h1 class=\"text-align-center\" data-node-id=\"8c741994-37db-4591-ad0f-d58a5fd7fe8a\" data-pm-slice=\"1 1 []\">Work Experience Verification Letter</h1>\r\n<p class=\"text-align-center\" data-node-id=\"68462331-63aa-40e1-80fe-6a6eaed56dcb\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"35a2c706-cd77-44d5-a902-a276177d455c\"><strong>[Your Name]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"98cc32a5-1f17-4a24-a59e-869b98407f2f\">HR Manager</p>\r\n<p class=\"text-align-justify\" data-node-id=\"182cd0a5-e971-47b6-a8d1-148617b36aea\"><strong>[Your Company Name]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"3259bb26-82be-4b72-90d5-c8c4f41456cd\"><strong>[Your Company Address]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"3b7b58a9-ee1a-40f3-8ad0-5a148da2de22\"><strong>[Your Email]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"725ca64e-197f-43d3-b058-a1404dc12b32\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"841b2e41-0659-43a1-bc37-ba55bbea18cb\">October 18, 2067</p>\r\n<p class=\"text-align-justify\" data-node-id=\"36444a2b-be73-46b3-8204-7de716e73f3c\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"540e3b71-fcd4-41b3-a970-e27cfe7b7bc8\"><strong>Elva Mattie</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"e3f90f16-4a95-4360-8a3d-3b78a278cc89\">Hiring Manager<br>Innovatech Systems Inc.<br>3794 Coal Street,<br>Centre Hall, PA 16828</p>\r\n<p class=\"text-align-justify\" data-node-id=\"3d9fb3dd-45ea-4f13-8a91-b207f4ecdd03\">Dear Ms. Mattie,</p>\r\n<p class=\"text-align-justify\" data-node-id=\"2766c83d-f113-4c4e-914c-509a9b3a14ec\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"70c399d9-1911-47fc-a9d7-4d7aaea25af4\">I am writing this Work Experience Verification Letter on behalf of Melissa Lee, who has been employed as an IT Specialist at <strong>[Your Company Name]</strong>. This letter serves to confirm her position, responsibilities, and the duration of her employment here. Melissa has been a valued member of our team since she joined our company.</p>\r\n<p class=\"text-align-justify\" data-node-id=\"9aa9ebf6-9e54-4a7b-8bc4-e29b725d02ca\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"ddc764a6-4ff8-4563-bcb9-baa2d8b54c2a\">As an IT specialist, her role included ensuring the security of our digital infrastructure, maintaining our IT systems, and troubleshooting technical issues as required. Her dedication and expertise have been a significant contribution to our company\'s success. She has demonstrated proficiency in executing her tasks efficiently and effectively, resulting in an excellent productivity level.</p>\r\n<p class=\"text-align-justify\" data-node-id=\"3e54d92b-135c-4857-88cb-26fb5b2cbeb0\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"817d47e2-419e-4c32-bb64-fddd438a388b\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"866e763c-da2f-4f2f-8fa2-394551847a29\">Sincerely,</p>\r\n<p class=\"text-align-justify\" data-node-id=\"d1ec026e-3aac-474a-8c12-c8764bbd7e2e\">&nbsp;</p>\r\n<p class=\"text-align-justify\" data-node-id=\"8c5f6531-5e47-4931-b17f-5e3f7a5e31be\"><strong>[Your Name]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"bc41ef63-2a82-4250-8673-5da24f1dfca4\"><em>HR Manager</em></p>\r\n<p class=\"text-align-justify\" data-node-id=\"465071f9-4003-4155-9b92-ee8aa6e5c2a4\"><strong>[Your Company Name]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"e169a5e6-5a95-49db-9e41-f4cc06141a5e\"><strong>[Your Number]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"9d964e21-e339-459a-9739-de0e2060ac65\"><strong>[Your Email]</strong></p>\r\n<p class=\"text-align-justify\" data-node-id=\"ccbe78da-480b-4268-8094-ef2069b0f239\">&nbsp;</p>', 1, '1744371990_f41b9aa46a54f51b814c.png', '2025-04-09 06:10:13', '2025-04-11 11:46:30'),
(4, 'Offer Latter ', '<p class=\"\" data-start=\"144\" data-end=\"167\"><strong data-start=\"144\" data-end=\"167\"><img src=\"../upload/1744266334_17b23768290e579fd83b.webp\" alt=\"http://localhost:8080/upload/1744266334_17b23768290e579fd83b.webp\" width=\"93\" height=\"56\"></strong></p>\r\n<p class=\"\" data-start=\"169\" data-end=\"282\"><strong data-start=\"169\" data-end=\"187\">{{company_name}}</strong><br data-start=\"187\" data-end=\"190\">{{company_address}}<br data-start=\"207\" data-end=\"210\">{{company_phone}}<br data-start=\"233\" data-end=\"236\">{{company_email}}</p>\r\n<p class=\"\" data-start=\"169\" data-end=\"282\"><strong data-start=\"284\" data-end=\"293\">Date:</strong> {{today_date}}</p>\r\n<p class=\"\" data-start=\"309\" data-end=\"385\"><strong data-start=\"309\" data-end=\"316\">To,</strong><br data-start=\"316\" data-end=\"319\">{{candidate_name}}</p>\r\n<h3 class=\"\" data-start=\"392\" data-end=\"428\">&nbsp;</h3>\r\n<h3 class=\"\" data-start=\"392\" data-end=\"428\"><strong data-start=\"396\" data-end=\"428\">Subject: Offer of Employment</strong></h3>\r\n<p>&nbsp;</p>\r\n<p class=\"\" data-start=\"430\" data-end=\"452\">Dear {{candidate_name}},</p>\r\n<p class=\"\" data-start=\"454\" data-end=\"642\">We are pleased to offer you the position of <strong data-start=\"498\" data-end=\"513\">{{job_title}}</strong> at <strong data-start=\"169\" data-end=\"187\">{{company_name}}</strong>. We were very impressed with your background and experience, and we are excited to have you join our team.</p>\r\n<p class=\"\" data-start=\"644\" data-end=\"698\">Below are the terms and conditions of your employment:</p>\r\n<ul data-start=\"700\" data-end=\"1064\">\r\n<li class=\"\" data-start=\"700\" data-end=\"729\">\r\n<p class=\"\" data-start=\"702\" data-end=\"729\"><strong data-start=\"702\" data-end=\"715\">Position:</strong> <strong data-start=\"498\" data-end=\"513\">{{job_title}}</strong></p>\r\n</li>\r\n<li class=\"\" data-start=\"730\" data-end=\"762\">\r\n<p class=\"\" data-start=\"732\" data-end=\"762\"><strong data-start=\"732\" data-end=\"747\">Start Date:</strong> {{start_date}}</p>\r\n</li>\r\n<li class=\"\" data-start=\"763\" data-end=\"800\">\r\n<p class=\"\" data-start=\"765\" data-end=\"800\"><strong data-start=\"765\" data-end=\"780\">Department:</strong> {{department_name}}</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1066\" data-end=\"1274\">Your employment with <strong data-start=\"169\" data-end=\"187\">{{company_name}}</strong>&nbsp;will be subject to a probation period of [X] months. During this period, either party may terminate the employment by giving [X] days\' notice or salary in lieu thereof.</p>\r\n<p class=\"\" data-start=\"1276\" data-end=\"1428\">Please note that this offer is contingent upon your acceptance and signing of the enclosed documents and completion of the required joining formalities.</p>\r\n<p class=\"\" data-start=\"1430\" data-end=\"1551\">We are confident that you will make a significant contribution to our company and look forward to your positive response.</p>\r\n<p class=\"\" data-start=\"1682\" data-end=\"1702\">Welcome to the team!</p>\r\n<p class=\"\" data-start=\"1704\" data-end=\"1773\">Warm regards,<br data-start=\"1717\" data-end=\"1720\"><strong data-start=\"1720\" data-end=\"1735\">{{created_by}}</strong><br data-start=\"1735\" data-end=\"1738\">{{department_name}}<br data-start=\"1756\" data-end=\"1759\">{{company_name}}</p>\r\n<hr class=\"\" data-start=\"1775\" data-end=\"1778\">\r\n<h3 class=\"\" data-start=\"1780\" data-end=\"1807\"><strong data-start=\"1784\" data-end=\"1807\">Acceptance of Offer</strong></h3>\r\n<p>&nbsp;</p>\r\n<p class=\"\" data-start=\"1809\" data-end=\"1907\">I, <strong data-start=\"169\" data-end=\"187\">{{candidate_name}}</strong>, accept the offer of employment from <strong data-start=\"169\" data-end=\"187\">{{company_name}}</strong>&nbsp;as outlined above.</p>\r\n<p class=\"\" data-start=\"1909\" data-end=\"1982\"><strong data-start=\"1909\" data-end=\"1923\">Signature:</strong> ____________________<br data-start=\"1944\" data-end=\"1947\"><strong data-start=\"1947\" data-end=\"1956\">Date:</strong> _________________________</p>', 1, '1744284699_70cc2f7969a1bfb2d16e.webp', '2025-04-10 11:31:39', '2025-04-10 12:10:20'),
(5, 'exprience1', '<body id=\"i3fj\"><h1 id=\"ihka\">Work Experience Verification Letter</h1><p>[Your Name]</p><p>HR Manager</p><p>[Your Company Name]</p></body><style>* { box-sizing: border-box; } body {margin: 0;}#ihka{text-align:center;}</style>', 1, '1744367174_ea4cb4d78b74262ff2e7.jpg', '2025-04-11 10:26:14', '2025-04-11 10:26:14');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding`
--

CREATE TABLE `onboarding` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `offer_later_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `onboarding_status` varchar(100) NOT NULL,
  `docu_submitted` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding`
--

INSERT INTO `onboarding` (`id`, `candidate_id`, `department_id`, `job_id`, `offer_later_id`, `start_date`, `onboarding_status`, `docu_submitted`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 1, 1, 1, 0, '2025-04-12', 'completed', 'yes', '2025-03-26 00:08:08', '2025-03-26 00:08:50', 1),
(2, 5, 13, 5, 1, '2025-04-25', 'completed', 'yes', '2025-04-15 05:32:07', '2025-04-15 05:32:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_type` int(11) DEFAULT NULL,
  `remaining_paid_leaves` int(11) DEFAULT NULL,
  `used_paid_leaves` int(11) DEFAULT NULL,
  `month_year` varchar(7) DEFAULT NULL,
  `total_leaves` int(11) DEFAULT NULL,
  `total_paid_leaves` int(11) NOT NULL,
  `salary_amount` decimal(10,0) NOT NULL,
  `acc_number` int(20) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `acc_in_name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `branch_code` varchar(255) NOT NULL,
  `tax_deduction` decimal(10,0) DEFAULT NULL,
  `bonuses` decimal(10,0) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `user_id`, `leave_type`, `remaining_paid_leaves`, `used_paid_leaves`, `month_year`, `total_leaves`, `total_paid_leaves`, `salary_amount`, `acc_number`, `bank_name`, `ifsc_code`, `acc_in_name`, `branch_name`, `branch_code`, `tax_deduction`, `bonuses`, `net_salary`, `payment_date`, `payment_status`, `created_at`, `created_by`, `updated_at`) VALUES
(18, 4, 0, 0, 0, '2025-05', 0, 0, '0', 1234567890, 'bob', '123abc', 'sneha makvana', 'ved road', '123sdf', '0', '0', '0.00', '2025-04-24', 'Paid', '2025-04-21 00:05:02', 1, '2025-04-21 00:05:02');

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_date` date NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `designation_id` varchar(200) NOT NULL,
  `goals_achieved` varchar(200) NOT NULL,
  `team_work` varchar(200) NOT NULL,
  `management` varchar(200) NOT NULL,
  `presentation_skill` varchar(200) NOT NULL,
  `behaviour` varchar(200) NOT NULL,
  `rating` int(11) NOT NULL,
  `notes` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance`
--

INSERT INTO `performance` (`id`, `user_id`, `review_date`, `reviewer_id`, `designation_id`, `goals_achieved`, `team_work`, `management`, `presentation_skill`, `behaviour`, `rating`, `notes`, `created_at`, `updated_at`) VALUES
(1, 12, '2025-04-19', 3, '1', 'exacllent', 'good', 'good', 'good', 'good', 5, 'sdsdds', '2025-03-26 02:05:47', '2025-03-26 02:05:47'),
(3, 2, '2025-04-30', 1, '1', 'asad', 'ad', 'sas', 'ad', 'ad', 5, 'hi', '2025-04-11 02:40:37', '2025-04-11 02:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `smtp_id` int(11) NOT NULL,
  `smtp_protocol` varchar(20) NOT NULL DEFAULT 'smtp',
  `smtp_host` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_username` varchar(255) NOT NULL,
  `smtp_password` varchar(255) NOT NULL,
  `smtp_encryption` enum('ssl','tls','none') NOT NULL DEFAULT 'tls',
  `smtp_from_email` varchar(255) NOT NULL,
  `smtp_from_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`smtp_id`, `smtp_protocol`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `smtp_from_email`, `smtp_from_name`, `created_at`, `updated_at`) VALUES
(1, 'smtp', 'p3plzcpnl505531.prod.phx3.secureserver.net', 465, 'support@hrweb.fableadtechnolabs.in', 'C?gURsHKG}MI', 'ssl', 'support@hrweb.fableadtechnolabs.in', 'HR Portal', '2025-04-01 06:53:20', '2025-04-01 06:53:20');

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subtask_title` varchar(255) DEFAULT NULL,
  `subtask_status` varchar(255) DEFAULT NULL,
  `subtask_assigned_date` datetime DEFAULT NULL,
  `subtask_due_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`id`, `task_id`, `user_id`, `subtask_title`, `subtask_status`, `subtask_assigned_date`, `subtask_due_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'SUBTASK 1', 'Pending', '2025-04-22 00:00:00', '2025-05-01 00:00:00', 1, '2025-04-22 00:41:06', '2025-04-22 00:41:06'),
(2, 1, 2, 'subtask crud 1', 'Pending', '2025-04-23 00:00:00', '2025-04-26 00:00:00', 1, '2025-04-22 00:42:21', '2025-04-22 00:42:21'),
(3, 1, 2, 'subtask crud 2', 'Pending', '2025-04-24 00:00:00', '2025-04-29 00:00:00', 1, '2025-04-22 00:42:21', '2025-04-22 00:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `task_title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `task_status` varchar(255) NOT NULL,
  `assigned_date` date NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `user_id`, `department_id`, `task_title`, `description`, `task_status`, `assigned_date`, `due_date`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 2, 26, 'example', 'asadxas', 'pending', '2025-04-07', '2025-03-31', '2025-03-27 06:11:54', 1, '2025-03-30 23:45:53'),
(2, 3, 16, 'example', NULL, 'completed', '2025-04-17', '2025-03-30', '2025-03-27 06:29:28', 1, '2025-03-30 23:46:09'),
(4, 2, 18, 'example', NULL, 'pending', '2025-04-07', '2025-04-02', '2025-03-29 00:06:37', 1, '2025-03-29 00:06:37'),
(5, 4, 12, 'design for profile page', NULL, 'Pending', '2025-04-18', '2025-04-21', '2025-04-18 01:49:58', 3, '2025-04-18 01:49:58');

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `training_title` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','hr','employee','candidate') NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `chat_status` enum('online','offline') DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `last_activity`, `created_at`, `updated_at`, `chat_status`) VALUES
(1, 'komal', 'komal@gmail.com', '$2y$10$/0zvStbb5hVxRzxiaD.bLelEyk1Qab4C3j1DUNFTKj5BL740.LTT6', 'admin', '2025-04-22 04:28:37', '2025-04-22 04:28:37', '2025-04-21 22:58:37', 'online'),
(2, 'shital', 'shital@gmail.com', '$2y$10$fhkCkjXlOds.ZdQvg0ou4ufhC0.scPbljQiC5.DFnlEV8RFM2JdLW', 'employee', '2025-04-21 11:56:50', '2025-04-21 12:06:11', '2025-04-21 06:36:11', 'online'),
(3, 'hirva kalsariya', 'hirva@gmail.com', '$2y$10$GZuhcOFU8dpUVGhbE00TTu8RCsYGHsgnLhCmvR96GDcQcEZ8.Z/46', 'hr', '2025-04-18 07:10:01', '2025-04-18 07:10:01', '2025-04-18 01:40:01', 'online'),
(4, 'sneha makvana', 'sneha@gmail.com', '$2y$10$N.bohnOPi5HDvM7SVNjur.oWvvOQtxZbhaGci1OwkKMBesTg7JdTK', 'employee', '2025-04-18 07:18:14', '2025-04-18 07:51:28', '2025-04-18 02:21:28', 'offline'),
(6, 'heta', 'hirva74@gmail.com', NULL, 'employee', NULL, '2025-03-21 11:33:43', '2025-03-21 06:03:43', 'offline'),
(9, 'candidate1', 'candidate1@gmail.com', NULL, 'employee', NULL, '2025-03-22 06:19:00', '2025-03-22 00:49:00', 'offline'),
(13, 'xyz', 'xyz@gmail.com', NULL, 'candidate', NULL, '2025-03-26 00:46:38', '2025-03-26 00:46:38', 'offline'),
(16, 'sonalika ', 'sonalika@gmail.com', '$2y$10$S8/omTapFaSI8/2/kY0JueXw6dzOrC/7K6JiBRgda1yRvqw9J.Qtu', 'employee', NULL, '2025-03-26 23:58:49', '2025-03-26 23:58:49', 'offline'),
(17, 'Cruz Mclaughlin', 'byfoc@mailinator.com', '$2y$10$T5JG.QgovQ9eSv/pn9rYUuejbyXlb.CrSE5EhUpEssPMRGSkFkOCG', 'admin', NULL, '2025-03-27 02:38:59', '2025-03-27 02:38:59', 'offline'),
(19, 'Jessamine Meyer', 'tocynuso@mailinator.com', NULL, 'candidate', NULL, '2025-03-27 04:33:47', '2025-03-27 04:33:47', 'offline'),
(20, 'employee2 ', 'employee2@gmail.com', '$2y$10$EnVT6bi2rC8.vTXUeXLEbuLWZkbGk7LFbIkmJugxXmdOj0qJ9yB2m', 'employee', NULL, '2025-03-27 06:58:17', '2025-03-27 06:58:17', 'offline'),
(21, 'bcdd', 'bcda@gmail.com', NULL, 'candidate', NULL, '2025-03-28 04:27:42', '2025-03-28 04:27:42', 'offline'),
(22, 'srk patel', 'srk@gmail.com', '$2y$10$qaWmBmOktQSJVg7Pjc7RuOfPG8RG/4uGc2SluWR6GEzgj2Ts9WSm.', 'employee', NULL, '2025-04-06 23:38:27', '2025-04-06 23:38:27', 'offline'),
(23, 'Kaden Mullins', 'bivu@mailinator.com', '$2y$10$MSckErkPucQkXHhEfuSKbOZ8wMGsOg9.rGTJRRF7k/E8UxzjE3wKu', 'employee', NULL, '2025-04-08 06:54:48', '2025-04-08 01:24:48', 'offline'),
(25, 'candiate20', 'candidate20@gmail.com', NULL, 'employee', NULL, '2025-04-15 11:02:27', '2025-04-15 05:32:27', 'offline');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `contact_number` int(20) NOT NULL,
  `employee_id` int(20) NOT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `working_location` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `salary` decimal(10,0) NOT NULL,
  `status` enum('scheduled','candidate','completed') DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `firstname`, `lastname`, `email`, `password`, `gender`, `date_of_birth`, `address_1`, `address_2`, `state`, `postcode`, `city_id`, `country_id`, `contact_number`, `employee_id`, `designation_id`, `department_id`, `joining_date`, `working_location`, `role`, `salary`, `status`, `resume`, `job_id`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 1, 'komal', 'kaslariya', 'komal@gmail.com', '', 'female', '2025-02-21', 'surat', 'surat', 'gujarat', '123456', 1, 1, 1234567890, 1, NULL, NULL, '0000-00-00', NULL, 'admin', '0', 'completed', NULL, NULL, 'woman.jpg', '2025-02-20 12:52:49', '2025-04-11 01:13:34'),
(2, 2, 'shital', 'patel', 'shital@gmail.com', '', 'female', '2025-02-28', 'briliyanschool', 'katargam', 'gujarat', '123456', 1, 1, 1234567890, 2, 1, 1, '0000-00-00', NULL, 'employee', '30000', NULL, NULL, 1, '1740057539_1c5132e8bce9dd27e9b1.jpg', '2025-02-20 07:48:59', '2025-04-21 06:36:11'),
(3, 3, 'hirva', 'kalsariya', 'hirva@gmail.com', '', 'female', '2025-02-28', 'briliyanschool', 'katargam', 'gujarat', '123456', 1, 1, 1234567890, 3, 1, 1, '2025-02-25', 'Remote', 'hr', '40000', NULL, NULL, NULL, '1740113611_681b3ad8d8164f02bbe5.webp', '2025-02-20 23:23:31', '2025-02-20 23:23:31'),
(4, 4, 'sneha', 'makvana', 'sneha@gmail.com', '', 'female', '2025-02-26', 'briliyanschool', 'katargam', 'gujarat', '123456', 1, 1, 2147483647, 0, 1, 1, '2025-02-28', '', 'employee', '0', 'completed', 'upload/resumes/1740117426_9970c43f7cd91e77ecfa.pdf', 1, '', '2025-04-15 00:27:06', '2025-03-26 01:54:39'),
(5, 5, 'komal', NULL, 'komal78@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'scheduled', 'upload/resumes/1742284442_9af8380c4e4f6aef6a48.pdf', 6, '', '2025-03-18 02:24:02', '2025-03-20 07:23:47'),
(6, 6, 'heta', NULL, 'hirva74@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1234567890, 0, NULL, NULL, '0000-00-00', NULL, 'employee', '0', 'completed', 'upload/resumes/1742285108_4c50f4bcfeefefb0d364.pdf', 6, '', '2025-03-18 02:35:08', '2025-03-21 06:03:43'),
(7, 7, 'heta', NULL, 'komal@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'candidate', 'upload/resumes/1742551777_613eaae56366a0b8b20e.pdf', 7, '', '2025-03-21 04:39:37', '2025-03-21 04:39:37'),
(8, 8, 'heta', NULL, 'komal@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1234567890, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'candidate', 'upload/resumes/1742551828_72e7c049ad9d49713c9b.pdf', 7, '', '2025-03-21 04:40:28', '2025-03-21 04:40:28'),
(9, 9, 'candidate1', NULL, 'candidate1@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, 'employee', '0', 'completed', 'upload/resumes/1742622910_e9a9ef13174709fe7d15.pdf', 8, '', '2025-03-22 00:25:10', '2025-03-22 00:49:00'),
(10, 10, 'candidate2', NULL, 'candidate2@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, 'employee', '0', 'completed', 'upload/resumes/1742890193_0144ea98808696fe11d4.pdf', 17, '', '2025-03-25 02:39:53', '2025-03-25 03:51:01'),
(11, 11, 'candidate3', NULL, 'candidate3@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, 'employee', '0', 'completed', 'upload/resumes/1742894524_6a9a13a77f10fb0565d3.pdf', 20, '', '2025-03-25 03:52:04', '2025-03-25 04:04:57'),
(13, 13, 'xyz', NULL, 'xyz@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'scheduled', 'uploads/resumes/1742969798_b1ca4835838b12ecc65b.pdf', 2, NULL, '2025-03-26 00:46:38', '2025-03-28 05:17:29'),
(16, 16, 'sonalika', '', 'sonalika@gmail.com', '', 'female', '2025-04-05', 'briliyanschool', '', 'gujarat', '123456', 23, 31, 1234567890, 4, 5, 13, '2025-03-28', 'Remote', 'employee', '0', NULL, NULL, NULL, '1743053329_d286b0b6ec671ec0aa05.png', '2025-03-26 23:58:49', '2025-03-26 23:58:49'),
(17, 17, 'Cruz', 'Mclaughlin', 'byfoc@mailinator.com', '', 'female', '2004-10-06', '66 West Old Avenue', 'Aliquam ut dolor eu ', 'Placeat autem dolor', '123456', 23, 31, 766, 5, 5, 13, '1982-06-06', 'Remote', 'admin', '0', NULL, NULL, NULL, NULL, '2025-03-27 02:38:59', '2025-03-27 02:38:59'),
(18, 18, 'komal', NULL, 'komal@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'candidate', 'uploads/resumes/1743069622_aacc00dce1a946f37804.pdf', 1, NULL, '2025-03-27 04:30:22', '2025-03-27 04:30:22'),
(19, 19, 'Jessamine Meyer', NULL, 'tocynuso@mailinator.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1234567890, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'scheduled', 'uploads/resumes/1743069827_aea940ec074b31fbc20d.pdf', 2, NULL, '2025-03-27 04:33:47', '2025-03-28 05:25:51'),
(20, 20, 'employee2', '', 'employee2@gmail.com', '', 'female', '2025-03-15', 'briliyanschool', '', 'gujarat', '123456', 28, 44, 0, 6, 15, 29, '0000-00-00', '', 'employee', '0', NULL, NULL, NULL, NULL, '2025-03-27 06:58:17', '2025-03-27 06:58:17'),
(21, 21, 'bcdd', NULL, 'bcda@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, '', '0', 'scheduled', 'uploads/resumes/1743155862_ffc1f7fcbf4f79ee2f91.pdf', 10, NULL, '2025-03-28 04:27:42', '2025-03-28 04:49:44'),
(22, 22, 'srk', 'patel', 'srk@gmail.com', '', 'female', '2025-04-26', 'sasasa', '', 'gujarat', '123456', 31, 47, 1234567890, 7, 17, 36, '2025-04-30', 'Remote', 'employee', '20000', NULL, NULL, NULL, '1744002507_e0842b404e2eab9674a7.jpg', '2025-04-06 23:38:27', '2025-04-06 23:38:27'),
(23, 23, 'Kaden', 'Mullins', 'bivu@mailinator.com', '', 'female', '1983-07-07', '850 East White Cowley Boulevard', 'Blanditiis maiores a', 'Autem animi qui quo', '123456', 29, 38, 640, 9, 18, 22, '2012-06-25', 'Remote', 'employee', '25000', NULL, NULL, NULL, '1744095092_60daf14ed7a139ef2841.png', '2025-04-08 01:21:32', '2025-04-08 01:24:48'),
(24, 24, 'Fulton', 'Ortiz', 'dekusaxoc@mailinator.com', '', 'male', '1995-07-11', '98 East Old Extension', 'Architecto sint et r', 'Rerum soluta consect', '123456', 26, 45, 1234567890, 10, 18, 18, '1999-09-20', 'On-Site', 'employee', '20000', NULL, NULL, NULL, NULL, '2025-04-08 01:38:27', '2025-04-08 01:38:27'),
(25, 25, 'candiate20', NULL, 'candidate20@gmail.com', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2147483647, 0, NULL, NULL, '0000-00-00', NULL, 'employee', '0', 'completed', 'uploads/resumes/1744714507_9dca34c68b777eacaef3.pdf', 5, NULL, '2025-04-15 05:25:07', '2025-04-15 05:32:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_detail`
--
ALTER TABLE `account_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_logo`
--
ALTER TABLE `company_logo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_of_month_certificates`
--
ALTER TABLE `employee_of_month_certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `empreport`
--
ALTER TABLE `empreport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emp_of_month`
--
ALTER TABLE `emp_of_month`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exprience`
--
ALTER TABLE `exprience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exprience_letter_templetes`
--
ALTER TABLE `exprience_letter_templetes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_location`
--
ALTER TABLE `job_location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `job_location_addresses`
--
ALTER TABLE `job_location_addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_type`
--
ALTER TABLE `leave_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onboarding`
--
ALTER TABLE `onboarding`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`smtp_id`);

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_detail`
--
ALTER TABLE `account_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `company_logo`
--
ALTER TABLE `company_logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employee_of_month_certificates`
--
ALTER TABLE `employee_of_month_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `empreport`
--
ALTER TABLE `empreport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_of_month`
--
ALTER TABLE `emp_of_month`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exprience`
--
ALTER TABLE `exprience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exprience_letter_templetes`
--
ALTER TABLE `exprience_letter_templetes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `job_location`
--
ALTER TABLE `job_location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `job_location_addresses`
--
ALTER TABLE `job_location_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_type`
--
ALTER TABLE `leave_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `onboarding`
--
ALTER TABLE `onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `smtp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
