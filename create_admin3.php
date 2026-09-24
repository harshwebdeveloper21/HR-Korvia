<?php
$host = 'localhost';
$db   = 'hr_protal_new_db';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$email = 'admin@example.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');

$mysqli->query("DELETE FROM users WHERE email='$email'");

$mysqli->query("INSERT INTO users (email, password, role, created_at, updated_at) VALUES ('$email', '$password', 'admin', '$now', '$now')");
$userId = $mysqli->insert_id;

$mysqli->query("INSERT INTO user_info (user_id, firstname, lastname, created_at, updated_at) VALUES ($userId, 'Super', 'Admin', '$now', '$now')");
echo "Admin user created: admin@example.com / admin123\n";
$mysqli->close();
