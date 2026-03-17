<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
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
<div class="row">
    <div class="col-lg-10 col-md-8 grid-margin mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Change Password</h4>
                <form class="form-sample" id="changePasswordForm">
                    <!-- Current Password Field with Icon -->
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label leave-sm-emp" for="current_password">Current Password</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-lock fs-5"></i></span>
                                </div>
                                <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter Current Password" />
                            </div>
                            <div id="current_password_error" class="error-message text-danger"></div> <!-- Error message for current password -->
                        </div>
                    </div>

                    <!-- New Password Field with Icon -->
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label leave-sm-emp" for="new_password">New Password</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-lock-outline fs-5"></i></span>
                                </div>
                                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter New Password" />
                            </div>
                            <div id="new_password_error" class="error-message text-danger"></div> <!-- Error message for new password -->
                        </div>
                    </div>

                    <!-- Confirm New Password Field with Icon -->
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label leave-sm-emp" for="confirm_new_password">Confirm New Password</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-lock-check fs-5"></i></span>
                                </div>
                                <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm New Password" />
                            </div>
                            <div id="confirm_new_password_error" class="error-message text-danger"></div> <!-- Error message for confirm password -->
                            <div id="error_message" class="text-danger d-none"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group row text-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn hr-btnbg attendenceall">Submit</button>
                            <div id="success_message" class="text-success d-none"></div>
                        </div>
                    </div>
                </form>

                <!-- Success Message -->

                <!-- Error Message -->
                <!-- <div id="error_message" class="text-danger d-none"></div> -->

                <!-- Error Message Div -->
                <!-- <div id="error_message" class="text-danger d-none"></div> -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#changePasswordForm").on('submit', function(e) {
            e.preventDefault();

            var current_password = $("#current_password").val();
            var new_password = $("#new_password").val();
            var confirm_new_password = $("#confirm_new_password").val();

            // Clear previous error messages
            $(".error-message").text('');
            $("#error_message").addClass('d-none');
            $("#success_message").addClass('d-none');

            // Form validation
            var valid = true;

            if (!current_password) {
                $("#current_password_error").text("Current password is required.");
                valid = false;
            }
            if (!new_password) {
                $("#new_password_error").text("New password is required.");
                valid = false;
            }
            if (!confirm_new_password) {
                $("#confirm_new_password_error").text("Confirm password is required.");
                valid = false;
            }
            if (new_password !== confirm_new_password) {
                $("#confirm_new_password_error").text("New password and confirm password do not match.");
                valid = false;
            }

            if (!valid) return;
            $('#loader').show();

            // If validation passes, make the AJAX request
            $.ajax({
                url: '<?= base_url("/api/changePassword") ?>',
                type: 'POST',
                data: {
                    current_password: current_password,
                    new_password: new_password,
                },
                dataType: 'json',
                success: function(response) {
                    $('#loader').hide();

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Password changed successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // $("#success_message").text("Password changed successfully.").removeClass('d-none');
                            $("#changePasswordForm")[0].reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();

                    var errorResponse = xhr.responseJSON;
                    if (errorResponse && errorResponse.messages && errorResponse.messages.error) {
                        $("#error_message").text(errorResponse.messages.error).removeClass('d-none');
                    } else {
                        $("#error_message").text("An error occurred. Please try again.").removeClass('d-none');
                    }
                }
            });
        });
    });
</script>


<?= $this->endSection() ?>
