-- Run in phpMyAdmin with the app database selected.
ALTER TABLE onboarding ADD COLUMN IF NOT EXISTS annual_ctc DECIMAL(12,2) NULL DEFAULT NULL AFTER offer_later_id;

SHOW COLUMNS FROM onboarding LIKE 'annual_ctc';
