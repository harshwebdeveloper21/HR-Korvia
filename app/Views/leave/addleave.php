<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    

    @media (max-width: 576px) {
        .leave-sm-emp {
            min-height: 24px;
            display: flex;
            align-items: center;
        }
        .form-label {
            font-size: 14px;
        }
    }
</style>
<div class="modal fade" id="addLeaveModal" tabindex="-1" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveModalLabel">Add Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="AddLeaveForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Leave Type</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="leave_type" id="leave_type" placeholder="Enter Leave type" />
                        </div>
                        <small class="text-danger error" id="leaveError"></small>
                    </div>
                    <div class="mb-3">
                        <label for="address_details" class="form-label">Number Of Leaves</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-calendar-range fs-5"></i></span>
                            </div>

                            <input type="number" class="form-control" name="number_of_leaves" id="number_of_leaves" placeholder="Enter Total " />
                        </div>
                    </div>
                    <button class="btn hr-btnbg float-end" id="addsubmitBtn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title sm-padding-leave">Add Leave</h4>
                <form class="form-sample" method="POST" id="LeaveForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="row gy-3">
                        <!-- Employee Name -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">Employee Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                <?php if ($role == 'admin' || $role == 'hr') : ?>
                                    <select class="form-select" name="user_id">
                                        <option value="" disabled selected>Select Employee</option>
                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?= esc($user['id']); ?>"><?= esc($user['username']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input type="text" class="form-control" value="<?= esc($users[0]['username']); ?>" readonly>
                                    <input type="hidden" name="user_id" value="<?= esc($users[0]['id']); ?>">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label leave-sm-emp mb-0">Leave Type</label>

                                <?php if ($role == 'admin' || $role == 'hr') : ?>
                                    <button type="button"
                                        class="btn btn-sm d-flex align-items-center rounded"
                                        style="background-color:#E66136; color:#fff;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addLeaveModal">
                                        <i class="mdi mdi-plus me-1"></i> Add Leave Type
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="input-group mt-2">
                                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                <select class="form-select" name="leave_id">
                                    <option value="" disabled selected>Select Leave Type</option>
                                    <?php foreach ($leaveTypes as $leaveType) : ?>
                                        <option value="<?= $leaveType['id']; ?>">
                                            <?= esc($leaveType['leave_type']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">Reason</label>
                            <textarea class="form-control" name="reason" placeholder="Enter Reason" rows="4"></textarea>
                        </div>

                        <!-- Leave Duration -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">Duration</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-clock-outline fs-5"></i></span>
                                <select class="form-select" name="leave_duration" id="leave_duration">
                                    <option value="full_day" selected>Full Day</option>
                                    <option value="half_day">Half Day</option>
                                </select>
                            </div>
                        </div>

                        <!-- Half Day Type -->
                        <div class="col-md-6 d-none" id="half_day_type_container">
                            <label class="form-label leave-sm-emp">Half Day Type</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-clock-fast fs-5"></i></span>
                                <select class="form-select" name="half_day_type" id="half_day_type">
                                    <option value="first_half">First Half</option>
                                    <option value="second_half">Second Half</option>
                                </select>
                            </div>
                        </div>

                        <!-- No. of Days -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">No. of Days</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-calendar-range fs-5"></i></span>
                                <input type="number" class="form-control" name="no_of_day" id="no_of_day" placeholder="Enter Number of Days" step="0.5" />
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                <input type="date" class="form-control" name="start_date" id="start_date" />
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <label class="form-label leave-sm-emp">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                <input type="date" class="form-control" name="end_date" id="end_date" />
                            </div>
                        </div>
                        <?php if ($role == 'admin' || $role == 'hr') : ?>
                            <div class="col-md-6">
                                <label class="form-label leave-sm-emp">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                    <select class="form-select" name="status" id="status">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="approved">approved</option>
                                        <option value="rejected">rejected</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-4 text-end">
                        <a href="<?= base_url('leaveview') ?>" class="btn hr-btnbg interviewsmbtn me-2">Back</a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn">Submit</button>
                    </div>

                    <div id="responseMessage" class="mt-2"></div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("start_date").setAttribute("min", today);
        
        const leaveDuration = document.getElementById('leave_duration');
        const halfDayTypeContainer = document.getElementById('half_day_type_container');
        const noOfDaysInput = document.getElementById('no_of_day');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        leaveDuration.addEventListener('change', function() {
            if (this.value === 'half_day') {
                halfDayTypeContainer.classList.remove('d-none');
                noOfDaysInput.value = '0.5';
                noOfDaysInput.setAttribute('readonly', 'true');
                if (startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
                endDateInput.setAttribute('readonly', 'true');
                // Reset styling if it was invalid before
                endDateInput.classList.remove('is-invalid');
                noOfDaysInput.classList.remove('is-invalid');
            } else {
                halfDayTypeContainer.classList.add('d-none');
                noOfDaysInput.removeAttribute('readonly');
                endDateInput.removeAttribute('readonly');
            }
        });

        startDateInput.addEventListener('change', function() {
            if (leaveDuration.value === 'half_day') {
                endDateInput.value = this.value;
            }
        });
    });
    $(document).ready(function () {
    const token = localStorage.getItem('token'); // JWT token
    const form = document.querySelector('#LeaveForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default submission

        const formData = new FormData(form);
        $('form .is-invalid').removeClass('is-invalid');
        $('form .invalid-feedback').remove();

        let hasError = false;

        const requiredFields = [
            'user_id',
            'leave_id',
            'reason',
            'no_of_day',
            'start_date',
            'end_date',
        ];

        // If user is admin or HR, validate status too
        const role = "<?= $role ?>";
        if (role === 'admin' || role === 'hr') {
            requiredFields.push('status');
        }

        requiredFields.forEach((field) => {
            const fieldElement = document.querySelector(`[name="${field}"]`);
            if (fieldElement) {
                const value = fieldElement.value.trim();
                if (!value) {
                    hasError = true;
                    fieldElement.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    errorDiv.innerText = 'This field is required.';
                    fieldElement.parentElement.appendChild(errorDiv);
                }
            }
        });

        if (hasError) {
            return; // Stop submission if validation fails
        }

        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Append CSRF token
        data['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';

        // Send POST request
        fetch('/api/leave', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then(async (response) => {
                const responseData = await response.json();
                if (!response.ok) {
                    if (response.status === 400 && responseData.errors) {
                        // Server-side validation error handling
                        Object.entries(responseData.errors).forEach(([field, errorMsg]) => {
                            const inputField = document.querySelector(`[name="${field}"]`);
                            if (inputField) {
                                inputField.classList.add('is-invalid');
                                let errorDiv = document.createElement('div');
                                errorDiv.classList.add('invalid-feedback');
                                errorDiv.innerText = errorMsg;
                                inputField.parentElement.appendChild(errorDiv);
                            }
                        });
                        throw new Error('Validation failed');
                    }
                    throw new Error(responseData.message || `HTTP error! Status: ${response.status}`);
                }
                return responseData;
            })
            .then((responseData) => {
                if (responseData.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: responseData.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.href = "/leaveview";
                    });

                    form.reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: responseData.message,
                    });
                }
            })
            .catch((error) => {
                console.error('Error submitting leave record:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again later.',
                });
            });
    });
});


    $(document).ready(function() {
        $("#AddLeaveForm").submit(function(e) {
            e.preventDefault(); // Prevent default form submission
            $(".error").html(""); // Clear previous validation errors

            // var formData = $(this).serialize(); // Serialize form data
            const form = $(this);
            const formData = form.serializeArray();

            // Add CSRF token manually
            formData.push({
                name: '<?= csrf_token() ?>',
                value: '<?= csrf_hash() ?>'
            });
            $('#loader').show();

            $.ajax({
                url: "<?= base_url('api/leavetype/add'); ?>", // API route to handle insertion
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    $('#loader').hide();

                    if (response.success) {
                        // Append new leave type to the dropdown inside the modal
                        $("#leave_id").append(
                            `<option value="${response.leave_type.leave_id}" selected>
                            ${response.leave_type.leave_type}
                        </option>`
                        );

                        // Append new leave type to the main dropdown (outside modal)
                        $("select[name='leave_id']").append(
                            `<option value="${response.leave_type.id}" selected>
                            ${response.leave_type.leave_type}
                        </option>`
                        );

                        // Show success message
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Leave Type added successfully!",
                            timer: 2000,
                            showConfirmButton: false,

                        });

                        // Reset form and close modal
                        $("#AddLeaveForm")[0].reset();
                        $("#addLeaveModal").modal("hide");
                    } else {
                        // Display validation errors below input fields
                        if (response.errors) {
                            if (response.errors.leave_type) {
                                $("#leaveError").html(response.errors.leave_type);
                            }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message,

                            });
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('#loader').hide();

                    console.error("AJAX Error: " + error);
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>