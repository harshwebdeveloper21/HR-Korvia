<?php
// Include the database connection
require_once 'db_connection.php';
// var_dump($_POST); 
// exit;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve and sanitize input
    $adminUsername = htmlspecialchars(trim($_POST['adminUsername']));
    $adminEmail = htmlspecialchars(trim($_POST['adminEmail']));
    $adminPassword = htmlspecialchars(trim($_POST['adminPassword']));
    $companyName = htmlspecialchars(trim($_POST['companyname'])); // Added company name field
    // $subscriptionPlan = isset($_POST['subscriptionPlan']) ? htmlspecialchars(trim($_POST['subscriptionPlan'])) : null;
    // $staffLimit = isset($_POST['staffLimit']) ? htmlspecialchars(trim($_POST['staffLimit'])) : null;

    // Validate input
    if (empty($adminUsername) || empty($adminEmail) || empty($adminPassword) || empty($companyName)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        exit;
    }


    // Check if subscription plan is selected
    // if (empty($subscriptionPlan)) {
    //     echo json_encode(['status' => 'error', 'message' => 'Please select a subscription plan.']);
    //     exit;
    // }

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $adminEmail]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        exit;
    }

    // Hash the password
    // $hashedPassword =  md5($adminPassword);
    $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

    // Insert admin into the database
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email,password,role)
        VALUES (:username, :email, :password, :role)
    ");
    $result = $stmt->execute([
        'username' => $adminUsername,
        'email' => $adminEmail,
         'password' => $hashedPassword,
        'role' => "admin",

    ]);

    if ($result) {
        // ============= 
        $userId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
        INSERT INTO user_info (user_id, firstname, email,role)
        VALUES (:user_id, :firstname, :email, :role)
    ");
    $stmt->execute([
        'user_id' => $userId,
        'firstname' => $adminUsername,
        'email' => $adminEmail,
        'role'=>"admin",
    ]);
    $logo_img = 'fab_logo.jpg';
        // Check if a logo is uploaded and process it
        if (isset($_FILES['logo_img']) && $_FILES['logo_img']['error'] == UPLOAD_ERR_OK) {
            // $uploadDir = 'upload/';
            $uploadDir = __DIR__ . '/../public/upload/'; 
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['logo_img']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('logo_', true) . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            // Validate file t  ype (optional)
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.']);
                exit;
            }

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES['logo_img']['tmp_name'], $filePath)) {
                $logo_img = $fileName;  // Store the file path if uploaded
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload logo.']);
                exit;
            }
        }

        // Check if $logo_img is still NULL and assign default
        if ($logo_img === NULL) {
            $logo_img = 'fab_logo.jpg';  // Set default logo path if no logo was uploaded
        }

        // Debugging: Check if the $logo_img is correctly set


        // Insert into company_logo table
        $stmt = $pdo->prepare("
    INSERT INTO company_logo (company_name, logo_img)
    VALUES (:company_name, :logo_img)
");
        $stmt->execute([
            'company_name' => $companyName,
            'logo_img' => $logo_img  // Use the logo image path (either uploaded or default)
        ]);
        // Create a .txt file after successful admin creation
        $filePath = '../installation_success.txt'; // Path to the .txt file
        $fileContent = "Admin setup completed successfully.\n";
        $fileContent .= "Admin Username: $adminUsername\n";
        $fileContent .= "Admin Email: $adminEmail\n";
        $fileContent .= "Company Name: $companyName\n";
        $fileContent .= "Company Logo: $logo_img\n";
        // $fileContent .= "Subscription Plan ID: $subscriptionPlan\n";
        // $fileContent .= "Staff Limit: $staffLimit\n";
        $fileContent .= "Installation Time: " . date('Y-m-d H:i:s') . "\n";

        // Write content to the file
        file_put_contents($filePath, $fileContent);

        echo json_encode(['status' => 'success', 'message' => 'Admin updated successfully.', 'url' => $_SESSION['BASE_URL']]);
        $_SESSION['step'] = '4';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add admin.']);
    }
}
