<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
      @media (max-width: 767px) {
    .interviewsmbtn{
     font-size: 10px !important;
    padding: 8px !important;
    margin-top: 10px !important;
    }
}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit SMTP Email</h4>
                <form class="form-sample" method="POST" id="SMTPEmailForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" name="smtp_id" id="smtp_id" value=""> <!-- Hidden input for id -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-6 col-form-label">Enable Mail Sent</label>
                                <div class="col-sm-8 col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sent_mail_enable" name="sent_mail_enable">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Protocol</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-server fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="smtp_protocol" id="smtp_protocol" placeholder="Enter smtp protocol" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Host</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-server-network fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="smtp_host" id="smtp_host" placeholder="Enter smtp host" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Port</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-server-network fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="smtp_port" id="smtp_port" placeholder="Enter smtp port" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Username</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account-circle fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="smtp_username" id="smtp_username" placeholder="Enter smtp username" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-lock fs-5"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="smtp_password" id="smtp_password" placeholder="Enter smtp password" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">Encryption</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-lock-outline fs-5"></i></span>
                                        </div>
                                        <select class="form-control" name="smtp_encryption" id="smtp_encryption">
                                            <option value="" disabled selected>Select Encryption</option>
                                            <option value="ssl">SSL</option>
                                            <option value="tls">TLS</option>
                                            <option value="none">None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">From Email</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-email fs-5"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="smtp_from_email" id="smtp_from_email" placeholder="Enter smtp from email" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label leave-sm-emp">From Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="smtp_from_name" id="smtp_from_name" placeholder="Enter smtp from name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="<?= base_url(
                            "/dashboard",
                        ) ?>" class="btn hr-btnbg interviewsmbtn">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn">Submit</button>
                    </div>
                    <div id="responseMessage"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token

        // Fetch SMTP settings on page load
        $.ajax({
            url: '<?= base_url(
                "api/smtp/getSmtpSettings",
            ) ?>', // Your API endpoint for fetching SMTP settings
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Pre-fill the form fields with the fetched data
                    var smtpSettings = response.data;
                    if (smtpSettings.sent_mail_enable == 1) {
                        document.getElementById("sent_mail_enable").checked = true;
                        $('#sent_mail_enable').prop('checked', true);
                    }

                    $('#smtp_protocol').val(smtpSettings.smtp_protocol);
                    $('#smtp_host').val(smtpSettings.smtp_host);
                    $('#smtp_port').val(smtpSettings.smtp_port);
                    $('#smtp_username').val(smtpSettings.smtp_username);
                    $('#smtp_password').val(smtpSettings.smtp_password);
                    $('#smtp_encryption').val(smtpSettings.smtp_encryption);
                    $('#smtp_from_email').val(smtpSettings.smtp_from_email);
                    $('#smtp_from_name').val(smtpSettings.smtp_from_name);
                    $('#smtp_id').val(smtpSettings.smtp_id);
                } else {
                    // Handle error if no settings are found
                    $('#responseMessage').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                // Handle error
                $('#responseMessage').html('<div class="alert alert-danger">Error: ' + error + '</div>');
            }
        });

        $('#SMTPEmailForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            $(".invalid-feedback").remove(); // Remove previous error messages
            $(".is-invalid").removeClass("is-invalid"); // Remove invalid input styling

            const formData = new FormData(this);
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfTokenValue = $('#csrfToken').val();

            $.ajax({
                url: '<?= base_url(
                    "api/smtp/updateSmtpSettings",
                ) ?>', // Your API endpoint for updating SMTP settings
                type: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                data: formData,
                 contentType: false,
                processData: false,
                success: function(response) {
                    if (response.message) {
                        // Show success message with SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload the page after user clicks OK
                        });
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.errors) {
                        $.each(response.errors, function(field, message) {
                            var fieldName = '#' + field;
                            $(fieldName).addClass('is-invalid');
                            var errorHtml = '<div class="invalid-feedback">' + message + '</div>';
                            $(fieldName).after(errorHtml);
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fix the errors in the form.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
