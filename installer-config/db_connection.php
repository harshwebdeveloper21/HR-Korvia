<?php
session_start();

// Retrieve database configuration from session
$host = $_SESSION['DB_HOST'] ?? '';
$dbName = $_SESSION['DB_NAME'] ?? '';
$username = $_SESSION['DB_USER'] ?? '';
$password = $_SESSION['DB_PASSWORD'] ?? '';

if (empty($host) || empty($dbName) || empty($username)) {
    die(json_encode(['status' => 'error', 'message' => 'Database configuration is missing.']));
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]));
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
}
?>
