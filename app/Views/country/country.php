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
                <h4 class="card-title">Add Country</h4>
                <form class="form-sample" id="countryForm">
                      <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" id="id" name="id" /> <!-- For editing -->

                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Country Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                                        </div>
                                        <input type="text" id="country_name" name="country_name" class="form-control" placeholder="Enter Country Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-end sm-margin">
                        <a href="/countryview" class="btn hr-btnbg">Back</a>
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
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('id');
        if (editId) {
            isEditMode = true;
            countryId = editId;
             $.ajax({
                url: `/api/country/${countryId}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const country = responseData.data;
                        $('#country_name').val(country.country_name);
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
                    console.error('Error fetching country:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching country.",
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
            const countryName = $('#country_name').val().trim();
            if (countryName === '') {
                $('#country_name').addClass('is-invalid');
                let errorDiv = $('#country_name').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#country_name').parent().append(errorDiv);
                }
                errorDiv.text('Country name is required.');
                isValid = false;
            } else {
                $('#country_name').removeClass('is-invalid');
            }

            if (!isValid) return;

            // Determine if we are creating or updating
            const url = isEditMode ? `/api/country/${countryId}` : '/api/country';
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
                            window.location.href = "/countryview"; // Redirect after confirmation
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: responseData.message,
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

                    let errorMessage = "An error occurred. Please try again later.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
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
        });
    });
</script>


<?= $this->endSection(); ?>