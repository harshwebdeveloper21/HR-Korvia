<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .iv-form-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }
    .iv-form-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }
    .iv-form-header h3 {
        font-size: 22px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }
    .iv-form-header p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }
    .iv-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 18px 20px 6px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .iv-section-title {
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
    .iv-section + .iv-section { margin-top: 6px; }
    #interviewForm .form-label {
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }
    #interviewForm .form-label .req { color: #dc3545; }
    #interviewForm .form-control,
    #interviewForm .form-select {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 7px 10px;
        font-size: 13.5px;
        color: #111827;
        min-height: 38px;
    }
    #interviewForm textarea.form-control { min-height: 38px; }
    #interviewForm .form-control:focus,
    #interviewForm .form-select:focus {
        border-color: var(--hr-primary, #e66136);
        box-shadow: 0 0 0 2px rgba(var(--hr-primary-rgb, 230, 97, 54), .2);
    }
    #interviewForm .mb-3 { margin-bottom: 12px !important; }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 3px;
    }
    .is-invalid { border-color: #dc3545 !important; }
    .iv-autofill {
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .04);
        border: 1px dashed rgba(var(--hr-primary-rgb, 230, 97, 54), .35);
        border-radius: 6px;
        padding: 10px 12px 0;
        margin-bottom: 14px;
    }
    .iv-autofill-hint {
        display: flex;
        gap: 6px;
        align-items: flex-start;
        font-size: 12.5px;
        color: #4b5563;
        margin-top: 18px;
    }
    .iv-autofill-hint i {
        color: var(--hr-primary, #e66136);
        font-size: 16px;
        line-height: 1;
    }
    @media (max-width: 767px) {
        .iv-autofill-hint { margin-top: 0; }
    }
    .iv-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="iv-form-container">
            <div class="iv-form-header">
                <div>
                    <h3 id="page-main-title">Interview Information Form</h3>
                    <p>Fill in the candidate's interview details below.</p>
                </div>
            </div>

            <form class="form-sample" method="POST" action="" id="interviewForm" novalidate>
                <input type="hidden" id="id" name="id" value="">
                <input type="hidden" id="candidate_id" name="candidate_id" value="">
                <input type="hidden" id="job_id" name="job_id" value="">

                <div class="iv-card">
                    <div class="iv-autofill" id="candidate-autofill-row">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="auto_fill_candidate">Candidate</label>
                                <select class="form-select" id="auto_fill_candidate">
                                    <option value="">+ New Candidate (add now)</option>
                                    <?php if (isset($candidates)): foreach ($candidates as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= esc($c['candidate_name']) ?> (<?= esc($c['email']) ?>)</option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-8 mb-3">
                                <div class="iv-autofill-hint" id="candidateHint">
                                    <i class="mdi mdi-information-outline"></i>
                                    <span>Fill the details below and a new candidate will be added automatically when you submit. Pick an existing candidate to auto-fill their details instead.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Position Details -->
                    <div class="iv-section">
                        <div class="iv-section-title"><i class="mdi mdi-briefcase-outline"></i> Position Details</div>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="position_applied_for">Position Applied For <span class="req">*</span></label>
                                <select class="form-select" id="position_select">
                                    <option value="">Select Job</option>
                                    <?php if (isset($jobs)): foreach ($jobs as $j): ?>
                                        <option value="<?= $j['id'] ?>"><?= esc($j['job_title']) ?></option>
                                    <?php endforeach; endif; ?>
                                    <option value="other">Other (type manually)</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none" name="position_applied_for" id="position_applied_for" placeholder="Type position name" autocomplete="off" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="schedule_date">Date of Interview <span class="req">*</span></label>
                                <input type="datetime-local" class="form-control" name="schedule_date" id="schedule_date" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="department_id">Department</label>
                                <select class="form-select" name="department_id" id="department_id">
                                    <option value="">Select Department</option>
                                    <?php if (!empty($departments)): foreach ($departments as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= esc($d['department_name']) ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="interviewer_id">Interviewer Name</label>
                                <select class="form-select" name="interviewer_id" id="interviewer_id">
                                    <option value="">Select Interviewer</option>
                                    <?php foreach ($interviewers as $interviewer): ?>
                                        <option value="<?= $interviewer['id'] ?>"><?= esc($interviewer['username'] ?? trim(($interviewer['first_name'] ?? '') . ' ' . ($interviewer['last_name'] ?? ''))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="source_of_application">Source of Application</label>
                                <input type="text" class="form-control" name="source_of_application" id="source_of_application" list="sourceList" placeholder="e.g. Job Portal, Referral" autocomplete="off" />
                                <datalist id="sourceList">
                                    <option value="Job Portal"></option>
                                    <option value="LinkedIn"></option>
                                    <option value="Company Website"></option>
                                    <option value="Employee Referral"></option>
                                    <option value="Consultancy"></option>
                                    <option value="Walk-in"></option>
                                    <option value="Social Media"></option>
                                    <option value="Campus"></option>
                                </datalist>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="interview_round">Interview Round</label>
                                <input type="text" class="form-control" name="interview_round" id="interview_round" list="roundList" placeholder="e.g. 1st Round" autocomplete="off" />
                                <datalist id="roundList">
                                    <option value="1st Round"></option>
                                    <option value="2nd Round"></option>
                                    <option value="Technical Round"></option>
                                    <option value="HR Round"></option>
                                    <option value="Final Round"></option>
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Candidate Details -->
                    <div class="iv-section">
                        <div class="iv-section-title"><i class="mdi mdi-account-outline"></i> Candidate Details</div>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="full_name">Candidate Name <span class="req">*</span></label>
                                <input type="text" class="form-control" name="full_name" id="full_name" placeholder="e.g. John Doe" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="mobile_number">Contact Number <span class="req">*</span></label>
                                <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="10 digit number" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="email">Email Address <span class="req">*</span></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="e.g. email@example.com" />
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="current_address">Current Address</label>
                                <textarea class="form-control" name="current_address" id="current_address" placeholder="Enter current address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Experience -->
                    <div class="iv-section">
                        <div class="iv-section-title"><i class="mdi mdi-school-outline"></i> Education &amp; Experience</div>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="highest_qualification">Highest Qualification</label>
                                <input type="text" class="form-control" name="highest_qualification" id="highest_qualification" placeholder="e.g. B.Com, MBA" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="college_university">Institute / University</label>
                                <input type="text" class="form-control" name="college_university" id="college_university" placeholder="Institute / University name" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="total_experience">Total Experience</label>
                                <input type="text" class="form-control" name="total_experience" id="total_experience" placeholder="e.g. 3 Years 2 Months" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="relevant_experience">Relevant Experience</label>
                                <input type="text" class="form-control" name="relevant_experience" id="relevant_experience" placeholder="e.g. 2 Years" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="previous_company">Current / Last Employer</label>
                                <input type="text" class="form-control" name="previous_company" id="previous_company" placeholder="Company name" />
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="form-label" for="previous_job_title">Current Designation</label>
                                <input type="text" class="form-control" name="previous_job_title" id="previous_job_title" placeholder="e.g. Sales Executive" />
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="technical_skills">Key Skills / Areas of Expertise</label>
                                <textarea class="form-control" name="technical_skills" id="technical_skills" placeholder="e.g. Tally, Excel, Client Handling" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Compensation & Availability -->
                    <div class="iv-section">
                        <div class="iv-section-title"><i class="mdi mdi-cash-multiple"></i> Compensation &amp; Availability</div>
                        <div class="row">
                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label" for="previous_salary">Current CTC</label>
                                <input type="text" class="form-control" name="previous_salary" id="previous_salary" placeholder="e.g. 4,80,000" />
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label" for="expected_salary">Expected CTC</label>
                                <input type="text" class="form-control" name="expected_salary" id="expected_salary" placeholder="e.g. 6,00,000" />
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label" for="notice_period">Notice Period</label>
                                <input type="text" class="form-control" name="notice_period" id="notice_period" placeholder="e.g. 30 Days" />
                            </div>
                            <div class="col-md-6 col-lg-3 mb-3">
                                <label class="form-label" for="joining_date">Earliest Joining Date</label>
                                <input type="date" class="form-control" name="joining_date" id="joining_date" />
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="reason_for_leaving">Reason for Change / Leaving Current Role</label>
                                <textarea class="form-control" name="reason_for_leaving" id="reason_for_leaving" placeholder="Reason for change" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="iv-footer">
                    <a href="<?= base_url("/addinterview") ?>" class="btn btn-light" id="cancelBtn">Cancel</a>
                    <button type="submit" class="btn hr-btnbg" id="submitBtn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span><span id="submitLabel">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const candidatesData = <?= isset($candidates) ? json_encode($candidates) : '[]' ?>;
    const jobsData = <?= isset($jobs) ? json_encode($jobs) : '[]' ?>;

    const requirements = [
        { id: 'schedule_date', message: 'Date of interview is required' },
        { id: 'full_name', message: 'Candidate name is required' },
        { id: 'mobile_number', message: 'Contact number is required', pattern: /^\d{10}$/, patternMessage: 'Contact number must be 10 digits' },
        { id: 'email', message: 'Email address is required', type: 'email' }
    ];

    function showError(field, message) {
        field.addClass('is-invalid');
        if (field.next('.invalid-feedback').length === 0) {
            field.after('<div class="invalid-feedback">' + message + '</div>');
        } else {
            field.next('.invalid-feedback').text(message);
        }
    }

    function clearErrors() {
        $('#interviewForm .invalid-feedback').remove();
        $('#interviewForm .is-invalid').removeClass('is-invalid');
    }

    function scrollToFirstError() {
        const firstErr = $('#interviewForm .is-invalid').first();
        if (firstErr.length) {
            $('html, body').animate({ scrollTop: firstErr.offset().top - 120 }, 200);
            firstErr.trigger('focus');
        }
    }

    $('#interviewForm').on('input change', 'input, select, textarea', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    function validateForm() {
        let isValid = true;
        const positionChoice = $('#position_select').val();
        if (!positionChoice) {
            isValid = false;
            showError($('#position_select'), 'Position applied for is required');
        } else if (positionChoice === 'other' && !($('#position_applied_for').val() || '').trim()) {
            isValid = false;
            showError($('#position_applied_for'), 'Please type the position name');
        }
        requirements.forEach(function(req) {
            const field = $('#' + req.id);
            const val = (field.val() || '').trim();
            let errorMsg = '';

            if (!val) {
                errorMsg = req.message;
            } else if (req.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                errorMsg = 'Please enter a valid email address';
            } else if (req.pattern && !req.pattern.test(val)) {
                errorMsg = req.patternMessage || 'Invalid format';
            }

            if (errorMsg) {
                isValid = false;
                showError(field, errorMsg);
            }
        });
        return isValid;
    }

    function toDateTimeLocal(value) {
        if (!value) return '';
        const v = String(value).replace(' ', 'T');
        return v.length >= 16 ? v.substring(0, 16) : v;
    }

    function applyJob(jobId) {
        const job = jobsData.find(j => j.id == jobId);
        if (!job) return;
        $('#position_select').val(job.id);
        $('#position_applied_for').val(job.job_title).addClass('d-none');
        $('#job_id').val(job.id);
        if (job.department_id) {
            $('#department_id').val(job.department_id).trigger('change');
        }
    }

    function setManualPosition(title) {
        $('#position_select').val('other');
        $('#position_applied_for').val(title || '').removeClass('d-none');
        $('#job_id').val('');
    }

    $('#position_select').on('change', function() {
        const val = $(this).val();
        if (val === 'other') {
            setManualPosition('');
            $('#position_applied_for').trigger('focus');
        } else if (val) {
            applyJob(val);
        } else {
            $('#position_applied_for').val('').addClass('d-none');
            $('#job_id').val('');
        }
    });

    $('#auto_fill_candidate').on('change', function() {
        const id = $(this).val();
        $('#candidate_id').val(id);
        $('#candidateHint span').text(id
            ? 'Details auto-filled from the candidate record. Any changes you make here will also update that candidate.'
            : 'Fill the details below and a new candidate will be added automatically when you submit. Pick an existing candidate to auto-fill their details instead.');
        if (!id) {
            $('#full_name, #email, #mobile_number, #current_address').val('');
            return;
        }
        const candidate = candidatesData.find(c => c.id == id);
        if (candidate) {
            $('#full_name').val(candidate.candidate_name);
            $('#email').val(candidate.email);
            $('#mobile_number').val(candidate.phone_number);
            $('#current_address').val(candidate.current_address || candidate.address || '');
            if (candidate.job_id) {
                applyJob(candidate.job_id);
            }
        }
    });

    // Edit mode: detect ID in URL
    let isEditMode = false;
    let interviewId = null;

    const params = new URLSearchParams(window.location.search);
    let id = params.get('id');
    if (!id) {
        const pathParts = window.location.pathname.split('/');
        const last = pathParts[pathParts.length - 1];
        if (last && !isNaN(last)) id = last;
    }

    if (id) {
        isEditMode = true;
        interviewId = id;
        $('#submitLabel').text('Update');
        $('#page-main-title').text('Edit Interview Information');
        $('#candidate-autofill-row').hide();

        $.ajax({
            url: '/api/interviews/' + id,
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                if (response.status !== 'success') return;
                const data = response.data;

                for (const key in data) {
                    if (key === 'educations' || key === 'experiences' || key === 'rounds') continue;
                    const field = $('#interviewForm #' + key);
                    if (field.length && data[key] !== null) {
                        field.val(data[key]);
                    }
                }

                $('#schedule_date').val(toDateTimeLocal(data.schedule_date));

                const savedDepartment = data.department_id;
                const savedTitle = (data.position_applied_for || '').trim().toLowerCase();
                const matchedJob = jobsData.find(j => j.id == data.job_id)
                    || jobsData.find(j => (j.job_title || '').trim().toLowerCase() === savedTitle && savedTitle);
                if (matchedJob) {
                    applyJob(matchedJob.id);
                    if (savedDepartment) $('#department_id').val(savedDepartment);
                } else if (data.position_applied_for) {
                    setManualPosition(data.position_applied_for);
                }

                const firstEdu = (data.educations || [])[0];
                if (firstEdu) {
                    if (!data.highest_qualification) $('#highest_qualification').val(firstEdu.degree || '');
                    if (!data.college_university) $('#college_university').val(firstEdu.university || '');
                }

                const firstExp = (data.experiences || [])[0];
                if (firstExp) {
                    if (!data.previous_company) $('#previous_company').val(firstExp.company || '');
                    if (!data.previous_job_title) $('#previous_job_title').val(firstExp.role || '');
                    if (!data.total_experience) $('#total_experience').val(firstExp.total_experience || '');
                    if (!data.previous_salary) $('#previous_salary').val(firstExp.last_salary || '');
                    if (!data.notice_period) $('#notice_period').val(firstExp.notice_period || '');
                    if (!data.reason_for_leaving) $('#reason_for_leaving').val(firstExp.reason_for_leaving || '');
                }

                const firstRound = (data.rounds || [])[0];
                if (firstRound) {
                    if (!data.interviewer_id) $('#interviewer_id').val(firstRound.interviewer_id || '');
                    if (!data.interview_round) $('#interview_round').val(firstRound.interview_round || '');
                }
            }
        });
    }

    $('#interviewForm').on('submit', function(e) {
        e.preventDefault();
        clearErrors();

        if (!validateForm()) {
            scrollToFirstError();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                customClass: { confirmButton: 'hr-btnbg' }
            });
            return false;
        }

        $('#submitBtn').attr('disabled', true);
        $('#submitSpinner').removeClass('d-none');

        const formData = new FormData(this);
        const csrfName = $('meta[name="csrf-token"]').attr('data-name');
        const csrfHash = $('meta[name="csrf-token"]').attr('content');
        if (csrfName && csrfHash) formData.append(csrfName, csrfHash);

        const url = isEditMode ? '/api/interviews/' + interviewId : '/api/interviews';

        $.ajax({
            url: url,
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Saved successfully',
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    window.location.href = '/addinterview';
                });
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    for (const field in response.errors) {
                        showError($('#' + field), response.errors[field]);
                    }
                    scrollToFirstError();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fix the highlighted errors.',
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: (response && response.message) ? response.message : 'Something went wrong!',
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                }
            },
            complete: function() {
                $('#submitSpinner').addClass('d-none');
                $('#submitBtn').attr('disabled', false);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
