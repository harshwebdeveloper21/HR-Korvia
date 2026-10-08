-- Run in phpMyAdmin with the app database selected.
CREATE TABLE IF NOT EXISTS employee_joining_forms (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT(11) UNSIGNED NOT NULL,
    form_data LONGTEXT NULL,
    checklist TEXT NULL,
    updated_by INT(11) NULL DEFAULT NULL,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_employee_joining_forms_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SHOW TABLES LIKE 'employee_joining_forms';
