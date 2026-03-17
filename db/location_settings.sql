-- phpMyAdmin SQL Dump
-- Location Settings Table for Check-in Location Verification
-- This table stores the admin-configured location (lat/long) and radius for check-in verification

CREATE TABLE IF NOT EXISTS `location_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` decimal(10,8) NOT NULL COMMENT 'Office latitude',
  `longitude` decimal(11,8) NOT NULL COMMENT 'Office longitude',
  `radius` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Allowed radius in meters. 0 means exact location check',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default record (can be updated by admin)
INSERT INTO `location_settings` (`latitude`, `longitude`, `radius`) VALUES
(0.00000000, 0.00000000, 0.00);

