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
                <h4 class="card-title">Add Department</h4>
                <form class="form-sample" id="departmentForm">
                     <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" id="id" name="id" /> <!-- For editing -->
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Department Name</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="department_name" id="department_name" placeholder="Enter Department Name" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-end sm-margin">

                        <a href="/departmentview" class="btn hr-btnbg">
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
    let isEditMode = false;
    let departmentId = null;

    $('#departmentForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);

        let isValid = true;
        const departmentName = $('#department_name').val().trim();

        if (departmentName === '') {
            $('#department_name').addClass('is-invalid');
            let errorDiv = $('#department_name').parent().find('.invalid-feedback');
            if (errorDiv.length === 0) {
                errorDiv = $('<div class="invalid-feedback"></div>');
                $('#department_name').parent().append(errorDiv);
            }
            errorDiv.text('Department name is required.');
            isValid = false;
        } else {
            $('#department_name').removeClass('is-invalid');
        }

        if (isValid) {
            const url = isEditMode ? `/api/department/${departmentId}` : '/api/department';
            $('#loader').show();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'Authorization': `Bearer ${token}`
                    // ❌ DO NOT set Content-Type manually when using FormData
                },
                contentType: false,
                processData: false,
                success: function (responseData) {
                    $('#loader').hide();
                    Swal.fire({
                        title: "Success!",
                        text: responseData.message,
                        icon: "success",
                        confirmButtonText: "OK",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg'
                        }
                    }).then(() => {
                        window.location.href = "/departmentview";
                    });
                },
                error: function(xhr, status, error) {
    $('#loader').hide();

    let errorMessage = "An error occurred. Please try again later.";

    if (xhr.responseJSON) {
        if (xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseJSON.messages) {
            // Build a combined message from validation errors
            errorMessage = Object.values(xhr.responseJSON.messages).join('\n');
        }
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


        const params = new URLSearchParams(window.location.search);
        const Id = params.get('id');
        if (Id) {
            fetchDepartmentData(Id);
        }

        function fetchDepartmentData(Id) {
            $.ajax({
                url: `/api/department/${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const department = responseData.data;
                        $('#department_name').val(department.department_name);
                        $('#submitBtn').text('Update');
                        $('.card-title').text('Edit Department');
                        departmentId = department.id;
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Department not found.",
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
                    console.error('Error fetching department:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error fetching department.",
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