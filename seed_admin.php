<?php
/**
 * Standalone Admin Seeder Script
 * Access via browser: http://localhost/fableadhrportal/seed_admin.php
 * Or via terminal: php seed_admin.php
 */

require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Bootstrap CodeIgniter application framework
$app = \Config\Services::codeigniter();
$app->initialize();

$seeder = \Config\Database::seeder();
try {
    $seeder->call('App\Database\Seeds\AdminSeeder');
    echo "<h1>Database Connection & Admin Setup Successful!</h1>";
    echo "<p><strong>Email:</strong> admin@gmail.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Role:</strong> admin</p>";
} catch (\Throwable $e) {
    echo "<h1>Setup Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
