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
                <h4 class="card-title">Employee Onboarding</h4>
                <form class="form-sample" method="POST" action="" id="onbordingForm">
                    <!-- Personal Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="candidate_id" id="candidate"
                                            onchange="fetchJobId()">
                                            <option value="" disabled selected>Select candidate name</option>
                                            <?php foreach (
                                                $candidates
                                                as $candidate
                                            ): ?>
                                                <option value="<?= $candidate[
                                                    "id"
                                                ] ?>">
                                                    <?= $candidate[
                                                        "candidate_name"
                                                    ] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job Title</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-briefcase fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="job_title" id="job_title"
                                            placeholder="Enter Interview Title" />
                                        <script>
                                            function fetchJobId() {
                                                let candidateId = document.getElementById("candidate").value;
                                                console.log(candidateId);
                                                $('#job_title').prop('disabled', true);
                                                if (candidateId) {
                                                    fetch(`<?= base_url(
                                                        "api/get-candidate-job/",
                                                    ) ?>${candidateId}`)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.status === 'success') {
                                                                document.getElementById("job_title").value = data.job_title;
                                                            } else {
                                                                document.getElementById("job_title").value = "Not Found";
                                                            }
                                                        })
                                                        .catch(error => console.error('Error:', error));
                                                } else {
                                                    document.getElementById("job_title").value = "";
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Department</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-office-building fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="department_id" id="department_id">
                                            <option value="" disabled selected>Select department Title</option>
                                            <?php foreach (
                                                $departments
                                                as $department
                                            ): ?>
                                                <option value="<?= $department[
                                                    "id"
                                                ] ?>">
                                                    <?= $department[
                                                        "department_name"
                                                    ] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Start Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="start_date" id="start_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Start Date -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Onboarding Status</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-checkbox-marked-circle-outline fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="onboarding_status" id="onboarding_status">
                                            <option value="" disabled selected>Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Documents Submitted</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="docu_submitted" id="docu_submitted" rows="3" placeholder="List documents submitted..."></textarea>
                                    <!-- <div class="invalid-feedback" id="docu_error" style="display:none;">
                                        Please provide details of the documents submitted.
                                    </div> -->
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Offer Letter Template</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-file-document fs-5"></i>
                                            </span>
                                        </div>
                                        <select class="form-select" name="offer_later_id" id="offer_later_id">
                                            <option value="" disabled selected>Select Offer Letter Template</option>
                                            <?php foreach (
                                                $offerLetters
                                                as $letter
                                            ): ?>
                                                <option value="<?= $letter[
                                                    "id"
                                                ] ?>"><?= esc(
    $letter["title"],
) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-6">
                              <div class="col-sm-3">
                                 <div class="text-center" id="template-preview">
                            <img id="template-image" src="" alt="Offer Template Preview" class="img-fluid" style="max-height: 50px; display: none; border: 1px solid #ccc; padding: 5px;">
                        </div>
                              </div>
                          </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end mt-4">
                        <a href="<?= base_url(
                            "/onboardingview",
                        ) ?>" class="btn hr-btnbg interviewsmbtn">Back</a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn"> <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>Submit</button>
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
        let onboardingId = null; // To store the country ID for updating

        $('#onbordingForm').on('submit', function(e) {
            e.preventDefault();

             $('#submitBtn').attr('disabled', true);                // Disable the button
             $('#submitSpinner').removeClass('d-none');
            let formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
const csrfHash = $('meta[name="csrf-token"]').attr('content');
formData.append(csrfName, csrfHash); // ✅ Add CSRF to FormData
            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            const url = isEditMode ? `/api/onboarding/${onboardingId}` : '/api/onboarding';
            const method = isEditMode ? 'POST' : 'POST'; // Method for both actions

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {


                    if (response.status === 'success') {
                        let fullMessage = response.message;

                        if (response.employeeNote) {
                            fullMessage += '\n\n' + response.employeeNote;
                        }
                        // Show success alert using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: fullMessage,
                            confirmButtonText: 'OK',
                            showConfirmButton: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        }).then(() => {
                            // Redirect to candidate view page after the alert
                            window.location.href = "/onboardingview";
                        });

                        // Reset form
                        $('#onbordingForm')[0].reset();
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

                    if (xhr.responseJSON && xhr.responseJSON.errors) {


                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {
                            let inputField = $(`[name="${key}"]`);
                            if (inputField.length) {
                                inputField.addClass('is-invalid');

                                // Remove existing error message before adding a new one
                                inputField.next('.invalid-feedback').remove();

                                inputField.after(`<div class="invalid-feedback">${value}</div>`);
                            }
                        });

                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong! Please try again.',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                 complete: function() {
            $('#submitSpinner').addClass('d-none');         // Hide spinner
            $('#submitBtn').attr('disabled', false);        // Re-enable button
        }

            });

        });

        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        // If not found, try extracting from the URL path
        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            Id = pathParts[pathParts.length - 1];
        }

        if (Id) {
            fetchUserData(Id);
        }

        function fetchUserData(Id) {
            // alert("hi..");
            $.ajax({
                url: `<?= base_url("/api/onboardingedit/") ?>${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,

                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const onboarding = responseData.data;

                        $('#job_title').prop('disabled', true);
                        $('#candidate').val(onboarding.candidate_id);
                        $('#department_id').val(onboarding.department_id);
                        $('#department_id').val(onboarding.department_id);
                        $('#offer_later_id').val(onboarding.offer_later_id);
                        $('#job_title').val(onboarding.job_title);
                        $('#start_date').val(onboarding.start_date);
                        $('#onboarding_status').val(onboarding.onboarding_status);
                        // $('#bank_name').val(onboarding.bank_name);
                        // $('#acc_number').val(onboarding.acc_number);
                        $('#docu_submitted').val(onboarding.docu_submitted);
                        $('#id').val(onboarding.id); //Set the hidden ID field for updating

                        // Check if the resume exists, and if it does, display it with a clickable link

                        $('#submitBtn').text('Update'); // Change button text to "Update"
                        $('.card-title').text('Edit Department');
                        onboardingId = onboarding.id; // Set the department ID for future reference
                        isEditMode = true; // Set edit mode flag
                    } else {
                        $('#responseMessage').html('<p class="text-danger">onboarding not found.</p>');
                    }
                },
                // error: function(xhr, status, error) {
                //     console.error('Error fetching country:', error);
                //     $('#responseMessage').html('<p class="text-danger">Error fetching onboarding.</p>');
                // }
            });
        }
          $('#offer_later_id').on('change', function () {
            const templateId = $(this).val();

            if (templateId) {
                $.ajax({
                    url: `<?= base_url("api/offer-template/") ?>${templateId}`,
                    type: 'GET',
                    success: function (response) {
                        if (response.status && response.data.template_img) {
                            $('#template-image').attr('src', response.data.template_img).show();
                        } else {
                            $('#template-image').hide();
                        }
                    },
                    error: function () {
                        $('#template-image').hide();
                    }
                });
            } else {
                $('#template-image').hide();
            }
        });
    });
</script>

<?= $this->endSection() ?>
