-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 06:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
  `document` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `user_id`, `department_id`, `task_title`, `description`, `task_status`, `assigned_date`, `due_date`, `document`, `created_at`, `created_by`, `updated_at`) VALUES
(1, 2, 2, 'PHP Laravel development Task', '', 'In-Progress', '2025-04-17', '2025-04-28', '[{\"original\":\"offerletter_Candidate_1_2025-04-17_11-27-54.pdf\",\"stored\":\"1745321942_cf0644cff03c7fb7fde8.pdf\"}]', '2025-04-17 04:39:45', 1, '2025-04-22 06:09:02'),
(2, 3, 3, 'Accounting Task', NULL, 'In-Progress', '2025-04-17', '2025-04-30', NULL, '2025-04-17 04:40:41', 1, '2025-04-21 00:21:45'),
(3, 2, 1, 'Fultter development task', NULL, 'Pending', '2025-04-18', '2025-04-22', NULL, '2025-04-17 07:29:17', 1, '2025-04-21 02:55:00'),
(4, 4, 2, 'Accounting Task', NULL, 'In-Progress', '2025-04-18', '2025-05-07', NULL, '2025-04-18 05:30:19', 1, '2025-04-21 00:22:48'),
(5, 3, 2, 'Eu veniam iste ut e', 'Tenetur qui quibusda', 'In-Progress', '1982-02-03', '2006-12-27', NULL, '2025-04-18 05:31:42', 1, '2025-04-18 05:31:42'),
(6, 3, 4, 'Node Js Task', NULL, 'Pending', '2025-04-21', '2025-04-27', NULL, '2025-04-20 23:53:51', 1, '2025-04-20 23:53:51'),
(7, 2, 4, 'Ex eveniet magna et', 'Quam atque ullamco d', 'Completed', '2018-12-15', '2025-04-21', NULL, '2025-04-21 00:20:47', 1, '2025-04-21 02:43:36'),
(8, 4, 5, 'PHP CI4 development Task 03', NULL, 'Pending', '2025-04-21', '2025-05-09', NULL, '2025-04-21 04:23:55', 1, '2025-04-21 04:23:55'),
(9, 2, 1, 'Iusto laboris saepe ', 'Labore nemo adipisic', 'In-Progress', '2000-04-12', '2025-04-21', NULL, '2025-04-21 04:44:37', 1, '2025-04-21 04:44:37'),
(10, 3, 3, 'Facere exercitatione', 'Possimus labore qui', 'Pending', '2025-04-21', '2025-05-01', 'upload/document/1745231953_29fbd897fc1ea39e6600.pdf', '2025-04-21 05:09:13', 1, '2025-04-21 05:09:13'),
(11, 3, 1, 'Anroled task', '', 'In-Progress', '2025-04-24', '2025-05-02', '1745232366_d326e8612892230b979e.pdf', '2025-04-21 05:16:06', 1, '2025-04-21 05:16:06'),
(12, 2, 4, 'Cillum perferendis f', 'Id aut facilis est q', 'In-Progress', '2025-04-21', '2025-04-25', 'upload/document/1745240278_bc8cbebe5b79927ce623.pdf', '2025-04-21 07:27:58', 1, '2025-04-21 07:27:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
