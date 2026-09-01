<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal - Sign In</title>
    <link rel="shortcut icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>" type="image/png">
    <link rel="icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>" type="image/png" sizes="192x192">
    <link rel="icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>" type="image/png" sizes="512x512">
    <link rel="icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>" type="image/png" sizes="32x32">
    <link rel="icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="msapplication-TileImage" content="<?= base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png') ?>">
    <meta name="msapplication-TileColor" content="#e66136">
    <meta name="theme-color" content="#e66136">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Fablead HRMS">
    <meta name="application-name" content="Fablead HRMS">
    <link rel="stylesheet" href="<?= base_url(env('ImagePath') . 'assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url(env("ImagePath") . "assets/css/login.css?v=" . time()) ?>">
</head>

<body class="login-page-body">

    <!-- Floating Background Canvas Elements -->
    <div class="login-bg-container">
        <!-- Interactive Particle Wave Canvas -->
        <canvas id="particleCanvas"></canvas>

        <!-- Radial Dot Grid -->
        <div class="login-bg-grid"></div>

        <!-- Ambient Glowing Mesh Orbs -->
        <div class="floating-orb floating-orb-1"></div>
        <div class="floating-orb floating-orb-2"></div>
        <div class="floating-orb floating-orb-3"></div>
    </div>

    <!-- Loading overlay -->
    <div id="loader" style="display: none;">
        <div class="loader-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Main Centered Login Card -->
    <div class="login-card-wrapper">
        <div class="login-card">
            
            <!-- Brand Logo -->
            <div class="login-logo-container">
                <a href="https://www.fableadtechnolabs.com/" target="_blank" title="Fablead Developers Technolab">
                    <img src="<?= base_url(env('ImagePath') . 'assets/images/fab_logo.png') ?>" alt="Fablead HRMS" class="login-logo">
                </a>
            </div>

            <!-- Welcome Header with proper breathing room -->
            <div class="login-header">
                <h4>Hello! Let's get started</h4>
                <p>Sign in to continue to your HR portal</p>
            </div>

            <!-- Login Form -->
            <form id="loginForm" novalidate>
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                
                <!-- Email Address -->
                <div class="login-form-group">
                    <label for="exampleInputEmail1" class="login-label">Email Address</label>
                    <div class="login-input-wrapper">
                        <i class="mdi mdi-email-outline input-icon"></i>
                        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="name@company.com" name="email" autocomplete="email" required>
                    </div>
                    <span id="emailError" class="login-field-error"></span>
                </div>

                <!-- Password -->
                <div class="login-form-group">
                    <label for="exampleInputPassword1" class="login-label">Password</label>
                    <div class="login-input-wrapper">
                        <i class="mdi mdi-lock-outline input-icon"></i>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Enter your password" autocomplete="current-password" required>
                        <i class="mdi mdi-eye-outline toggle-password" title="Toggle password visibility"></i>
                    </div>
                    <span id="passwordError" class="login-field-error"></span>
                </div>

                <!-- Options Row: Remember Me -->
                <div class="login-options-row">
                    <label class="login-checkbox-label">
                        <input type="checkbox" name="remember_me" value="1">
                        <span>Remember Me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button class="btn-login-submit" id="btnSubmit" type="submit">
                    <span>SIGN IN</span>
                </button>

                <!-- Feedback / Status Alerts -->
                <div id="loginAlertBox" class="login-alert-box" style="display: none;"></div>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <a href="https://www.fableadtechnolabs.com/" target="_blank">
                    © <?= date('Y') ?> Copyright - Fablead Developers Technolab
                </a>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                let hasError = false;
                const email = $('#exampleInputEmail1').val().trim();
                const password = $('#exampleInputPassword1').val().trim();

                // Validate email
                if (!email) {
                    $('#emailError').text('Email is required.');
                    hasError = true;
                } else {
                    $('#emailError').text('');
                }

                // Validate password
                if (!password) {
                    $('#passwordError').text('Password is required.');
                    hasError = true;
                } else {
                    $('#passwordError').text('');
                }

                if (hasError) return;

                const $btn = $('#btnSubmit');
                const originalBtnText = $btn.html();
                
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in...');
                $('#loginAlertBox').hide().empty();

                $.ajax({
                    url: '/login',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            localStorage.setItem('token', response.token);

                            $('#loginAlertBox')
                                .html('<div class="alert alert-success"><i class="mdi mdi-check-circle-outline me-2"></i>Login Successful! Redirecting...</div>')
                                .slideDown(200);

                            setTimeout(() => {
                                window.location.href = response.redirect || '/dashboard';
                            }, 800);
                        } else {
                            $btn.prop('disabled', false).html(originalBtnText);
                            $('#loginAlertBox')
                                .html('<div class="alert alert-danger"><i class="mdi mdi-alert-circle-outline me-2"></i>' + (response.message || 'Login failed.') + '</div>')
                                .slideDown(200);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalBtnText);

                        let errorMsg = 'Invalid email or password.';

                        if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.email) $('#emailError').text(errors.email);
                            if (errors.password) $('#passwordError').text(errors.password);
                            return;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        $('#loginAlertBox')
                            .html('<div class="alert alert-danger"><i class="mdi mdi-alert-circle-outline me-2"></i>' + errorMsg + '</div>')
                            .slideDown(200);
                    }
                });
            });

            // Clear errors on input
            $('#exampleInputEmail1, #exampleInputPassword1').on('input', function() {
                $('#loginAlertBox').slideUp(150).empty();
                $('#emailError').text('');
                $('#passwordError').text('');
            });

            // Toggle password visibility
            $('.toggle-password').on('click', function() {
                $(this).toggleClass('mdi-eye-outline mdi-eye-off-outline');
                const input = $('#exampleInputPassword1');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                } else {
                    input.attr('type', 'password');
                }
            });

        });

        // ══════════════════════════════════════════════════════
        // Interactive Ambient Particle Mesh Canvas
        // ══════════════════════════════════════════════════════
        (function() {
            const canvas = document.getElementById('particleCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            const particleCount = 38;
            const maxDistance = 110;
            const mouse = { x: null, y: null, radius: 140 };

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            window.addEventListener('mousemove', function(e) {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });

            window.addEventListener('mouseout', function() {
                mouse.x = null;
                mouse.y = null;
            });

            class Particle {
                constructor() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.6;
                    this.vy = (Math.random() - 0.5) * 0.6;
                    this.radius = Math.random() * 2 + 1.2;
                }
                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    if (this.x < 0 || this.x > width) this.vx *= -1;
                    if (this.y < 0 || this.y > height) this.vy *= -1;

                    // Mouse gentle repulsion
                    if (mouse.x !== null && mouse.y !== null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < mouse.radius) {
                            const force = (mouse.radius - dist) / mouse.radius;
                            this.x -= (dx / dist) * force * 1.5;
                            this.y -= (dy / dist) * force * 1.5;
                        }
                    }
                }
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(230, 97, 54, 0.4)';
                    ctx.fill();
                }
            }

            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();

                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < maxDistance) {
                            const alpha = (1 - dist / maxDistance) * 0.16;
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(230, 97, 54, ${alpha})`;
                            ctx.lineWidth = 1;
                            ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(animate);
            }
            animate();
        })();
    </script>
</body>

</html>
