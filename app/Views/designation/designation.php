<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    @media (min-width: 375px) and (max-width: 667px) {
.sm-margin{
    margin-top: 8px !important;
    /* margin-right: -8px !important; */
}
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Designation</h4>
                <form class="form-sample" id="designationForm">
                     <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Department Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-domain fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="department_id" id="department_id">
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $department) : ?>
                                                <option value="<?= $department['id']; ?>"><?= $department['department_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Designation Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="designation_name" id="designation_name" placeholder="Enter Designation Name" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-group text-end sm-margin">
                        <a href="<?= base_url('/designationview')?>" class="btn hr-btnbg">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token
        let isEditMode = false; // Flag to track whether we're in edit mode
        let designationId = null; // To store the designation ID for updating

        // Handle form submission (both create and update)
        $('#designationForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
            let isValid = true;

            // Validate designation name
            const designationName = $('#designation_name').val().trim();
            if (designationName === '') {
                $('#designation_name').addClass('is-invalid');
                let errorDiv = $('#designation_name').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#designation_name').parent().append(errorDiv);
                }
                errorDiv.text('Designation name is required.');
                isValid = false; // Stop the AJAX call if validation fails
            } else {
                $('#designation_name').removeClass('is-invalid');
            }

            // Validate department selection
            const departmentId = $('#department_id').val().trim();
            if (departmentId === '') {
                $('#department_id').addClass('is-invalid');
                let errorDiv = $('#department_id').parent().find('.invalid-feedback');
                if (errorDiv.length === 0) {
                    errorDiv = $('<div class="invalid-feedback"></div>');
                    $('#department_id').parent().append(errorDiv);
                }
                errorDiv.text('Department selection is required.');
                isValid = false;
            } else {
                $('#department_id').removeClass('is-invalid');
            }

            // If validation passes, submit the form
            if (isValid) {
                const url = isEditMode ? `/api/designation/${designationId}` : '/api/designation';
                const method = isEditMode ? 'POST' : 'POST'; // Use PUT for updates
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

                        if (responseData && responseData.message) {
                            Swal.fire({
                                title: "Success!",
                                text: responseData.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg',

                                }
                            }).then(() => {
                                window.location.href = "/designationview"; // Redirect to designation view
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Something went wrong!",
                                icon: "error",
                                confirmButtonText: "OK",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg',

                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loader').hide();

                        let errorMessage = "An error occurred. Please try again later.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: "Error!",
                            text: errorMessage,
                            icon: "error",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',
                            }
                        });
                    }
                });
            }
        });

        // Fetch designation data for edit mode
        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');
        if (Id) {
            fetchDesignationData(Id);
        }

        function fetchDesignationData(Id) {
            $.ajax({
                url: `/api/designation/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const designation = responseData.data;
                        $('#designation_name').val(designation.designation_name);
                        $('#department_id').val(designation.department_id);
                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit Designation');
                        designationId = designation.id;
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Designation not found.",
                            icon: "error",
                            confirmButtonText: "OK",
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching designation:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching designation.",
                        icon: "error",
                        confirmButtonText: "OK",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?>