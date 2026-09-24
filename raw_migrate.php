<?php
$host = 'localhost';
$db   = 'hr_protal_new_db';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function tableExists($mysqli, $table) {
    $res = $mysqli->query("SHOW TABLES LIKE '$table'");
    return $res->num_rows > 0;
}

function colExists($mysqli, $table, $col) {
    $res = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
    return $res->num_rows > 0;
}

// 1. Create branches table
if (!tableExists($mysqli, 'branches')) {
    $mysqli->query("CREATE TABLE `branches` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `code` VARCHAR(50) NOT NULL,
        `address` TEXT NULL,
        `city` VARCHAR(100) NULL,
        `phone` VARCHAR(30) NULL,
        `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        `deleted_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_code` (`code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "OK: branches table created\n";
} else {
    echo "SKIP: branches table already exists\n";
}

// 2. Add branch_id to users
if (!colExists($mysqli, 'users', 'branch_id')) {
    $mysqli->query("ALTER TABLE `users` ADD COLUMN `branch_id` INT(11) UNSIGNED NULL AFTER `role`");
    $mysqli->query("ALTER TABLE `users` ADD INDEX `idx_branch_id` (`branch_id`)");
    echo "OK: users.branch_id added\n";
} else {
    echo "SKIP: users.branch_id already exists\n";
}
if (!colExists($mysqli, 'users', 'can_transfer_staff')) {
    $mysqli->query("ALTER TABLE `users` ADD COLUMN `can_transfer_staff` TINYINT(1) NOT NULL DEFAULT 0 AFTER `branch_id`");
    echo "OK: users.can_transfer_staff added\n";
} else {
    echo "SKIP: users.can_transfer_staff already exists\n";
}

// 3. Create branch_rules
if (!tableExists($mysqli, 'branch_rules')) {
    $mysqli->query("CREATE TABLE `branch_rules` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `branch_id` INT(11) UNSIGNED NOT NULL,
        `enable_payroll` TINYINT(1) NOT NULL DEFAULT 0,
        `payroll_type` VARCHAR(50) NULL DEFAULT 'monthly',
        `working_hours_per_day` DECIMAL(5,2) NULL,
        `include_holidays_in_working_days` TINYINT(1) NOT NULL DEFAULT 0,
        `half_day_hours` DECIMAL(5,2) NULL,
        `sunday_off` TINYINT(1) NOT NULL DEFAULT 1,
        `sunday_pay_type` VARCHAR(50) NULL,
        `saturday_off_enabled` TINYINT(1) NOT NULL DEFAULT 0,
        `saturday_off_type` VARCHAR(50) NULL,
        `saturday_off_pattern` VARCHAR(100) NULL,
        `saturday_half_day_enabled` TINYINT(1) NOT NULL DEFAULT 0,
        `saturday_half_day_pattern` VARCHAR(100) NULL,
        `saturday_pay_type` VARCHAR(50) NULL,
        `saturday_working_hours` DECIMAL(5,2) NULL DEFAULT 4,
        `saturday_full_day_override` TINYINT(1) NOT NULL DEFAULT 1,
        `yearly_holidays` INT(11) NULL,
        `enable_tax` TINYINT(1) NOT NULL DEFAULT 0,
        `tax_type` VARCHAR(50) NULL,
        `tax` DECIMAL(10,2) NULL,
        `salary_above_tax` DECIMAL(10,2) NULL,
        `lunch_break` TIME NULL,
        `start_time` TIME NULL,
        `half_time` TIME NULL,
        `end_time` TIME NULL,
        `grace_period` INT(11) NULL DEFAULT 0,
        `grace_minutes` INT(11) NULL DEFAULT 10,
        `enable_overtime` TINYINT(1) NOT NULL DEFAULT 0,
        `overtime_multiplier` DECIMAL(5,2) NULL,
        `overtime_rate_type` VARCHAR(50) NULL,
        `min_overtime_count_in_minutes` INT(11) NULL,
        `sandwich_leave` TINYINT(1) NOT NULL DEFAULT 0,
        `enable_geofencing` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_branch_id` (`branch_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "OK: branch_rules table created\n";
} else {
    echo "SKIP: branch_rules table already exists\n";
}

// 4. Create staff_transfers
if (!tableExists($mysqli, 'staff_transfers')) {
    $mysqli->query("CREATE TABLE `staff_transfers` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NOT NULL,
        `from_branch_id` INT(11) UNSIGNED NOT NULL,
        `to_branch_id` INT(11) UNSIGNED NOT NULL,
        `transferred_by` INT(11) UNSIGNED NOT NULL,
        `reason` TEXT NULL,
        `effective_date` DATE NULL,
        `created_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        KEY `idx_user_id` (`user_id`),
        KEY `idx_from_branch` (`from_branch_id`),
        KEY `idx_to_branch` (`to_branch_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "OK: staff_transfers table created\n";
} else {
    echo "SKIP: staff_transfers table already exists\n";
}

// 5. Create audit_logs
if (!tableExists($mysqli, 'audit_logs')) {
    $mysqli->query("CREATE TABLE `audit_logs` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNSIGNED NULL,
        `action` VARCHAR(100) NOT NULL,
        `model` VARCHAR(100) NULL,
        `record_id` INT(11) NULL,
        `old_value` JSON NULL,
        `new_value` JSON NULL,
        `ip_address` VARCHAR(45) NULL,
        `created_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        KEY `idx_user_id` (`user_id`),
        KEY `idx_model_record` (`model`, `record_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "OK: audit_logs table created\n";
} else {
    echo "SKIP: audit_logs table already exists\n";
}

// 6. Default Head Office Data
$res = $mysqli->query("SELECT id FROM branches WHERE code='HO'");
if ($res->num_rows == 0) {
    $now = date('Y-m-d H:i:s');
    $mysqli->query("INSERT INTO branches (name, code, address, city, phone, status, created_at, updated_at) VALUES ('Head Office', 'HO', 'Default Office', 'Head Office', '', 'active', '$now', '$now')");
    echo "OK: Head Office branch created\n";
} else {
    echo "SKIP: Head Office branch already exists\n";
}

$res = $mysqli->query("SELECT id FROM branches WHERE code='HO'");
$row = $res->fetch_assoc();
$branchId = (int)$row['id'];

// Assign unassigned HR and employees to HO
$mysqli->query("UPDATE users SET branch_id = $branchId WHERE role IN ('hr','employee') AND branch_id IS NULL");
echo "OK: Assigned existing users without branch to Head Office\n";

// Mark migrations as done so CI4 doesn't crash on future migrate runs
$time = time();
$batch = 2;
$migrationsToMark = [
    '2026-09-24-000001' => 'App\\\\Database\\\\Migrations\\\\CreateBranchesTable',
    '2026-09-24-000002' => 'App\\\\Database\\\\Migrations\\\\AddBranchColumnsToUsers',
    '2026-09-24-000003' => 'App\\\\Database\\\\Migrations\\\\CreateBranchRulesTable',
    '2026-09-24-000004' => 'App\\\\Database\\\\Migrations\\\\CreateStaffTransfersTable',
    '2026-09-24-000005' => 'App\\\\Database\\\\Migrations\\\\CreateAuditLogsTable',
    '2026-09-24-000006' => 'App\\\\Database\\\\Migrations\\\\DataMigration_DefaultBranch'
];

foreach ($migrationsToMark as $version => $class) {
    $check = $mysqli->query("SELECT id FROM migrations WHERE version='$version'");
    if ($check->num_rows == 0) {
        $mysqli->query("INSERT INTO migrations (version, class, `group`, namespace, time, batch) VALUES ('$version', '$class', 'default', 'App', $time, $batch)");
    }
}
echo "OK: Migrations marked as done in migrations table.\n";

$mysqli->close();
echo "\nAll done! Branch tables are ready in hr_protal_new_db.\n";
