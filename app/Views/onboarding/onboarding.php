<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }
    }

    /* Candidate Select2 + Quick Add Button Group */
    .candidate-input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: stretch !important;
        width: 100% !important;
    }
    .candidate-input-group .input-group-prepend {
        display: flex;
    }
    .candidate-input-group .input-group-text {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-right: none;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
    }
    .candidate-input-group .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }
    .candidate-input-group .select2-container .select2-selection--single {
        height: 100% !important;
        min-height: 42px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fff !important;
    }
    .candidate-input-group .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 10px !important;
        padding-right: 25px !important;
        color: #495057 !important;
        font-size: 13px !important;
    }
    .candidate-input-group .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        right: 8px !important;
    }
    .candidate-input-group .select2-container--default.select2-container--focus .select2-selection--single,
    .candidate-input-group .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #E66136 !important;
        box-shadow: 0 0 0 0.2rem rgba(230, 97, 54, 0.2) !important;
    }
    .candidate-input-group .quick-add-btn {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
        background-color: #E66136 !important;
        border: 1px solid #E66136 !important;
        color: #fff !important;
        padding: 0 14px !important;
        margin: 0 !important;
        height: 100% !important;
        min-height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        z-index: 4;
    }
    .candidate-input-group .quick-add-btn:hover {
        background-color: #cf4f27 !important;
        border-color: #cf4f27 !important;
        color: #fff !important;
    }
    .candidate-input-group .quick-add-btn i,
    .candidate-input-group .quick-add-btn .mdi,
    .candidate-input-group .quick-add-btn:hover i,
    .candidate-input-group .quick-add-btn:focus i {
        color: #ffffff !important;
        fill: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }
    .select2-dropdown {
        border: 1px solid #E66136 !important;
        border-radius: 6px !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.12) !important;
        z-index: 1055 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #E66136 !important;
        color: #fff !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 6px 10px !important;
        outline: none !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #E66136 !important;
    }

    /* Modal Styling */
    #quickAddCandidateModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    #quickAddCandidateModal .modal-header {
        background: linear-gradient(135deg, #E66136 0%, #f07b54 100%);
        color: #fff;
        padding: 16px 24px;
        border-bottom: none;
    }
    #quickAddCandidateModal .modal-body {
        padding: 24px;
        background: #ffffff;
    }
    #quickAddCandidateModal .modal-footer {
        padding: 14px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    #quickAddCandidateModal .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #344767;
        margin-bottom: 5px;
    }
    #quickAddCandidateModal .input-group-text {
        background-color: #fff;
        border: 1px solid #ced4da;
        border-right: none;
        color: #E66136;
    }
    #quickAddCandidateModal .form-control:focus,
    #quickAddCandidateModal .form-select:focus {
        border-color: #E66136 !important;
        box-shadow: 0 0 0 0.2rem rgba(230, 97, 54, 0.2) !important;
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
                                <label class="col-sm-3 col-form-label">Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group candidate-input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <select class="form-select select2" name="candidate_id" id="candidate" style="width: 100%;" onchange="fetchJobId()">
                                            <option value="" disabled selected>Select candidate name</option>
                                            <?php foreach ($candidates as $candidate): ?>
                                                <option value="<?= $candidate["id"] ?>">
                                                    <?= esc($candidate["candidate_name"]) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn hr-btnbg quick-add-btn" id="btnQuickAddCandidate" title="Quick Add New Candidate">
                                            <i class="mdi mdi-plus fs-4 text-white" style="color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; font-size: 22px !important; font-weight: bold; line-height: 1;"></i>
                                        </button>
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
                                            placeholder="Enter Interview Title" readonly />
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

<!-- Quick Add Candidate Modal -->
<div class="modal fade" id="quickAddCandidateModal" tabindex="-1" aria-labelledby="quickAddCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold d-flex align-items-center mb-0" id="quickAddCandidateModalLabel">
                    <i class="mdi mdi-account-plus me-2 fs-4"></i> Quick Add Candidate
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickCandidateForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center" style="font-size: 13px; border-left: 4px solid #E66136; background-color: #fff6f3; color: #8a3b14;">
                        <i class="mdi mdi-information-outline fs-5 me-2"></i>
                        <span>Register a candidate quickly to instantly generate their joining letter & onboarding details.</span>
                    </div>
                    <div class="row g-3">
                        <!-- Candidate Name -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_candidate_name">Candidate Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-account"></i></span>
                                <input type="text" class="form-control" name="candidate_name" id="quick_candidate_name" placeholder="Enter Full Name" required />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_email">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-email"></i></span>
                                <input type="email" class="form-control" name="email" id="quick_email" placeholder="name@example.com" required />
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_phone_number">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-phone"></i></span>
                                <input type="tel" class="form-control" name="phone_number" id="quick_phone_number" placeholder="10-digit mobile number" maxlength="10" required />
                            </div>
                        </div>

                        <!-- Job Position -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_job_id">Job / Position <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-briefcase"></i></span>
                                <select class="form-select" name="job_id" id="quick_job_id" required>
                                    <option value="" disabled selected>Select Job Position</option>
                                    <?php foreach ($jobs as $job): ?>
                                        <option value="<?= $job['id'] ?>"><?= esc($job['job_title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Resume (Optional) -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_resume">Resume <small class="text-muted">(Optional - PDF, DOC, DOCX up to 2MB)</small></label>
                            <input type="file" class="form-control" name="resume" id="quick_resume" accept=".pdf,.doc,.docx" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="col-md-6">
                            <label class="form-label" for="quick_notes">Notes <small class="text-muted">(Optional)</small></label>
                            <textarea class="form-control" name="notes" id="quick_notes" rows="1" placeholder="Initial interview notes / comments..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn hr-btnbg" id="btnSaveQuickCandidate">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="quickCandidateSpinner" role="status" aria-hidden="true"></span>
                        <i class="mdi mdi-plus-circle me-1" id="quickCandidateIcon"></i> Save & Select
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function fetchJobId() {
        let candidateId = document.getElementById("candidate").value;
        $('#job_title').prop('disabled', true);
        if (candidateId) {
            fetch(`<?= base_url("api/get-candidate-job/") ?>${candidateId}`)
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

    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Flag to track whether we're in edit mode
        let onboardingId = null; // To store the onboarding ID for updating

        // Initialize Select2 on candidate select
        $('#candidate').select2({
            placeholder: "Select candidate name",
            allowClear: true,
            width: '100%'
        });

        $('#candidate').on('change', function() {
            fetchJobId();
        });

        // Quick Add Candidate Button Handler
        $('#btnQuickAddCandidate').on('click', function() {
            $('#quickCandidateForm')[0].reset();
            $('#quickCandidateForm').find('.is-invalid').removeClass('is-invalid');
            $('#quickCandidateForm').find('.invalid-feedback').remove();
            $('#quickAddCandidateModal').modal('show');
        });

        // Quick Add Candidate Form Submission
        $('#quickCandidateForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSaveQuickCandidate');
            const spinner = $('#quickCandidateSpinner');
            const icon = $('#quickCandidateIcon');

            const phone = $('#quick_phone_number').val().trim();
            if (!/^\d{10}$/.test(phone)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Phone Number',
                    text: 'Please enter a valid 10-digit phone number.',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'hr-btnbg' }
                });
                return;
            }

            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');

            let formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            if (csrfName && csrfHash) {
                formData.append(csrfName, csrfHash);
            }

            $.ajax({
                url: '<?= base_url("api/candidate") ?>',
                type: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#quickAddCandidateModal').modal('hide');
                        const candidateData = response.data || {};
                        const newId = candidateData.id || '';
                        const newName = candidateData.candidate_name || $('#quick_candidate_name').val();
                        const selectedJobTitle = $('#quick_job_id option:selected').text().trim();

                        if (newId) {
                            if ($(`#candidate option[value="${newId}"]`).length === 0) {
                                const newOption = new Option(newName, newId, true, true);
                                $('#candidate').append(newOption).trigger('change');
                            } else {
                                $('#candidate').val(newId).trigger('change');
                            }
                        }

                        if (selectedJobTitle) {
                            $('#job_title').val(selectedJobTitle);
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Candidate Created!',
                            text: `${newName} has been added and selected.`,
                            timer: 2200,
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });

                        $('#department_id').focus();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to create candidate',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorHtml = '<ul class="text-start mb-0" style="font-size:13px;">';
                        $.each(xhr.responseJSON.errors, function(k, v) {
                            errorHtml += `<li>${v}</li>`;
                            let field = $(`#quickCandidateForm [name="${k}"]`);
                            field.addClass('is-invalid');
                        });
                        errorHtml += '</ul>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Failed',
                            html: errorHtml,
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong while adding the candidate.',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    }
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });

        $('#onbordingForm').on('submit', function(e) {
            e.preventDefault();

            $('#submitBtn').attr('disabled', true);
            $('#submitSpinner').removeClass('d-none');
            let formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            formData.append(csrfName, csrfHash);

            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            const url = isEditMode ? `/api/onboarding/${onboardingId}` : '/api/onboarding';
            const method = isEditMode ? 'POST' : 'POST';

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
                            window.location.href = "/onboardingview";
                        });

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
                    $('#submitSpinner').addClass('d-none');
                    $('#submitBtn').attr('disabled', false);
                }
            });
        });

        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        if (!Id) {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const lastPart = pathParts[pathParts.length - 1];
            if (lastPart && !isNaN(lastPart)) {
                Id = lastPart;
            }
        }

        if (Id && !isNaN(Id)) {
            fetchUserData(Id);
        }

        const templateIdParam = params.get('template_id');
        if (templateIdParam) {
            $('#offer_later_id').val(templateIdParam).trigger('change');
        }

        function fetchUserData(Id) {
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
                        $('#candidate').val(onboarding.candidate_id).trigger('change');
                        $('#department_id').val(onboarding.department_id);
                        $('#offer_later_id').val(onboarding.offer_later_id).trigger('change');
                        $('#job_title').val(onboarding.job_title);
                        $('#start_date').val(onboarding.start_date);
                        $('#onboarding_status').val(onboarding.onboarding_status);
                        $('#docu_submitted').val(onboarding.docu_submitted);
                        $('#id').val(onboarding.id);

                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit Onboarding');
                        onboardingId = onboarding.id;
                        isEditMode = true;
                    } else {
                        $('#responseMessage').html('<p class="text-danger">Onboarding not found.</p>');
                    }
                }
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
