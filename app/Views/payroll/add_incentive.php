<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<?php
$isEdit       = !empty($incentive);
$selectedDept = $isEdit ? ($incentive['department_id'] ?? 'all') : 'all';
$selectedEmp  = $isEdit ? (int) ($incentive['employee_id'] ?? 0) : 0;
$selectedMon  = $isEdit ? (int) ($incentive['month'] ?? date('n')) : (int) date('n');
$selectedYr   = $isEdit ? (int) ($incentive['year'] ?? date('Y')) : (int) date('Y');
$selectedType = $isEdit ? ($incentive['incentive_type'] ?? '') : '';
$pctVal       = $isEdit ? number_format((float) ($incentive['percentage'] ?? 0), 2, '.', '') : '';
$baseSalVal   = $isEdit ? number_format((float) ($incentive['base_salary'] ?? 0), 2, '.', '') : '0.00';
$incAmtVal    = $isEdit ? number_format((float) ($incentive['incentive_amount'] ?? 0), 2, '.', '') : '0.00';
$remarksVal   = $isEdit ? ($incentive['remarks'] ?? '') : '';
$currentYear  = (int) date('Y');
?>
<style>
    .incentive-readonly {
        background-color: #f3f6f4 !important;
        font-weight: 600;
        color: #0f4a33;
    }
    .incentive-amount-highlight {
        background-color: #eaf5ef !important;
        border-color: #0f4a33;
        font-weight: 700;
        color: #0f4a33;
        font-size: 1rem;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .field-error {
        color: #dc3545;
        font-size: 0.825rem;
        margin-top: 4px;
    }
    .btn-incentive-secondary {
        height: 36px !important;
        min-height: 36px !important;
        padding: 6px 14px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        border-radius: 4px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        line-height: 1.2 !important;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                    <h4 class="card-title mb-0"><?= $isEdit ? 'Edit Incentive' : 'Add Incentive' ?></h4>
                    <a href="/manageIncentive" class="btn hr-btnbg">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Manage Incentive
                    </a>
                </div>

                <div id="formAlert" class="alert alert-danger d-none" role="alert"></div>

                <form id="incentiveForm" novalidate>
                    <?php if ($isEdit): ?>
                        <input type="hidden" id="incentive_id" value="<?= (int) $incentive['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <!-- Department -->
                        <div class="col-md-6 col-lg-4">
                            <label for="department_id" class="form-label fw-semibold">Department</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="all" <?= $selectedDept === 'all' ? 'selected' : '' ?>>All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= (int) $dept['id'] ?>" <?= ((string) $selectedDept === (string) $dept['id']) ? 'selected' : '' ?>>
                                        <?= esc($dept['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Employee -->
                        <div class="col-md-6 col-lg-4">
                            <label for="employee_id" class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                            <select class="form-select" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                            </select>
                            <div class="field-error" id="err_employee_id"></div>
                        </div>

                        <!-- Incentive Type -->
                        <div class="col-md-6 col-lg-4">
                            <label for="incentive_type" class="form-label fw-semibold">Incentive Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="incentive_type" name="incentive_type" required>
                                <option value="">Select Incentive Type</option>
                                <?php foreach ($incentiveTypes as $type): ?>
                                    <option value="<?= esc($type) ?>" <?= ($selectedType === $type) ? 'selected' : '' ?>>
                                        <?= esc($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="field-error" id="err_incentive_type"></div>
                        </div>

                        <!-- Month -->
                        <div class="col-md-6 col-lg-3">
                            <label for="month" class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                            <select class="form-select" id="month" name="month" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= ($selectedMon === $m) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <div class="field-error" id="err_month"></div>
                        </div>

                        <!-- Year -->
                        <div class="col-md-6 col-lg-3">
                            <label for="year" class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="year" name="year" required>
                                <?php for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++): ?>
                                    <option value="<?= $y ?>" <?= ($selectedYr === $y) ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <div class="field-error" id="err_year"></div>
                        </div>

                        <!-- Incentive Percentage -->
                        <div class="col-md-6 col-lg-3">
                            <label for="percentage" class="form-label fw-semibold">Incentive Percentage (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="percentage" name="percentage"
                                       min="0.01" max="100" step="0.01" placeholder="e.g. 10"
                                       value="<?= esc($pctVal) ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="field-error" id="err_percentage"></div>
                        </div>

                        <!-- Base Salary -->
                        <div class="col-md-6 col-lg-3">
                            <label for="base_salary_display" class="form-label fw-semibold">Base Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control incentive-readonly" id="base_salary_display"
                                       value="<?= esc($baseSalVal) ?>" readonly>
                                <input type="hidden" id="base_salary" name="base_salary" value="<?= esc($baseSalVal) ?>">
                            </div>
                        </div>

                        <!-- Incentive Amount -->
                        <div class="col-md-6 col-lg-4">
                            <label for="incentive_amount_display" class="form-label fw-semibold">Incentive Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control incentive-amount-highlight" id="incentive_amount_display"
                                       value="<?= esc($incAmtVal) ?>" readonly>
                                <input type="hidden" id="incentive_amount" name="incentive_amount" value="<?= esc($incAmtVal) ?>">
                            </div>
                            <small class="text-muted">Calculated as (Base Salary × Percentage) / 100</small>
                        </div>

                        <!-- Remarks -->
                        <div class="col-md-6 col-lg-8">
                            <label for="remarks" class="form-label fw-semibold">Remarks (Optional)</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                      placeholder="Optional notes or reason for incentive..."><?= esc($remarksVal) ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-wrap gap-2 justify-content-end">
                        <a href="/manageIncentive" class="btn btn-light border btn-incentive-secondary">Cancel</a>
                        <button type="submit" class="btn hr-btnbg" id="saveIncentiveBtn">
                            <i class="mdi mdi-content-save me-1"></i> <?= $isEdit ? 'Update Incentive' : 'Save Incentive' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const token = localStorage.getItem('token') || '';
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const preselectedEmployeeId = <?= (int) $selectedEmp ?>;
    const incentiveId = <?= $isEdit ? (int) $incentive['id'] : 'null' ?>;

    function formatINR(num) {
        return Number(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function recalculateIncentive() {
        const baseSalary = parseFloat($('#base_salary').val()) || 0;
        const pct = parseFloat($('#percentage').val()) || 0;
        const amount = (baseSalary * pct) / 100;

        $('#base_salary_display').val(formatINR(baseSalary));
        $('#incentive_amount').val(amount.toFixed(2));
        $('#incentive_amount_display').val(formatINR(amount));
    }

    function loadEmployees(departmentId, selectedEmpId = 0) {
        $.ajax({
            url: '<?= base_url("api/incentives/employees") ?>',
            type: 'GET',
            data: { department_id: departmentId || 'all' },
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            success: function (res) {
                const $emp = $('#employee_id');
                $emp.empty().append('<option value="">Select Employee</option>');
                if (res.status && Array.isArray(res.employees)) {
                    res.employees.forEach(function (emp) {
                        const sel = (selectedEmpId && Number(selectedEmpId) === Number(emp.id)) ? 'selected' : '';
                        $emp.append(`<option value="${emp.id}" data-dept="${emp.department_id || ''}" ${sel}>${emp.name} (${emp.emp_code})</option>`);
                    });
                }
                if (selectedEmpId) {
                    fetchBaseSalary();
                }
            }
        });
    }

    function fetchBaseSalary() {
        const employeeId = $('#employee_id').val();
        const month = $('#month').val();
        const year = $('#year').val();

        if (!employeeId) {
            $('#base_salary').val('0.00');
            recalculateIncentive();
            return;
        }

        $.ajax({
            url: '<?= base_url("api/incentives/base-salary") ?>',
            type: 'GET',
            data: { employee_id: employeeId, month: month, year: year },
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            success: function (res) {
                if (res.status) {
                    const base = parseFloat(res.base_salary) || 0;
                    $('#base_salary').val(base.toFixed(2));
                    recalculateIncentive();
                }
            }
        });
    }

    function clearErrors() {
        $('#formAlert').addClass('d-none').text('');
        $('.field-error').text('');
        $('.is-invalid').removeClass('is-invalid');
    }

    function validateForm() {
        clearErrors();
        let valid = true;

        const employeeId = $('#employee_id').val();
        const month = $('#month').val();
        const year = $('#year').val();
        const incentiveType = $('#incentive_type').val();
        const pctRaw = $('#percentage').val();
        const pct = parseFloat(pctRaw);

        if (!employeeId) {
            $('#employee_id').addClass('is-invalid');
            $('#err_employee_id').text('Please select an employee.');
            valid = false;
        }
        if (!incentiveType) {
            $('#incentive_type').addClass('is-invalid');
            $('#err_incentive_type').text('Please select an incentive type.');
            valid = false;
        }
        if (!month) {
            $('#month').addClass('is-invalid');
            $('#err_month').text('Month is required.');
            valid = false;
        }
        if (!year) {
            $('#year').addClass('is-invalid');
            $('#err_year').text('Year is required.');
            valid = false;
        }
        if (pctRaw === '' || isNaN(pct) || pct <= 0 || pct > 100) {
            $('#percentage').addClass('is-invalid');
            $('#err_percentage').text('Percentage must be greater than 0 and no more than 100.');
            valid = false;
        }

        return valid;
    }

    // Initial load
    loadEmployees($('#department_id').val(), preselectedEmployeeId);
    if (isEdit) {
        recalculateIncentive();
    }

    $('#department_id').on('change', function () {
        $('#base_salary').val('0.00');
        recalculateIncentive();
        loadEmployees($(this).val(), 0);
    });

    $('#employee_id, #month, #year').on('change', function () {
        if ($(this).is('#employee_id')) {
            const deptFromEmp = $(this).find('option:selected').data('dept');
            if (deptFromEmp && $('#department_id').val() === 'all') {
                // Keep department_id filter as-is; server also falls back to employee's department
            }
        }
        fetchBaseSalary();
    });

    $('#percentage').on('input change', function () {
        recalculateIncentive();
    });

    $('#incentiveForm').on('submit', function (e) {
        e.preventDefault();
        if (!validateForm()) {
            return;
        }

        const selectedOptionDept = $('#employee_id option:selected').data('dept');
        const deptVal = $('#department_id').val() !== 'all'
            ? $('#department_id').val()
            : (selectedOptionDept || '');

        const payload = {
            department_id: deptVal,
            employee_id: $('#employee_id').val(),
            month: $('#month').val(),
            year: $('#year').val(),
            incentive_type: $('#incentive_type').val(),
            percentage: $('#percentage').val(),
            remarks: $('#remarks').val()
        };

        const url = isEdit
            ? `<?= base_url("api/incentives/update") ?>/${incentiveId}`
            : '<?= base_url("api/incentives/create") ?>';

        const $btn = $('#saveIncentiveBtn');
        $btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            success: function (res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message || 'Incentive saved successfully.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn hr-btnbg' }
                    }).then(function () {
                        window.location.href = '/manageIncentive';
                    });
                } else {
                    $('#formAlert').removeClass('d-none').text(res.message || 'Failed to save incentive.');
                    $btn.prop('disabled', false);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                const resp = xhr.responseJSON || {};
                const msg = resp.message || 'Validation failed. Please check your inputs.';
                $('#formAlert').removeClass('d-none').text(msg);

                if (resp.errors && typeof resp.errors === 'object') {
                    Object.keys(resp.errors).forEach(function (key) {
                        if ($(`#err_${key}`).length) {
                            $(`#${key}`).addClass('is-invalid');
                            $(`#err_${key}`).text(resp.errors[key]);
                        }
                    });
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: msg,
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'btn hr-btnbg' }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
