<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal</title>
    <!-- plugins:css -->
    <?= $this->include('dashboard/header_link'); ?>
    <style>
        /* Deduction modals: always on top (fixes mobile popup not visible) */
        .modal-backdrop { z-index: 9998 !important; }
        .modal { z-index: 9999 !important; }
        @media (max-width: 768px) {
            .modal .modal-dialog { max-height: 90vh; overflow-y: auto; }
        }
        /* Touch-friendly: no 300ms delay, deduction triggers work on mobile */
        .payroll-deduction-info, .btn-deduction-info-single, #profileDeductionInfoBtn, .btn-deduction-info {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            cursor: pointer;
        }
    </style>
</head>

<body class="with-welcome-text">
<div id="loader">
    <div class="loader-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?= $this->include('dashboard/navbar.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?= $this->include('dashboard/sidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <?= $this->renderSection('content'); ?>
                </div>

                <?= $this->include('dashboard/footer.php'); ?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <?= $this->include('dashboard/mobile_bottom_nav.php'); ?>
    <?= $this->include('dashboard/footer_link.php'); ?>
    <?= $this->renderSection('scripts'); ?>
</body>

</html>