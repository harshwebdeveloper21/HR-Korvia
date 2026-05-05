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
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <div class="align-items-center d-md-flex justify-content-between mb-3">

                    <h4 class="card-title">Manage Employees</h4>
                    <div class="d-md-flex gap-2">
                        <select class="form-select" id="departmentFilter">
                            <option value="">All Departments</option>
                        </select>
                        <a href="/employee" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Employee
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="employee-table">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Name</th>
                                <th class="desktop-only-col">Email</th>
                                <th class="desktop-only-col">Department</th>
                                <th class="desktop-only-col">Role</th>
                                <th class="desktop-only-col">Rem. Paid Leave</th>
                                <th class="desktop-only-col">Rem. Sick Leave</th>
                                <th class="desktop-only-col">Action</th>
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
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
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

<!-- Salary Increment Modal -->
<div class="modal fade" id="incrementModal" tabindex="-1" aria-labelledby="incrementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#E66136; color:white;">
                <h5 class="modal-title" id="incrementModalLabel">
                    <i class="mdi mdi-trending-up me-1"></i> Salary Increment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="incrementForm">
                    <input type="hidden" name="increment_user_id" id="increment_user_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Employee:</label>
                        <span id="increment_employee_name" class="ms-1"></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Current Salary (&#8377;):</label>
                            <input type="text" class="form-control bg-light" id="current_salary" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Increment Date:</label>
                            <input type="text" class="form-control bg-light" id="last_increment_date" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="increment_amount" class="form-label fw-bold">New Increment Amount (&#8377;) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-plus"></i></span>
                            <input type="number" class="form-control" id="increment_amount" name="increment_amount" placeholder="e.g. 2000" min="1" required>
                        </div>
                        <small class="text-muted">This amount will be added to the current salary.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Effective From Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="increment_date" name="increment_date" required value="<?= date('Y-m-d') ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnSaveIncrement" style="background:#E66136; border-color:#E66136;">
                    <i class="mdi mdi-check me-1"></i> Update Salary
                </button>
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
        function fetchEmployees(departmentId = '') {
            $.ajax({
                url: '<?= base_url('/api/employees') ?>',
                type: 'GET',
                data: departmentId ? {
                    department_id: departmentId
                } : {},

                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function (response) {
                    if (response.status && response.employees) {
                        const employees = response.employees;
                        let tableRows = '';


                        if (employees.length === 0) {
                            // tableRows += `
                            //     <tr>
                            //         <td colspan="6" class="text-center text-muted">No employees found</td>
                            //     </tr>
                            // `;
                        } else {

                            employees.forEach((employee) => {
                                const empName = `${capitalizeFirstLetter(employee.user_info.firstname || 'N/A')} ${capitalizeFirstLetter(employee.user_info.lastname || '')}`;
                                const empEmail = employee.user?.email || 'N/A';
                                const empDept = employee.user_info?.department_name || 'N/A';
                                const empRole = employee.user?.role ? employee.user.role.charAt(0).toUpperCase() + employee.user.role.slice(1) : 'N/A';
                                const empRemPaid = employee.user_info?.remaining_paid_leave !== undefined ? employee.user_info.remaining_paid_leave : 0;
                                const empRemSick = employee.user_info?.remaining_sick_leave !== undefined ? employee.user_info.remaining_sick_leave : 0;

                                tableRows += `
                                    <tr data-id="${employee.user.id}">
                                        <td style="display:none;">${employee.user.id}</td>
                                        <td class="py-1">
                                            <div class="d-flex align-items-start align-items-baseline">
                                                <a href="/employee/profile/${employee.user_info.id}" class="text-decoration-none">
                                                    <img src="${employee.user_info.profile_image_url}" alt="Profile" width="40" height="40" class="rounded-circle me-2"
                                                        onerror="this.onerror=null; this.src='<?= base_url(env('ImagePath') . '/upload/default-profile.jpg') ?>';">
                                                </a>
                                                <a href="/employee/profile/${employee.user_info.id}" class="text-decoration-none text-dark">
                                                    <span>${empName}</span>
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
                                                        <span class="detail-value">${empRemPaid}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Rem. Sick Leave:</span>
                                                        <span class="detail-value">${empRemSick}</span>
                                                    </div>
                                                    <div class="detail-actions">
                                                        <a href="#" data-id="${employee.user.id}" data-name="${empName}" data-salary="${employee.user_info.salary || 0}" data-last-date="${employee.user_info.last_increment_date || 'N/A'}" class="btn btn-sm btn-success open-increment-modal" title="Increment"><i class="mdi mdi-cash-plus"></i> Increment</a>
                                                        <a href="#" data-id="${employee.user.id}" data-pass="${employee.user.password}" class="btn btn-sm btn-secondary open-password-modal" title="Password"><i class="fa fa-key"></i> Password</a>
                                                        <a href="/employee/profile/${employee.user_info.id}" class="btn btn-sm btn-primary" title="View"><i class="mdi mdi-eye text-white"></i> View</a>
                                                        <a href="/employee/${employee.user.id}" class="btn btn-sm btn-warning" title="Edit"><i class="mdi mdi-pencil"></i> Edit</a>
                                                        <a href="#" class="btn btn-sm btn-danger delete-employee" data-id="${employee.user.id}" title="Delete"><i class="mdi mdi-delete"></i> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="desktop-only-col">${empEmail}</td>
                                        <td class="desktop-only-col">${empDept}</td>
                                        <td class="desktop-only-col">${empRole}</td>
                                        <td class="desktop-only-col">${empRemPaid}</td>
                                        <td class="desktop-only-col">${empRemSick}</td>
                                        <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                            <a href="#" data-id="${employee.user.id}" data-name="${empName}" data-salary="${employee.user_info.salary || 0}" data-last-date="${employee.user_info.last_increment_date || 'N/A'}" class="text-success fs-5 open-increment-modal" title="Salary Increment">
                                                <i class="mdi mdi-cash-plus"></i>
                                            </a>
                                            <a href="#" data-id="${employee.user.id}" data-pass="${employee.user.password}" class="text-primary fs-5 open-password-modal" title="Password">
                                                <i class="fa fa-key" aria-hidden="true"></i>
                                            </a>
                                            <a href="/employee/profile/${employee.user_info.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                            <a href="/employee/${employee.user.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                            <a href="#" class="text-danger fs-5 delete-employee" data-id="${employee.user.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                                        </td>
                                        <td class="mobile-expand-col text-center">
                                            <button type="button" class="expand-toggle" data-target="emp-details-${employee.user.id}" aria-label="Expand details"></button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        // console.log(tableRows);


                        const $table = $('#employee-table');

                        // Destroy if already initialized
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }

                        // Inject rows into <tbody>
                        $('#employee-table-body').html(tableRows);

                        // Delay to let DOM fully update
                        setTimeout(() => {
                            $table.DataTable({
                                order: [
                                    [5, 'desc']
                                ],
                                columnDefs: [
                                    {
                                        targets: 0,
                                        visible: false,
                                        searchable: false
                                    },
                                    {
                                        targets: 7, // mobile expand column
                                        orderable: false,
                                        searchable: false
                                    }
                                ],
                                language: {
                                    search: "",
                                    searchPlaceholder: "Search"
                                }
                            });
                            // Apply mobile visibility
                            if (typeof applyMobileTableVisibility === 'function') {
                                applyMobileTableVisibility();
                            }
                        }, 10);

                    } else {
                        $('#employee-table-body').html(`
                            <tr>
                                <td colspan="6" class="text-center text-muted">No employees found</td>
                            </tr>
                        `);
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to fetch employee', 'error');
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

        // ✅ Department filter change event
        $('#departmentFilter').on('change', function () {
            const selectedDeptId = $(this).val();
            fetchEmployees(selectedDeptId); // Pass selected department ID
        });

        // 🚀 Initial calls
        fetchDepartments();
        fetchEmployees(); // Load all employees initially


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
        const userId = $(this).data('id');
        const userpss = $(this).data('pass'); // fix here

        $('#password_user_id').val(userId);
        // $('#password').val(userpss); // populate password field
        // $('#confirm_password').val(userpss); // populate password field

        const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
        modal.show();
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                    modal.hide();
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

</script>

<?= $this->endSection(); ?>