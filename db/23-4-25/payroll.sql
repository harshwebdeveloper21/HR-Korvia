-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 08:36 AM
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
-- Database: `hr_portal`
--

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
(18, 4, 0, 0, 0, '2025-05', 0, 0, 0, 1234567890, 'bob', '123abc', 'sneha makvana', 'ved road', '123sdf', 0, 0, 0.00, '2025-04-24', 'Paid', '2025-04-21 00:05:02', 1, '2025-04-21 00:05:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
