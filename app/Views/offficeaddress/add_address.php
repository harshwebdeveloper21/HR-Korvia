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
<div class="modal fade" id="addStateModal" tabindex="-1" aria-labelledby="addStateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStateModalLabel">Add Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stateForm">
                    <div class="mb-3">
                        <label for="country_name" class="form-label">State Name</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                            </div>
                            <input type="text" id="state_name" name="state_name" class="form-control" placeholder="Enter State Name" />
                        </div>
                        <div id="state_name_error" class="invalid-feedback d-block mt-1 text-danger"></div>
                    </div>

                    <button type="submit" class="btn hr-btnbg float-end" id="submitState">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- city modal -->
<div class="modal fade" id="addCityModal" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCityModalLabel">Add City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cityForm">
                    <div class="mb-3">
                        <label for="city_name" class="form-label">City Name</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-city fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="city_name" id="city_name" placeholder="Enter city Name" />

                        </div>
                        <div id="city_name_error" class="invalid-feedback d-block text-danger mt-1"></div>
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Country Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                            </div>
                            <select class="form-select" name="country_id" id="countries_id_modal">
                                <option value="">Select your country</option>
                                <?php foreach ($countries as $country) : ?>
                                    <option value="<?= $country['id']; ?>"><?= $country['country_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="country_id_error" class="invalid-feedback d-block text-danger mt-1"></div>
                    </div>

                    <button type="submit" class="btn hr-btnbg float-end" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- country modal -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCountryModalLabel">Add Country</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="countryForm">
                    <div class="mb-3">
                        <label for="country_name" class="form-label">Country Name</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                            </div>
                            <input type="text" id="country_name" name="country_name" class="form-control" placeholder="Enter Country Name" />
                        </div>
                        <div id="country_name_error" class="invalid-feedback d-block text-danger mt-1"></div>
                    </div>

                    <button type="submit" class="btn hr-btnbg float-end" id="submitContry">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Location Address</h4>
                <form class="form-sample" method="POST" action="" id="AddressForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location</label>
                               
                                    <input type="hidden" id="id" name="id" value="">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-map-marker fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="locations_id" id="locations_id">
                                            <option value="" disabled selected>Select Location</option>
                                            <?php foreach ($locations as $location) : ?>
                                                <option value="<?= $location['location_id']; ?>"><?= esc($location['job_location']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                               
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                               


                                    <textarea class="form-control" name="address" id="address" placeholder="Enter Address"></textarea>

                                </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label>State</label>
                                    <button type="button" class="btn p-1 btn-sm d-flex align-items-center rounded-pill addbtn-white" style="background-color: #E66136;font-size:11px" data-bs-toggle="modal" data-bs-target="#addStateModal">
                                        <i class="mdi mdi-plus"></i> ADD STATE
                                    </button>
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-map fs-5"></i></span>
                                    </div>
                                    <select class="form-select" name="state_id" id="state_id">
                                        <option value="">Select your State</option>
                                        <?php foreach ($states as $state) : ?>
                                            <option value="<?= $state['id']; ?>"><?= $state['state_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label>Country</label>
                                    <button type="button" class="btn p-1 btn-sm d-flex align-items-center rounded-pill addbtn-white" style="background-color: #E66136;font-size:11px" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                                        <i class="mdi mdi-plus"></i> ADD COUNTRY
                                    </button>
                                </div>

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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <label>City</label>
                                    <button type="button" class="btn p-1 btn-sm d-flex align-items-center rounded-pill addbtn-white" style="background-color: #E66136;font-size:11px" data-bs-toggle="modal" data-bs-target="#addCityModal">
                                        <i class="mdi mdi-plus"></i> ADD CITY
                                    </button>
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-city fs-5"></i></span>
                                    </div>
                                    <select class="form-select" name="city_id" id="city_id">
                                        <option value="">Select your city</option>
                                        <?php foreach ($cities as $city) : ?>
                                            <option value="<?= $city['id']; ?>"><?= $city['city_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Postal Code</label>
                               
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-mailbox fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter Postal Code" />
                                    </div>
                                
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-end sm-margin">
                        <a href="<?= base_url('/addressview') ?>" class="btn hr-btnbg">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                    <div id="responseMessage"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Flag to track whether we're in edit mode
        let addressId = null; // To store the country ID for updating

        $('#AddressForm').on('submit', function(e) {
            e.preventDefault();


            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            const formData = new FormData(this);
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfTokenValue = $('#csrfToken').val();
            formData.append(csrfTokenName, csrfTokenValue);

            const url = isEditMode ? `/api/save-location-address/${addressId}` : '/api/save-location-address'; // Endpoint for update or create
            const method = isEditMode ? 'POST' : 'POST'; // Method for both actions
            $('#loader').show();

            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'Authorization': `Bearer ${token}`,

                },
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#loader').hide();

                    if (response.status === 'success') {
                        // Show success alert using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        }).then(() => {
                            // Redirect to candidate view page after the alert
                            window.location.href = "/addressview";
                        });

                        // Reset form
                        $('#AddressForm')[0].reset();
                        if (isEditMode) {
                            $('#submitBtn').text('Submit');
                            isEditMode = false;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },

                error: function(xhr) {
                    $('#loader').hide();

                    let errors = xhr.responseJSON.errors;

                    if (typeof displayValidationErrors === 'function') displayValidationErrors(errors);
                }
            });
        });
        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');

        if (Id) {
            fetchUserData(Id);
        }


        function fetchUserData(Id) {

            $.ajax({
                url: `/api/job_address/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const job = responseData.data;

                        $('#locations_id').val(job.locations_id);
                        $('#address').val(job.address);
                        $('#city_id').val(job.city_id);
                        $('#state_id').val(job.state_id);
                        $('#country_id').val(job.country_id);
                        $('#postal_code').val(job.postal_code);

                        $('#id').val(job.id); //Set the hidden ID field for updating
                        $('#submitBtn').text('Update'); // Change button text to "Update"
                        $('.card-title').text('Edit Address');
                        addressId = job.address_id; // Set the department ID for future reference
                        isEditMode = true; // Set edit mode flag

                    } else {
                        $('#responseMessage').html('<p class="text-danger">job not found.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching country:', error);
                    $('#responseMessage').html('<p class="text-danger">Error fetching job.</p>');
                }
            });
        }
    });
     $(document).ready(function () {
         const token = localStorage.getItem('token');
    $("#countryForm").submit(function (e) {
        e.preventDefault();

        const countryName = $("#country_name").val().trim();
        const errorContainer = $("#country_name_error");
        errorContainer.text("");

        if (countryName === "") {
            errorContainer.text("Please enter a country name.");
            return;
        }

        $.ajax({
            url: "<?= base_url('api/add-country') ?>",
            type: "POST",
            data: { country_name: countryName },
            headers: {
                Authorization: "Bearer " + token
            },
            success: function (response) {
                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Country added successfully!",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $("#countryForm")[0].reset();
                    $("#addCountryModal").modal("hide");

                    $("#country_id").append(
                        `<option value="${response.country.id}" selected>${response.country.country_name}</option>`
                    );
                      $("#countries_id_modal").append(
                    `<option value="${response.country.id}">${response.country.country_name}</option>`
                 );
                } else {
                    // 🔥 Handle duplicate error or other custom message
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: response.message || "Failed to add country."
                    });
                }
            },
            error: function (xhr) {
                let res = xhr.responseJSON;
                if (res?.errors?.country_name) {
                    errorContainer.text(res.errors.country_name);
                } else if (res?.message) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: res.message
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Something went wrong. Please try again."
                    });
                }
            }
        });
    });
});

$(document).ready(function () {
     const token = localStorage.getItem('token');
    $("#stateForm").submit(function (e) {
        e.preventDefault();

        let stateName = $("#state_name").val().trim();
        let errorContainer = $("#state_name_error");
        errorContainer.text("");

        if (stateName === "") {
            errorContainer.text("Please enter a state name.");
            return;
        }

        $.ajax({
            url: "<?= base_url('api/add-state') ?>",
            type: "POST",
            data: { state_name: stateName },
            headers: {
                Authorization: "Bearer " + token
            },
            success: function (response) {
                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "State added successfully!",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $("#addStateModal").modal("hide");
                    $("#stateForm")[0].reset();
                    errorContainer.text("");

                    $("#state_id, #state_id_modal").append(
                        `<option value="${response.country.id}" selected>${response.country.state_name}</option>`
                    );
                } else {
                    // 👇 Show duplicate or custom error message
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: response.message || "Failed to add state."
                    });
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                if (res?.errors?.state_name) {
                    errorContainer.text(res.errors.state_name);
                } else if (res?.message) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: res.message
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An unexpected error occurred."
                    });
                }
            }
        });
    });
});



 $(document).ready(function () {
     const token = localStorage.getItem('token');
    $("#cityForm").submit(function (e) {
        e.preventDefault();

        let cityName = $("#city_name").val().trim();
        let countryId = $("#countries_id_modal").val();
        let cityError = $("#city_name_error");
        let countryError = $("#country_id_error");

        // Reset previous errors
        cityError.text("");
        countryError.text("");

        let isValid = true;

        if (cityName === "") {
            cityError.text("Please enter a city name.");
            isValid = false;
        }
        if (countryId === "") {
            countryError.text("Please select a country.");
            isValid = false;
        }

        if (!isValid) return;

        $.ajax({
            url: "<?= base_url('api/add-city') ?>",
            type: "POST",
            data: {
                city_name: cityName,
                country_id: countryId
            },
            headers: {
                Authorization: "Bearer " + token // if required
            },
          success: function (response) {
    if (response.status === "success") {
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "City added successfully!",
            timer: 2000,
            showConfirmButton: false
        });

        // Reset form and close modal
        $("#cityForm")[0].reset();
        $("#addCityModal").modal("hide");

        // Add new city to dropdown
        $("#city_id").append(
            `<option value="${response.city.id}" selected>${response.city.city_name}</option>`
        );
    } else {
        // 👇 Handle duplicate or general error message
        Swal.fire({
            icon: "error",
            title: "Failed",
            text: response.message || "Failed to add city."
        });
    }
},
error: function (xhr) {
    const res = xhr.responseJSON;
    if (res?.errors?.city_name) {
        $("#city_name_error").text(res.errors.city_name);
    }
    if (res?.errors?.country_id) {
        $("#country_id_error").text(res.errors.country_id);
    }
    if (res?.message) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message
        });
    }
}

           
        });
    });
});
</script>
<?= $this->endSection(); ?>