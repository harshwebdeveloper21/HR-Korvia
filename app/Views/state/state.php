<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    @media (min-width: 375px) and (max-width: 667px) {
        .sm-margin {
            margin-top: 8px !important;
            /* margin-right: -8px !important; */
        }
    }
</style>
<!-- Form for Create and Edit Country -->
<div class="row">
    <div class="col-12 gri  d-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add State</h4>
                <form class="form-sample" id="countryForm">
                      <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" id="id" name="id" /> <!-- For editing -->

                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">State Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                                        </div>
                                        <input type="text" id="state_name" name="state_name" class="form-control" placeholder="Enter State Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-end sm-margin">
                        <a href="/stateView" class="btn hr-btnbg">Back</a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token from login
        let isEditMode = false; // Flag to track whether we're in edit mode
        let countryId = null; // To store the country ID for updating

        // Check if we're editing an existing country
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('id');
        if (editId) {
            isEditMode = true;
            countryId = editId;

            // Fetch the country data and populate the form for editing
            $.ajax({
                url: `/api/state/${countryId}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const country = responseData.data;
                        $('#state_name').val(country.state_name);
                        $('#id').val(country.id);
                        $('#submitBtn').text('Update');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Country not found.",
                            icon: "error",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching state:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching state.",
                        icon: "error",
                        confirmButtonText: "OK",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });
                }
            });
        }

        // Handle form submission (both create and update)
        $('#countryForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this); // ✅ FIXED HERE
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfTokenValue = $('#csrfToken').val();
            formData.append(csrfTokenName, csrfTokenValue);
            let isValid = true;

            // Validate country name
            const countryName = $('#state_name').val().trim();
            if (countryName === '') {
                $('#state_name').addClass('is-invalid');
                let errorDiv = $('#state_name').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#state_name').parent().append(errorDiv);
                }
                errorDiv.text('State name is required.');
                isValid = false;
            } else {
                $('#state_name').removeClass('is-invalid');
            }

            if (!isValid) return;

            // Determine if we are creating or updating
            const url = isEditMode ? `/api/state/${countryId}` : '/api/state';
            const method = 'POST'; // Using POST for both create and update
            $('#loader').show();

            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'Authorization': `Bearer ${token}`,
                  
                },
                 contentType: false, // ✅ Required for FormData
                    processData: false, // ✅ Required for FormData
                success: function(responseData) {
                    $('#loader').hide();

                    if (responseData.status === 'success') {
                        Swal.fire({
                            title: "Success!",
                            text: responseData.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        }).then(() => {
                            window.location.href = "/stateView";
                        });
                    } else {
                        // Custom error handling if status is not "success"
                        Swal.fire({
                            title: "Error!",
                            text: responseData.message || "An unknown error occurred.",
                            icon: "error",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#loader').hide();

                    // Clear previous validation errors
                    $('#state_name').removeClass('is-invalid');
                    $('#state_name').parent().find('.invalid-feedback').remove();

                    // Handle duplicate state error (409 Conflict)
                    if (xhr.status === 409) {
                        $('#state_name').addClass('is-invalid');
                        let errorDiv = $('<div class="invalid-feedback"></div>').text(xhr.responseJSON?.message || "State already exists.");
                        $('#state_name').parent().append(errorDiv);
                        return;
                    }

                    // Handle validation errors (400 Bad Request)
                    if (xhr.status === 400 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.state_name) {
                            $('#state_name').addClass('is-invalid');
                            let errorDiv = $('<div class="invalid-feedback"></div>').text(errors.state_name);
                            $('#state_name').parent().append(errorDiv);
                        }
                        return;
                    }

                    // Generic error fallback
                    Swal.fire({
                        title: "Error!",
                        text: "An error occurred. Please try again later.",
                        icon: "error",
                        confirmButtonText: "OK",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',
                        }
                    });

                    console.error('AJAX error:', xhr.responseText);
                }
            });

        });
    });
</script>


<?= $this->endSection(); ?>