-- Face Attendance Feature - Database Migration
-- Run these queries in phpMyAdmin or your database tool

-- 1. Add face_photo column to user_info table
ALTER TABLE `user_info` 
ADD COLUMN `face_photo` VARCHAR(255) NULL DEFAULT NULL AFTER `profile_image`;

-- 2. Add checkin_method column to attendance table
ALTER TABLE `attendance` 
ADD COLUMN `checkin_method` VARCHAR(50) NULL DEFAULT 'manual' AFTER `status`;

-- Done! The face attendance feature is now ready to use.

