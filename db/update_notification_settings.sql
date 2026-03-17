-- Update notification_settings table to add leave_notifications_enabled and birthday_notifications_enabled fields
-- Run this SQL if you already have the notification_settings table

-- Add leave_notifications_enabled (run only if column doesn't exist)
ALTER TABLE `notification_settings` 
ADD COLUMN `leave_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled' AFTER `attendance_notifications_enabled`;

-- Add birthday_notifications_enabled (run only if column doesn't exist)
ALTER TABLE `notification_settings` 
ADD COLUMN `birthday_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled' AFTER `leave_notifications_enabled`;

-- Note: If you get "Duplicate column name" error, it means the column already exists - that's okay!
-- If table doesn't exist yet, use the full CREATE TABLE from notification_settings.sql

