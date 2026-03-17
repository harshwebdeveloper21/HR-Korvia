<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted form data
    $dbHost = $_POST['dbHost'] ?? '';
    $dbName = $_POST['dbName'] ?? '';
    $dbUser = $_POST['dbUser'] ?? '';
    $dbPassword = $_POST['dbPassword'] ?? '';
    $baseUrl = $_POST['baseUrl'] ?? '';

    // Validate the inputs (basic validation)
    if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($baseUrl)) {
        echo 'Please fill in all the fields.';
        exit;
    }

    // Test database connection
    try {
        $dbName = $_POST['dbName'] ?? '';
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $pdo->query("SHOW DATABASES LIKE '$dbName'");
        if ($query->rowCount() == 0) {
            $pdo->exec("CREATE DATABASE `$dbName`");
        }
    } catch (PDOException $e) {
        echo 'Invalid Database configration.';
        exit;
    }

    // Store variables in session
    $_SESSION['DB_HOST'] = $dbHost;
    $_SESSION['DB_NAME'] = $dbName;
    $_SESSION['DB_USER'] = $dbUser;
    $_SESSION['DB_PASSWORD'] = $dbPassword;
    $_SESSION['BASE_URL'] = $baseUrl;

    // Define the project root path and configuration file paths
    // $projectRootPath = '../fablead-crm';
    $projectRootPath = '../';
    $appConfigPath = $projectRootPath . 'app/Config/App.php';
    // print_r($appConfigPath);
    $dbConfigPath = $projectRootPath . 'app/Config/Database.php';

    // Ensure the directory structure exists
    if (!is_dir(dirname($appConfigPath))) {
        mkdir(dirname($appConfigPath), 0755, true);
    }

    //  file if it doesn't exist
    if (!file_exists($appConfigPath)) {
        echo 'App config file was not found.';
        exit;
    }

    //  file if it doesn't exist
    if (!file_exists($dbConfigPath)) {
        echo 'Database config file was not found.';
        exit;
    }

    // Read the contents of the configuration files
    $appContents = file_get_contents($appConfigPath);
    $dbContents = file_get_contents($dbConfigPath);

    // Replace the placeholders in the database configuration file
    // $dbContents = str_replace(
    //     ['{DB_HOST}', '{DB_NAME}', '{DB_USER}', '{DB_PASSWORD}'],
    //     [$dbHost, $dbName, $dbUser, $dbPassword],
    //     $dbContents
    // );

    $newValues = [
        "'host' => '" . $dbHost . "'",
        "'database' => '" . $dbName . "'",
        "'username' => '" . $dbUser . "'",
        "'password' => '" . $dbPassword . "'",
    ];

    $dbContents = preg_replace(
        [
            "/('host'\s*=>\s*)'[^']*'/",
            "/('database'\s*=>\s*)'[^']*'/",
            "/('username'\s*=>\s*)'[^']*'/",
            "/('password'\s*=>\s*)'[^']*'/"
        ],
        $newValues,
        $dbContents
    );


    // Replace the base Url Value
    $appContents = preg_replace(
        '/(\$baseUrl\s*=\s*)(["\'])(.*?)(\2);/i',
        '$1$2' . $baseUrl . '$2;',
        $appContents
    );

    // Save the updated contents back to the configuration files
    $dbWriteResult = file_put_contents($dbConfigPath, $dbContents);
    $appWriteResult = file_put_contents($appConfigPath, $appContents);

    if ($dbWriteResult !== false && $appWriteResult !== false) {
        echo 'success';
        $_SESSION['step'] = '2';
    } else {
        echo 'Error saving configuration files.';
    }
}
