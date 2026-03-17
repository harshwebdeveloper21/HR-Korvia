<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .main-dec-div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filterdept select {
        margin-top: .5rem;
    }

    .filterbtn {
        margin-top: 1.1rem;
    }

    .filtermenu {
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .departmrgin {
        margin-right: 10px !important;
    }
      select#statusFilter {
        background: white;
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
            padding: 5px !important;
            font-size: 10px !important;
            margin: 7px !important;
        }

        .filterbtnpadd {
            padding: 2px !important;
            /* margin-left: 112px !important; */

        }

        .filtermenu {
            margin-bottom: 12px !important;
            /* margin-left:-54px !important; */
            justify-content: space-between;
            width: 100%;
        }

        .fontsmfiltertitle {
            font-size: 13px !important;
        }

        .departmrgin {
            font-size: 11px !important;
        }

        .filterbtn {
            margin-top: .5rem !important;
        }

        .filtermarginjob {
            margin-bottom: 10px !important;
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
            margin-left: -5rem !important;
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
        }

        #task-table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #task-table_length label::first-text,
        #task-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #task-table_length label {
            font-size: 0;
            /* hide text */
        }

        #task-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #task-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #task-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        #task-table_filter label {
            font-size: 0;
        }

        #task-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        .form-control {
            height: 0px !important;
        }

        .attendencepaddbottom {
            margin-bottom: 5px !important
        }
        .sm-font-size-filter {
            font-size: 12px !important;
            margin-right: 5px !important;
        }

        .fonsize-titile-sm {
            font-size: 13px !important;
        }

        /* div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 29px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        } */
    }
    .capitalize-text {
        text-transform: capitalize;
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between mb-3">
                    <h4 class="card-title filtermarginjob">Manage SubTasks</h4>
                    <div class="d-md-flex gap-2">                         
                        <select class="form-select" id="statusFilter">
                            <option value="All">All SubTask</option>
                            <option value="Pending">Pending</option>
                            <option value="Reassign">Reassign</option>
                            <option value="In-Progress">In-Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="task-table">
                        <thead class="table-light">
                            <tr>
                                <th>Employee Name</th>
                                <th>Task</th>
                                <th>SubTask Title</th>
                                <th>Assigned Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="action-column" style="width: 130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    const token = localStorage.getItem('token');

    function getUserRole() {
        if (!token) return '';
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.role;
    }

    const userRole = getUserRole();

    // Initial fetch
    fetchTasks();

    // Handle filter change
    $('#statusFilter').on('change', function () {
        fetchTasks($(this).val());
    });

    // Fetch and render tasks
    function fetchTasks(statusFilter = 'All') {
        $.ajax({
            url: '<?= base_url('api/subtask/getAll') ?>',
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function (response) {
                if (response.status === 'success' && Array.isArray(response.data)) {
                    let rows = '';
                    const baseImagePath = "<?= base_url(env('ImagePath')) ?>";
                    const tasks = statusFilter === 'All' ? response.data : response.data.filter(task => task.subtask_status === statusFilter);

                    tasks.forEach(task => {
                        const profileImage = task.profile_image ? `upload/${task.profile_image}` : `${baseImagePath}upload/default-profile.jpg`;
                        const taskUrl = `/task/profile/${task.id}`;
                        const subtaskUrl = `/subtask/profile/${task.id}`;
                        const actionButtons = userRole !== 'employee'
                            ? `<a href="#" class="text-danger fs-5 delete-subtask" data-id="${task.id}" title="Delete"><i class="mdi mdi-delete"></i></a>`
                            : `<a href="${subtaskUrl}" class="text-primary fs-5 d-none" title="View"><i class="mdi mdi-eye"></i></a>`;

                        const progressMap = {
                            'Pending': [25, 'bg-danger', '25%'],
                            'Reassign': [50, 'bg-warning', '50%'],
                            'In-Progress': [75, 'bg-primary', '75%'],
                            'Completed': [100, 'bg-success', '100%']
                        };

                        const [progressValue, progressClass, progressLabel] = progressMap[task.subtask_status] || [0, '', ''];

                        const statusHTML = `
                            <div class="mb-1">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar ${progressClass}" role="progressbar" style="width: ${progressValue}%;" aria-valuenow="${progressValue}" aria-valuemin="0" aria-valuemax="100">${progressLabel}</div>
                                </div>
                            </div>
                            <div>
                                <select class="form-select form-select-sm subtask-status-dropdown" data-id="${task.id}">
                                    ${Object.keys(progressMap).map(status => `
                                        <option value="${status}" ${task.subtask_status === status ? 'selected' : ''}>${status}</option>
                                    `).join('')}
                                </select>
                            </div>`;

                        rows += `
                            <tr data-id="${task.id}">
                                <td class="py-1">
                                    <a href="${taskUrl}" class="text-decoration-none text-dark d-flex align-items-center gap-2">
                                        <img src="${profileImage}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <span class="capitalize-text">${task.username}</span>
                                    </a>
                                </td>
                                <td class="capitalize-text">${task.task_title}</td>
                                <td class="capitalize-text">${task.subtask_title}</td>
                                <td class="capitalize-text">${task.subtask_assigned_date}</td>
                                <td class="capitalize-text">${task.subtask_due_date}</td>
                                <td class="py-1">${statusHTML}</td>
                                <td class="action-column d-flex align-items-center" style="gap: 8px; padding-bottom: 38px">${actionButtons}</td>
                            </tr>`;
                    });

                    // Reset and re-render table
                    if ($.fn.DataTable.isDataTable('#task-table')) {
                        $('#task-table').DataTable().clear().destroy();
                    }

                    $('#task-table tbody').html(rows);

                    //Hide action column for employees
                    if (userRole === 'employee') {
                        $('.action-column').hide();
                    } else {
                        $('.action-column').show();
                    }

                    $('#task-table').DataTable({
                        language: {
                            search: '',
                            searchPlaceholder: 'Search'
                        }
                    });
                } else {
                    Swal.fire('Error', 'No subtask data found.', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch subtasks.', 'error');
            }
        });
    }

    // Status update handler
    $(document).on('change', '.subtask-status-dropdown', function () {
        const taskId = $(this).data('id');
        const newStatus = $(this).val();

        $.ajax({
            url: '<?= base_url('api/subtask/updateStatus') ?>',
            type: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            data: JSON.stringify({ id: taskId, status: newStatus }),
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire('Updated!', 'Subtask status updated.', 'success');
                } else {
                    Swal.fire('Error', 'Failed to update status', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server error occurred.', 'error');
            }
        });
    });

    // Delete subtask
    $(document).on('click', '.delete-subtask', function (e) {
        e.preventDefault();
        const taskId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('api/subtask/') ?>${taskId}`,
                    type: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', 'The subtask record has been deleted.', 'success').then(() => fetchTasks($('#statusFilter').val()));
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'There was an error deleting the subtask.';
                        if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    });
});
</script>


<?= $this->endSection(); ?>