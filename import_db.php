<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db_connection.php';

    $sqlFilePath = __DIR__ . '/db/crm_db.sql';

    // Check if the SQL file exists
    if (!file_exists($sqlFilePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Database SQL file not found.']);
        exit;
    }

    try {
        // Drop the database if it exists
        $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");

        // Create the database
        $pdo->exec("CREATE DATABASE `$dbName`");
        $pdo->exec("USE `$dbName`");

        // Read SQL file
        $sqlContent = file_get_contents($sqlFilePath);
        $sqlQueries = explode(";\n", $sqlContent);

        // Execute SQL queries
        foreach ($sqlQueries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Database imported successfully.']);
        $_SESSION['step'] = '3';
        
    } catch (PDOException $e) {
        // echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        echo json_encode(['status' => 'error', 'message' => 'Database  exception error.']);
    }
}
?>
