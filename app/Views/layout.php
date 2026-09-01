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
        /* Global Uniform Button Styling */
        .btn-secondary, .modal-footer .btn-secondary, .btn-light-secondary {
            background-color: #6c757d !important;
            border: 2px solid #6c757d !important;
            color: #ffffff !important;
            border-radius: 4px !important;
            font-family: Poppins, sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            padding: 6px 14px !important;
            min-height: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1.2 !important;
            transition: all 0.2s ease !important;
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
            color: #ffffff !important;
        }

        .hr-btnbg, .btn-primary.hr-btnbg, button.hr-btnbg, a.hr-btnbg {
            background-color: #E66136 !important;
            border: 2px solid #F05929 !important;
            border-radius: 4px !important;
            color: #ffffff !important;
            font-family: Poppins, sans-serif !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            padding: 6px 14px !important;
            min-height: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1.2 !important;
            transition: all 0.2s ease !important;
        }
        .hr-btnbg:hover {
            border: 2px solid #e66136 !important;
            background-color: #ffffff !important;
            color: #e66136 !important;
        }
        .hr-btnbg:disabled, .hr-btnbg[disabled] {
            background-color: #f0845a !important;
            border-color: #f0845a !important;
            color: #ffffff !important;
            opacity: 0.8 !important;
            cursor: not-allowed !important;
        }

        .interviewsmbtn {
            font-size: 14px !important;
            padding: 6px 16px !important;
            min-height: 36px !important;
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