<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>

<style>
    #employee-table thead th {
        background-color: #000 !important;
        color: #fff !important;
        padding: 12px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
    }

    .profile-img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .action-icons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .action-icons a {
        font-size: 18px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-add-emp {
        background-color: #f05a28;
        color: #fff;
        border-radius: 5px;
        padding: 8px 15px;
        font-weight: 600;
    }

    .btn-add-emp:hover {
        background-color: #d84b1d;
        color: #fff;
    }

    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
        min-width: 85px;
        text-align: center;
    }

    .status-pending {
        background-color: #ffc107;
        color: #000;
    }

    .status-approved {
        background-color: #28a745;
        color: #fff;
    }

    .status-rejected {
        background-color: #dc3545;
        color: #fff;
    }

    .status-cancelled {
        background-color: #6c757d;
        color: #fff;
    }

    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    /* Horizontal scrolling for REASON column */
    .reason-wrap {
        white-space: nowrap !important;
        overflow-x: auto;
        min-width: 200px;
        max-width: 350px;
        line-height: 1.6;
        padding-bottom: 5px;
    }

    /* Custom scrollbar for reason-wrap */
    .reason-wrap::-webkit-scrollbar {
        height: 4px;
    }

    .reason-wrap::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .reason-wrap::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #employee-table thead th:nth-child(7),
    #employee-table thead th:nth-child(8) {
        text-align: center;
    }

    #employee-table tbody td:nth-child(7),
    #employee-table tbody td:nth-child(8) {
        text-align: center;
        vertical-align: middle;
    }

    /* Align headers for Status and Action */
    #employee-table thead th:nth-child(7),
    #employee-table thead th:nth-child(8) {
        text-align: center !important;
    }

    .action-icons-wrapper {
        display: flex;
        justify-content: center;
        gap: 12px;
        align-items: center;
        width: 100%;
        margin: 0 auto;
    }

    .action-icons-wrapper a {
        font-size: 19px;
        display: inline-flex;
        transition: opacity 0.2s;
        text-decoration: none;
    }

    .status-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Employee Leave Requests</h4>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="departmentFilter" style="width: 180px;">
                            <option value="">All Departments</option>
                        </select>
                        <select class="form-select" id="employeeFilter" style="width: 200px;">
                            <option value="">All Employees</option>
                        </select>
                        <a href="<?= base_url('/addleave') ?>" class="btn btn-add-emp">
                            + Add Leave
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table w-100" id="employee-table">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>NAME</th>
                                <th>START DATE</th>
                                <th>END DATE</th>
                                <th>DAYS</th>
                                <th>REASON</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="employee-table-body">
                            <!-- Data will be populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const token = localStorage.getItem('token');

        function capitalizeFirstLetter(string) {
            return string ? string.charAt(0).toUpperCase() + string.slice(1).toLowerCase() : 'N/A';
        }

        function fetchLeaves() {
            const employeeId = $('#employeeFilter').val();
            const departmentId = $('#departmentFilter').val();

            $.ajax({
                url: '<?= base_url('/api/get-leaves') ?>',
                type: 'GET',
                data: { employee_id: employeeId },
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (response) {
                    if (response.status === 'success' && response.data) {
                        let tableRows = '';
                        response.data.forEach((leave) => {
                            const empName = `${capitalizeFirstLetter(leave.firstname)} ${capitalizeFirstLetter(leave.lastname)}`;
                            const profileImg = leave.profile_image ? `<?= base_url('upload/') ?>${leave.profile_image}` : '<?= base_url(env('ImagePath') . '/upload/default-profile.jpg') ?>';
                            const statusClass = `status-${leave.status.toLowerCase()}`;

                            tableRows += `
                                <tr data-id="${leave.id}">
                                    <td style="display:none;">${leave.id}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="${profileImg}" class="profile-img" alt="Profile" onerror="this.src='<?= base_url(env('ImagePath') . '/upload/default-profile.jpg') ?>'">
                                            <span>${empName}</span>
                                        </div>
                                    </td>
                                    <td>${leave.start_date}</td>
                                    <td>${leave.end_date}</td>
                                    <td>${leave.no_of_day}</td>
                                    <td><div class="reason-wrap">${leave.reason || 'N/A'}</div></td>
                                    <td><div class="status-wrapper"><span class="status-badge ${statusClass}">${leave.status}</span></div></td>
                                    <td>
                                        <div class="action-icons-wrapper">
                                            ${leave.status.toLowerCase() === 'pending' ? `
                                                <a href="#" class="confirm-btn text-success approve-leave" data-id="${leave.id}" title="Approve">
                                                    <i class="mdi mdi-check-circle"></i>
                                                </a>
                                                <a href="#" class="reject-btn text-danger reject-leave" data-id="${leave.id}" title="Reject">
                                                    <i class="mdi mdi-close-circle"></i>
                                                </a>
                                            ` : ''}
                                            <a href="#" class="delete-btn text-danger delete-leave" data-id="${leave.id}" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        const $table = $('#employee-table');
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }

                        $('#employee-table-body').html(tableRows);

                        $table.DataTable({
                            order: [[0, 'desc']],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            },
                            columnDefs: [
                                { targets: 0, visible: false },
                                { targets: 7, orderable: false },
                                // ✅ Add these alignment fixes:
                                { targets: '_all', className: 'align-middle' },
                                { targets: 6, className: 'align-middle text-center' },  // STATUS
                                { targets: 7, className: 'align-middle text-center' }   // ACTION
                            ]
                        });
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Failed to fetch leave records', 'error');
                }
            });
        }

        // Populate Filters
        function populateFilters() {
            // Departments
            $.ajax({
                url: '<?= base_url('/api/getdepartments') ?>',
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (res) {
                    if (res.status && res.departments) {
                        res.departments.forEach(d => $('#departmentFilter').append(`<option value="${d.id}">${d.department_name}</option>`));
                    }
                }
            });

            // Employees
            $.ajax({
                url: '<?= base_url('/api/employees') ?>',
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (res) {
                    if (res.status && res.employees) {
                        res.employees.forEach(e => {
                            const name = `${e.user_info.firstname} ${e.user_info.lastname}`;
                            $('#employeeFilter').append(`<option value="${e.user.id}">${name}</option>`);
                        });
                    }
                }
            });
        }

        populateFilters();
        fetchLeaves();

        $('#departmentFilter, #employeeFilter').on('change', function () {
            fetchLeaves();
        });
$(document).on('click', '.approve-leave', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    Swal.fire({
        title: 'Process Leave Request?',
        text: "Would you like to approve or reject this leave request?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Approve',
    }).then((result) => {
        let newStatus = '';
        if (result.isConfirmed) {
            newStatus = 'approved';
        }
        if (newStatus) {
            $.ajax({
                url: `<?= base_url('/api/leave/update') ?>/${id}`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ status: newStatus }),
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire(newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + '!', `Leave request has been ${newStatus}.`, 'success');
                        fetchLeaves();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
});
        // Reject Leave
        $(document).on('click', '.reject-leave', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            Swal.fire({
                title: 'Reject Leave?',
                text: "Do you want to reject this leave request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                confirmButtonText: 'Yes, reject!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('/api/leave/update') ?>/${id}`,
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ status: 'rejected' }),
                        headers: { 'Authorization': `Bearer ${token}` },
                        success: function (res) {
                            if (res.status === 'success') {
                                Swal.fire('Rejected!', 'Leave request has been rejected.', 'success');
                                fetchLeaves();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Delete Leave
        $(document).on('click', '.delete-leave', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This leave record will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('/api/leave') ?>/${id}`,
                        type: 'DELETE',
                        headers: { 'Authorization': `Bearer ${token}` },
                        success: function (res) {
                            if (res.status === 'success') {
                                Swal.fire('Deleted!', 'Leave record has been deleted.', 'success');
                                fetchLeaves();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>