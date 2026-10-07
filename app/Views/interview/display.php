<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<style>
    .ivd-wrap {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }
    .ivd-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }
    .ivd-header h3 {
        font-size: 22px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }
    .ivd-header .ivd-sub {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 13px;
        color: #6b7280;
    }
    .ivd-status {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .12);
        color: var(--hr-primary-text, var(--hr-primary, #e66136));
    }
    .ivd-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .ivd-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        padding: 0 16px !important;
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        border-radius: 6px;
        white-space: nowrap;
    }
    .ivd-actions .btn i { font-size: 16px; line-height: 1; }
    .ivd-actions .btn.btn-light {
        background: #fff;
        border: 1px solid #dee2e6;
        color: #344054;
    }
    .ivd-actions .btn.btn-light:hover { background: #f5f6f8; }
    .ivd-actions .btn.hr-btnbg { border: 1px solid var(--hr-primary, #0f3d2e); }
    .ivd-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 18px 20px 8px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .ivd-section-title {
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
        margin: 4px 0 12px;
    }
    .ivd-item {
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #e5e7eb;
    }
    .ivd-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 3px;
    }
    .ivd-value {
        font-size: 14px;
        color: #111827;
        word-break: break-word;
        white-space: pre-line;
        min-height: 20px;
    }
    .ivd-value.is-empty { color: #9ca3af; }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="ivd-wrap">
            <div class="ivd-header">
                <div>
                    <h3>Interview Information</h3>
                    <div class="ivd-sub">
                        <span id="ivdCandidateHeading">Loading...</span>
                        <span class="ivd-status d-none" id="ivdStatus"></span>
                    </div>
                </div>
                <div class="ivd-actions">
                    <a href="#" class="btn hr-btnbg" id="ivdEditBtn"><i class="mdi mdi-pencil me-1"></i> Edit</a>
                    <a href="#" class="btn btn-light" id="ivdPrintBtn" target="_blank" rel="noopener"><i class="mdi mdi-printer me-1"></i> Print</a>
                    <a href="#" class="btn btn-light" id="ivdDownloadBtn"><i class="mdi mdi-download me-1"></i> Download PDF</a>
                    <a href="<?= base_url("/addinterview") ?>" class="btn btn-light"><i class="mdi mdi-arrow-left me-1"></i> Back</a>
                </div>
            </div>

            <div class="ivd-card">
                <div class="ivd-section-title"><i class="mdi mdi-briefcase-outline"></i> Position Details</div>
                <div class="row">
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Position Applied For</div><div class="ivd-value" data-field="job_title"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Date of Interview</div><div class="ivd-value" data-field="interview_datetime"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Department</div><div class="ivd-value" data-field="department_name"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Interviewer Name</div><div class="ivd-value" data-field="interviewer_name"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Source of Application</div><div class="ivd-value" data-field="source_of_application"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Interview Round</div><div class="ivd-value" data-field="interview_round"></div></div>
                </div>

                <div class="ivd-section-title"><i class="mdi mdi-account-outline"></i> Candidate Details</div>
                <div class="row">
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Candidate Name</div><div class="ivd-value" data-field="candidate_name"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Contact Number</div><div class="ivd-value" data-field="mobile_number"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Email Address</div><div class="ivd-value" data-field="email"></div></div>
                    <div class="col-12 ivd-item"><div class="ivd-label">Current Address</div><div class="ivd-value" data-field="current_address"></div></div>
                </div>

                <div class="ivd-section-title"><i class="mdi mdi-school-outline"></i> Education &amp; Experience</div>
                <div class="row">
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Highest Qualification</div><div class="ivd-value" data-field="highest_qualification"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Institute / University</div><div class="ivd-value" data-field="college_university"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Total Experience</div><div class="ivd-value" data-field="total_experience"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Relevant Experience</div><div class="ivd-value" data-field="relevant_experience"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Current / Last Employer</div><div class="ivd-value" data-field="previous_company"></div></div>
                    <div class="col-md-6 col-lg-4 ivd-item"><div class="ivd-label">Current Designation</div><div class="ivd-value" data-field="previous_job_title"></div></div>
                    <div class="col-12 ivd-item"><div class="ivd-label">Key Skills / Areas of Expertise</div><div class="ivd-value" data-field="technical_skills"></div></div>
                </div>

                <div class="ivd-section-title"><i class="mdi mdi-cash-multiple"></i> Compensation &amp; Availability</div>
                <div class="row">
                    <div class="col-md-6 col-lg-3 ivd-item"><div class="ivd-label">Current CTC</div><div class="ivd-value" data-field="previous_salary"></div></div>
                    <div class="col-md-6 col-lg-3 ivd-item"><div class="ivd-label">Expected CTC</div><div class="ivd-value" data-field="expected_salary"></div></div>
                    <div class="col-md-6 col-lg-3 ivd-item"><div class="ivd-label">Notice Period</div><div class="ivd-value" data-field="notice_period"></div></div>
                    <div class="col-md-6 col-lg-3 ivd-item"><div class="ivd-label">Earliest Joining Date</div><div class="ivd-value" data-field="joining_date"></div></div>
                    <div class="col-12 ivd-item"><div class="ivd-label">Reason for Change / Leaving Current Role</div><div class="ivd-value" data-field="reason_for_leaving"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const token = localStorage.getItem('token');
    const interviewId = window.location.pathname.split('/').pop();

    if (!interviewId || isNaN(interviewId)) {
        $('#ivdCandidateHeading').text('Invalid interview ID.');
        return;
    }

    $('#ivdEditBtn').attr('href', '/interviews/' + interviewId);

    $('#ivdPrintBtn').attr('href', '/interview/pdf/' + interviewId);
    $('#ivdDownloadBtn').attr('href', '/interview/pdf/' + interviewId + '?download=1');
    function isBlankDate(value) {
        return !value || String(value).indexOf('0000-00-00') === 0;
    }

    function formatDate(value) {
        if (isBlankDate(value)) return '';
        const d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d)) return value;
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function formatDateTime(value) {
        if (isBlankDate(value)) return '';
        const d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d)) return value;
        const datePart = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        const hasTime = d.getHours() !== 0 || d.getMinutes() !== 0;
        return hasTime ? datePart + ', ' + d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' }) : datePart;
    }

    $.ajax({
        url: '/api/interviews/' + interviewId,
        type: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function (response) {
            if (response.status !== 'success') {
                $('#ivdCandidateHeading').text('Interview not found.');
                return;
            }
            const data = Object.assign({}, response.data);
            const edu = (data.educations || [])[0] || {};
            const exp = (data.experiences || [])[0] || {};
            const round = (data.rounds || [])[0] || {};

            data.interview_datetime = formatDateTime(data.schedule_date) || formatDate(data.interview_date);
            data.joining_date = formatDate(data.joining_date);
            data.highest_qualification = data.highest_qualification || edu.degree;
            data.college_university = data.college_university || edu.university;
            data.previous_company = data.previous_company || exp.company;
            data.previous_job_title = data.previous_job_title || exp.role;
            data.total_experience = data.total_experience || exp.total_experience;
            data.previous_salary = data.previous_salary || exp.last_salary;
            data.notice_period = data.notice_period || exp.notice_period;
            data.reason_for_leaving = data.reason_for_leaving || exp.reason_for_leaving;
            data.interview_round = data.interview_round || round.interview_round;

            $('.ivd-value[data-field]').each(function () {
                const value = data[$(this).data('field')];
                const hasValue = value !== null && value !== undefined && String(value).trim() !== '';
                $(this).text(hasValue ? value : '-').toggleClass('is-empty', !hasValue);
            });

            $('#ivdCandidateHeading').text((data.candidate_name || 'Candidate') + (data.job_title ? ' — ' + data.job_title : ''));
            const status = data.selection_status || data.status;
            if (status) {
                $('#ivdStatus').text(status).removeClass('d-none');
            }

        },
        error: function () {
            $('#ivdCandidateHeading').text('Error fetching interview details.');
        }
    });
});
</script>

<?= $this->endSection() ?>
