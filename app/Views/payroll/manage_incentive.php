<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<?php $currentYear = (int) date('Y'); ?>
<style>
    .incentive-header-bar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
    }
    .incentive-badge-amt {
        background-color: #eaf5ef;
        color: #0f4a33;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }
    .incentive-type-badge {
        background-color: #eef2f7;
        color: #2c3e50;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
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
                <div class="incentive-header-bar">
                    <h4 class="card-title mb-0">Manage Incentive</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="exportIncentivesBtn" class="btn hr-btnbg">
                            <i class="mdi mdi-file-excel me-1"></i> Export
                        </button>
                        <a href="/addIncentive" class="btn hr-btnbg">
                            <i class="mdi mdi-plus-circle-outline me-1"></i> Add Incentive
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <label for="filter_department" class="form-label fw-semibold">Department</label>
                        <select id="filter_department" class="form-select">
                            <option value="all">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= (int) $dept['id'] ?>"><?= esc($dept['department_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label for="filter_employee" class="form-label fw-semibold">Employee</label>
                        <select id="filter_employee" class="form-select">
                            <option value="all">All Employees</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="filter_year" class="form-label fw-semibold">Year</label>
                        <select id="filter_year" class="form-select">
                            <option value="all">All Years</option>
                            <?php for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="filter_month" class="form-label fw-semibold">Month</label>
                        <select id="filter_month" class="form-select">
                            <option value="all">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-2 d-flex align-items-end">
                        <button type="button" id="resetFiltersBtn" class="btn btn-light border btn-incentive-secondary">
                            <i class="mdi mdi-refresh me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="incentiveTable" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Month/Year</th>
                                <th>Type</th>
                                <th>Percentage</th>
                                <th>Base Salary</th>
                                <th>Incentive Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    const token = localStorage.getItem('token') || '';
    let incentiveDataTable = null;

    function formatINR(num) {
        return '₹' + Number(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(text) {
        return $('<div>').text(text ?? '').html();
    }

    function loadEmployeesFilter(deptId) {
        $.ajax({
            url: '<?= base_url("api/incentives/employees") ?>',
            type: 'GET',
            data: { department_id: deptId || 'all' },
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            success: function (res) {
                const $emp = $('#filter_employee');
                const currentVal = $emp.val();
                $emp.empty().append('<option value="all">All Employees</option>');
                if (res.status && Array.isArray(res.employees)) {
                    res.employees.forEach(function (emp) {
                        $emp.append(`<option value="${emp.id}">${escapeHtml(emp.name)} (${escapeHtml(emp.emp_code)})</option>`);
                    });
                }
                if (currentVal && $emp.find(`option[value="${currentVal}"]`).length) {
                    $emp.val(currentVal);
                }
            }
        });
    }

    function loadIncentives() {
        const params = {
            department_id: $('#filter_department').val(),
            employee_id: $('#filter_employee').val(),
            year: $('#filter_year').val(),
            month: $('#filter_month').val()
        };

        $.ajax({
            url: '<?= base_url("api/incentives/list") ?>',
            type: 'GET',
            data: params,
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            success: function (res) {
                if (incentiveDataTable) {
                    incentiveDataTable.destroy();
                }

                const $tbody = $('#incentiveTable tbody');
                $tbody.empty();

                const rows = (res.status && Array.isArray(res.data)) ? res.data : [];
                rows.forEach(function (item) {
                    const tr = `
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">${escapeHtml(item.employee_name)}</div>
                                <small class="text-muted">${escapeHtml(item.emp_code)}</small>
                            </td>
                            <td>${escapeHtml(item.department_name)}</td>
                            <td>${escapeHtml(item.month_year_label)}</td>
                            <td><span class="incentive-type-badge">${escapeHtml(item.incentive_type)}</span></td>
                            <td>${Number(item.percentage).toFixed(2)}%</td>
                            <td>${formatINR(item.base_salary)}</td>
                            <td><span class="incentive-badge-amt">${formatINR(item.incentive_amount)}</span></td>
                            <td class="text-center text-nowrap">
                                <a href="/editIncentive/${item.id}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-incentive" data-id="${item.id}" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(tr);
                });

                incentiveDataTable = $('#incentiveTable').DataTable({
                    pageLength: 10,
                    ordering: false,
                    responsive: true,
                    language: {
                        emptyTable: 'No incentive records found.'
                    }
                });
            }
        });
    }

    loadEmployeesFilter('all');
    loadIncentives();

    $('#filter_department').on('change', function () {
        loadEmployeesFilter($(this).val());
        $('#filter_employee').val('all');
        loadIncentives();
    });

    $('#filter_employee, #filter_year, #filter_month').on('change', function () {
        loadIncentives();
    });

    $('#resetFiltersBtn').on('click', function () {
        $('#filter_department').val('all');
        loadEmployeesFilter('all');
        $('#filter_employee').val('all');
        $('#filter_year').val('all');
        $('#filter_month').val('all');
        loadIncentives();
    });

    $('#exportIncentivesBtn').on('click', function () {
        const query = $.param({
            department_id: $('#filter_department').val(),
            employee_id: $('#filter_employee').val(),
            year: $('#filter_year').val(),
            month: $('#filter_month').val()
        });
        window.location.href = `<?= base_url("api/incentives/export") ?>?${query}`;
    });

    $(document).on('click', '.btn-delete-incentive', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete Incentive?',
            text: 'Are you sure you want to delete this incentive record?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-light border'
            }
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `<?= base_url("api/incentives/delete") ?>/${id}`,
                type: 'DELETE',
                headers: token ? { 'Authorization': 'Bearer ' + token } : {},
                success: function (res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: res.message || 'Incentive deleted successfully.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        });
                        loadIncentives();
                    }
                },
                error: function (xhr) {
                    const resp = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message || 'Failed to delete incentive.'
                    });
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
