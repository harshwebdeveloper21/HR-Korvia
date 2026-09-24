<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require_once FCPATH . '../system/bootstrap.php';
$db = \Config\Database::connect();

$email = 'admin@example.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';
$now = date('Y-m-d H:i:s');

$userCheck = $db->table('users')->where('email', $email)->get()->getRowArray();
if (!$userCheck) {
    $db->table('users')->insert([
        'email' => $email,
        'password' => $password,
        'role' => $role,
        'created_at' => $now,
        'updated_at' => $now
    ]);
    $userId = $db->insertID();

    $db->table('user_info')->insert([
        'user_id' => $userId,
        'firstname' => 'Super',
        'lastname' => 'Admin',
        'phone_number' => '1234567890',
        'created_at' => $now,
        'updated_at' => $now
    ]);
    echo "Admin user created: admin@example.com / admin123\n";
} else {
    echo "Admin user already exists: admin@example.com\n";
}
