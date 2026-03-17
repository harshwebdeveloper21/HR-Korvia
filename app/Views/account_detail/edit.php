<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Account Detail</h4>
                <form class="form-sample" method="POST" id="accountdetail">
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
                                    <div class="text-danger" id="user_id-error"></div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Name In Account</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Account Holder Name"
                                            name="acc_in_name" id="acc_in_name" />
                                    </div>
                                    <div class="text-danger" id="acc_in_name-error"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">

                                <label class="col-sm-3 col-form-label">Account Number</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter account number"
                                            name="acc_number" id="acc_number" />
                                    </div>
                                    <div class="text-danger" id="acc_number-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-4">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Bank Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter bank name"
                                            name="bank_name" id="bank_name" />
                                    </div>
                                    <div class="text-danger" id="bank_name-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">IFSC Code</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-barcode fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter IFSC Code"
                                            name="ifsc_code" id="ifsc_code" />
                                    </div>
                                    <div class="text-danger" id="ifsc_code-error"></div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Branch Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-check fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="Branch Name" />
                                    </div>
                                    <div class="text-danger" id="branch_name-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Branch Code</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-calendar-check fs-5"></i></span>
                            </div>
                            <input type="text" class="form-control" name="branch_code" id="branch_code" placeholder="Enter Branch Code" />
                        </div>
                        <div class="text-danger" id="branch_code-error"></div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="<?= base_url('/account-detail-view') ?>" class="btn hr-btnbg">
                    Back
                </a>
                <button type="submit" class="btn hr-btnbg" id="submitBtn">Update</button>
            </div>
            <div id="responseMessage"></div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    const token = localStorage.getItem('token');
    const url = window.location.pathname;
    const segments = url.split('/');
    const accountId = segments[segments.length - 1]; // Get ID from URL

    if (!isNaN(accountId)) {
        $.ajax({
         
            url: "<?= site_url('api/account-detail-get')?>/" + accountId,
            type: "GET",
            headers: {
                    'Authorization': `Bearer ${token}`
                },
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    const data = response.data;
                    $('#id').val(data.id);
                    $('#user_id').val(data.user_id);
                    $('#acc_in_name').val(data.acc_in_name);
                    $('#acc_number').val(data.acc_number);
                    $('#bank_name').val(data.bank_name);
                    $('#ifsc_code').val(data.ifsc_code);
                    $('#branch_name').val(data.branch_name);
                    $('#branch_code').val(data.branch_code);
                } else {
                    $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#responseMessage').html('<div class="alert alert-danger">Something went wrong while fetching data.</div>');
            }
        });
    }

    $('#accountdetail').on('submit', function (e) {
    e.preventDefault();

    const token = localStorage.getItem('token');
    const formData = new FormData(this);
    const accountId = $('#id').val();

    $.ajax({
        url: "<?= site_url('api/account-detail-update') ?>/" + accountId,
        type: "POST",
        headers: {
            'Authorization': `Bearer ${token}`
        },
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            $('.text-danger').html('');
            $('#responseMessage').html('');

            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    buttonsStyling: false,
                                    customClass: {
                                    confirmButton: 'hr-btnbg', 
     
                                    }
                }).then(() => {
                    window.location.href = "<?= base_url('account-detail-view') ?>";
                });
            } else if (response.errors) {
                $.each(response.errors, function (key, value) {
                    $('#' + key + '-error').html(value);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message
                });
            }
        },
        error: function (xhr) {
            let msg = "An error occurred during submission.";
            if (xhr.responseJSON?.errors) {
                $.each(xhr.responseJSON.errors, function (key, value) {
                    $('#' + key + '-error').html(value);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg,
                    buttonsStyling: false,
                                    customClass: {
                                    confirmButton: 'hr-btnbg', 
     
                                    }
                });
            }
        }
    });
});


});
</script>


<?= $this->endSection(); ?>