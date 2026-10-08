-- Run in phpMyAdmin with the app database selected. Safe to run more than once.
ALTER TABLE attendance
    ADD COLUMN IF NOT EXISTS lunch_start_time TIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS lunch_end_time TIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS lunch_duration TIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS lunch_duration_seconds INT(11) NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS lunch_is_overdue INT(11) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS lunch_overdue_minutes INT(11) NOT NULL DEFAULT 0;

SHOW COLUMNS FROM attendance LIKE 'lunch%';
