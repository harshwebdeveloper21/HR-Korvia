-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 12:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new26hrportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_detail`
--

CREATE TABLE `account_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `acc_number` varchar(255) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `acc_in_name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `branch_code` varchar(255) NOT NULL,
  `created_by` int(111) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `resume` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `locations_id` int(11) NOT NULL,
  `addresses_id` int(11) NOT NULL,
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
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `paid_days` int(11) DEFAULT 0,
  `unpaid_days` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_type`
--

CREATE TABLE `leave_type` (
  `id` int(11) NOT NULL,
  `leave_type` varchar(100) NOT NULL,
  `number_of_leaves` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `onboarding`
--

CREATE TABLE `onboarding` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `onboarding_status` varchar(100) NOT NULL,
  `docu_submitted` text DEFAULT NULL,
  `offer_later_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `tax_deduction` decimal(10,0) DEFAULT NULL,
  `bonuses` decimal(10,0) DEFAULT NULL,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `acc_number` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `acc_in_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `branch_code` varchar(50) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `salary_deduction` decimal(45,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'smtp', 'p3plzcpnl505531.prod.phx3.secureserver.net', 465, 'support@hrweb.fableadtechnolabs.in', 'C?gURsHKG}MI', 'ssl', 'support@hrweb.fableadtechnolabs.in', 'HR Portal', '2025-04-01 06:53:20', '2025-04-02 05:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subtask_title` varchar(255) NOT NULL,
  `subtask_status` varchar(50) DEFAULT 'pending',
  `subtask_assigned_date` date DEFAULT NULL,
  `subtask_due_date` date DEFAULT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`files`)),
  `description` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `task_title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `task_status` varchar(255) NOT NULL,
  `assigned_date` date NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `document` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `training_title` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `chat_status` enum('online','offline') DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `gender` enum('male','female') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `contact_number` int(20) DEFAULT NULL,
  `employee_id` int(20) NOT NULL,
  `designation_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `working_location` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `salary` decimal(10,0) NOT NULL,
  `status` enum('scheduled','candidate','completed') DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_logo`
--
ALTER TABLE `company_logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_of_month_certificates`
--
ALTER TABLE `employee_of_month_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `empreport`
--
ALTER TABLE `empreport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_of_month`
--
ALTER TABLE `emp_of_month`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exprience`
--
ALTER TABLE `exprience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exprience_letter_templetes`
--
ALTER TABLE `exprience_letter_templetes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_location`
--
ALTER TABLE `job_location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_location_addresses`
--
ALTER TABLE `job_location_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_type`
--
ALTER TABLE `leave_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onboarding`
--
ALTER TABLE `onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `smtp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
