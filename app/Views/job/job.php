<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
<!-- <link rel="stylesheet" href="<? //= base_url('assets/css/jobs.css')
?>"> -->
<link rel="stylesheet" href="<?= base_url(
    env("ImagePath") . "assets/css/jobs.css",
) ?>">
<style>
    /* Custom Styling for the Slider */
    #salarySlider {
        margin-top: 10px;
        height: 4px;
        /* Adjust thickness */
        background: #E66136;
        /* Line color */
        border-radius: 3px;
    }

    #ageSlider {
        margin-top: 10px;
        height: 4px;
        /* Adjust thickness */
        background: #E66136;
        /* Line color */
        border-radius: 3px;
    }

    .noUi-handle {
        width: 16px !important;
        height: 16px !important;
        border-radius: 50%;
        /* Make handle circular */
        background: #E66136;
        /* Handle color */
        box-shadow: none;
        border: none;
    }

    .noUi-handle:before,
    .noUi-handle:after {
        display: none !important;
    }

    .noUi-connect {
        background: #E66136 !important;

    }

    .noUi-target {
        border: none !important;
    }

    #multiStepForm .job-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--hr-primary-text, var(--hr-primary, #e66136));
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .08);
        border-left: 3px solid var(--hr-primary, #e66136);
        border-radius: 4px;
        padding: 7px 12px;
        margin: 4px 0 14px;
    }
    #multiStepForm .form-group { margin-bottom: 14px !important; }
    #multiStepForm .form-group > label,
    #multiStepForm .job-label-row label {
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }
    #multiStepForm .job-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 26px;
        margin-bottom: 4px;
    }
    #multiStepForm .job-label-row label { margin-bottom: 0; }
    #multiStepForm .job-add-btn {
        font-size: 11px;
        padding: 2px 8px;
        line-height: 1.5;
        border-radius: 4px;
    }
    #multiStepForm .form-control,
    #multiStepForm .form-select { min-height: 38px; font-size: 13.5px; }
    #multiStepForm .job-gender-box {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px 18px;
        min-height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px 12px;
        background: #fff;
    }
    #multiStepForm .job-gender-box .form-check { margin: 0; }
    #multiStepForm .salary-range-container {
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 10px 14px;
    }
    .job-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 6px;
    }

    @media (max-width: 767px) {
        .addsmbtnres {
            font-size: 12px !important;
            padding: 8px !important;
        }
    }
</style>
<!-- departement -->
<div class="modal fade" id="adddepartementModal" tabindex="-1" aria-labelledby="adddepartementModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adddepartementModalLabel">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="departmentForm">
                    <?php
                        $curUserRole = $currentUserRole ?? session()->get('role') ?? '';
                        $isAdminOrHr = in_array($curUserRole, ['admin', 'hr']);
                        $isBranchAdminRole = ($curUserRole === 'branch_admin');
                        $userBranchId = $currentUserBranchId ?? session()->get('branch_id') ?? '';
                        $branchesList = $branches ?? (new \App\Models\BranchModel())->getActiveBranches();
                    ?>
                    <?php if ($isAdminOrHr): ?>
                    <div class="mb-3">
                        <label for="dept_modal_branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select class="form-select" name="branch_id" id="dept_modal_branch_id" required>
                            <option value="">Select Branch</option>
                            <?php foreach ($branchesList as $branch): ?>
                                <option value="<?= esc($branch['id']) ?>"><?= esc($branch['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-danger mt-1" id="dept_branch_error"></div>
                    </div>
                    <?php elseif ($isBranchAdminRole): ?>
                    <!-- Hidden branch_id auto-filled for branch_admin -->
                    <input type="hidden" name="branch_id" id="dept_modal_branch_id" value="<?= esc($userBranchId) ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="department_name" class="form-label">Department Name</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-briefcase fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="department_name" id="department_name"
                                placeholder="Enter Department Name" />
                        </div>
                        <div class="text-danger mt-1" id="department_name_error"></div>
                    </div>

                    <button type="submit" class="btn hr-btnbg float-end" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAddressModalLabel">Add Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAddressForm">
                    <div class="mb-3">
                        <label for="address_name" class="form-label">Location</label>
                        <input type="hidden" id="id" name="id" value="">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-map-marker fs-5"></i></span>
                            </div>
                            <select class="form-select" name="locations_id" id="modal_locations_id">
                                <option value="" disabled selected>Select Location</option>
                                <?php foreach ($located as $locateds): ?>
                                    <option value="<?= $locateds[
                                        "location_id"
                                    ] ?>"><?= esc(
                                         $locateds["job_location"],
                                     ) ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <small id="location_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-home-city fs-5"></i></span>
                            </div>
                            <textarea class="form-control" name="address" id="address"
                                placeholder="Enter Address"></textarea>
                        </div>
                        <small id="address_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">State</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-home-city fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="state" id="state" placeholder="Enter State" />
                        </div>
                        <small id="state_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">City</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-city fs-5"></i></span>
                            </div>
                            <select class="form-select" name="city_id" id="city_id">
                                <option value="">Select your city</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?= $city[
                                        "id"
                                    ] ?>"><?= $city["city_name"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <small id="city_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Country</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-earth fs-5"></i></span>
                            </div>
                            <select class="form-select" name="country_id" id="country_id">
                                <option value="">Select your country</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country[
                                        "id"
                                    ] ?>"><?= $country[
                                         "country_name"
                                     ] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <small id="country_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Postal Code</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-mailbox fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="postal_code" id="postal_code"
                                placeholder="Enter Postal Code" />
                        </div>
                        <small id="postal_name_error" class="text-danger"></small> <!-- Error message container -->
                    </div>
                    <button type="submit" class="btn hr-btnbg float-end" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- location -->
<div class="modal fade" id="addlocationModal" tabindex="-1" aria-labelledby="addlocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addlocationModalLabel">Add Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="LocationForm">
                    <div class="mb-3">
                        <label for="country_name" class="form-label">Job Location</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="job_location" id="job_location"
                                placeholder="Enter location Name" />
                        </div>
                        <div id="job_location_error" class="text-danger mt-1" style="font-size: 13px;"></div>
                    </div>
                    <button type="submit" class="btn hr-btnbg float-end" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <!-- <h2 class="text-left">Add JobAdd Job</h2> -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="card-title">Add Job</h2>
                    <a href="/jobview" class="btn hr-btnbg addsmbtnres">
                        All Job
                    </a>
                </div>

                <form id="multiStepForm">
                    <input type="hidden" id="id" name="id" value="">

                    <div class="job-section-title"><i class="mdi mdi-briefcase-outline"></i> Job Details</div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 form-group">
                            <label>Job Title</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-briefcase-outline"></i></span>
                                <input type="text" class="form-control" name="job_title" id="job_title"
                                    placeholder="Enter job title">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <div class="job-label-row">
                                <label>Department</label>
                                <button type="button" class="btn btn-sm hr-btnbg job-add-btn" data-bs-toggle="modal"
                                    data-bs-target="#adddepartementModal">
                                    <i class="mdi mdi-plus"></i> Add Department
                                </button>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-domain"></i></span>
                                <select class="form-select" name="department_id" id="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department["id"] ?>"><?= esc($department["department_name"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <label>Job Type</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-briefcase"></i></span>
                                <select class="form-select" name="job_type" id="job_type">
                                    <option value="full">Full Time</option>
                                    <option value="part">Part Time</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <label>Experience (Years)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-briefcase-check-outline"></i></span>
                                <input type="number" class="form-control" name="experience" id="experience"
                                    placeholder="Enter experience in years">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <label>Close Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                <input type="date" class="form-control" name="close_date" id="close_date">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <label>Gender</label>
                            <div class="job-gender-box">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="both" value="both">
                                    <label class="form-check-label" for="both">Both</label>
                                </div>
                            </div>
                            <div class="error" id="gender-Error"></div>
                        </div>
                    </div>

                    <div class="job-section-title"><i class="mdi mdi-map-marker-outline"></i> Location &amp; Range</div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 form-group">
                            <div class="job-label-row">
                                <label>Location</label>
                                <button type="button" class="btn btn-sm hr-btnbg job-add-btn" data-bs-toggle="modal"
                                    data-bs-target="#addlocationModal">
                                    <i class="mdi mdi-plus"></i> Add Location
                                </button>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-map-marker"></i></span>
                                <select id="locations_id" name="locations_id" class="form-select">
                                    <option value="">Select Location Name</option>
                                    <?php foreach ($locations as $job): ?>
                                        <option value="<?= $job["location_id"] ?>"><?= esc($job["job_location"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 form-group">
                            <div class="job-label-row">
                                <label>Address</label>
                                <button type="button" class="btn btn-sm hr-btnbg job-add-btn" data-bs-toggle="modal"
                                    data-bs-target="#addAddressModal">
                                    <i class="mdi mdi-plus"></i> Add Address
                                </button>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-map-marker"></i></span>
                                <select id="addresses_id" name="addresses_id" class="form-select">
                                    <option value="">Select Address</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 form-group">
                            <label>Age Range</label>
                            <div class="salary-range-container">
                                <div class="d-flex justify-content-between">
                                    <span>Min: <span id="ageMinValue">18</span></span>
                                    <span class="badge" style="background-color: #E66136;" id="age">18 - 65</span>
                                    <span>Max: <span id="ageMaxValue">65</span></span>
                                </div>
                                <div id="ageSlider"></div>
                                <input type="hidden" name="age" id="age_range_hidden">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 form-group">
                            <label>Salary Range</label>
                            <div class="salary-range-container">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Min:</span>
                                        <input type="number" id="salaryMinValue" class="form-control form-control-sm" value="1000" style="width: 100px; min-height: 30px;" min="0">
                                    </div>
                                    <span class="badge" style="background-color: #E66136;" id="salary_range">1000 - 100000</span>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Max:</span>
                                        <input type="number" id="salaryMaxValue" class="form-control form-control-sm" value="100000" style="width: 100px; min-height: 30px;" min="0">
                                    </div>
                                </div>
                                <div id="salarySlider"></div>
                                <input type="hidden" name="salary_range" id="salary_range_hidden">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6 form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3"
                                placeholder="Enter description"></textarea>
                        </div>
                    </div>

                    <div class="job-form-actions">
                        <a href="/jobview" class="btn btn-light addsmbtnres">Cancel</a>
                        <button type="submit" class="btn hr-btnbg addsmbtnres" id="mainSubmitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<script>
    $(document).ready(function () {
        const token = localStorage.getItem("token");
        let isEditMode = false;
        let jobId = new URLSearchParams(window.location.search).get("id");

        function validateForm() {
            let isValid = true;
            let inputs = $("#multiStepForm").find("input, textarea, select");

            inputs.each(function () {
                let input = $(this);
                let nameAttr = input.attr("name");
                if (!nameAttr) return true; // Skip inputs without a name attribute
                
                let fieldName = nameAttr.replace(/_/g, " "); // Convert underscores to spaces for readability
                let label = input.closest(".col-md-6, .col-md-4, .col-md-12, .form-group").find("label").first().text().trim(); // Get the label text
                if (!label) label = fieldName;

                // Skip validation for the description field or hidden fields
                if (input.attr("name") === "description" || input.attr("type") === "hidden") {
                    return true; // Skip validation
                }

                if (input.is(":radio")) {
                    let radioGroup = $(`input[name="${input.attr("name")}"]`);
                    let isChecked = radioGroup.is(":checked");

                    if (!isChecked) {
                        let container = radioGroup.closest(".col-md-6, .col-md-4, .col-md-12, .form-group");
                        let errorContainer = container.find(".invalid-feedback");
                        if (!errorContainer.length) {
                            container.append(`<div class="invalid-feedback d-block">${label} is required.</div>`);
                        }
                        isValid = false;
                    } else {
                        radioGroup.closest(".col-md-6, .col-md-4, .col-md-12, .form-group").find(".invalid-feedback").remove();
                    }
                } else {
                    let val = input.val();
                    let isEmpty = (val === null || val === undefined || (typeof val === 'string' && !val.trim()));

                    if (isEmpty) {
                        input.addClass("is-invalid");

                        // Handling for any field (input, textarea, select) inside input-group
                        if (input.closest(".input-group").length) {
                            let inputGroup = input.closest(".input-group");
                            let errorContainer = inputGroup.next(".invalid-feedback");
                            if (!errorContainer.length) {
                                inputGroup.after(`<div class="invalid-feedback d-block">${label} is required.</div>`);
                            }
                        } else {
                            if (!input.next(".invalid-feedback").length) {
                                input.after(`<div class="invalid-feedback d-block">${label} is required.</div>`);
                            }
                        }
                        isValid = false;
                    } else {
                        input.removeClass("is-invalid");

                        // Remove error message for normal inputs
                        input.next(".invalid-feedback").remove();

                        // Remove error message for fields inside input-group
                        if (input.closest(".input-group").length) {
                            input.closest(".input-group").next(".invalid-feedback").remove();
                        }
                    }
                }
            });

            return isValid;
        }

        // Live validation clearing on input / change
        $(document).on("input change", "#multiStepForm input, #multiStepForm select, #multiStepForm textarea", function () {
            let input = $(this);
            let val = input.val();
            if (val !== null && val !== undefined && (typeof val !== 'string' || val.trim() !== '')) {
                input.removeClass("is-invalid");
                input.next(".invalid-feedback").remove();
                if (input.closest(".input-group").length) {
                    input.closest(".input-group").next(".invalid-feedback").remove();
                }
            }
        });

        initializeSalarySlider();
        initializeAgeSlider();

        const url = jobId ? "<?= base_url(
            "api/job/",
        ) ?>" + jobId : "<?= base_url("api/job") ?>";
        const method = jobId ? 'POST' : 'POST'; // Method for both actions
        $("#multiStepForm").on("submit", function (e) {
            e.preventDefault(); // ALWAYS FIRST!
            
            try {
                const ageSlider = document.getElementById("ageSlider");
                if (ageSlider && ageSlider.noUiSlider) {
                    const ageValues = ageSlider.noUiSlider.get();
                    $('#age_range_hidden').val(ageValues[0] + '-' + ageValues[1]);
                }
                const salarySlider = document.getElementById("salarySlider");
                if (salarySlider && salarySlider.noUiSlider) {
                    const salaryValues = salarySlider.noUiSlider.get();
                    $('#salary_range_hidden').val(salaryValues[0] + '-' + salaryValues[1]);
                }
            } catch (err) {
                console.warn("Slider error:", err);
            }

            if (!validateForm()) {
                const firstErr = $("#multiStepForm .is-invalid, #multiStepForm .invalid-feedback").first();
                if (firstErr.length) {
                    $('html, body').animate({ scrollTop: firstErr.offset().top - 120 }, 200);
                }
                return;
            }

            let formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }
            $('#loader').show();

            $.ajax({
                url: url,
                type: method,
                dataType: "json", // Ensure it expects JSON
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#loader').hide();

                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message || "Job saved correctly.",
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.href = "/jobview";
                    });
                    $('#multiStepForm')[0].reset();
                    if (isEditMode) {
                        $('#mainSubmitBtn').text('Submit');
                        isEditMode = false;
                    }
                },
                error: function (xhr) {
                    $('#loader').hide();
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let firstError = "";
                        if (typeof displayValidationErrors === 'function') displayValidationErrors(errors);
                        
                        if (firstError) {
                            Swal.fire('Validation Error', firstError, 'error');
                        }
                    } else {
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? 
                                    xhr.responseJSON.message : "Failed to save the job data.";
                        Swal.fire("Error!", msg, "error");
                    }
                },
            });
        });
        // const params = new URLSearchParams(window.location.search);
        // let jobId = params.get('id');
        // console.log(leaveId);
        if (jobId) {
            fetchUserData(jobId);
        }
        // console.log(leaveId);

        function fetchUserData(jobId) {
            // alert("hi..");
            $.ajax({
                url: `/api/jobupdate/${jobId}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function (responseData) {
                    if (responseData.status === 'success') {
                        const job = responseData.data;

                        $('#job_title').val(job.job_title);
                        $('#description').val(job.description);
                        $('#department_id').val(job.department_id);
                        $('#status').val(job.status);
                        $('#locations_id').val(job.locations_id);
                        $('#addresses_id').val(job.addresses_id);
                        $('#locations_id').val(job.locations_id).trigger("change"); // ✅ Trigger location change

                        // ✅ Store the saved address ID before the API call
                        window.savedAddressId = job.addresses_id;
                        $('#age').val(job.age);
                        $('#job_type').val(job.job_type);
                        $('#experience').val(job.experience);
                        $('#salary_range').val(job.salary_range);
                        $('#post_date').val(job.post_date);
                        $('#close_date').val(job.close_date); // Populate the form fields
                        $('#id').val(job.id); //Set the hidden ID field for updating

                        let salaryMinMax = job.salary_range.split('-'); // Assuming "1000-50000"
                        let salaryMin = parseInt(salaryMinMax[0]);
                        let salaryMax = parseInt(salaryMinMax[1]);

                        if (!isNaN(salaryMin) && !isNaN(salaryMax)) {
                            initializeSalarySlider(salaryMin, salaryMax);
                        }

                        // Set the age range
                        let ageMinMax = job.age.split('-'); // Assuming "18-40"
                        let ageMin = parseInt(ageMinMax[0]);
                        let ageMax = parseInt(ageMinMax[1]);

                        if (!isNaN(ageMin) && !isNaN(ageMax)) {
                            initializeAgeSlider(ageMin, ageMax);
                        }
                        $('#mainSubmitBtn').text('Update'); // Change button text to "Update"
                        $('.card-title').text('Edit job');
                        jobId = job.id; // Set the department ID for future reference
                        isEditMode = true; // Set edit mode flag
                        $("input[name='gender']").prop("checked", false); // Uncheck all first
                        if (job.gender) {
                            $(`input[name='gender'][value='${job.gender}']`).prop("checked", true);
                        }
                    } else {
                        $('#responseMessage').html('<p class="text-danger">job not found.</p>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching country:', error);
                    $('#responseMessage').html('<p class="text-danger">Error fetching job.</p>');
                }
            });

        }
    });


    function initializeSalarySlider(min = 1000, max = 100000) {
        const salarySlider = document.getElementById("salarySlider");

        if (!salarySlider) return;

        // Destroy previous instance if reinitializing
        if (salarySlider.noUiSlider) {
            salarySlider.noUiSlider.destroy();
        }

        noUiSlider.create(salarySlider, {
            start: [min, max],
            connect: true,
            range: {
                min: 1000,
                max: 100000
            },
            step: 500,
            tooltips: false,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        // Update UI values when slider is moved
        salarySlider.noUiSlider.on("update", function (values) {
            const minVal = values[0];
            const maxVal = values[1];
            document.getElementById("salaryMinValue").value = minVal;
            document.getElementById("salaryMaxValue").value = maxVal;
            document.getElementById("salary_range").textContent = `${minVal} - ${maxVal}`;
            document.getElementById("salary_range_hidden").value = `${minVal}-${maxVal}`;
        });

        // Update slider when input changes
        document.getElementById("salaryMinValue").addEventListener('change', function () {
            salarySlider.noUiSlider.set([this.value, null]);
        });
        
        document.getElementById("salaryMaxValue").addEventListener('change', function () {
            salarySlider.noUiSlider.set([null, this.value]);
        });
    }

    function initializeAgeSlider(min = 18, max = 65) {
        const ageSlider = document.getElementById("ageSlider");
        if (!ageSlider) return;
        // Destroy previous instance if reinitializing
        if (ageSlider.noUiSlider) {
            ageSlider.noUiSlider.destroy();
        }
        noUiSlider.create(ageSlider, {
            start: [min, max],
            connect: true,
            range: {
                min: 18,
                max: 65
            },
            step: 1, // Use a small step for age slider
            tooltips: false,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });
        // Update displayed age range when slider is moved
        ageSlider.noUiSlider.on("update", function (values) {
            document.getElementById("ageMinValue").textContent = values[0];
            document.getElementById("ageMaxValue").textContent = values[1];
            document.getElementById("age").textContent = `${values[0]} - ${values[1]}`;
            document.getElementById("age_range_hidden").value = `${values[0]}-${values[1]}`;
        });
    }

    $(document).ready(function () {
        $("#departmentForm").submit(function (e) {
            e.preventDefault();

            $('#department_name_error').text('');

            let departmentName = $("#department_name").val().trim();

            if (departmentName === "") {
                $('#department_name_error').text('Department Name is required.');
                return;
            }

            <?php if ($isAdminOrHr): ?>
            if (!$("#dept_modal_branch_id").val()) {
                $('#dept_branch_error').text('Please select a branch.');
                return;
            }
            <?php endif; ?>

            // ✅ CSRF
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');

            // ✅ Prepare form data
            let formData = $(this).serializeArray();
            formData.push({
                name: csrfName,
                value: csrfHash
            });

            const jobDeptToken = localStorage.getItem('token');
            let reqHeaders = {};
            if (jobDeptToken && jobDeptToken !== 'null' && jobDeptToken !== 'undefined') {
                reqHeaders['Authorization'] = 'Bearer ' + jobDeptToken;
            }

            $.ajax({
                url: "<?= base_url("api/department/add") ?>",
                type: "POST",
                headers: reqHeaders,
                data: $.param(formData), // serialize + CSRF
                dataType: "json",
                success: function (response) {
                    $('#department_name_error').text('');

                    if (response.success) {
                        let newOption = `<option value="${response.department.id}" selected>${response.department.department_name}</option>`;
                        $("#department_id").append(newOption);
                        $("#department_id_modal").append(newOption);

                        $("#departmentForm")[0].reset();
                        $("#adddepartementModal").modal("hide");

                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Department added successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // ✅ update CSRF hash after success (optional but good practice)
                        if (response.csrf_token) {
                            $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                        }
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message || "Something went wrong.",
                            showConfirmButton: true
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: " + error);
                    let errorMessage = "Something went wrong while adding the department.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: "error",
                        title: "AJAX Error",
                        text: errorMessage,
                        showConfirmButton: true
                    });
                }
            });
        });
    });

    $(document).ready(function () {
        $("#LocationForm").submit(function (e) {
            e.preventDefault(); // Prevent default form submission

            // Clear previous error
            $("#job_location_error").text("");

            const jobLocation = $("#job_location").val().trim();

            // Validation
            if (jobLocation === "") {
                $("#job_location_error").text("Job location is required.");
                return;
            }

            // CSRF token from meta
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');

            // Serialize form and append CSRF manually
            const formData = $(this).serializeArray();
            formData.push({
                name: csrfName,
                value: csrfHash
            });

            $.ajax({
                url: "<?= base_url("api/location/add") ?>",
                type: "POST",
                data: $.param(formData),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        // Append new option to both dropdowns
                        const newOption = `<option value="${response.location.id}" selected>${response.location.job_location}</option>`;
                        $("#locations_id").append(newOption);
                        $("#modal_locations_id").append(newOption);

                        // Reset and close modal
                        $("#LocationForm")[0].reset();
                        $("#addlocationModal").modal("hide");

                        // Show success
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "Location added successfully.",
                            showConfirmButton: false,
                            timer: 2000,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });

                        // ✅ Update CSRF hash for next request
                        if (response.csrf_token) {
                            $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                        }
                    } else {
                        $("#job_location_error").text(response.message || "Something went wrong.");
                    }
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    const message = res?.message || "Server error occurred.";
                    $("#job_location_error").text(message);
                }
            });
        });
    });


    $(document).ready(function () {
        var modalMode = false; // Track if modal is open

        // Track when modal opens
        $('#addAddressModal').on('show.bs.modal', function () {
            modalMode = true; // Modal is open
            var selectedLocation = $('#locations_id').val();
            $('#modal_locations_id').val(selectedLocation);
        });

        // Track when modal closes
        $('#addAddressModal').on('hide.bs.modal', function () {
            modalMode = false; // Modal is closed
        });

        // Load addresses when location changes
        $('#locations_id').on('change', function () {
            var locationId = $(this).val();
            $('#modal_locations_id').val(locationId);

            if (!locationId) {
                $('#addresses_id').empty().append('<option value="">Select Address</option>');
                return;
            }

            var apiUrl = "<?= base_url("api/getAddresses") ?>"; // Default API
            var requestType = "POST"; // Default request type

            $.ajax({
                url: apiUrl,
                type: requestType,
                data: {
                    location_id: locationId
                },
                dataType: "json",
                success: function (response) {
                    $('#addresses_id').empty().append('<option value="">Select Address</option>');

                    if (response.length > 0) {
                        $.each(response, function (index, address) {
                            $('#addresses_id').append('<option value="' + address.address_id + '">' + address.address + '</option>');
                        });

                        // Restore selected address if applicable
                        if (window.savedAddressId) {
                            $('#addresses_id').val(window.savedAddressId);
                            window.savedAddressId = null; // Clear after setting
                        }
                    } else {
                        $('#addresses_id').append('<option value="">No addresses found</option>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching addresses:", xhr.responseText);
                }
            });
        });

        // Submit new address from modal
        $('#addAddressForm').submit(function (e) {
            e.preventDefault();

            let addressname = $("#address").val();
            let statename = $("#state").val();
            let cityName = $("#city_id").val();
            let countryId = $("#country_id").val();
            let postalname = $("#postal_code").val();

            let addressError = $("#address_name_error");
            let stateError = $("#state_name_error");
            let cityError = $("#city_name_error");
            let countryError = $("#country_name_error");
            let postalError = $("#postal_name_error");

            // Reset error messages
            addressError.text("");
            stateError.text("");
            cityError.text("");
            countryError.text("");
            postalError.text("");

            let isValid = true;

            if (addressname === "") {
                addressError.text("Please enter a address.").css("color", "red");
                isValid = false;
            }
            if (statename === "") {
                stateError.text("Please enter a state.").css("color", "red");
                isValid = false;
            }
            if (cityName === "") {
                cityError.text("Please enter a city name.").css("color", "red");
                isValid = false;
            }
            if (countryId === "") {
                countryError.text("Please select a country.").css("color", "red");
                isValid = false;
            }
            if (postalname === "") {
                postalError.text("Please enter postal code.").css("color", "red");
                isValid = false;
            }

            if (!isValid) return;

            // CSRF token
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');

            // Append CSRF token to serialized data
            let formData = $(this).serializeArray();
            formData.push({
                name: csrfName,
                value: csrfHash
            });

            $.ajax({
                url: "<?= base_url("api/addAddress") ?>",
                type: "POST",
                data: $.param(formData), // serializeArray + CSRF
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Address added successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });

                        $('#addAddressModal').modal('hide');
                        $('#addAddressForm')[0].reset();

                        if (typeof modalMode !== 'undefined' && modalMode) {
                            $('#addresses_id').empty().append('<option value="' + response.data.address_id + '">' + response.data.address + '</option>')
                                .val(response.data.address_id).trigger('change');
                        } else {
                            window.savedAddressId = response.data.address_id;
                            $('#locations_id').trigger('change');
                        }

                        // ✅ Update CSRF hash in meta tag
                        if (response.csrf_token) {
                            $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + response.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function (xhr) {
                    console.error("Error adding address:", xhr.responseText);
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>
