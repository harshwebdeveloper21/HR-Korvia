-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 01:11 PM
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
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`id`, `task_id`, `user_id`, `subtask_title`, `subtask_status`, `subtask_assigned_date`, `subtask_due_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 0, NULL, 'Esse magni dolores ', 'In-Progress', '2025-04-21', '2025-04-23', 1, '2025-04-21 05:46:18', '2025-04-21 05:46:18'),
(2, 0, NULL, 'Esse magni dolores ', 'In-Progress', '2025-04-21', '2025-04-23', 1, '2025-04-21 05:49:19', '2025-04-21 05:49:19'),
(3, 0, NULL, 'crud', 'In-Progress', '2025-04-21', '2025-04-23', 1, '2025-04-21 06:17:58', '2025-04-21 06:17:58'),
(4, 3, 2, 'php crud sub task', 'In-Progress', '2025-04-21', '2025-04-25', 1, '2025-04-21 06:29:00', '2025-04-21 06:29:00'),
(5, 1, 2, 'task 1', 'completed', '2025-04-22', '2025-04-24', 1, '2025-04-21 07:43:12', '2025-04-23 09:08:11'),
(6, 1, 2, 'task 2', 'Reassign', '2025-04-23', '2025-04-25', 1, '2025-04-21 07:43:12', '2025-04-22 12:19:54'),
(7, 2, 3, 'task 3', 'In-Progress', '2025-04-22', '2025-04-24', 1, '2025-04-21 07:56:57', '2025-04-21 07:56:57'),
(8, 2, 3, 'task 4 ', 'In-Progress', '2025-04-23', '2025-04-25', 1, '2025-04-21 07:56:57', '2025-04-21 07:56:57'),
(9, 2, 3, 'task 5', 'In-Progress', '2025-04-26', '2025-04-29', 1, '2025-04-21 07:56:57', '2025-04-21 07:56:57'),
(10, 6, 3, 'node 1', 'Pending', '2025-04-23', '2025-05-03', 1, '2025-04-21 08:27:20', '2025-04-21 08:27:20'),
(11, 6, 3, 'node 2', 'Pending', '2025-05-01', '2025-05-06', 1, '2025-04-21 08:27:20', '2025-04-21 08:27:20'),
(12, 3, 2, 'uy0pi', 'In-Progress', '2025-04-23', '2025-04-26', 1, '2025-04-21 08:29:30', '2025-04-21 08:29:30'),
(13, 3, 2, 'uy0pi', 'In-Progress', '2025-04-23', '2025-04-26', 1, '2025-04-21 08:29:40', '2025-04-21 08:29:40'),
(14, 3, 2, 'hjkhkhjk', 'In-Progress', '2025-05-01', '2025-05-09', 1, '2025-04-21 08:29:40', '2025-04-21 08:29:40'),
(15, 3, 2, 'uy0pi', 'In-Progress', '2025-04-23', '2025-04-26', 1, '2025-04-21 08:29:43', '2025-04-21 08:29:43'),
(16, 3, 2, 'hjkhkhjk', 'In-Progress', '2025-05-01', '2025-05-09', 1, '2025-04-21 08:29:43', '2025-04-21 08:29:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
