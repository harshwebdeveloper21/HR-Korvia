-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 08:40 AM
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
(1, 'smtp', 'smtp.gmail.com', 465, 'arunfablead@gmail.com', 'bhwc gawz rgwe vfcf', 'ssl', 'arunfablead@gmail.com', 'HR Portal', '2025-04-01 06:53:20', '2025-04-30 05:01:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`smtp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `smtp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
