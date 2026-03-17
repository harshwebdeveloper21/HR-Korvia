<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit SubTask</h4>
                <form class="form-sample" method="POST" id="updateSubtaskForm">
                     <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" name="id" id="id" value=""> <!-- Hidden input for id -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Employee Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="user_id" id="user_id">
                                            <option value="" disabled selected>Select Employee Name</option>
                                            <?php foreach ($employees as $employee) : ?>
                                                <option value="<?= $employee['id']; ?>"><?= esc($employee['username']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="error" id="Error-user_id"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Task</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="task_id" id="task_id">
                                            <option value="" disabled selected>Select Task</option>

                                        </select>
                                    </div>
                                    <div class="error" id="Error-task_id"></div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">SubTask Title</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-pencil fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="subtask_title" id="subtask_title" placeholder="Enter SubTask Title" />
                                    </div>
                                    <div class="error" id="Error-subtask_title"></div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">SubTask Status</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle fs-5"></i>
                                            </span>
                                        </div>
                                        <select class="form-select" name="subtask_status" id="subtask_status">
                                            <option value="" disabled selected>Select Task Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="In-Progress">In-Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Reassign">Reassign</option> <!-- New option added -->
                                        </select>
                                    </div>
                                    <div class="error" id="Error-subtask_status"></div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">SubTask Assigned Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="subtask_assigned_date" id="subtask_assigned_date" />
                                    </div>
                                    <div class="error" id="Error-subtask_assigned_date"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">SubTask Due Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-check fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="subtask_due_date" id="subtask_due_date" />
                                    </div>
                                    <div class="error" id="Error-subtask_due_date"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('/all_subtask') ?>" class="btn hr-btnbg">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        const url = window.location.pathname;
        const segments = url.split('/');
        const accountId = segments[segments.length - 1]; // Get ID from URL

        if (!isNaN(accountId)) {
            $.ajax({
                url: "<?= site_url('api/subtask-detail-get') ?>/" + accountId,
                type: "GET",
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        const data = response.data;
                        $('#id').val(data.id);
                        $('#user_id').val(data.user_id);

                        // Load tasks for the user
                        loadTasksForUser(data.user_id, data.task_id);

                        $('#subtask_title').val(data.subtask_title);
                        $('#subtask_status').val(data.subtask_status);
                        $('#subtask_assigned_date').val(data.subtask_assigned_date);
                        $('#subtask_due_date').val(data.subtask_due_date);
                    } else {
                        $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#responseMessage').html('<div class="alert alert-danger">Something went wrong while fetching data.</div>');
                }
            });
        }

        // On user change
        $('#user_id').on('change', function() {
            var userId = $(this).val();
            loadTasksForUser(userId);
        });

        function loadTasksForUser(userId, selectedTaskId = null) {
            $('#task_id').empty();
            $('#task_id').append('<option value="" disabled selected>Loading tasks...</option>');

            if (userId) {
                $.ajax({
                    url: "<?= base_url('api/subtasks/get-tasks-by-user') ?>/" + userId,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        $('#task_id').empty();

                        if (response.length > 0) {
                            $.each(response, function(key, task) {
                                const selected = (selectedTaskId && selectedTaskId == task.id) ? 'selected' : '';
                                $('#task_id').append('<option value="' + task.id + '" ' + selected + '>' + task.task_title + '</option>');
                            });
                        } else {
                            $('#task_id').append('<option value="" disabled>No tasks available</option>');
                        }
                    }
                });
            } else {
                $('#task_id').empty();
                $('#task_id').append('<option value="" disabled selected>Select Task</option>');
            }
        }
    });
    $('#updateSubtaskForm').on('submit', function(e) {
        e.preventDefault();

        const token = localStorage.getItem('token');
        const formData = {
            id: $('#id').val(),
            user_id: $('#user_id').val(),
            task_id: $('#task_id').val(),
            subtask_title: $('#subtask_title').val(),
            subtask_status: $('#subtask_status').val(),
            subtask_assigned_date: $('#subtask_assigned_date').val(),
            subtask_due_date: $('#subtask_due_date').val()
        };
        
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
        $('#loader').show();

        $.ajax({
            url: "<?= site_url('api/subtask-update') ?>",
            type: "PUT",
            headers: {
                'Authorization': `Bearer ${token}`
            },
            contentType: "application/json",
            data: JSON.stringify(formData),
            dataType: "json",
            success: function(response) {
                $('#loader').hide();

                if (response.status) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect after success message
                        window.location.href = "<?= site_url('all_subtask') ?>";
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                $('#loader').hide();

                Swal.fire('Error', 'Something went wrong while updating the subtask.', 'error');
            }
        });
    });
</script>
<?= $this->endSection(); ?>