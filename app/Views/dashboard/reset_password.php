<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal</title>
    <?= $this->include('dashboard/header_link'); ?>
    <link rel="stylesheet" href="<?= base_url(env('ImagePath') . 'assets/css/login.css?v=' . time()); ?>">
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
                                <img src="<?= getCompanyLogo(); ?>" alt="logo" class="reset-logo">
                            </div>
                            <h6 class="fw-light text-center">Reset Password</h6>
                            <form id="ResetForm" class="pt-3">
                                <input type="hidden" id="resetToken" value="<?= $_GET['token'] ?? '' ?>">

                                <div class="form-group">
                                    <label>New Password</label>
                                    <div class="input-wrapper">
                                        <i class="mdi mdi-lock"></i>
                                        <input type="password" class="form-control form-control-lg" id="new_password" placeholder="Enter new password" name="new_password">
                                    </div>
                                    <span id="newPasswordError" class="text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <div class="input-wrapper">
                                        <i class="mdi mdi-lock-check"></i>
                                        <input type="password" class="form-control form-control-lg" id="confirm_password" placeholder="Confirm new password" name="confirm_password">
                                    </div>
                                    <span id="confirmPasswordError" class="text-danger"></span>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn hr-btnbg" type="submit">Submit</button>
                                </div>
                            </form>

                            <div id="resetSuccess" class="text-success d-none"></div>
                            <div id="resetError" class="text-danger d-none"></div>

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
        $(document).ready(function() {
            const token = localStorage.getItem('token'); // JWT token

            $("#ResetForm").on('submit', function(e) {
                e.preventDefault();
                var token = $("#resetToken").val();
                var new_password = $("#new_password").val();
                var confirm_password = $("#confirm_password").val();

                if (new_password !== confirm_password) {
                    $("#confirmPasswordError").text("Passwords do not match.");
                    return;
                }
                $('#loader').show();

                $.ajax({
                    url: '<?= base_url('api/resetPassword') ?>',
                    type: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    data: {
                        token: token,
                        new_password: new_password,
                        confirm_password: confirm_password,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>' // Include CSRF token
                    },
                    success: function(response) {
                        $('#loader').hide();
                        console.log(response); // Add this line to debug response
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#E66136',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                // Redirect after the alert closes
                                window.location.href = '/login';
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Something went wrong!',
                                confirmButtonColor: '#E66136',
                                confirmButtonText: 'Try Again'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loader').hide();

                        console.error(xhr.responseText); // Log detailed error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred. Please try again later.',
                            confirmButtonColor: '#E66136',
                            confirmButtonText: 'Try Again'
                        });
                    }
                });

            });
        });
    </script>

    <?= $this->include('dashboard/footer_link.php'); ?>
</body>

</html>