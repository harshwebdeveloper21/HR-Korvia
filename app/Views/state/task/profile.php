<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url(
    env("ImagePath") . "assets/css/task_profile.css",
) ?>">

<style>
    .attachmnet {
        margin-left: 4px;
        margin-bottom: 4px;
    }

    .tabsubpad {
        padding: 4px;
    }

    .table.employee-table thead th {
        background-color: #ffffff !important;
        /* White background */
        color: #000000 !important;
        /* Black text */
        vertical-align: middle;
        text-align: left;
    }

    .sm-font-status {
        font-size: 14px;
        font-weight: 600;
        padding: 10px;
    }


    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 5px !important;
            margin-top: 10px !important;
        }

        .tab-content {
            overflow: hidden;
        }

        .tab-pane {
            overflow-x: auto;

        }

        .font-size-candidate {
            font-size: 10px !important;
        }

        .info-box {
            padding: 0px !important;
        }

        .task-sm-margin {
            margin-top: 0px !important;
        }

        .tabsubpad {
            padding-top: 4px !important;
        }

        .sm-font-status {
            font-size: 12px !important;
        }

        .cart-sm-title {
            font-size: 14px !important;
            margin-bottom: 5px !important;
            margin-top: 10px !important;
        }
    }
    .capitalize-text {
        text-transform: capitalize;
    }
</style>
<div class="modal fade" id="editSubtaskModal" tabindex="-1" aria-labelledby="editSubtaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subtask</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSubtaskForm">
                    <input type="hidden" id="subtask_id">
                    <div class="mb-3">
                        <label class="form-label">Subtask Title</label>
                        <input type="text" class="form-control" id="subtask_title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigned Date</label>
                        <input type="date" class="form-control" id="subtask_assigned_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="subtask_due_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Files</label>
                        <input type="file" name="files[]" class="form-control" id="files" multiple>

                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="subtask_status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Reassign">Reassign</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn hr-btnbg">Update Subtask</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container my-4 bg-white p-4 rounded shadow-sm">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-dark">
            <i class="fas fa-tasks me-2 iconfontsize" style="color: #E66136;"></i> Task Details
        </h4>
        <a href="<?= base_url(
            "/taskview",
        ) ?>" class="btn hr-btnbg interviewsmbtn">
            <i class="mdi mdi-arrow-left me-1 iconfontsize"></i> Back
        </a>
    </div>
    <div class="row g-4">
        <!-- Left Column: Employee Info -->
        <div class="col-md-12 col-lg-12 col-xl-6">
            <div class="info-box">
                <div class="section-title"><i class="fas fa-user-circle me-2" style="color: #E66136;"></i> Employee
                    Information</div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-account-outline me-1" style="color: #E66136;"></i> Name:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="employeeName"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-email-outline me-1" style="color: #E66136;"></i> Email:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate" id="employeeEmail"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-card-account-details-outline me-1" style="color: #E66136;"></i> Employee ID:
                    </label>
                    <div class="col-sm-7 col-6 font-size-candidate">
                        <div class="info-value font-size-candidate" id="employee_id"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-briefcase-outline me-1" style="color: #E66136;"></i> Designation:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="employeeDesignation"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-domain me-1" style="color: #E66136;"></i> Department:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="employeeDepartment"></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Right Column: Task Info -->
        <div class="col-md-12 task-sm-margin col-lg-12 col-xl-6">
            <div class="info-box">
                <div class="section-title"><i class="fas fa-tasks me-2" style="color: #E66136;"></i> Task Information
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-clipboard-text-outline me-1" style="color: #E66136;"></i> Task Title:
                    </label>
                    <div class="col-sm-7 col-6">
                        <span class="info-value font-size-candidate capitalize-text" id="task_title"></span>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-progress-clock me-1" style="color: #E66136;"></i> Status:
                    </label>
                    <div class="col-sm-7 col-6">
                        <span class="status-badge font-size-candidate capitalize-text" id="task_status"></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-calendar-plus-outline me-1" style="color: #E66136;"></i> Assigned Date:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="assigned_date"></div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-calendar-range-outline me-1" style="color: #E66136;"></i> Due Date:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="due_date"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-5 info-label col-6 font-size-candidate">
                        <i class="mdi mdi-account-tie-outline me-1" style="color: #E66136;"></i> Assigned by:
                    </label>
                    <div class="col-sm-7 col-6">
                        <div class="info-value font-size-candidate capitalize-text" id="created_by_username"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
   <div class="attachmnet">
        <div class="section-title">
            <i class="mdi mdi-paperclip me-1" style="color: #E66136;"></i> Attachment
            <!-- <a href="#" class="float-end" id="downloadAll">Download All</a> -->
        </div>

        <div class="d-flex flex-wrap gap-3 mt-3 attachment-container">
            <!-- JavaScript will inject files here -->
        </div>
    </div>


    <div class="bg-white tabsubpad rounded shadow-sm">
        <!-- Tabs -->
        <ul class="nav nav-tabs tab-container" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="subtasks-tab" data-bs-toggle="tab" data-bs-target="#subtasks"
                    type="button" role="tab">
                    <i class="mdi mdi-format-list-checks me-1" style="color: #E66136;"></i> Subtasks
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="Files-tab" data-bs-toggle="tab" data-bs-target="#Files" type="button"
                    role="tab">
                    <i class="mdi mdi-comment-text-multiple-outline me-1" style="color: #E66136;"></i> Files
                </button>
            </li>

        </ul>


        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Subtasks Tab -->
            <div class="tab-pane fade show active" id="subtasks" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="mdi mdi-format-list-checks me-1" style="color: #E66136;"></i> All Subtasks
                    </h6>
                    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                        <div class="circle-indicator"></div>
                        <span class="progress-status">2/4</span>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table employee-table table-bordered">
                        <thead class="">
                            <tr>
                                <th class="sm-font-status">
                                    <i class="fas fa-check-square"></i> Status
                                </th>
                                <th class="sm-font-status">
                                    <i class="fas fa-file-alt"></i> Subtask Title
                                </th>
                                <th class="sm-font-status">
                                    <i class="fas fa-calendar-plus"></i> Assigned Date
                                </th>
                                <th class="sm-font-status">
                                    <i class="fas fa-calendar-check"></i> Due Date
                                </th>
                                <th class="sm-font-status">
                                    <i class="fas fa-tools"></i> Action
                                </th>
                            </tr>
                        </thead>
                        <tbody id="subtaskList">
                            <!-- Dynamic rows go here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Files Tab -->
            <div class="tab-pane fade" id="Files" role="tabpanel" aria-labelledby="Files-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="mdi mdi-file-document-outline me-1" style="color: #E66136;"></i> Files
                    </h6>
                </div>
                <hr>
                <div id="file-details-container"></div>
            </div>

            <!-- Activities Tab -->
            <div class="tab-pane fade" id="activities" role="tabpanel">
                <p class="text-muted mt-3">Recent activity will appear here.</p>
            </div>
        </div>

    </div>
</div>
<!-- commnet -->
<div class="container my-4 bg-white p-4 rounded shadow-sm">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="">
                <!-- Comments Section -->
                <h6 class="fw-bold mt-3"><i class="bi bi-chat-dots text-dark"></i> Comments</h6>
                <div class="comment-box p-3 mt-2">
                    <div class="comment-list" id="commentList">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment d-flex">

                                <?php $profileImage = !empty(
                                    $comment["profile_image"]
                                )
                                    ? $comment["profile_image"]
                                    : "1789966027_54c5a38ccda20f7c2bac.jpg"; ?>
                                <img src="<?= base_url(
                                    "upload/" . $profileImage,
                                ) ?>" alt="User" class="profile-pic me-2 rounded-circle" style="width:40px; height:40px; object-fit:cover;">

                                <div>
                                    <strong><?= esc(
                                        $comment["firstname"],
                                    ) ?></strong>
                                    <p class="mb-1"><?= $comment[
                                        "comment"
                                    ] ?></p>
                                    <small class="text-muted"><?= $comment[
                                        "created_at"
                                    ] ?></small>
                                </div>

                            </div>
                            <hr>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Add Comment Section -->
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <input type="hidden" id="taskId" value="<?= $taskId ?>">
                        <textarea id="commentText" class="form-control comment-input me-2" rows="2" placeholder="Write a comment..."></textarea>
                    </div>
                    <button id="addComment" class="btn hr-btnbg mt-2"><i class="bi bi-send"></i>Commnet</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        const userId = window.location.pathname.split('/').pop();
        // Define the formatDate function first
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString(); // You can customize format here
        }

        $.ajax({
            url: `<?= site_url("task/details") ?>/${userId}`,
            type: "GET",
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    let data = response.data;

                    // $("#employee_id").text(data.employee_id ?? 'N/A');
                    $("#employee_id").text(data.employee_id ? 'EMP#' + data.employee_id : 'N/A');
                    $("#employeeEmail").text(data.email ?? 'N/A'); // optional if email available
                    $("#employeeDesignation").text(data.designation_title ?? 'N/A');
                    $("#employeeDepartment").text(data.department_name ?? 'N/A');
                    let profileImage = data.profile_image ?
                        `<?= base_url("upload/") ?>${data.profile_image}` :
                        `<?= base_url("upload/1789966027_54c5a38ccda20f7c2bac.jpg") ?>`;

                    $("#employeeImage").attr('src', profileImage);

                    // Task fields
                    $("#employeeName").text(data.employee_name ?? 'N/A');
                    $("#task_title").text(data.task_title ?? 'N/A');
                    $("#assigned_date").text(data.assigned_date ?? 'N/A');
                    $("#description").text(data.description ?? 'N/A');
                    $("#due_date").text(data.due_date ?? 'N/A');
                    $("#task_status").text(data.task_status ?? 'N/A');
                    $(".task_title").text(data.task_title ?? 'N/A');
                    $("#created_by_username").text(data.created_by_username ?? 'N/A');

                    if (response.status === 'success') {
                        let filesHtml = '';
                        const subtasks = response.subtasks;

                        if (subtasks.length > 0) {
                            subtasks.forEach(function(subtask) {
                                if (Array.isArray(subtask.files) && subtask.files.length > 0) {
                                    subtask.files.forEach(function(file) {
                                        let icon = getIcon(file.name); // Reuse your icon function
                                        filesHtml += `
                                        <div class="file-box d-flex flex-column align-items-center justify-content-between text-center">
                                            <div class="file-icon mb-2">${icon}</div>
                                            <div class="file-name mb-1 capitalize-text">${file.name}</div>
                                            <small><a href="${file.url}" class="download-link" download="${file.name}">Download</a></small>
                                        </div>
                                    `;
                                    });
                                }
                            });
                        } else {
                            filesHtml = '<p>No files available for the selected subtask.</p>';
                        }

                        $('#file-details-container').html(filesHtml);
                    } else {
                        $('#file-details-container').html('<p>Error loading subtasks or files.</p>');
                    }


                    // Clear previous subtasks
                    $("#subtaskList").empty();

                    let totalSubtasks = response.subtasks.length;
                    let completedSubtasks = response.subtasks.filter(sub => sub.subtask_status === 'Completed').length;
                    $('.progress-status').text(`${completedSubtasks}/${totalSubtasks}`);

                    if (response.subtasks && response.subtasks.length > 0) {
                        response.subtasks.forEach(sub => {
                            const isChecked = sub.subtask_status === 'Completed' ? 'checked' : '';
                            const statusRaw = sub.subtask_status || 'Pending';
                            const statusLabel = statusRaw.charAt(0).toUpperCase() + statusRaw.slice(1); // Capitalize status

                            const row = `
                                <tr>
                                    <td><input type="checkbox" class="subtask-check" data-id="${sub.id}" ${isChecked}></td>
                                    <td class="capitalize-text">${sub.subtask_title}</td>
                                    <td class="capitalize-text">${sub.subtask_assigned_date}</td>
                                    <td class="capitalize-text">${sub.subtask_due_date}</td>
                                     <td>
                                        <a href="javascript:void(0);"
                                            class="text-warning fs-5 edit-subtask"
                                            title="Edit"
                                            data-id="${sub.id}"
                                            data-title="${sub.subtask_title}"
                                            data-assigned="${sub.subtask_assigned_date}"
                                            data-due="${sub.subtask_due_date}"
                                            data-status="${sub.subtask_status}">
                                                <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="#" class="text-danger fs-5 delete-subtask" data-id="${sub.id}" title="Delete"><i class="mdi mdi-delete"></i></a>

                                    </td>
                                    </tr>
                                `;
                            $("#subtaskList").append(row);
                        });
                    } else {
                        $("#subtaskList").append(`
                            <tr>
                                <td colspan="4" class="text-muted">No subtasks available for this task.</td>
                            </tr>
                            `);
                    }

                    // Document Attachments
                    $(".attachment-container").empty(); // Clear previous

                    if (data.documents && data.documents.length > 0) {
                        data.documents.forEach((doc) => {
                            let icon = getIcon(doc.name);
                            $(".attachment-container").append(`
                                <div class="attachment-box d-flex flex-column align-items-center justify-content-between text-center">
                                    <div class="attachment-icon mb-2">${icon}</div>
                                    <div class="attachment-name mb-1 capitalize-text">${doc.name}</div>
                                    <small><a href="${doc.url}" class="download-link" download="${doc.name}">Download</a></small>
                                </div>
                            `);
                        });
                    } else {
                        $(".attachment-container").html('<p class="text-muted">No attachments available.</p>');
                    }
                    // Render Comments
                    let commentBox = $("#commentBox");
                    commentBox.empty();

                    if (response.comments && response.comments.length > 0) {
                        let table = `
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">Image</th>
                                        <th>Username</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        response.comments.forEach(comment => {
                            let userImage = comment.profile_image ?
                                `<?= base_url(
                                    "upload/",
                                ) ?>${comment.profile_image}` :
                                `<?= base_url("upload/1789966027_54c5a38ccda20f7c2bac.jpg") ?>`;

                            table += `
                                <tr>
                                    <td><img src="${userImage}" class="rounded-circle" width="45" height="45" style="object-fit: cover;" alt="User Image"></td>
                                    <td class="capitalize-text">${comment.username}</td>
                                    <td class="capitalize-text">${comment.comment}</td>
                                    <td>${formatDate(comment.created_at)}</td>
                                </tr>
                            `;
                        });

                        table += `
                        </tbody>
                    </table>
                `;

                        commentBox.html(table);
                    } else {
                        commentBox.html('<p class="text-muted mt-3">No comments added yet.</p>');
                    }
                    $("#error-message").hide();
                } else {
                    $("#error-message").text('task record not found. Please check the ID and try again.').show();
                }
            },
            error: function(xhr) {
                if (xhr.status === 404) {
                    $("#error-message").text('task record not found. Please check the ID and try again.').show();
                } else {
                    $("#error-message").text('An error occurred while loading task data. Please try again later.').show();
                }
            }
        });
    });
    let getIcon = (filename) => {
        let ext = filename.split('.').pop().toLowerCase();
        switch (ext) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'ðŸ–¼ï¸';
            case 'pdf':
                return 'ðŸ“„';
            case 'doc':
            case 'docx':
                return 'ðŸ“';
            case 'xls':
            case 'xlsx':
                return 'ðŸ“Š';
            case 'zip':
            case 'rar':
                return 'ðŸ—œï¸';
            case 'txt':
                return 'ðŸ“ƒ';
            default:
                return 'ðŸ“';
        }
    };
    $(document).on('click', '.edit-subtask', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const assigned = $(this).data('assigned');
        const due = $(this).data('due');
        const status = $(this).data('status');

        $('#subtask_id').val(id);
        $('#subtask_title').val(title);
        $('#subtask_assigned_date').val(assigned);
        $('#subtask_due_date').val(due);
        $('#subtask_status').val(status);

        $('#editSubtaskModal').modal('show');
    });

    $('#editSubtaskForm').on('submit', function(e) {
        e.preventDefault();

        const id = $('#subtask_id').val();
        const formData = new FormData();

        formData.append('id', id);
        formData.append('subtask_title', $('#subtask_title').val());
        formData.append('subtask_assigned_date', $('#subtask_assigned_date').val());
        formData.append('subtask_due_date', $('#subtask_due_date').val());
        formData.append('subtask_status', $('#subtask_status').val());

        let files = $('#files')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        $.ajax({
            url: `/api/subtasks/update/${id}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    $('#editSubtaskModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Refresh page after alert
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: response.message || 'Something went wrong.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Failed to update subtask. Please try again.'
                });
            }
        });
    });

    $(document).on('change', '.subtask-check', function() {
        const $checkbox = $(this);
        const subtaskId = $checkbox.data('id');
        const isChecked = $checkbox.is(':checked');
        const originalStatus = isChecked ? 'Pending' : 'Completed'; // reversed to simulate trying to change back
        const status = isChecked ? 'Completed' : 'Pending';
        const token = localStorage.getItem('token');

        // If trying to uncheck a completed subtask, prevent it
        if (!isChecked && originalStatus === 'Completed') {
            Swal.fire({
                icon: 'info',
                title: 'Not Allowed',
                text: 'This subtask has already been completed and cannot be unchecked.',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                }

            });
            $checkbox.prop('checked', true); // revert back to checked
            return;
        }

        $.ajax({
            url: `<?= site_url("api/subtask/update-status") ?>/${subtaskId}`,
            type: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            data: JSON.stringify({
                subtask_status: status
            }),
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Subtask status completed successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#E66136'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to update subtask status.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something went wrong while updating.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#E66136'
                });
            }
        });
    });

    $(document).ready(function() {

        $('#addComment').on('click', function() {
            var commentText = $('#commentText').val().trim();
            var taskId = $('#taskId').val();
            var $button = $(this); // Reference the button

            if (commentText !== '') {
                $button.prop('disabled', true); // Disable the button to prevent multiple clicks

                $.ajax({
                    url: '<?= site_url("api/task/addComment") ?>',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_id: taskId,
                        comment: commentText
                    },
                    success: function(response) {
                        if (response.success) {
                            var comment = response.comment;
                            var profileImage = comment.profile_image ? comment.profile_image : '1789966027_54c5a38ccda20f7c2bac.jpg';
                            var html = `
                                <div class="comment d-flex">
                                    <img src="<?= base_url(
                                        "upload/",
                                    ) ?>${profileImage}" alt="User" class="profile-pic me-2 rounded-circle" style="width:40px; height:40px; object-fit:cover;">
                                    <div>
                                        <strong class="capitalize-text">${comment.firstname}</strong>
                                        <p class="mb-1 capitalize-text">${comment.comment}</p>
                                        <small class="text-muted">Just now</small>
                                    </div>
                                </div>
                                <hr>
                            `;
                            $('#commentList').append(html);
                            $('#commentText').val('');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to add comment!',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg'
                                }
                            });

                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Something went wrong!',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg'
                            }
                        });
                    },
                    complete: function() {
                        $button.prop('disabled', false); // Re-enable button after request
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Comment',
                    text: 'Please write something before submitting!',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg'
                    }
                });
            }
        });
        // Handle delete action
        $(document).on('click', '.delete-subtask', function(e) {
            const token = localStorage.getItem('token'); // JWT token

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
                        url: `<?= base_url("api/subtask/") ?>${taskId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The subtask record has been deleted.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'hr-btnbg'
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'hr-btnbg'
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error deleting the subtask. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg'
                                }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
