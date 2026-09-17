<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
<style>
    /* Custom Styling for the Slider */
    #salarySlider {
        margin-top: 10px;
        height: 4px; /* Adjust thickness */
        background: #E66136; /* Line color */
        border-radius: 3px;
    }
    #ageSlider {
        margin-top: 10px;
        height: 4px; /* Adjust thickness */
        background: #E66136; /* Line color */
        border-radius: 3px;
    }
    .noUi-handle {
        width: 16px !important; 
        height: 16px !important; 
        border-radius: 50%; /* Make handle circular */
        background: #E66136; /* Handle color */
        box-shadow: none;
        border: none;
    }
    .noUi-handle:before, .noUi-handle:after {
        display: none !important;
    }
    .noUi-connect {
     background: #E66136 !important;
    
}
.noUi-target{
    border: none !important;
}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Job</h4>
                <form class="form-sample" method="POST" action="" id="LeaveForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job Title</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-briefcase fs-5"></i></span>
                                        </div>
                                        <input type="hidden" id="id" name="id" value="">
                                        <input type="text" class="form-control" name="job_title" id="job_title" placeholder="Enter Job Title" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Description</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div>
                                        <textarea class="form-control" name="description" id="description" placeholder="Enter Job Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Department</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-office-building fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="department_id" id="department_id">
                                            <option value="" disabled selected>Select department Type</option>
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?= $department['id']; ?>"><?= $department['department_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Gender</label>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <div class="form-check me-3 ms-3">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                                        <label class="form-check-label me-4" for="male">Male</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                        <label class="form-check-label me-4" for="female">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                                        <label class="form-check-label" for="other">Other</label>
                                    </div>
                                    <div class="gender-group"></div>
                                </div>

                            </div>
                        </div>


                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Age</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <!-- <div class="w-100">
                                            <div class="d-flex justify-content-between">
                                                <span>Min: <span id="ageMinValue">18</span></span>
                                                <span>Max: <span id="ageMaxValue">65</span></span>
                                            </div>
                                            <input type="range" id="age_min" name="age_min" class="form-range" min="18" max="65" step="1" value="18" oninput="updateAgeRange()">
                                            <input type="range" id="age_max" name="age_max" class="form-range" min="18" max="65" step="1" value="65" oninput="updateAgeRange()">
                                            <input type="hidden" name="age" id="age_range_hidden">
                                            <div class="text-center mt-2">
                                                Selected Age Range: <span class="badge" style="background-color: #E66136;" id="age">18 - 65</span>
                                            </div>
                                        </div> -->
                                        <div class="w-100 salary-range-container">
      <div class="d-flex justify-content-between">
          <span>Min: <span id="ageMinValue">18</span></span>
          <span>Max: <span id="ageMaxValue">65</span></span>
      </div>

      <!-- Age Range Slider -->
      <div id="ageSlider"></div>

      <input type="hidden" name="age" id="age_range_hidden">

      <div class="text-center mt-2">
          Selected Range: <span class="badge" style="background-color: #E66136;" id="age">18 - 65</span>
      </div>
  </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Salary Range</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <!-- <div class="input-group-prepend">
                    <span class="input-group-text"><i class="mdi mdi-currency-usd fs-5"></i></span>
                </div> -->
                                        <div class="w-100 salary-range-container">
                                            <div class="d-flex justify-content-between">
                                                <span>Min: <span id="salaryMinValue">1000</span></span>
                                                <span>Max: <span id="salaryMaxValue">100000</span></span>
                                            </div>

                                            <!-- Salary Range Slider -->
                                            <div id="salarySlider"></div>

                                            <input type="hidden" name="salary_range" id="salary_range_hidden">

                                            <div class="text-center mt-2">
                                                Selected Range: <span class="badge" style="background-color: #E66136;" id="salary_range">1000 - 100000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job Type</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-briefcase-check fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="job_type" id="job_type">
                                            <option value="" disabled selected>Select Status</option>
                                            <option value="full">full</option>
                                            <option value="part">part</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Experience</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-timer-sand fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="experience" id="experience" placeholder="Enter Experience" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Location</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-map-marker fs-5"></i></span>
                                        </div>
                                        <select id="locations_id" name="locations_id" class="form-select">
                                            <option value="" disabled selected>Select Employee Name</option>
                                            <?php foreach ($locations as $job) : ?>
                                                <option value="<?= $job['location_id']; ?>"><?= esc($job['job_location']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-remove fs-5"></i></span>
                                        </div>

                                        <select id="addresses_id" name="addresses_id" class="form-select">
                                            <option value="">Select Address</option>
                                        </select>

                                        <script>
                                            $(document).ready(function() {
                                                $('#locations_id').change(function() {
                                                    var locationId = $(this).val();

                                                    if (locationId) {
                                                        $.ajax({
                                                            url: "<?= site_url('api/getAddressesByLocation') ?>",
                                                            type: "POST",
                                                            data: {
                                                                location_id: locationId
                                                            },
                                                            dataType: "json",
                                                            success: function(response) {
                                                                $('#addresses_id').empty().append('<option value="">Select Address</option>');
                                                                if (typeof displayValidationErrors === 'function') displayValidationErrors(response);
                // }
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;

                    if (typeof displayValidationErrors === 'function') displayValidationErrors(errors);
                }


            });

        });
        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');
        // console.log(leaveId);
        if (Id) {
            fetchUserData(Id);
        }
        // console.log(leaveId);

        function fetchUserData(Id) {
            // alert("hi..");
            $.ajax({
                url: `/api/jobupdate/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const job = responseData.data;

                        $('#job_title').val(job.job_title);
                        $('#description').val(job.description);
                        $('#department_id').val(job.department_id);
                        $('#status').val(job.status);
                        $('#locations_id').val(job.locations_id);
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

                        $('#salary_min').val(salaryMin);
                        $('#salary_max').val(salaryMax);
                        $('#salaryMinValue').text(salaryMin);
                        $('#salaryMaxValue').text(salaryMax);
                        $('#salary_range').text(`${salaryMin} - ${salaryMax}`);

                        // Set the age range
                        let ageMinMax = job.age.split('-'); // Assuming "18-40"
                        let ageMin = parseInt(ageMinMax[0]);
                        let ageMax = parseInt(ageMinMax[1]);

                        $('#age_min').val(ageMin);
                        $('#age_max').val(ageMax);
                        $('#ageMinValue').text(ageMin);
                        $('#ageMaxValue').text(ageMax);
                        $('#age').text(`${ageMin} - ${ageMax}`);
                        $('#submitBtn').text('Update'); // Change button text to "Update"
                        $('.card-title').text('Edit Department');
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
                error: function(xhr, status, error) {
                    console.error('Error fetching country:', error);
                    $('#responseMessage').html('<p class="text-danger">Error fetching job.</p>');
                }
            });

        }

        // function populateForm(data) {
        //     $("#id").val(data.id);
        //     console.log(data);
        //     $('#job_title').val(data.job_title);
        //                 $('#description').val(data.description);
        //                 $('#department_id').val(data.department_id);
        //                 $('#status').val(data.status);
        //                 $('#location').val(data.location);
        //                 $('#age').val(data.age);
        //                 $('#job_type').val(data.job_type);
        //                 $('#experience').val(data.experience);
        //                 $('#salary_range').val(data.salary_range);
        //                 $('#post_date').val(data.post_date);
        //                 $('#close_date').val(data.close_date); // Populate the form fields
        //              //Set the hidden ID field for updating
        // }


    });

    // function updateAgeRange() {
    //     let minAge = document.getElementById('age_min').value;
    //     let maxAge = document.getElementById('age_max').value;

    //     document.getElementById('ageMinValue').innerText = minAge;
    //     document.getElementById('ageMaxValue').innerText = maxAge;
    //     document.getElementById('age').innerText = minAge + " - " + maxAge;
    //     document.getElementById('age_range_hidden').value = minAge + '-' + maxAge;


    // }

    // function updateSalaryRange() {
    //     let minVal = document.getElementById("salary_min").value;
    //     let maxVal = document.getElementById("salary_max").value;

    //     if (parseInt(minVal) > parseInt(maxVal)) {
    //         document.getElementById("salary_min").value = maxVal;
    //         minVal = maxVal;
    //     }

    //     document.getElementById("salaryMinValue").innerText = minVal;
    //     document.getElementById("salaryMaxValue").innerText = maxVal;
    //     document.getElementById("salary_range").innerText = minVal + " - " + maxVal;

    //     // Update hidden input field
    //     document.getElementById("salary_range_hidden").value = minVal + "-" + maxVal;
    // }
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
        document.getElementById("salary_range").textContent = `${minVal} - ${maxVal}`;
        document.getElementById("salary_range_hidden").value = `${minVal}-${maxVal}`;
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
</script>
<!-- <script>
document.addEventListener("DOMContentLoaded", function () {
    var salarySlider = document.getElementById("salarySlider");

    noUiSlider.create(salarySlider, {
        start: [1000, 100000], // Initial Min & Max
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

    // Update displayed salary range
    salarySlider.noUiSlider.on("update", function (values) {
        document.getElementById("salary_range").textContent = values[0] + " - " + values[1];
        document.getElementById("salary_range_hidden").value = values[0] + "-" + values[1]; // Store value in hidden input
    });
});
</script> -->



<?= $this->endSection(); ?>