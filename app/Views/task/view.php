<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .main-dec-div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* .filterdept select {
        margin-top: .5rem;
    } */

    .filterbtn {
        margin-top: 0.1rem;
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
    .form-select{
        height: 2.44rem;
    }


    @media (max-width: 767.98px) {
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
            font-size: 14px !important;
            margin: 7px !important;
        }

        .filterbtnpadd {
            padding: 2px !important;
        }

        .filtermenu {
            margin-bottom: 12px !important;
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
            margin-top: -0.5rem !important;
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
            /* margin-left: -3rem !important;  */
            float: left !important;
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
        }

        #task-table_length label {
            margin-top: 1px;
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

        #task-table_filter label {
            font-size: 0;
        }

        #task-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #task-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #task-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        /* div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 2.05rem !important;
        } */


    }
     @media (min-width: 767px) {
        div.dataTables_wrapper div.dataTables_filter label input {
            width: 226px !important;
        }
    }
    .capitalize-text {
        text-transform: capitalize;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="subtaskModal" tabindex="-1" aria-labelledby="subtaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg d-flex justify-content-center align-items-center"> <!-- Centered Modal -->
        <div class="modal-content rounded-3">
            <div class="modal-header text-center"> <!-- Centered Header Text -->
                <h5 class="modal-title" id="subtaskModalLabel">Add Subtasks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Subtask form inside modal -->
                <form id="subtaskForm" enctype="multipart/form-data">
                    <div class="row justify-content-center"> <!-- Center content horizontally -->
                        <div class="col-12">
                            <!-- Hidden Inputs -->
                            <input type="hidden" id="subtask_user_id" value="">
                            <input type="hidden" id="main_task_id" value="">
                            <div class="row">
                                <!-- Employee Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Employee Name</label>
                                    <input type="text" class="form-control" id="user_id" readonly>
                                </div>

                                <!-- Task Title -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Task Title</label>
                                    <input type="text" class="form-control" id="task_title" readonly>
                                </div>
                            </div>

                            <!-- Dynamic Subtask Fields -->
                            <div id="subtaskGroupContainer">
                                <div class="subtask-group mb-3 border p-3 rounded position-relative">
                                    <div class="row g-2 align-items-start">
                                        <div class="col-md-12">
                                            <!-- Header row with title and remove icon -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">SubTask Details</label>
                                                <button type="button" class="btn btn-link text-danger p-0 remove-subtask" title="Remove Subtask">
                                                    <i class="mdi mdi-close fs-4"></i>
                                                </button>
                                            </div>
                                            <hr>

                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">SubTask Title</label>
                                                    <input type="text" name="subtask_title[]" class="form-control" placeholder="Subtask Title">

                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Assigned Date</label>
                                                    <input type="date" name="subtask_assigned_date[]" class="form-control">

                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Due Date</label>
                                                    <input type="date" name="subtask_due_date[]" class="form-control">

                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Upload Files</label>
                                                    <input type="file" name="files[]" class="form-control" data-index="${subtaskIndex}" multiple>

                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">SubTask Status</label>
                                                    <select name="subtask_status[]" class="form-select">
                                                        <option value="Pending">Pending</option>
                                                        <option value="In-Progress">In-Progress</option>
                                                        <option value="Completed">Completed</option>
                                                    </select>
                                                </div>
                                                <div class="invalid-feedback"></div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description[]" class="form-control" placeholder="Description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Add More Button -->
                            <button type="button" id="addMoreSubtask" class="btn hr-btnbg mt-2">+ Add More</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer"> <!-- Center footer buttons -->
                <button type="button" class="btn hr-btnbg rounded" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn hr-btnbg" id="saveSubtaskBtn">Save Subtasks</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between mb-3">

                    <h4 class="card-title">Manage Tasks</h4>

                    <div class="d-md-flex gap-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="In-Progress">In-Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        
                        <?php $role = session()->get('role'); ?>
                        <?php if ($role !== 'employee') : ?>
                            <a href="/task" class="btn hr-btnbg attendenceall text-nowrap">
                                <i class="mdi mdi-plus iconfontsize"></i> Add Task
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="task-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Title</th>
                                <th class="desktop-only-col">Assigned Date</th>
                                <th class="desktop-only-col">Due Date</th>
                                <th class="desktop-only-col">Status</th>
                                <th style="display: none;">Created At</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="task-table tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token

        // Get user role from the JWT payload
        function getUserRole() {
            let payload = JSON.parse(atob(token.split('.')[1])); // Decode JWT
            return payload.role; // Extract user role
        }

        function fetchTasks(statusFilter = '') {
            $.ajax({
                url: '<?= base_url('/api/task/getAll') ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const tasks = response.data;
                        let tableRows = '';
                        let userRole = getUserRole(); // Get role from JWT

                        // Filter tasks by status if a filter is applied
                        const filteredTasks = statusFilter ?
                            tasks.filter(task => task.task_status === statusFilter) :
                            tasks;

                        filteredTasks.forEach((task) => {
                            let actionButtons = '';

                            if (userRole !== 'employee') {
                                actionButtons = `
                            <a href="#" 
                            class="fs-5 subtask-btn"
                            style="color: #E66136;" 
                            data-id="${task.id}" 
                            data-username="${task.username}" 
                            data-task_title="${task.task_title}" 
                            data-user_id="${task.user_id}" 
                            title="Add Subtask" 
                            data-bs-toggle="modal" 
                            data-bs-target="#subtaskModal">
                            <i class="mdi mdi-plus-box"></i>
                            </a>
                            <a href="/task/profile/${task.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye" ></i></a>
                            <a href="/task/${task.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                            <a href="#" class="text-danger fs-5 delete-task" data-id="${task.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                        `;
                            } else {
                                actionButtons = `
                            <a href="/task/profile/${task.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                        `;
                            }

                            let progressBarClass = '';
                            let progressValue = 0;
                            let progressText = '';

                            if (task.task_status === 'Pending') {
                                progressValue = 25;
                                progressBarClass = 'bg-danger';
                                progressText = '25%';
                            } else if (task.task_status === 'In-Progress') {
                                progressValue = 50;
                                progressBarClass = 'bg-warning';
                                progressText = '50%';
                            } else if (task.task_status === 'Completed') {
                                progressValue = 100;
                                progressBarClass = 'bg-success';
                                progressText = '100%';
                            }

                            let statusDisplay = `
                        <div>
                            <div class="progress mb-1" style="height: 20px;">
                                <div class="progress-bar ${progressBarClass}" role="progressbar" style="width: ${progressValue}%;" aria-valuenow="${progressValue}" aria-valuemin="0" aria-valuemax="100">
                                    ${progressText}
                                </div>
                            </div>
                    `;

                            if (userRole !== 'employee') {
                                statusDisplay += `
                            <select class="form-select status-dropdown" data-id="${task.id}">
                                <option value="Pending" ${task.task_status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="In-Progress" ${task.task_status === 'In-Progress' ? 'selected' : ''}>In-Progress</option>
                                <option value="Completed" ${task.task_status === 'Completed' ? 'selected' : ''}>Completed</option>
                            </select>
                        `;
                            }

                            statusDisplay += '</div>';

                            // Mobile action buttons
                            let mobileActionsHtml = '';
                            if (userRole !== 'employee') {
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="#" class="btn btn-sm subtask-btn" style="background: #E66136; color: white;" data-id="${task.id}" data-username="${task.username}" data-task_title="${task.task_title}" data-user_id="${task.user_id}" data-bs-toggle="modal" data-bs-target="#subtaskModal"><i class="mdi mdi-plus-box"></i> Subtask</a>
                                        <a href="/task/profile/${task.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                        <a href="/task/${task.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-task" data-id="${task.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                    </div>
                                `;
                            } else {
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/task/profile/${task.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                    </div>
                                `;
                            }

                            tableRows += `
                        <tr data-id="${task.id}">
                            <td class="py-1">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <a href="/task/profile/${task.id}" class="text-decoration-none">
                                        <img src="/upload/${task.profile_image || 'default-profile.jpg'}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    </a>
                                    <div style="flex: 1;">
                                        <a href="/task/profile/${task.id}" class="text-decoration-none text-dark">
                                            <span class="capitalize-text">${task.username}</span>
                                        </a>
                                        <div class="expanded-details" id="task-details-${task.id}" onclick="event.stopPropagation();">
                                            <div class="detail-row">
                                                <span class="detail-label">Title:</span>
                                                <span class="detail-value">${task.task_title}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Assigned Date:</span>
                                                <span class="detail-value">${task.assigned_date}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Due Date:</span>
                                                <span class="detail-value">${task.due_date}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Status:</span>
                                                <span class="detail-value">${task.task_status}</span>
                                            </div>
                                            ${mobileActionsHtml}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="desktop-only-col capitalize-text">${task.task_title}</td>
                            <td class="desktop-only-col capitalize-text">${task.assigned_date}</td>
                            <td class="desktop-only-col capitalize-text">${task.due_date}</td>
                            <td class="desktop-only-col py-1">${statusDisplay}</td>
                            <td style="display: none;">${task.created_at}</td>
                            <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px; padding-bottom: 38px;">
                                ${actionButtons}
                            </td>
                            <td class="mobile-expand-col text-center">
                                <button type="button" class="expand-toggle" data-target="task-details-${task.id}" aria-label="Expand details"></button>
                            </td>
                        </tr>
                    `;
                        });
                        const $table = $('#task-table');
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }

                        $('#task-table tbody').html(tableRows);
                        setTimeout(() => {
                            $table.DataTable({
                                order: [
                                    [5, 'desc']
                                ],
                                columnDefs: [
                                    {
                                        targets: 5, // created_at
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
                                    searchPlaceholder: "Search",
                                }
                            });
                            // Apply mobile visibility
                            if (typeof applyMobileTableVisibility === 'function') {
                                applyMobileTableVisibility();
                            }
                        }, 10);

                        // $('#task-table').DataTable(); // Reinitialize
                    } else {
                        Swal.fire('Error', 'Failed to load task records', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch task records', 'error');
                }
            });
        }

        // Call the fetchTasks function on page load
        fetchTasks();
        $(document).on('change', '.status-dropdown', function() {
            let taskId = $(this).data('id');
            let newStatus = $(this).val();
            console.log(newStatus);
            updateTaskStatus(taskId, newStatus);
        });

        function updateTaskStatus(taskId, newStatus) {
            const token = localStorage.getItem('token'); // JWT token
            $.ajax({
                url: `<?= base_url('/api/task/updateStatus') ?>`, // API endpoint for updating status
                type: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                data: JSON.stringify({
                    id: taskId,
                    status: newStatus
                }),
                success: function(response) {
                    if (response.status === 'success') {
                        // Display success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated',
                            text: 'Subtask status updated successfully!',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#E66136'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });

                        // Update the status dropdown with the new status
                        $(`tr[data-id="${taskId}"] .status-dropdown`).val(newStatus);

                        // Re-render the progress bar dynamically
                        updateProgressBar(taskId, newStatus);


                    } else {
                        Swal.fire('Error', 'Failed to update task status', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to update task status', 'error');
                }
            });
        }

        // Function to update the progress bar
        function updateProgressBar(taskId, newStatus) {
            let progressBarClass = '';
            let progressValue = 0;
            let progressText = '';

            if (newStatus === 'Pending') {
                progressValue = 25;
                progressBarClass = 'bg-danger';
                progressText = '25%';
            } else if (newStatus === 'In-Progress') {
                progressValue = 50;
                progressBarClass = 'bg-warning';
                progressText = '50%';
            } else if (newStatus === 'Completed') {
                progressValue = 100;
                progressBarClass = 'bg-success';
                progressText = '100%';
            }

            // Dynamically update the progress bar for the specific task
            $(`tr[data-id="${taskId}"] .progress-bar`)
                .removeClass('bg-danger bg-warning bg-success') // Remove existing classes
                .addClass(progressBarClass) // Add new class based on status
                .css('width', `${progressValue}%`) // Update the width of the progress bar
                .text(progressText); // Update the text inside the progress bar
        }

        // Handle delete action
        $(document).on('click', '.delete-task', function(e) {
            e.preventDefault();
            const taskId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                    cancelButton: 'hr-btnbg',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/task/${taskId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', 'The task record has been deleted.', 'success').then(() => {
                                    $(`tr[data-id="${taskId}"]`).remove();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    responseData.message, // Show the actual message from server
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error deleting the city. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message; // Get actual error message
                            }

                            Swal.fire(
                                'Error!',
                                errorMessage, // Show dynamic error message
                                'error'
                            );
                        }
                    });
                }
            });
        });
        // Filter change listener
        $('#statusFilter').on('change', function() {
            const selectedStatus = $(this).val(); // e.g., 'completed'
            fetchTasks(selectedStatus); // Fetch filtered tasks
        });
    });
    $(document).on('click', '.subtask-btn', function() {
        const taskId = $(this).data('id');
        const userId = $(this).data('user_id');
        const userName = $(this).data('username');
        const taskTitle = $(this).data('task_title');

        // Set fields in modal
        $('#user_id').val(userName); // Display name (readonly)
        $('#task_title').val(taskTitle); // Display task title

        // Set hidden fields for use in AJAX
        $('#subtask_user_id').val(userId);
        $('#main_task_id').val(taskId);
    });
    $(document).ready(function() {
        let subtaskIndex = 1;

        $('#addMoreSubtask').on('click', function() {
            const html = `
        <div class="subtask-group mb-3 border p-3 rounded position-relative" data-index="${subtaskIndex}">
            <div class="row g-2 align-items-start">
                <div class="col-md-12">
                    <!-- Header row with title and remove icon -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">SubTask Details</label>
                        <button type="button" class="btn btn-link text-danger p-0 remove-subtask" title="Remove Subtask">
                            <i class="mdi mdi-close fs-4"></i>
                        </button>
                    </div>
                    <hr>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">SubTask Title</label>
                            <input type="text" name="subtask_title[]" class="form-control" placeholder="Subtask Title" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Assigned Date</label>
                            <input type="date" name="subtask_assigned_date[]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="subtask_due_date[]" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                        <label class="form-label">Upload Files</label>
                        <input type="file" name="files_${subtaskIndex}[]" class="form-control" multiple>
                    </div>
                        <div class="col-md-4">
                            <label class="form-label">SubTask Status</label>
                            <select name="subtask_status[]" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="In-Progress">In-Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <textarea name="description[]" class="form-control" placeholder="Description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
            $('#subtaskGroupContainer').append(html);
            subtaskIndex++;

        });

        // Remove subtask on click
        $(document).on('click', '.remove-subtask', function() {
            $(this).closest('.subtask-group').remove();
        });
    });


    $('#saveSubtaskBtn').on('click', function() {
        const formData = new FormData();

        const subtasks = [];

        $('#subtaskGroupContainer .subtask-group').each(function(index) {
            const subtask = {
                subtask_title: $(this).find('input[name="subtask_title[]"]').val(),
                subtask_assigned_date: $(this).find('input[name="subtask_assigned_date[]"]').val(),
                subtask_due_date: $(this).find('input[name="subtask_due_date[]"]').val(),
                subtask_status: $(this).find('select[name="subtask_status[]"]').val(),
                description: $(this).find('textarea[name="description[]"]').val()
            };

            subtasks.push(subtask);

            const fileInput = $(this).find('input[type="file"]')[0];
            if (fileInput && fileInput.files.length > 0) {
                for (let i = 0; i < fileInput.files.length; i++) {
                    formData.append(`files[${index}][]`, fileInput.files[i]);
                }
            }
        });

        formData.append('subtasks', JSON.stringify(subtasks));
        formData.append('task_id', $('#main_task_id').val() || 0);
        formData.append('user_id', $('#subtask_user_id').val() || 0);

        $.ajax({
            url: '<?= base_url("api/subtasks/create") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg'
                    }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 400 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.messages;

                    // 1. Clear previous errors
                    $('#subtaskForm .form-control, #subtaskForm .form-select').removeClass('is-invalid');
                    $('#subtaskForm .invalid-feedback').remove(); // Remove old feedback

                    // 2. Display errors below respective fields
                    $.each(errors, function(fieldName, message) {
                        // If field is an array like subtask_title[], remove [] for name selector
                        const cleanedName = fieldName.replace(/\[\]/g, '');

                        // Select all matching fields
                        const $fields = $(`[name="${fieldName}"], [name="${cleanedName}[]"]`);

                        $fields.each(function() {
                            const $input = $(this);
                            $input.addClass('is-invalid');

                            // Only add if not already present
                            if ($input.next('.invalid-feedback').length === 0) {
                                $input.after(`<div class="invalid-feedback">${message}</div>`);
                            }
                        });
                    });

                    // 3. Scroll to first error field
                    const $firstError = $('.is-invalid').first();
                    if ($firstError.length) {
                        $('html, body').animate({
                            scrollTop: $firstError.offset().top - 100
                        }, 500);
                    }
                } else {
                    Swal.fire('Error', 'Server error occurred.', 'error');
                }
            }
        });
    });
</script>

<?= $this->endSection(); ?>