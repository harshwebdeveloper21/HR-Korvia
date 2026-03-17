<?php
/**
 * Cleanup Script: Remove Non-Admin Push Subscriptions
 * 
 * This script removes all push notification subscriptions that belong to non-admin users.
 * Only admin users should have push notification subscriptions.
 * 
 * Run this script once to clean up the database:
 * php cleanup-non-admin-subscriptions.php
 */

// Database credentials (from app/Config/Database.php)
$hostname = 'localhost';
$username = 'u573967329_sanvihrusr';
$password = '>A1g9sPc';
$database = 'u573967329_sanvihr';
$port = 3306;

// Connect to database
try {
    $db = new mysqli($hostname, $username, $password, $database, $port);
    
    if ($db->connect_error) {
        die("❌ Connection failed: " . $db->connect_error . "\n");
    }
    
    $db->set_charset("utf8mb4");
} catch (Exception $e) {
    die("❌ Database connection error: " . $e->getMessage() . "\n");
}

// Get all subscriptions
$subscriptionsResult = $db->query("SELECT * FROM push_subscriptions");
$pushSubscriptions = $subscriptionsResult->fetch_all(MYSQLI_ASSOC);

// Get all users
$usersResult = $db->query("SELECT id, username, role FROM users");
$users = $usersResult->fetch_all(MYSQLI_ASSOC);

// Create user lookup array
$userLookup = [];
foreach ($users as $user) {
    $userLookup[$user['id']] = $user;
}

echo "🔍 Checking push subscriptions...\n\n";

$deletedCount = 0;
$adminCount = 0;
$totalCount = count($pushSubscriptions);

foreach ($pushSubscriptions as $subscription) {
    $userId = $subscription['user_id'];
    $user = $userLookup[$userId] ?? null;
    
    if (!$user) {
        echo "⚠️  Subscription ID {$subscription['id']}: User ID {$userId} not found - DELETING\n";
        $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE id = ?");
        $stmt->bind_param("i", $subscription['id']);
        $stmt->execute();
        $stmt->close();
        $deletedCount++;
        continue;
    }
    
    if ($user['role'] !== 'admin') {
        echo "❌ Subscription ID {$subscription['id']}: User '{$user['username']}' (Role: {$user['role']}) - DELETING\n";
        $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE id = ?");
        $stmt->bind_param("i", $subscription['id']);
        $stmt->execute();
        $stmt->close();
        $deletedCount++;
    } else {
        echo "✅ Subscription ID {$subscription['id']}: User '{$user['username']}' (Role: {$user['role']}) - KEEPING\n";
        $adminCount++;
    }
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SUMMARY:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total subscriptions found: {$totalCount}\n";
echo "Admin subscriptions (kept): {$adminCount}\n";
echo "Non-admin subscriptions (deleted): {$deletedCount}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

if ($deletedCount > 0) {
    echo "✅ Cleanup completed! All non-admin subscriptions have been removed.\n";
    echo "💡 Only admin users can now receive push notifications.\n";
} else {
    echo "✅ No cleanup needed. All subscriptions belong to admin users.\n";
}

$db->close();
echo "\n";

