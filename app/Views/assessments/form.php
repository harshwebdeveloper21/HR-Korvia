<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<?php
$assessment = $assessment ?? null;
$isEdit = $assessment !== null;
$val = static function (string $field, $default = '') use ($assessment) {
    return old($field, $assessment[$field] ?? $default);
};
$oldRating = static function (string $key) use ($savedRatings) {
    return (int) old('rating_' . $key, $savedRatings[$key] ?? 0);
};
$selectedRec = old('recommendation', $assessment['recommendation'] ?? '');
$returnTo = old('return_to', $returnTo ?? '');
$cancelUrl = $returnTo === 'interviews' ? '/addinterview' : '/assessment';
$selectedInterview = (string) old('interview_id', $assessment['interview_id'] ?? ($preselectInterview ?? ''));
$autoFillOnLoad = !$isEdit && !empty($preselectInterview) && old('interview_id') === null;
?>
<style>
    .as-wrap { width: 100%; max-width: 1400px; margin: 0 auto; }
    .as-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
    .as-header h3 { font-size: 22px; font-weight: 600; color: #111827; margin-bottom: 2px; }
    .as-header p { font-size: 13px; color: #6b7280; margin: 0; }
    .as-score {
        text-align: center; min-width: 120px; padding: 8px 18px; border-radius: 8px;
        border: 1px solid rgba(var(--hr-primary-rgb, 230, 97, 54), .35);
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .06);
    }
    .as-score .num { font-size: 24px; font-weight: 700; color: var(--hr-primary-text, var(--hr-primary, #e66136)); line-height: 1.1; }
    .as-score .lbl { font-size: 11px; color: #6b7280; font-weight: 600; }
    .as-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px 20px 10px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .as-section-title {
        display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
        color: var(--hr-primary-text, var(--hr-primary, #e66136));
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .08);
        border-left: 3px solid var(--hr-primary, #e66136);
        border-radius: 4px; padding: 7px 12px; margin: 6px 0 14px;
    }
    .as-section-title .tag { opacity: .75; font-weight: 600; }
    #assessmentForm .form-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    #assessmentForm .form-label .req { color: #dc3545; }
    #assessmentForm .form-control, #assessmentForm .form-select { border-radius: 6px; border: 1px solid #d1d5db; padding: 7px 10px; font-size: 13.5px; min-height: 38px; }
    #assessmentForm .form-control:focus, #assessmentForm .form-select:focus { border-color: var(--hr-primary, #e66136); box-shadow: 0 0 0 2px rgba(var(--hr-primary-rgb, 230, 97, 54), .2); }
    #assessmentForm .mb-3 { margin-bottom: 12px !important; }
    .as-hint { font-size: 12px; color: #6b7280; font-style: italic; margin: -6px 0 10px; }

    .as-rating-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
    .as-rating-table th, .as-rating-table td { padding: 8px 10px; border-bottom: 1px solid #eef0f3; vertical-align: middle; }
    .as-rating-table tr:last-child td { border-bottom: none; }
    .as-rating-table thead th { font-size: 12px; text-align: center; }
    .as-rating-table thead th:first-child { text-align: left; }
    .as-rating-table .param-title { font-weight: 600; font-size: 13.5px; color: #111827; }
    .as-rating-table .param-desc { font-size: 12px; color: #6b7280; }
    .as-rating-table td.rate-cell { text-align: center; width: 11%; }
    .as-rating-table tr.is-missing td { background: #fff5f5; }
    .rate-option { display: inline-flex; flex-direction: column; align-items: center; cursor: pointer; margin: 0; }
    .rate-option input { position: absolute; opacity: 0; pointer-events: none; }
    .rate-option span {
        width: 34px; height: 34px; border-radius: 50%; border: 1.5px solid #cbd2dc; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px; color: #4b5563; background: #fff; transition: all .15s ease;
    }
    .rate-option:hover span { border-color: var(--hr-primary, #e66136); }
    .rate-option input:checked + span { background: var(--hr-primary, #e66136); border-color: var(--hr-primary, #e66136); color: var(--hr-on-primary, #fff); }
    .rate-option input:focus-visible + span { box-shadow: 0 0 0 3px rgba(var(--hr-primary-rgb, 230, 97, 54), .25); }
    .as-total-row { display: flex; justify-content: space-between; align-items: center; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; margin: 12px 0 16px; font-weight: 600; }
    .as-total-row .val { color: var(--hr-primary-text, var(--hr-primary, #e66136)); font-size: 16px; }

    .rec-options { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; margin-bottom: 14px; }
    .rec-option { position: relative; margin: 0; }
    .rec-option input { position: absolute; opacity: 0; pointer-events: none; }
    .rec-option span {
        display: flex; align-items: center; gap: 8px; border: 1.5px solid #d1d5db; border-radius: 8px; padding: 10px 12px;
        font-size: 13.5px; font-weight: 500; cursor: pointer; background: #fff; transition: all .15s ease;
    }
    .rec-option span::before { content: ""; width: 16px; height: 16px; border-radius: 4px; border: 1.5px solid #9ca3af; flex: 0 0 16px; }
    .rec-option input:checked + span { border-color: var(--hr-primary, #e66136); background: rgba(var(--hr-primary-rgb, 230, 97, 54), .06); color: var(--hr-primary-text, var(--hr-primary, #e66136)); font-weight: 600; }
    .rec-option input:checked + span::before { background: var(--hr-primary, #e66136); border-color: var(--hr-primary, #e66136); box-shadow: inset 0 0 0 3px #fff; }
    .rec-options.is-missing .rec-option span { border-color: #dc3545; }
    .as-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }

    @media (max-width: 767px) {
        .as-rating-table thead { display: none; }
        .as-rating-table, .as-rating-table tbody, .as-rating-table tr, .as-rating-table td { display: block; width: 100%; }
        .as-rating-table tr { border-bottom: 1px solid #eef0f3; padding: 8px 0; }
        .as-rating-table td { border: none; padding: 4px 10px; }
        .as-rating-table td.rate-cell { display: inline-block; width: auto; padding: 4px 6px; }
        .rate-option small { display: block; font-size: 10px; color: #6b7280; }
    }
    @media (min-width: 768px) {
        .rate-option small { display: none; }
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="as-wrap">
            <div class="as-header">
                <div>
                    <h3><?= $isEdit ? 'Edit Candidate Assessment' : 'Candidate Assessment Form' ?></h3>
                    <p>Confidential – For Internal Use Only</p>
                </div>
                <div class="as-score">
                    <div class="num"><span id="scoreTotal">0</span> / 30</div>
                    <div class="lbl">OVERALL RATING</div>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= $isEdit ? '/assessment/update/' . $assessment['id'] : '/assessment/store' ?>" id="assessmentForm" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="return_to" value="<?= esc($returnTo) ?>">
                <div class="as-card">
                    <div class="as-section-title"><span class="tag">Section A</span> | Interview Details</div>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="interviewSelect">Candidate Name <span class="req">*</span></label>
                            <select class="form-select" name="interview_id" id="interviewSelect">
                                <option value="">Select Candidate</option>
                                <?php foreach ($interviews as $inv):
                                    $invDate = (string) ($inv['interview_date'] ?? '');
                                    if ($invDate === '' || strpos($invDate, '0000') === 0) {
                                        $invDate = (!empty($inv['schedule_date']) && strpos($inv['schedule_date'], '0000') !== 0) ? substr($inv['schedule_date'], 0, 10) : '';
                                    }
                                ?>
                                    <option value="<?= $inv['id'] ?>"
                                        data-job="<?= esc($inv['position_applied_for'] ?? '') ?>"
                                        data-dept="<?= esc($inv['department_name'] ?? '') ?>"
                                        data-round="<?= esc($inv['interview_round'] ?? '') ?>"
                                        data-date="<?= esc($invDate) ?>"
                                        data-interviewer="<?= esc(trim((string) ($inv['interviewer_name'] ?? ''))) ?>"
                                        data-mobile="<?= esc($inv['mobile_number'] ?? '') ?>"
                                        <?= $selectedInterview === (string) $inv['id'] ? 'selected' : '' ?>>
                                        <?= esc($inv['candidate_name'] ?: 'Unknown Candidate') ?><?= !empty($inv['position_applied_for']) ? ' — ' . esc($inv['position_applied_for']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="jobTitle">Position Applied For</label>
                            <input type="text" class="form-control" name="job_title" id="jobTitle" placeholder="e.g. Sales Executive" value="<?= esc($val('job_title')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="interviewDate">Date of Interview <span class="req">*</span></label>
                            <input type="date" class="form-control" name="interview_date" id="interviewDate" value="<?= esc($val('interview_date')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="interviewerName">Interviewer Name</label>
                            <input type="text" class="form-control" name="interviewer_name" id="interviewerName" placeholder="Interviewer name" value="<?= esc($val('interviewer_name')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="interviewRound">Interview Round</label>
                            <input type="text" class="form-control" name="interview_round" id="interviewRound" list="asRoundList" placeholder="e.g. 1st Round" value="<?= esc($val('interview_round')) ?>">
                            <datalist id="asRoundList">
                                <option value="1st Round"></option>
                                <option value="2nd Round"></option>
                                <option value="Technical Round"></option>
                                <option value="HR Round"></option>
                                <option value="Final Round"></option>
                            </datalist>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="department">Department</label>
                            <select class="form-select" name="department" id="department">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= esc($dept['department_name']) ?>" <?= $val('department') === $dept['department_name'] ? 'selected' : '' ?>><?= esc($dept['department_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="contactNumber">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="contactNumber" placeholder="Contact number" value="<?= esc($val('contact_number')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label class="form-label" for="reportingManager">Reporting Manager</label>
                            <input type="text" class="form-control" name="reporting_manager" id="reportingManager" placeholder="Reporting manager" value="<?= esc($val('reporting_manager')) ?>">
                        </div>
                    </div>

                    <div class="as-section-title"><span class="tag">Section B</span> | Competency Evaluation</div>
                    <p class="as-hint">Please rate the candidate on each parameter (1 = Poor, 5 = Excellent).</p>
                    <div class="table-responsive-sm">
                        <table class="as-rating-table">
                            <thead>
                                <tr>
                                    <th>Evaluation Parameter</th>
                                    <?php foreach ($ratingLabels as $num => $label): ?>
                                        <th><?= $num ?><br><span style="font-weight:500;"><?= esc($label) ?></span></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competencies as $key => [$title, $desc]): $current = $oldRating($key); ?>
                                    <tr data-key="<?= $key ?>">
                                        <td>
                                            <div class="param-title"><?= esc($title) ?></div>
                                            <div class="param-desc"><?= esc($desc) ?></div>
                                        </td>
                                        <?php foreach ($ratingLabels as $num => $label): ?>
                                            <td class="rate-cell">
                                                <label class="rate-option" title="<?= esc($label) ?>">
                                                    <input type="radio" name="rating_<?= $key ?>" value="<?= $num ?>" <?= $current === $num ? 'checked' : '' ?>>
                                                    <span><?= $num ?></span>
                                                    <small><?= esc($label) ?></small>
                                                </label>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="as-total-row">
                        <span>Overall Rating (Total Score out of 30)</span>
                        <span class="val"><span id="scoreTotalRow">0</span> / 30</span>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="as-section-title"><span class="tag">Section C</span> | Key Strengths</div>
                            <textarea class="form-control" name="strengths" rows="3" placeholder="What did the candidate do well?"><?= esc(old('strengths', $savedFeedback['strengths'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="as-section-title"><span class="tag">Section D</span> | Areas of Concern / Development Needs</div>
                            <textarea class="form-control" name="weaknesses" rows="3" placeholder="Any concerns or areas to develop?"><?= esc(old('weaknesses', $savedFeedback['weaknesses'] ?? '')) ?></textarea>
                        </div>
                    </div>

                    <div class="as-section-title"><span class="tag">Section E</span> | Final Recommendation <span class="req text-danger">*</span></div>
                    <div class="rec-options" id="recOptions">
                        <?php foreach ($recommendations as $idx => $rec): ?>
                            <label class="rec-option">
                                <input type="radio" name="recommendation" value="<?= esc($rec) ?>" <?= $selectedRec === $rec ? 'checked' : '' ?>>
                                <span><?= esc($rec) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="as-footer">
                    <a href="<?= $cancelUrl ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn hr-btnbg" id="submitBtn"><?= $isEdit ? 'Update Assessment' : 'Submit Assessment' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    function calculateScore() {
        let total = 0;
        $('#assessmentForm .as-rating-table tbody tr').each(function () {
            const checked = $(this).find('input[type="radio"]:checked');
            if (checked.length) total += parseInt(checked.val(), 10);
        });
        $('#scoreTotal, #scoreTotalRow').text(total);
    }

    $('#assessmentForm').on('change', '.as-rating-table input[type="radio"]', function () {
        $(this).closest('tr').removeClass('is-missing');
        calculateScore();
    });
    $('#assessmentForm').on('change', 'input[name="recommendation"]', function () {
        $('#recOptions').removeClass('is-missing');
    });

    $('#interviewSelect').on('change', function () {
        const selected = $(this).find('option:selected');
        if (!selected.val()) return;
        const fill = function (selector, value) {
            if (value) $(selector).val(value);
        };
        fill('#jobTitle', selected.data('job'));
        fill('#interviewRound', selected.data('round'));
        fill('#interviewDate', selected.data('date'));
        fill('#interviewerName', selected.data('interviewer'));
        fill('#contactNumber', selected.data('mobile'));
        const dept = selected.data('dept');
        if (dept && $('#department option').filter(function () { return $(this).val() === dept; }).length) {
            $('#department').val(dept);
        }
    });

    $('#assessmentForm').on('submit', function (e) {
        let message = '';
        const $first = [];

        if (!$('#interviewSelect').val()) {
            message = 'Please select a candidate.';
            $first.push($('#interviewSelect'));
        } else if (!$('#interviewDate').val()) {
            message = 'Date of interview is required.';
            $first.push($('#interviewDate'));
        }

        $('#assessmentForm .as-rating-table tbody tr').each(function () {
            if (!$(this).find('input[type="radio"]:checked').length) {
                $(this).addClass('is-missing');
                if (!message) message = 'Please rate the candidate on every competency.';
                $first.push($(this));
            }
        });

        if (!$('input[name="recommendation"]:checked').length) {
            $('#recOptions').addClass('is-missing');
            if (!message) message = 'Please select a final recommendation.';
            $first.push($('#recOptions'));
        }

        if (message) {
            e.preventDefault();
            if ($first.length) {
                $('html, body').animate({ scrollTop: $first[0].offset().top - 120 }, 200);
            }
            Swal.fire({ icon: 'error', title: 'Incomplete form', text: message, customClass: { confirmButton: 'hr-btnbg' } });
        }
    });

    calculateScore();
    <?php if ($autoFillOnLoad): ?>
    $('#interviewSelect').trigger('change');
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
