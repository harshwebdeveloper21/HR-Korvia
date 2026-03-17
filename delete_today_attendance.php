<?php
/**
 * Script to delete today's attendance records from database
 * 
 * Usage: 
 * 1. Run via browser: http://your-domain.com/delete_today_attendance.php
 * 2. Or run via command line: php delete_today_attendance.php
 * 
 * WARNING: This will delete ALL attendance records for today's date!
 */

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Database configuration - adjust these if needed
require_once __DIR__ . '/app/Config/Database.php';

$dbConfig = new \Config\Database();
$db = \Config\Database::connect();

$today = date('Y-m-d');

echo "Deleting attendance records for date: {$today}\n\n";

// Delete all attendance records for today
$builder = $db->table('attendance');
$builder->where('date', $today);
$deleted = $builder->delete();

echo "Successfully deleted {$deleted} attendance record(s) for {$today}\n";
echo "Done!\n";

