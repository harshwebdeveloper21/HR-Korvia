<?php
/**
 * Creates the employee_leaves table and seeds it.
 * Run once via browser: http://localhost:8080/migrate-employee-leaves
 */
$mysqli = new mysqli("localhost", "root", "", "hrprotaldemo_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create table
$sql = "CREATE TABLE IF NOT EXISTS `employee_leaves` (
    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `paid_leave`  DECIMAL(5,1) NOT NULL DEFAULT 0,
    `casual_leave` DECIMAL(5,1) NOT NULL DEFAULT 0,
    `created_by`  INT(11) DEFAULT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_employee_leaves_employee` (`employee_id`),
    KEY `fk_emp_leave_employee` (`employee_id`),
    CONSTRAINT `fk_emp_leave_employee` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "✅ Table 'employee_leaves' created (or already exists).\n";
} else {
    echo "❌ Error creating table: " . $mysqli->error . "\n";
}

$mysqli->close();
echo "Done.\n";
?>
