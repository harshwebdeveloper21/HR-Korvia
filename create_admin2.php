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

$res = $mysqli->query("SELECT id FROM users WHERE email='$email'");
if ($res->num_rows == 0) {
    $mysqli->query("INSERT INTO users (email, password, role, created_at, updated_at) VALUES ('$email', '$password', 'admin', '$now', '$now')");
    $userId = $mysqli->insert_id;
    
    $mysqli->query("INSERT INTO user_info (user_id, firstname, lastname, phone_number, created_at, updated_at) VALUES ($userId, 'Super', 'Admin', '1234567890', '$now', '$now')");
    echo "Admin user created: admin@example.com / admin123\n";
} else {
    echo "Admin user already exists: admin@example.com\n";
}
$mysqli->close();
