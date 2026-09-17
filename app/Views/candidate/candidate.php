<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
@media (max-width: 767px) {
.addsmbtnres{
    font-size: 10px !important;
    padding: 8px !important;
}
}
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Candidate</h4>
                <form class="form-sample" method="POST" action="" id="candidateForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Candidate Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account-circle fs-5"></i></span>
                                        </div>
                                        <input type="hidden" name="id" id="candidate_id">
                                        <input type="text" class="form-control" name="candidate_name" id="candidate_name" placeholder="Enter your name" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-email fs-5"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Job -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Job</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-office-building fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="job_id" id="job_id">
                                            <option value="" disabled selected>Select job Title</option>
                                            <?php foreach ($jobs as $job): ?>
                                                <option value="<?= $job[
                                                    "id"
                                                ] ?>"><?= $job[
    "job_title"
] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Phone Number</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-phone fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Enter your phone number" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Resume</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resume" name="resume">
                                    <div id="currentResume"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Notes</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <!-- <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div> -->
                                        <textarea class="form-control" name="notes" id="notes" placeholder="Enter Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="text-end">
                        <a href="/candidateview" class="btn hr-btnbg addsmbtnres">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg addsmbtnres" id="submitBtn"><span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>Submit</button>
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
        let candidateId = null; // To store the country ID for updating

        $('#candidateForm').on('submit', function(e) {
            e.preventDefault();
            $('#submitBtn').attr('disabled', true);                // Disable the button
             $('#submitSpinner').removeClass('d-none');
            let formData = new FormData(this);
             const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            formData.append(csrfName, csrfHash);
            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');
            const baseUrl = "<?= base_url() ?>"; // This will generate the base URL dynamically from PHP

            const url = candidateId ? `${baseUrl}api/candidate/${candidateId}` : `${baseUrl}api/candidate`;
            const method = isEditMode ? 'POST' : 'POST'; // Method for both actions
            // $('#loader').show();

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
                    // $('#loader').hide(); // Hide loader

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
                            window.location.href = "/candidateview";
                        });

                        // Reset form
                        $('#candidateForm')[0].reset();
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
                    // $('#loader').hide(); // Hide loader on error

                    let errors = xhr.responseJSON.errors;

                    if (typeof displayValidationErrors === 'function') displayValidationErrors(errors);
                },
                  complete: function() {
            $('#submitSpinner').addClass('d-none');         // Hide spinner
            $('#submitBtn').attr('disabled', false);        // Re-enable button
        }
            });

        });
        // const params = new URLSearchParams(window.location.search);
        // let Id = params.get('id');



        //     if (!Id) {
        //     const pathParts = window.location.pathname.split('/');
        //     var temp_id = pathParts[pathParts.length - 1];
        //     if (!isNaN(temp_id) && !isNaN(parseFloat(temp_id))) {
        //         Id = temp_id;
        //     }
        // }
        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        // If not found, try extracting from the URL path
        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            var temp_id = pathParts[pathParts.length - 1];
            if (!isNaN(temp_id) && !isNaN(parseFloat(temp_id))) {
                Id = temp_id;
            }
        }

        if (Id) {
            candidateId = Id;
            fetchUserData(Id);
        }

        function fetchUserData(Id) {
            // alert("hi..");
            $.ajax({
                url: `/api/candidateedit/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,

                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const candidate = responseData.data;
                        $('#candidate_name').val(candidate.candidate_name);
                        $('#email').val(candidate.email);
                        $('#job_id').val(candidate.job_id);
                        $('#notes').val(candidate.notes);
                        $('#phone_number').val(candidate.phone_number);
                        $('#status').val(candidate.status);
                        // $('#notes').val(candidate.notes);

                        $('#id').val(candidate.id); //Set the hidden ID field for updating
                        if (candidate.resume) {
                            // Check if the resume exists, and if it does, display it with a clickable link
                            $('#currentResume').html(
                                `<a href="${candidate.resume}" target="_blank"><p style="color:#E66136; list-style:none;">View Current Resume</p></a>`
                            );
                        } else {
                            // If there's no resume, display a message saying no resume is uploaded
                            $('#currentResume').html('<p class="text-muted">No resume uploaded</p>');
                        }

                        $('#submitBtn').text('Update'); // Change button text to "Update"
                        $('.card-title').text('Edit Candidate');
                        candidateId = candidate.id; // Set the department ID for future reference
                        isEditMode = true; // Set edit mode flag
                    } else {
                        $('#responseMessage').html('<p class="text-danger">cnadidate not found.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching country:', error);
                    $('#responseMessage').html('<p class="text-danger">Error fetching cnadidate.</p>');
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
</script>

<?= $this->endSection() ?>
