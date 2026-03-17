<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    @media (min-width: 375px) and (max-width: 667px) {
        .sm-margin {
            margin-top: 8px !important;
            /* margin-right: -8px !important; */
        }
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Leave Type</h4>
                <form class="form-sample" method="POST" action="" id="LeaveForm">
                     <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Leave Type</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="hidden" id="id" name="id" value="">
                                        <input type="text" class="form-control" name="leave_type" id="leave_type" placeholder="Enter Leave type" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Allow Half Day?</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="allow_half_day" id="allow_half_day">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Number of leaves</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-range fs-5"></i></span>
                                        </div>

                                        <input type="number" class="form-control" name="number_of_leaves" id="number_of_leaves" placeholder="Enter Total " />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-end sm-margin">

                        <a href="/leavetypeview" class="btn hr-btnbg">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                    <div id="responseMessage"></div>

                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token from login
        let isEditMode = false; // Track whether we're editing
        let leaveId = null; // Store leave ID for updating

        // Check if we're editing an existing leave type
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('id');
        if (editId) {
            isEditMode = true;
            leaveId = editId;
            fetchLeaveType(leaveId);
        }

        // Fetch LeaveType data for editing
        function fetchLeaveType(leaveId) {
            $.ajax({
                url: `/api/leavetype/${leaveId}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const leave = responseData.data;
                        $('#leave_type').val(leave.leave_type);
                        $('#number_of_leaves').val(leave.number_of_leaves);
                        $('#allow_half_day').val(leave.allow_half_day);
                        $('#requires_approval').val(leave.requires_approval);
                        $('#id').val(leave.id);
                        $('#submitBtn').text('Update');
                    } else {
                        Swal.fire('Error', 'Leave type not found.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch leave type.', 'error');
                }
            });
        }

        // Handle form submission (Create/Update)
        $('#LeaveForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Clear previous error messages
            $('#leave_type').removeClass('is-invalid');
            $('.invalid-feedback').remove();

          const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        const leaveName = $('#leave_type').val().trim();

            // Basic validation
            if (leaveName === '') {
                $('#leave_type').addClass('is-invalid').after('<div class="invalid-feedback">Leave type is required.</div>');
                return; // Stop if validation fails
            }

            // Determine if we're inserting or updating
            const url = isEditMode ? `/api/leavetype/${leaveId}` : '/api/leavetype';
            const method = isEditMode ? 'POST' : 'POST'; // Both cases use POST
            $('#loader').show();

            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'Authorization': `Bearer ${token}`,
                   
                },
                 contentType: false,
                processData: false,
                success: function(responseData) {
                    $('#loader').hide();

                    if (responseData.status === 'success') {
                        Swal.fire({
                            title: 'Success',
                            text: responseData.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        }).then(() => {
                            window.location.href = '/leavetypeview'; // Redirect to listing page
                        });

                        $('#LeaveForm')[0].reset(); // Reset form after success
                        $('#submitBtn').text('Submit'); // Reset button text
                        isEditMode = false;
                    } else {
                        Swal.fire('Error', responseData.message, 'error');
                    }
                },
                error: function(xhr) {
                    $('#loader').hide();

                    let errorMsg = 'An error occurred. Please try again later.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>