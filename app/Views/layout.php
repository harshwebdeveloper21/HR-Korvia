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

        /* ══════════════════════════════════════════════════════════════════════════
           Global Fix for Input Groups & Prepend/Append Icons Alignment
           ══════════════════════════════════════════════════════════════════════════ */
        .input-group {
            position: relative;
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: stretch !important;
            width: 100% !important;
        }

        .input-group > .input-group-prepend,
        .input-group > .input-group-append,
        .input-group-prepend,
        .input-group-append {
            display: flex !important;
            align-items: stretch !important;
            margin: 0 !important;
        }

        .input-group-prepend .input-group-text,
        .input-group-append .input-group-text,
        .input-group > .input-group-text {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: auto !important;
            min-height: 38px !important;
            padding: 0.375rem 0.75rem !important;
            background-color: #E66136 !important;
            color: #ffffff !important;
            border: 1px solid #dee2e6 !important;
        }

        .input-group-prepend .input-group-text,
        .input-group > .input-group-text:first-child {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-top-left-radius: 4px !important;
            border-bottom-left-radius: 4px !important;
        }

        .input-group-append .input-group-text,
        .input-group > .input-group-text:last-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }

        .input-group > .input-group-prepend ~ .form-control,
        .input-group > .input-group-prepend ~ .form-select,
        .input-group > .input-group-text ~ .form-control,
        .input-group > .input-group-text ~ .form-select {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
            flex: 1 1 auto !important;
            width: 1% !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 38px !important;
        }

        .input-group > .form-control:not(:last-child),
        .input-group > .form-select:not(:last-child) {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        /* ══════════════════════════════════════════════════════
           Unified Table Action Icons Standard Across Entire Portal
           ══════════════════════════════════════════════════════ */
        table td a.text-primary i,
        table td a[title*="View" i] i,
        table td .text-primary i.mdi-eye {
            color: #2563eb !important;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        table td a.text-primary:hover i,
        table td a[title*="View" i]:hover i {
            color: #1d4ed8 !important;
            transform: scale(1.15);
        }

        table td a.text-warning i,
        table td button.text-warning i,
        table td a[title*="Edit" i] i,
        table td .text-warning i.mdi-pencil {
            color: #f59e0b !important;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        table td a.text-warning:hover i,
        table td button.text-warning:hover i,
        table td a[title*="Edit" i]:hover i {
            color: #d97706 !important;
            transform: scale(1.15);
        }

        table td a.text-danger i,
        table td button.text-danger i,
        table td a[title*="Delete" i] i,
        table td .text-danger i.mdi-delete {
            color: #ef4444 !important;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        table td a.text-danger:hover i,
        table td button.text-danger:hover i,
        table td a[title*="Delete" i]:hover i {
            color: #dc2626 !important;
            transform: scale(1.15);
        }

        /* Protect all inline-hidden elements from being overridden */
        [style*="display: none"], [style*="display:none"], [hidden] {
            display: none !important;
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