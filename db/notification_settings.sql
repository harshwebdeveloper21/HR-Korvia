-- phpMyAdmin SQL Dump
-- Notification Settings Table for Attendance Push Notifications
-- This table stores the admin preference for enabling/disabling attendance push notifications

CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
  `leave_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
  `birthday_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default record (all enabled by default)
INSERT INTO `notification_settings` (`attendance_notifications_enabled`, `leave_notifications_enabled`, `birthday_notifications_enabled`) VALUES (1, 1, 1);

