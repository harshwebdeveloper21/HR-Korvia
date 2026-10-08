-- Run in phpMyAdmin with the app database selected.
CREATE TABLE IF NOT EXISTS letter_template_settings (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    template_key VARCHAR(50) NOT NULL,
    content LONGTEXT NULL,
    updated_by INT(11) NULL DEFAULT NULL,
    created_at DATETIME NULL DEFAULT NULL,
    updated_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_letter_template_settings_key (template_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SHOW TABLES LIKE 'letter_template_settings';
