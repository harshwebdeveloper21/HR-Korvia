<!DOCTYPE html>
<html lang="en">
<style>
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 8px !important;
            padding: 6px !important;
            margin-top: 8px !important;
            /* margin-bottom: 5px !important; */
        }

        .iconfontsize {
            font-size: 11px !important;
        }
    }
</style>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal</title>
    <?= $this->include('dashboard/header_link'); ?>
    <!-- <link rel="stylesheet" href="assets/css/login.css"> -->
       <link rel="stylesheet" href="<?= base_url(env('ImagePath').'assets/css/login.css');?>">
</head>

<body class="with-welcome-text">
    <div id="loader" style="display:none;">
        <div class="loader-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <img src="<?= getCompanyLogo(); ?>" alt="logo" class="forgot-logo">
                            </div>
                            <h6 class="fw-light text-center">Forgot Password</h6>
                            <form id="ForgotForm" class="pt-3">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <div class="input-wrapper">
                                        <i class="mdi mdi-email"></i>
                                        <input type="email" class="form-control form-control-lg" id="email" placeholder="Enter your email" name="email">
                                    </div>
                                    <span id="emailError" class="text-danger"></span>
                                </div>

                                <div class="mt-3 d-grid gap-2 mb-4">
                                    <button class="btn hr-btnbg" type="submit">
                                    <span id="submitBtnText">Submit</span>
                                    <span id="submitBtnLoader" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                </button>
                                </div>
                                <div class="text-center">
                                    <a href="<?= base_url('/login') ?>" class="auth-link"> Back to login</a>
                                </div>
                            </form>

                            <div id="forgotSuccess" class="text-success d-none"></div>
                            <div id="forgotError" class="text-danger d-none"></div>

                            <div class="text-center mt-4">
                                <a href="https://www.fableadtechnolabs.com/" target="_blank" class="text-muted text-decoration-none" style="font-size: 13px;">© <?= date('Y') ?> Copyright - Fablead Developers Technolab</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
       $(document).ready(function () {
    const token = localStorage.getItem('token');

    $("#ForgotForm").on('submit', function (e) {
        e.preventDefault();

        var email = $("#email").val();
        $("#emailError").text("");

        if (!email) {
            $("#emailError").text("Email is required.");
            return;
        }

        // Show loader inside button
        $('#submitBtn').prop('disabled', true);
        $('#submitBtnText').text('Sending...');
        $('#submitBtnLoader').removeClass('d-none');

        $.ajax({
            url: '<?= base_url('api/sendResetLink') ?>',
            type: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            data: { email: email },
            success: function (response) {
                // Restore button
                $('#submitBtn').prop('disabled', false);
                $('#submitBtnText').text('Submit');
                $('#submitBtnLoader').addClass('d-none');

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#E66136',
                        confirmButtonText: 'OK'
                    });
                } else {
                    $("#emailError").text(response.message).css('color', 'red');
                }
            },
            error: function () {
                // In case of unexpected error
                $('#submitBtn').prop('disabled', false);
                $('#submitBtnText').text('Submit');
                $('#submitBtnLoader').addClass('d-none');
                $("#emailError").text("Something went wrong. Please try again.").css('color', 'red');
            }
        });
    });
});

    </script>

    <?= $this->include('dashboard/footer_link.php'); ?>
</body>

</html>