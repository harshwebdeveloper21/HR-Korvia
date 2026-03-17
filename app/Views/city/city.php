<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    @media (min-width: 375px) and (max-width: 667px) {
.sm-margin{
    margin-top: 8px !important;
    /* margin-right: -8px !important; */
}
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add City</h4>
                <form class="form-sample" id="cityForm">
                      <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" id="id" name="id" /> <!-- For editing -->
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">City Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-city fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="city_name" id="city_name" placeholder="Enter city Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Country Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="country_id" id="country_id">
                                            <option value="">Select your country</option>
                                            <?php foreach ($countries as $country) : ?>
                                                <option value="<?= $country['id']; ?>"><?= $country['country_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-end sm-margin">
                        <a href="cityview" class="btn hr-btnbg">
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
        const token = localStorage.getItem('token'); // JWT token from login
        let isEditMode = false; // Flag to track whether we're in edit mode
        let cityId = null; // To store the city ID for updating

        // Handle form submission (both create and update)
        $('#cityForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

           const formData = new FormData(this); // ✅ FIXED HERE
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfTokenValue = $('#csrfToken').val();
    formData.append(csrfTokenName, csrfTokenValue);
            let isValid = true;

            // Validate city name
            const cityName = $('#city_name').val().trim();
            if (cityName === '') {
                $('#city_name').addClass('is-invalid');
                let errorDiv = $('#city_name').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#city_name').parent().append(errorDiv);
                }
                errorDiv.text('City name is required.');
                isValid = false;
            } else {
                $('#city_name').removeClass('is-invalid');
            }

            // Validate country selection
            const countryIdValue = $('#country_id').val().trim();
            if (countryIdValue === '') {
                $('#country_id').addClass('is-invalid');
                let errorDiv = $('#country_id').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#country_id').parent().append(errorDiv);
                }
                errorDiv.text('Country selection is required.');
                isValid = false;
            } else {
                $('#country_id').removeClass('is-invalid');
            }

            if (isValid) {
                const url = isEditMode ? `/api/city/${cityId}` : '/api/city'; // POST request with city ID for update
                const method = isEditMode ? 'POST' : 'POST'; // Use POST for both create and update
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
                                window.location.href = "/cityview"; // Redirect to city list page
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
                     error: function(xhr) {
                        $('#loader').hide();

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire({
                                title: "Error!",
                                text: xhr.responseJSON.message,
                                icon: "error",
                                confirmButtonText: "OK",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg',
                                }
                            });
                        } else {
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
                        }
                    }
                });
            }
        });

        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');
        if (Id) {
            fetchUserData(Id);
        }

        function fetchUserData(Id) {
            $.ajax({
                url: `/api/city/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const city = responseData.data;
                        $('#city_name').val(city.city_name);
                        $('#country_id').val(city.country_id);
                        $('#id').val(city.id);
                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit City');
                        cityId = city.id;
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "City not found.",
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
                    console.error('Error fetching city:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching city.",
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