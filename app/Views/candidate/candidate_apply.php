<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HR Portal</title>
    <?= $this->include('dashboard/header_link'); ?>
    <link rel="stylesheet" href="<?= base_url(env('ImagePath') . 'assets/css/login.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="with-welcome-text">
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
                                                <?php foreach ($jobs as $job) : ?>
                                                    <option value="<?= $job['id']; ?>"><?= $job['job_title']; ?></option>
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
                        </div>

                        <div class="text-end">
                            <a href="/candidateview" class="btn hr-btnbg">
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
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Flag to track whether we're in edit mode
        let candidateId = null; // To store the country ID for updating

        $('#candidateForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            const url = isEditMode ? `/api/candidate/${candidateId}` : '/api/candidate';
            const method = isEditMode ? 'POST' : 'POST'; // Method for both actions
            $('#loader').show();

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
                    $('#loader').hide();

                    let errors = xhr.responseJSON.errors;

                    if (typeof displayValidationErrors === 'function') displayValidationErrors(errors);
                }
            });

        });

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
    });
</script>
<?= $this->include('dashboard/footer_link.php'); ?>
</body>

</html>