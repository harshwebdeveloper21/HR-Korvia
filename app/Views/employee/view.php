<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .main-dec-div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filtermenu {
        display: flex;
    }


    @media (max-width: 767px) {
        .filter-sm-res {
            flex-wrap: wrap !important;
        }

        .flex-direction-column {
            flex-direction: column;
        }

        .filter-sm-res h4 {
            flex: 1 1 100%;
            margin-bottom: 10px;
        }

        .filter-sm-res>div {
            flex: 1 1 100%;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-sm-res select {
            width: 100% !important;
            min-width: unset !important;
        }


        .filter-sm-res a {
            width: 100%;
        }

        .btnpdingam {
            margin: 0px !important;
        }

        .filterbtnpadd {
            padding: 2px !important;

        }

        .filtermenu {
            margin-bottom: 12px !important;
            /* margin-left: 7px !important; */
        }

        #departmentonbordingFilter {
            max-width: 150px;
            font-size: 14px;
            padding: 4px 8px;
        }

        .fontsmfiltertitle {
            font-size: 11px !important;
        }

        .filterbtn {
            margin-top: -0.5rem !important;
        }

        .fontsmfiltertitle {
            font-size: 12px !important;
        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
            /* margin-left: -3rem !important;  */
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 18%;
        }

        #employee-table_length label {
            margin-top: 1px;
            display: flex;
            align-items: center;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
            /* Hide "Search:" label text */
        }

        /* Hide the text inside the label */
        #employee-table_length::first-text,
        #employee-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #employee-table_length label {
            font-size: 0;
            /* hide text */
        }

        #employee-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #employee-table_filter label {
            font-size: 0;
        }

        #employee-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #employee-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }
    }

    @media (min-width: 768px) and (max-width: 1366px) {}

    @media (min-width: 767px) {
        div.dataTables_wrapper div.dataTables_filter label input {
            width: 321px !important;
        }
    }

@media (min-width: 768px) {
    .table-responsive {
        overflow-x: visible !important;
    }
    #employee-table {
        table-layout: auto !important;
        width: 100% !important;
        white-space: normal !important;
    }
    #employee-table th,
    #employee-table td {
        padding: 8px 6px !important;
        font-size: 0.8125rem;
        vertical-align: middle;
    }
    #employee-table th.action-column,
    #employee-table td.action-column {
        width: 95px !important;
        min-width: 95px !important;
    }
}

/* ─── Modal override: force visibility on ALL screen sizes for ALL modals ─── */
.modal {
    display: none;
    opacity: 1 !important;
    transition: none !important;
    z-index: 9999 !important;
}
.modal.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
}
.modal-backdrop.show {
    opacity: 0.5 !important;
    z-index: 9998 !important;
}
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <div class="align-items-center d-md-flex justify-content-between mb-3">

                    <h4 class="card-title">Manage Employees</h4>
                    <div class="d-md-flex gap-2 align-items-center emp-filter-container">
                        <select class="form-select" id="departmentFilter" style="min-width: 180px; width: auto;">
                            <option value="">All Departments</option>
                        </select>
                        <select class="form-select" id="monthFilter" style="min-width: 135px; width: auto;">
                            <option value="">All Months</option>
                            <option value="01">Jan</option>
                            <option value="02">Feb</option>
                            <option value="03">Mar</option>
                            <option value="04">Apr</option>
                            <option value="05">May</option>
                            <option value="06">Jun</option>
                            <option value="07">Jul</option>
                            <option value="08">Aug</option>
                            <option value="09">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                        </select>
                        <select class="form-select" id="yearFilter" style="min-width: 120px; width: auto;">
                            <option value="">All Years</option>
                            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="button" id="btnExportEmployees" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/employee" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Employee
                        </a>
                    </div>
                </div>

                <!-- Employee Tabs -->
                <ul class="nav nav-tabs mb-3" id="employeeTabs" role="tablist" style="border-bottom: 2px solid #E66136;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="active-tab" data-view="active" type="button" role="tab"
                            style="color:#E66136; border-bottom: 3px solid #E66136; font-weight:600;">Active Employees</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactive-tab" data-view="inactive" type="button" role="tab"
                            style="color:#6c757d; font-weight:600;">Inactive / Resigned</button>
                    </li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="employee-table">
                        <thead class="table-light">
                            <tr>
                                <th style="display:none;">ID</th>
                                <th style="width: 80px;">Emp ID</th>
                                <th>Name</th>
                                <th class="desktop-only-col">Email</th>
                                <th class="desktop-only-col">Department</th>
                                <th class="desktop-only-col">Role</th>
                                <th class="desktop-only-col text-center" style="width: 85px;">Rem. Paid</th>
                                <th class="desktop-only-col text-center" style="width: 85px;">Rem. Sick</th>
                                <th class="desktop-only-col text-center" style="width: 90px;">Status</th>
                                <th class="desktop-only-col text-center action-column" style="width: 95px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="employee-table-body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Password Reset Modal -->
<div class="modal" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="passwordForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="me-3 ms-3 mt-3">
                    <div class="mb-3 position-relative">
                        <input type="hidden" name="password_user_id" id="password_user_id">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <i class="fa fa-eye toggle-password" toggle="#password"
                            style="position: absolute; top: 38px; right: 15px; cursor: pointer;"></i>
                    </div>

                    <div class="mb-3 position-relative">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            required>
                        <i class="fa fa-eye toggle-password" toggle="#confirm_password"
                            style="position: absolute; top: 38px; right: 15px; cursor: pointer;"></i>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary hr-btnbg rounded">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employee Status Modal -->
<div class="modal" id="employeeStatusModal" tabindex="-1" aria-labelledby="employeeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header" style="background:#E66136; color:white;">
                <h5 class="modal-title" id="employeeStatusModalLabel">
                    <i class="mdi mdi-account-cog me-1"></i> Change Employee Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="employeeStatusForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="status_user_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Employee Name:</label>
                        <span id="status_employee_name" class="ms-1 fw-bold text-primary"></span>
                    </div>
                    <div class="mb-3">
                        <label for="status_select" class="form-label fw-bold">Select Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_select" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Resigned">Resigned</option>
                            <option value="Fired">Fired / Removed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status_reason" class="form-label fw-bold">Reason / Comment <small class="text-muted">(Required for Resigned/Fired/Inactive)</small></label>
                        <textarea class="form-control" id="status_reason" name="status_reason" rows="3" placeholder="Enter details or reason for resignation / removal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status_last_working_day" class="form-label fw-bold">Last Working Day</label>
                        <input type="date" class="form-control" id="status_last_working_day" name="last_working_day" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer border-top-0" style="background:#f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn hr-btnbg" id="btnSaveStatus">
                        <i class="mdi mdi-check me-1"></i> Save Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Month-wise Leave History Modal -->
<div class="modal" id="leaveHistoryMonthlyModal" tabindex="-1" aria-labelledby="leaveHistoryMonthlyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg,#E66136,#f0845a); color:#fff; padding:18px 24px;">
                <div>
                    <h5 class="modal-title mb-0" id="leaveHistoryMonthlyModalLabel">
                        <i class="mdi mdi-calendar-clock me-2"></i>Month-wise Leave History
                    </h5>
                    <small id="lm-employee-name" class="opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex gap-3 mb-3">
                    <div class="p-2 px-3 rounded bg-light border flex-fill">
                        <span class="text-muted d-block small">Remaining Paid Leave</span>
                        <strong id="lm-paid-leave" class="fs-5 text-success">0</strong>
                    </div>
                    <div class="p-2 px-3 rounded bg-light border flex-fill">
                        <span class="text-muted d-block small">Remaining Sick Leave</span>
                        <strong id="lm-sick-leave" class="fs-5 text-warning">0</strong>
                    </div>
                </div>

                <div id="lm-loader" class="text-center py-4">
                    <div class="spinner-border" style="color:#E66136;" role="status">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                    <p class="mt-2 text-muted small">Fetching monthly leave breakdown...</p>
                </div>

                <div id="lm-empty" class="text-center py-4" style="display:none;">
                    <i class="mdi mdi-calendar-blank" style="font-size:2.5rem; color:#ccc;"></i>
                    <p class="mt-2 text-muted fw-semibold">No leave history records found for this employee</p>
                </div>

                <div id="lm-table-wrapper" style="display:none;">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Month & Year</th>
                                <th>Paid Leave Used</th>
                                <th>Sick Leave Used</th>
                                <th>Total Days</th>
                            </tr>
                        </thead>
                        <tbody id="lm-tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8f9fa;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Salary Increment Modal -->
<div class="modal" id="incrementModal" tabindex="-1" aria-labelledby="incrementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header" style="background:#E66136; color:white;">
                <h5 class="modal-title" id="incrementModalLabel">
                    <i class="mdi mdi-cash-plus me-1"></i> Add Salary Increment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="increment_user_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Employee Name:</label>
                    <span id="increment_employee_name" class="ms-1 fw-bold text-primary"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Salary (&#8377;):</label>
                    <input type="text" class="form-control" id="current_salary" readonly style="background:#e9ecef;">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Increment Date:</label>
                    <input type="text" class="form-control" id="last_increment_date" readonly style="background:#e9ecef;">
                </div>
                <div class="mb-3">
                    <label for="increment_amount" class="form-label fw-bold">Increment Amount (&#8377;) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control" id="increment_amount" placeholder="e.g. 5000" required>
                </div>
                <div class="mb-3">
                    <label for="increment_date" class="form-label fw-bold">Effective Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="increment_date" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="modal-footer border-top-0" style="background:#f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn hr-btnbg" id="btnSaveIncrement">
                    <i class="mdi mdi-check me-1"></i> Update Salary
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Increment History Modal -->
<div class="modal" id="incrementHistoryModal" tabindex="-1" aria-labelledby="incrementHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg,#E66136,#f0845a); color:#fff; padding:18px 24px;">
                <div>
                    <h5 class="modal-title mb-0" id="incrementHistoryModalLabel">
                        <i class="mdi mdi-history me-2"></i>Salary Increment History
                    </h5>
                    <small id="ih-employee-name" class="opacity-75"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div id="ih-loader" class="text-center py-4">
                    <div class="spinner-border" style="color:#E66136;" role="status">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                    <p class="mt-2 text-muted small">Fetching increment history...</p>
                </div>

                <div id="ih-empty" class="text-center py-4" style="display:none;">
                    <i class="mdi mdi-information-outline" style="font-size:2.5rem; color:#ccc;"></i>
                    <p class="mt-2 text-muted fw-semibold">No increment history found for this employee.</p>
                </div>

                <div id="ih-table-wrapper" class="table-responsive" style="display:none;">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Previous Salary</th>
                                <th>Increment</th>
                                <th>New Salary</th>
                                <th>Effective Date</th>
                                <th>Updated At</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="ih-tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#f8f9fa;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const token = localStorage.getItem('token');

        function capitalizeFirstLetter(string) {
            return string ? string.charAt(0).toUpperCase() + string.slice(1).toLowerCase() : '';
        }
        // ✅ Fetch and display employees
        function fetchEmployees(departmentId = '', viewType = 'active', month = '', year = '') {
            $.ajax({
                url: '<?= base_url('/api/employees') ?>',
                type: 'GET',
                data: {
                    department_id: departmentId || '',
                    view: viewType
                },

                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function (response) {
                    if (response.status && response.employees) {
                        let employees = response.employees;

                        // Filter by month/year joining date if selected
                        if (month || year) {
                            employees = employees.filter((emp) => {
                                if (!emp.user_info.joining_date) return false;
                                const d = new Date(emp.user_info.joining_date);
                                if (isNaN(d.getTime())) return false;
                                const m = String(d.getMonth() + 1).padStart(2, '0');
                                const y = String(d.getFullYear());
                                if (month && m !== month) return false;
                                if (year && y !== year) return false;
                                return true;
                            });
                        }

                        let tableRows = '';

                        if (employees.length > 0) {
                            employees.forEach((employee) => {
                                const empIdCode = employee.user_info.employee_id || ('EMP-' + String(employee.user.id).padStart(3, '0'));
                                const empName = `${capitalizeFirstLetter(employee.user_info.firstname || 'N/A')} ${capitalizeFirstLetter(employee.user_info.lastname || '')}`;
                                const empEmail = employee.user?.email || 'N/A';
                                const empDept = employee.user_info?.department_name || 'N/A';
                                const empRole = employee.user?.role ? employee.user.role.charAt(0).toUpperCase() + employee.user.role.slice(1) : 'N/A';
                                const empRemPaid = employee.user_info?.remaining_paid_leave !== undefined ? employee.user_info.remaining_paid_leave : 0;
                                const empRemSick = employee.user_info?.remaining_sick_leave !== undefined ? employee.user_info.remaining_sick_leave : 0;
                                const empStatus = employee.user_info?.status || 'Active';
                                const empReason = employee.user_info?.status_reason || '';
                                const empLastDay = employee.user_info?.last_working_day || '';
                                const isInactive = ['resigned', 'fired', 'removed', 'inactive'].includes(empStatus.toLowerCase());

                                let statusBadge = '<span class="badge bg-success p-1 px-2">Active</span>';
                                if (isInactive) {
                                    const badgeColor = empStatus.toLowerCase() === 'resigned' ? 'bg-warning text-dark' : 'bg-danger';
                                    statusBadge = `<span class="badge ${badgeColor} p-1 px-2">${empStatus}</span>`;
                                    if (empReason) {
                                        statusBadge += `<br><small class="text-muted fst-italic" style="font-size:11px;">Reason: ${empReason}</small>`;
                                    }
                                    if (empLastDay) {
                                        statusBadge += `<br><small class="text-secondary" style="font-size:10px;">LWD: ${empLastDay}</small>`;
                                    }
                                }

                                tableRows += `
                                    <tr data-id="${employee.user.id}">
                                        <td style="display:none;">${employee.user.id}</td>
                                        <td class="fw-bold text-nowrap"><span class="badge bg-secondary p-1 px-2" style="font-size: 11px;">${empIdCode}</span></td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <a href="/employee/profile/${employee.user_info.id}" class="text-decoration-none me-2">
                                                    <img src="${employee.user_info.profile_image_url}" alt="Profile" width="32" height="32" class="rounded-circle"
                                                        onerror="this.onerror=null; this.src='<?= base_url(env('ImagePath') . '/upload/default-profile.jpg') ?>';">
                                                </a>
                                                <a href="/employee/profile/${employee.user_info.id}" class="text-decoration-none text-dark fw-semibold text-truncate d-inline-block" style="max-width: 140px;" title="${empName}">
                                                    ${empName}
                                                </a>                                                
                                            </div>
                                            <div style="flex: 1;" class="align-self-center">                                                
                                                <div class="expanded-details" id="emp-details-${employee.user.id}" onclick="event.stopPropagation();">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Email:</span>
                                                        <span class="detail-value">${empEmail}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Department:</span>
                                                        <span class="detail-value">${empDept}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Role:</span>
                                                        <span class="detail-value">${empRole}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Rem. Paid Leave:</span>
                                                        <span class="detail-value"><a href="#" class="open-leave-history text-decoration-none fw-bold text-success" data-id="${employee.user.id}" data-name="${empName}">${empRemPaid} <i class="mdi mdi-information-outline small text-muted"></i></a></span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Rem. Sick Leave:</span>
                                                        <span class="detail-value"><a href="#" class="open-leave-history text-decoration-none fw-bold text-warning" data-id="${employee.user.id}" data-name="${empName}">${empRemSick} <i class="mdi mdi-information-outline small text-muted"></i></a></span>
                                                    </div>
                                                    <div class="detail-actions">
                                                        <a href="#" data-id="${employee.user.id}" data-name="${empName}" data-status="${empStatus}" data-reason="${empReason}" data-lastday="${empLastDay}" class="btn btn-sm btn-info open-status-modal" title="Change Status"><i class="mdi mdi-account-cog"></i> Status</a>
                                                        ${!isInactive ? `<a href="#" data-id="${employee.user.id}" data-name="${empName}" data-salary="${employee.user_info?.salary || 0}" data-last-date="${employee.user_info?.last_increment_date || ''}" class="btn btn-sm btn-success open-increment-modal" title="Add Increment"><i class="mdi mdi-cash-plus"></i> Increment</a>` : ''}
                                                        <a href="#" data-id="${employee.user.id}" data-name="${empName}" class="btn btn-sm btn-outline-secondary open-increment-history" title="Increment History"><i class="mdi mdi-history"></i> Inc. History</a>
                                                        <a href="#" data-id="${employee.user.id}" data-name="${empName}" class="btn btn-sm btn-success open-leave-history" title="Leave History"><i class="mdi mdi-calendar-clock"></i> Leave History</a>
                                                        <a href="#" data-id="${employee.user.id}" data-pass="${employee.user.password}" class="btn btn-sm btn-secondary open-password-modal" title="Password"><i class="fa fa-key"></i> Password</a>
                                                        <a href="/employee/profile/${employee.user_info.id}" class="btn btn-sm btn-primary" title="View"><i class="mdi mdi-eye text-white"></i> View</a>
                                                        <a href="/employee/${employee.user.id}" class="btn btn-sm btn-warning" title="Edit"><i class="mdi mdi-pencil"></i> Edit</a>
                                                        <a href="#" class="btn btn-sm btn-danger delete-employee" data-id="${employee.user.id}" title="Delete"><i class="mdi mdi-delete"></i> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="desktop-only-col">
                                            <span class="text-muted small d-inline-block text-truncate" style="max-width: 140px;" title="${empEmail}">${empEmail}</span>
                                        </td>
                                        <td class="desktop-only-col">
                                            <span class="text-truncate d-inline-block" style="max-width: 120px;" title="${empDept}">${empDept}</span>
                                        </td>
                                        <td class="desktop-only-col"><span class="badge badge-outline-secondary" style="font-size: 11px;">${empRole}</span></td>
                                        <td class="desktop-only-col text-center"><a href="#" class="open-leave-history text-decoration-none fw-bold text-success" data-id="${employee.user.id}" data-name="${empName}">${empRemPaid} <i class="mdi mdi-information-outline small text-muted"></i></a></td>
                                        <td class="desktop-only-col text-center"><a href="#" class="open-leave-history text-decoration-none fw-bold text-warning" data-id="${employee.user.id}" data-name="${empName}">${empRemSick} <i class="mdi mdi-information-outline small text-muted"></i></a></td>
                                        <td class="desktop-only-col text-center">${statusBadge}</td>
                                        <td class="desktop-only-col text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="/employee/profile/${employee.user_info.id}" class="text-primary fs-5 me-1" title="View Profile">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="/employee/${employee.user.id}" class="text-warning fs-5 me-1" title="Edit Employee">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <div class="dropdown d-inline-block">
                                                    <a href="javascript:void(0)" class="text-secondary fs-5" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 13px; z-index: 1060; border-radius: 8px;">
                                                        <li>
                                                            <a href="#" data-id="${employee.user.id}" data-name="${empName}" data-status="${empStatus}" data-reason="${empReason}" data-lastday="${empLastDay}" class="dropdown-item py-2 open-status-modal">
                                                                <i class="mdi mdi-account-cog text-info me-2 fs-6"></i> Change Status
                                                            </a>
                                                        </li>
                                                        ${!isInactive ? `
                                                        <li>
                                                            <a href="#" data-id="${employee.user.id}" data-name="${empName}" data-salary="${employee.user_info?.salary || 0}" data-last-date="${employee.user_info?.last_increment_date || ''}" class="dropdown-item py-2 open-increment-modal">
                                                                <i class="mdi mdi-cash-plus text-success me-2 fs-6"></i> Add Increment
                                                            </a>
                                                        </li>
                                                        ` : ''}
                                                        <li>
                                                            <a href="#" data-id="${employee.user.id}" data-name="${empName}" class="dropdown-item py-2 open-increment-history">
                                                                <i class="mdi mdi-history text-secondary me-2 fs-6"></i> Increment History
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-id="${employee.user.id}" data-name="${empName}" class="dropdown-item py-2 open-leave-history">
                                                                <i class="mdi mdi-calendar-clock text-success me-2 fs-6"></i> Leave History
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-id="${employee.user.id}" data-pass="${employee.user.password}" class="dropdown-item py-2 open-password-modal">
                                                                <i class="fa fa-key text-secondary me-2"></i> Change Password
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider my-1"></li>
                                                        <li>
                                                            <a href="#" class="dropdown-item py-2 text-danger delete-employee" data-id="${employee.user.id}">
                                                                <i class="mdi mdi-delete me-2 fs-6"></i> Delete Employee
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                       
                                        <td class="mobile-expand-col text-center">
                                            <button type="button" class="expand-toggle" data-target="emp-details-${employee.user.id}" aria-label="Expand details"></button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }

                        const $table = $('#employee-table');

                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }

                        $('#employee-table-body').html(tableRows);

                        setTimeout(() => {
                            $table.DataTable({
                                order: [
                                    [2, 'asc']
                                ],
                                columnDefs: [
                                    { targets: 0, visible: false, searchable: false },
                                    { targets: 9, orderable: false, searchable: false },
                                    { targets: 10, orderable: false, searchable: false }
                                ],
                                language: {
                                    search: "",
                                    searchPlaceholder: "Search"
                                }
                            });
                            if (typeof applyMobileTableVisibility === 'function') {
                                applyMobileTableVisibility();
                            }
                        }, 10);

                    } else {
                        $('#employee-table-body').html(`
                            <tr>
                                <td colspan="10" class="text-center text-muted">No employees found</td>
                            </tr>
                        `);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to fetch employees', 'error');
                }
            });
        }

        // ✅ Fetch department list
        function fetchDepartments() {
            $.ajax({
                url: '<?= base_url('/api/getdepartments') ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function (response) {
                    if (response.status && response.departments) {
                        let options = `<option value="">All Departments</option>`;
                        response.departments.forEach((dept) => {
                            options += `<option value="${dept.id}">${dept.department_name}</option>`;
                        });
                        $('#departmentFilter').html(options);
                    }
                },
                error: function () {
                    console.error('Failed to load departments');
                }
            });
        }

        // ✅ Filter change events
        function triggerFilter() {
            const selectedDeptId = $('#departmentFilter').val();
            const selectedMonth  = $('#monthFilter').val();
            const selectedYear   = $('#yearFilter').val();
            const activeView     = $('#employeeTabs .nav-link.active').data('view');
            fetchEmployees(selectedDeptId, activeView, selectedMonth, selectedYear);
        }

        $('#departmentFilter, #monthFilter, #yearFilter').on('change', function () {
            triggerFilter();
        });

        // ✅ Tab switch event
        $('#employeeTabs .nav-link').on('click', function () {
            $('#employeeTabs .nav-link')
                .removeClass('active')
                .css({'color': '#6c757d', 'border-bottom': 'none', 'font-weight': '600'});
            $(this)
                .addClass('active')
                .css({'color': '#E66136', 'border-bottom': '3px solid #E66136'});

            triggerFilter();
        });

        // 🚀 Initial calls
        fetchDepartments();
        fetchEmployees('', 'active');

        // 📥 Export to Excel functionality
        $('#btnExportEmployees').on('click', function () {
            const $btn = $(this);
            const departmentId = $('#departmentFilter').val() || '';
            const month = $('#monthFilter').val() || '';
            const year = $('#yearFilter').val() || '';
            const viewType = $('#employeeTabs .nav-link.active').data('view') || 'active';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({
                department_id: departmentId,
                month: month,
                year: year,
                view: viewType
            });

            fetch(`<?= base_url('api/employee/export') ?>?${queryParams.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(async response => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                if (!response.ok) {
                    const err = await response.json().catch(() => ({ message: 'Export failed' }));
                    throw new Error(err.message || 'Export failed');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const dateStr = new Date().toISOString().slice(0, 10);
                a.download = `Employees_${viewType.charAt(0).toUpperCase() + viewType.slice(1)}_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Employee list exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export employees', 'error');
            });
        });

        function showBsModal(modalId) {
            var el = document.getElementById(modalId);
            if (!el) return;
            el.removeAttribute('aria-hidden');
            el.setAttribute('aria-modal', 'true');
            el.classList.add('show');
            el.style.display = 'block';
            el.style.zIndex = '9999';
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
            if (!document.getElementById(modalId + '-backdrop')) {
                var bd = document.createElement('div');
                bd.id = modalId + '-backdrop';
                bd.className = 'modal-backdrop show';
                bd.style.cssText = 'opacity:0.5; z-index:9998;';
                document.body.appendChild(bd);
            }
        }
        // Make globally accessible for code outside $(document).ready()
        window.showBsModal = showBsModal;

        function hideBsModal(modalId) {
            var el = document.getElementById(modalId);
            if (!el) return;
            el.classList.remove('show');
            el.removeAttribute('aria-modal');
            el.setAttribute('aria-hidden', 'true');
            el.style.display = 'none';
            var bd = document.getElementById(modalId + '-backdrop');
            if (bd) bd.remove();
            if (!document.querySelector('.modal.show')) {
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.querySelectorAll('.modal-backdrop').forEach(function(b){ b.remove(); });
            }
        }
        // Make globally accessible for code outside $(document).ready()
        window.hideBsModal = hideBsModal;

        // Close modal on dismiss button click
        $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"]', function () {
            var $m = $(this).closest('.modal');
            if ($m.length) hideBsModal($m.attr('id'));
        });

        // Close modal when clicking the backdrop
        $(document).on('click', '.modal-backdrop', function () {
            var open = document.querySelector('.modal.show');
            if (open) hideBsModal(open.id);
        });

        // ══════════════════════════════════════════════════════
        // Status Change Modal Handler
        // ══════════════════════════════════════════════════════
        $(document).on('click', '.open-status-modal', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const userId = $(this).data('id');
            const name   = $(this).data('name');
            const status = $(this).data('status') || 'Active';
            const reason = $(this).data('reason') || '';
            const lwd    = $(this).data('lastday') || new Date().toISOString().split('T')[0];

            $('#status_user_id').val(userId);
            $('#status_employee_name').text(name);
            $('#status_select').val(status);
            $('#status_reason').val(reason);
            $('#status_last_working_day').val(lwd);

            showBsModal('employeeStatusModal');
        });

        $('#employeeStatusForm').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const token = localStorage.getItem('token');

            $.ajax({
                url: '<?= base_url("api/employee/updateStatus") ?>',
                type: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                data: formData,
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire('Updated!', res.message, 'success');
                        hideBsModal('employeeStatusModal');
                        triggerFilter();
                    } else {
                        Swal.fire('Error', res.message || 'Failed to update status', 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
                }
            });
        });

        // ══════════════════════════════════════════════════════
        // Month-wise Leave History Modal Handler
        // ══════════════════════════════════════════════════════
        $(document).on('click', '.open-leave-history', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const userId = $(this).data('id');
            const empName = $(this).data('name') || 'Employee';
            const token = localStorage.getItem('token');

            $('#lm-employee-name').text(empName);
            $('#lm-loader').show();
            $('#lm-empty').hide();
            $('#lm-table-wrapper').hide();
            $('#lm-tbody').empty();

            showBsModal('leaveHistoryMonthlyModal');

            $.ajax({
                url: `<?= base_url('api/employee/leaveHistoryMonthly') ?>/${userId}`,
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (res) {
                    $('#lm-loader').hide();
                    if (res.status && res.monthly_history) {
                        $('#lm-paid-leave').text(res.remaining_paid_leave || 0);
                        $('#lm-sick-leave').text(res.remaining_sick_leave || 0);

                        if (res.monthly_history.length === 0) {
                            $('#lm-empty').show();
                            return;
                        }

                        let rows = '';
                        res.monthly_history.forEach((m) => {
                            rows += `
                                <tr>
                                    <td class="fw-bold">${m.month_year}</td>
                                    <td class="text-success fw-bold">${m.paid_used} days</td>
                                    <td class="text-warning fw-bold">${m.sick_used} days</td>
                                    <td class="fw-bold">${m.total_days} days</td>
                                </tr>
                            `;
                        });
                        $('#lm-tbody').html(rows);
                        $('#lm-table-wrapper').show();
                    } else {
                        $('#lm-empty').show();
                    }
                },
                error: function () {
                    $('#lm-loader').hide();
                    $('#lm-empty').show();
                }
            });
        });


        $(document).on('click', '.delete-employee', function (e) {
            e.preventDefault();
            const employeeId = $(this).data('id');
            const employeeName = $(this).closest('tr').find('td:nth-child(2) span').text().trim()
                || $(this).closest('tr').find('.detail-value').first().text().trim()
                || 'this employee';

            // ⚠️ Detailed warning listing ALL data that will be permanently erased
            Swal.fire({
                title: '⚠️ Permanent Delete Warning',
                html: `
                    <div style="text-align:left; font-size:14px; line-height:1.7;">
                        <p>You are about to <strong>permanently delete</strong> the employee record for:</p>
                        <p style="font-size:16px; font-weight:700; color:#E66136; margin:6px 0 12px;">👤 ${employeeName || 'Employee #' + employeeId}</p>
                
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete Everything',
                cancelButtonText: 'No, Keep Employee',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn hr-btnbg',
                },
                width: '520px',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/employee/${employeeId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Employee and all related data have been permanently deleted.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: { confirmButton: 'btn hr-btnbg' }
                                }).then(() => {
                                    $(`tr[data-id="${employeeId}"]`).remove();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: { confirmButton: 'btn hr-btnbg' }
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'There was an error deleting the employee.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: { confirmButton: 'btn hr-btnbg' }
                            });
                        }
                    });
                }
            });
        });

    });
    $(document).on('click', '.toggle-password', function () {
        const input = $($(this).attr('toggle'));
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);

        // toggle icon
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    $(document).on('click', '.open-password-modal', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const userId = $(this).data('id');
        $('#password_user_id').val(userId);
        $('#password').val('');
        $('#confirm_password').val('');

        showBsModal('passwordModal');
    });

    $('#passwordForm').submit(function (e) {
        e.preventDefault();

        const userId = $('#password_user_id').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const token = localStorage.getItem('token');

        if (password !== confirmPassword) {
            Swal.fire('Error', 'Passwords do not match.', 'error');
            return;
        }

        fetch('/api/change-password-user', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId,
                password: password,
                confirm_password: confirmPassword
            }),
        })
            .then((res) => res.json())
            .then((res) => {
                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    hideBsModal('passwordModal');
                    $('#passwordForm')[0].reset();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Something went wrong', 'error');
            });
    });

    // ---- Salary Increment Modal ----
    $(document).on('click', '.open-increment-modal', function (e) {
        e.preventDefault();
        const userId   = $(this).data('id');
        const name     = $(this).data('name');
        const salary   = $(this).data('salary');
        const lastDate = $(this).data('last-date');

        // Format the last increment date nicely
        let formattedDate = 'No previous increment';
        if (lastDate && lastDate !== 'N/A' && lastDate !== 'null' && lastDate !== '') {
            const d = new Date(lastDate);
            if (!isNaN(d.getTime())) {
                formattedDate = d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
            }
        }

        $('#increment_user_id').val(userId);
        $('#increment_employee_name').text(name);
        $('#current_salary').val(parseFloat(salary || 0).toLocaleString('en-IN'));
        $('#last_increment_date').val(formattedDate);
        $('#increment_amount').val('');

        const modal = new bootstrap.Modal(document.getElementById('incrementModal'));
        modal.show();
    });


    // Use .off().on() to prevent duplicate event stacking on modal reuse
    $('#btnSaveIncrement').off('click').on('click', function () {
        const $btn            = $(this);
        const userId          = $('#increment_user_id').val();
        const incrementAmount = parseFloat($('#increment_amount').val());
        const incrementDate   = $('#increment_date').val() || new Date().toISOString().split('T')[0];
        const token           = localStorage.getItem('token');

        if (!incrementAmount || incrementAmount <= 0) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter a valid increment amount greater than 0.', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            return;
        }

        if (!incrementDate) {
            Swal.fire({ icon: 'warning', title: 'Required Field', text: 'Please select the effective date.', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            return;
        }

        // Disable button immediately to prevent double-click duplicate submissions
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '<?= base_url("/api/employee/increment-salary") ?>',
            type: 'POST',
            headers: { 'Authorization': `Bearer ${token}` },
            data: { user_id: userId, increment_amount: incrementAmount, increment_date: incrementDate },
            success: function (response) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-check me-1"></i> Update Salary');
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Salary incremented successfully!', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false })
                        .then(() => {
                            bootstrap.Modal.getInstance(document.getElementById('incrementModal')).hide();
                            fetchEmployees($('#departmentFilter').val());
                        });
                } else {
                    Swal.fire('Error', response.message || 'Failed to update salary', 'error');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="mdi mdi-check me-1"></i> Update Salary');
                Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        });
    });

    // ══════════════════════════════════════════════════════
    // Increment History Modal — AJAX fetch
    // ══════════════════════════════════════════════════════
    $(document).on('click', '.open-increment-history', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const userId   = $(this).data('id');
        const empName  = $(this).data('name') || 'Employee';
        const token    = localStorage.getItem('token');

        // Reset UI
        $('#ih-employee-name').text(empName);
        $('#ih-loader').show();
        $('#ih-empty').hide();
        $('#ih-table-wrapper').hide();
        $('#ih-tbody').empty();

        const modal = new bootstrap.Modal(document.getElementById('incrementHistoryModal'));
        modal.show();

        // Fetch increment history via AJAX
        $.ajax({
            url: `/api/employee/increment-history-user/${userId}`,
            type: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
            success: function (res) {
                $('#ih-loader').hide();

                const history = res.history || [];
                const name    = res.employee_name || empName;
                $('#ih-employee-name').text(name);

                if (history.length === 0) {
                    $('#ih-empty').show();
                    return;
                }

                let rows = '';
                history.forEach((item, idx) => {
                    const fmtDate = (d) => {
                        if (!d) return '-';
                        const parsed = new Date(d);
                        return isNaN(parsed) ? d : parsed.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                    };
                    const fmtMoney = (v) => parseFloat(v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const diff = parseFloat(item.increment_amount || 0);
                    const badge = diff > 0
                        ? `<span class="badge" style="background:#d4edda;color:#155724;font-size:.85em;">+&#8377;${fmtMoney(diff)}</span>`
                        : `<span class="badge bg-secondary">&#8377;${fmtMoney(diff)}</span>`;

                    rows += `
                        <tr>
                            <td class="ps-4 text-muted">${idx + 1}</td>
                            <td class="fw-semibold">${name}</td>
                            <td>&#8377;${fmtMoney(item.previous_salary)}</td>
                            <td>${badge}</td>
                            <td class="fw-bold" style="color:#E66136;">&#8377;${fmtMoney(item.new_salary)}</td>
                            <td>${fmtDate(item.effective_from_date)}</td>
                            <td class="text-muted">${fmtDate(item.created_at)}</td>
                            <td class="text-muted fst-italic">${item.remarks || '-'}</td>
                        </tr>`;
                });

                $('#ih-tbody').html(rows);
                $('#ih-table-wrapper').show();
            },
            error: function (xhr) {
                $('#ih-loader').hide();
                const msg = xhr.responseJSON?.message || 'Failed to load increment history.';
                $('#ih-empty').find('p').text(msg);
                $('#ih-empty').show();
            }
        });
    });
</script>

<?= $this->endSection(); ?>