<?php
session_start();

if (!isset($_SESSION['BASE_URL'])) {
    // $_SESSION['BASE_URL'] = 'http://localhost/Installer_new';
    // $_SESSION['BASE_URL'] = 'http://localhost/installer';
    $_SESSION['BASE_URL'] = 'http://hrweb.fableadtechnolabs.in/';

}
// =============
// $installationSuccessFile = '../Installer_new/installation_success.txt';
$installationSuccessFile = '../installer/installation_success.txt';

if (file_exists($installationSuccessFile)) {
    // If the file exists, skip the form and redirect to the base URL
    header('Location: ' . $_SESSION['BASE_URL']);
    exit();
}
// ------------------
$currentStep = 1;
// Check session variables and update currentStep
if (isset($_SESSION['step']) && $_SESSION['step'] === '1') {
    $currentStep = 2;
} elseif (isset($_SESSION['step']) && $_SESSION['step'] === '2') {
    $currentStep = 3;
} elseif (isset($_SESSION['step']) && $_SESSION['step'] === '3') {
    $currentStep = 4;
} elseif (isset($_SESSION['step']) && $_SESSION['step'] === '4') {
    header('Location: ' . $_SESSION['BASE_URL']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hrportal-Installer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="installer-config/style.css">
</head>

<body class="">
    <div class="container mt-4 w-50 ">

        <!-- progress base -->
        <div class="progress" style="height: 5px;">
            <div id="progress-bar" class="progress-bar  progress-bar-striped progress-bar-animated" style="background-color: #E66136;"
                role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>

        <!-- Step 1: Unzip Project -->
        <div id="step1" class="step active step-container rounded p-4 shadow-lg border border-2">
            <h4 class="text-center" style="color:#E66136">
                <i class="bi bi-file-zip"></i> STEP-1: UNZIP PROJECT
            </h4>
            <hr>
            <div class="alert alert-danger alert-dismissible d-none mt-3" role="alert" id="alertStep1">

            </div>
            <div class="alert alert-success alert-dismissible d-none mt-3" role="alert" id="successAlertStep1">

            </div>
            <p class="text-center">Unzip the project by clicking the below button and wait until it's complete.</p>
            <div class="d-flex justify-content-center mt-4">
                <button class="btn d-flex align-items-center"style="background-color: #E66136;color:white" id="unzipBtn" <?php echo isset($_SESSION['step']) && $_SESSION['step'] == '1' ? 'disabled' : ''; ?>>
                    <i class="bi bi-arrow-down-circle me-2"></i>&nbsp; Unzip Project
                    <div class="loader mx-2" id="loaderStep1">
                        <div class="spinner-border text-light spinner-border-sm" role="status">
                            <span class="sr-only">Unzipping...</span>
                        </div>
                    </div>
                </button>
            </div>
            <div id="progressBarContainer" style="display:none;" class="progress mt-4">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="background-color: #E66136;"
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%</div>
            </div>
        </div>

        <!-- Step 2: Database Configuration -->
        <div id="step2" class="step step-container rounded p-4 shadow-lg border border-2">
            <h4 class="text-center"style="color:#E66136">
                <i class="bi bi-server"></i> STEP-2: DATABASE CONFIGURATION
            </h4>
            <hr>
            <div class="alert alert-danger alert-dismissible d-none mt-3" role="alert" id="alertStep2">
            </div>
            <div class="alert alert-success alert-dismissible d-none mt-3" role="alert" id="successAlertStep2">
            </div>
            <form id="dbForm" class="mt-4"  enctype="multipart/form-data">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-house"></i></span>
                        <input type="text" class="form-control" id="dbHost" name="dbHost" placeholder="e.g., localhost">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-database"></i></span>
                        <input type="text" class="form-control" id="dbName" name="dbName"
                            placeholder="Enter database name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-person-circle"></i></span>
                        <input type="text" class="form-control" id="dbUser" name="dbUser"
                            placeholder="Enter database username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="dbPassword" name="dbPassword"
                            placeholder="Enter database password">
                    </div>
                </div>
               
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-link-45deg"></i></span>
                        <input type="text" class="form-control" id="baseUrl" name="baseUrl"
                            placeholder="Enter Project Base URL">
                    </div>
                </div>
                <!-- Note or Description -->
                <small class="form-text text-danger">
                    <strong>Note :</strong> If a database with this name already exists, it will be dropped and
                    recreated. Please ensure you have backups of important data before proceeding.
                    <br>
                    <strong>Note :</strong> The base URL will look like this:
                    <code>http://localhost/hrportal/</code>

                </small>
                <div class="text-center mt-4 d-flex justify-content-center">
                    <button class="btn d-flex align-items-center"style="background-color: #E66136;color:white" type="submit" <?php echo isset($_SESSION['step']) && $_SESSION['step'] == '2' ? 'disabled' : ''; ?>>
                        <i class="bi bi-arrow-right-circle me-2" id="btnIcon"></i>&nbsp;Proceed
                        <div class="loader mx-2" id="loaderStep2">
                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                <span class="sr-only">Proceeding...</span>
                            </div>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 3: Import Database -->
        <div id="step3" class="step step-container rounded p-4 shadow-lg border border-2">
            <h4 class="text-center"style="color:#E66136">
                <i class="bi bi-cloud-upload-fill"></i> STEP-3: IMPORT DATABASE
            </h4>
            <hr>
            <div class="alert alert-danger alert-dismissible d-none mt-3" role="alert" id="alertStep3">
            </div>
            <div class="alert alert-success alert-dismissible d-none mt-3" role="alert" id="successAlertStep3">
            </div>
            <p class="text-center">Click the button below to import the database from the SQL file.</p>
            <div class="mt-4 d-flex justify-content-center">
                <button class="btn d-flex align-items-center" style="background-color: #E66136;color:white" id="importDbBtn" <?php echo isset($_SESSION['step']) && $_SESSION['step'] == '3' ? 'disabled' : ''; ?>>
                    <i class="bi bi-file-earmark-code-fill me-2"></i> &nbsp; Import Database
                    <div class="loader mx-2" id="loaderStep3" style="display: none;">
                        <div class="spinner-border text-light spinner-border-sm" role="status">
                            <span class="sr-only">Importing...</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Step 4: Admin Signup -->
        <div id="step4" class="step step-container rounded p-4 shadow-lg border border-2">
            <h4 class="text-center"style="color:#E66136">
                <i class="bi bi-person-badge-fill"></i> STEP-4: ADMIN SET-UP
            </h4>
            <hr>
            <div class="alert alert-danger alert-dismissible d-none mt-3" role="alert" id="alertStep4">
            </div>
            <div class="alert alert-success alert-dismissible d-none mt-3" role="alert" id="successAlertStep4">
            </div>
            <form id="adminForm" class="mt-4" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #E66136;color:white"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="adminUsername" name="adminUsername"
                            placeholder="Enter admin username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control" id="adminEmail" name="adminEmail"
                            placeholder="Enter admin email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-key-fill"></i></span>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword"
                            placeholder="Enter admin password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-briefcase"></i></span>
                        <input type="text" class="form-control" id="companyname" name="companyname"
                            placeholder="Enter company name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"style="background-color: #E66136;color:white"><i class="bi bi-file-earmark-image"></i></span>
                        <input type="file" class="form-control" id="logo_img" name="logo_img" accept="image/*">
                    </div>
                </div>
                <!-- ========== -->
                <!-- <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-list"></i></span>
                        <select class="form-control" id="subscriptionPlan" name="subscriptionPlan">
                            <option value="">Select Plan</option>
                            <option value="1">Basic Plan</option>
                            <option value="2">Premium Plan</option>
                        </select>
                    </div>
                </div> -->

                <!-- <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                        <input type="number" class="form-control" id="staffLimit" name="staffLimit"
                            placeholder="Enter staff limit" min="1">
                    </div>
                </div> -->
                <!-- ================ -->

                <div class="text-center mt-4 d-flex justify-content-center">
                    <button type="button" class="btn d-flex align-items-center" style="background-color: #E66136;color:white" id="adminSignupBtn" <?php echo isset($_SESSION['step']) && $_SESSION['step'] == '4' ? 'disabled' : ''; ?>>
                        <i class="bi bi-person-check-fill"></i> &nbsp;Add Admin
                        <div class="loader mx-2" id="loaderStep4">
                            <div class="spinner-border text-light spinner-border-sm" role="status">
                                <span class="sr-only">Proceeding...</span>
                            </div>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    let currentStep = <?php echo $currentStep; ?>;
</script>
<script src="installer-config/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>