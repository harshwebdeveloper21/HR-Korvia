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
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Location</h4>
                <form class="form-sample" id="departmentForm">
                     <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" id="id" name="id" /> <!-- For editing -->
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Job Location</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="job_location" id="job_location" placeholder="Enter location Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-end sm-margin">

                        <a href="/locationview" class="btn hr-btnbg">
                            Back
                        </a>
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
        const token = localStorage.getItem('token'); // JWT token
        let isEditMode = false; // Flag to track whether we're in edit mode
        let joblocationId = null; // To store the department ID for updating

        // Handle form submission (both create and update)
        $('#departmentForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

           const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
            let isValid = true;

            // Validate department name
            const departmentName = $('#job_location').val().trim();
            if (departmentName == '') {
                $('#job_location').addClass('is-invalid');
                let errorDiv = $('#job_location').parent().find('.invalid-feedback');
                if (errorDiv.length == 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#job_location').parent().append(errorDiv);
                   
                }
                errorDiv.text('location name is required.');
                isValid = false; // Stop the AJAX call if validation fails
            } else {
                $('#job_location').removeClass('is-invalid'); // Remove invalid class if input is valid
            }
           

            // If validation passes, submit the form
            if (isValid) {
                const url = isEditMode ? `/api/joblocation/${joblocationId}` : '/api/joblocation'; // API endpoint
                const method = 'POST'; // Using POST for both create and update

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                      
                    },
                      contentType: false,
                processData: false,
                    success: function(responseData) {
                        $('#loader').hide();

                        if (responseData && responseData.message) {
                            Swal.fire({
                                title: "Success!", // ✅ Correct title
                                text: responseData.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg',

                                }
                            }).then(() => {
                                window.location.href = "/locationview"; // ✅ Redirect on success
                            });
                        } else {
                            Swal.fire({
                                title: "Error!", // ✅ Will only show when there's actually an error
                                text: "Something went wrong!",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    },

                    error: function(xhr, status, error) {
                        $('#loader').hide();

                        // Check for 409 Conflict (Duplicate Entry)
                        if (xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.error) {
                            errorMessage = xhr.responseJSON.messages.error;
                        }

                        Swal.fire({
                            title: "Error!",
                            text: errorMessage,
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
        });

        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');
        if (Id) {
            fetchDepartmentData(Id);
        }

        function fetchDepartmentData(Id) {
            $.ajax({
                url: `/api/joblocation/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const location = responseData.data;
                        $('#job_location').val(location.job_location);
                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit location');
                        joblocationId = location.location_id;
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "location not found.",
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
                    console.error('Error fetching location:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching location.",
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
    });
</script>

<?= $this->endSection(); ?>