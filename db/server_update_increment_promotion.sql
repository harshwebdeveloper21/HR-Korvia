-- Run in phpMyAdmin with the app database selected.
ALTER TABLE salary_increment_history
    ADD COLUMN IF NOT EXISTS previous_designation_id INT(11) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS new_designation_id INT(11) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS previous_department_id INT(11) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS new_department_id INT(11) NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS reporting_manager VARCHAR(255) NULL DEFAULT NULL;

SHOW COLUMNS FROM salary_increment_history;
