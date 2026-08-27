<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal</title>
    <?= $this->include("dashboard/header_link") ?>
    <!-- <link rel="stylesheet" href="assets/css/login.css"> -->
        <link rel="stylesheet" href="<?= base_url(env("ImagePath") . "assets/css/login.css?v=" . time()) ?>">
</head>

<body class="with-welcome-text">
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left pb-4 pt-2 px-4 px-sm-5">
                            <div class="brand-logo mb-0 text-center">
                                <img src="<?= getCompanyLogo() ?>" alt="logo" class="login-logo mb-1">
                            </div>
                            <h4 class="text-center">Hello! Let's get started</h4>
                            <h6 class="fw-light text-center">Sign in to continue.</h6>
                            <form id="loginForm" class="pt-3">
                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email Address</label>
                                    <div class="input-wrapper">
                                        <i class="mdi mdi-email"></i>
                                        <input type="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Enter your email" name="email">
                                    </div>
                                    <span id="emailError" class="text-danger"></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <div class="input-wrapper">
                                        <i class="mdi mdi-lock"></i>
                                        <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Enter your password">
                                    </div>
                                    <span id="passwordError" class="text-danger"></span>
                                </div>
                                <label class="mt-2">
                                    <input type="checkbox" name="remember_me" value="1">
                                    Remember Me
                                </label>

                                <div id="loginError" class="error-message" style="color: red;"></div>
                                <div class="mt-3 w-100">
                                    <button class="btn hr-btnbg w-100" type="submit" style="height: 48px;">SIGN IN</button>
                                </div>
                            </form>
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
        $(document).on('submit', '#loginForm', function(e) {
            e.preventDefault();

            let hasError = false;

            // Get input values
            const email = $('#exampleInputEmail1').val().trim();
            const password = $('#exampleInputPassword1').val().trim();

            // Validate email
            if (!email) {
                $('#emailError').text('Email is required.');
                hasError = true;
            } else {
                $('#emailError').text(''); // Clear error when field is filled
            }

            // Validate password
            if (!password) {
                $('#passwordError').text('Password is required.');
                hasError = true;
            } else {
                $('#passwordError').text(''); // Clear error when field is filled
            }

            if (hasError) return; // Stop execution if errors exist
            $('#loader').show();

            $.ajax({
                url: '/login',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $('#loader').hide();

                        localStorage.setItem('token', response.token);

                        // // Show SweetAlert
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Login Successful',
                        //     // text: 'You will be redirected to the dashboard shortly...',
                        //     timer: 2000, // 2 seconds
                        //     showConfirmButton: false
                        // }).then(() => {
                        //     // Redirect after SweetAlert closes
                        //     window.location.href = '/dashboard';
                        // });
                        // Show styled success alert in the loginError div
                        $('#loginError')
                            .html('<div class="alert alert-success" role="alert">Login Successful!</div>')
                            .show();

                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 1000);
                    }

                },
                error: function(xhr) {
                    $('#loader').hide();

                    if (xhr.status === 400) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.email) {
                            $('#emailError').text(errors.email);
                        }
                        if (errors.password) {
                            $('#passwordError').text(errors.password);
                        }
                    } else if (xhr.status === 401) {
                        $('#loginError').text('Invalid email or password');
                    } else {
                        $('#loginError').text('Invalid email or password.');
                    }
                }
            });
        });

        // Clear error message when user starts typing
        $('#email, #password').on('input', function() {
            $(this).next('.error-message').text('');
        });
    </script>
    <?= $this->include("dashboard/footer_link.php") ?>
</body>

</html>
