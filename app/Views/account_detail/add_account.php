<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
 @media (max-width: 767px) {
    .interviewsmbtn{
     font-size: 12px !important;
    padding: 8px !important;
    margin-top: 10px !important;
}
}
</style>
<!-- account details -->
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Account Detail</h4>
               <form class="form-sample" method="POST" id="accountdetail">
    <input type="hidden" name="id" id="id" value=""> <!-- Hidden input for id -->

    <div class="row gy-3">
        <!-- Employee Name -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Employee Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                <select class="form-select" name="user_id" id="user_id">
                    <option value="" disabled selected>Select Employee Name</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?= $employee["id"] ?>"><?= esc(
    $employee["username"],
) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="text-danger" id="user_id-error"></div>
        </div>

        <!-- Account Holder Name -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Name In Account</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                <input type="text" class="form-control" name="acc_in_name" id="acc_in_name" placeholder="Account Holder Name" />
            </div>
            <div class="text-danger" id="acc_in_name-error"></div>
        </div>
    </div>

    <div class="row gy-3 mt-1">
        <!-- Account Number -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Account Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                <input type="text" class="form-control" name="acc_number" id="acc_number" placeholder="Enter account number" />
            </div>
            <div class="text-danger" id="acc_number-error"></div>
        </div>

        <!-- Bank Name -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Bank Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Enter bank name" />
            </div>
            <div class="text-danger" id="bank_name-error"></div>
        </div>
    </div>

    <div class="row gy-3 mt-1">
        <!-- IFSC Code -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">IFSC Code</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-barcode fs-5"></i></span>
                <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" placeholder="Enter IFSC Code" />
            </div>
            <div class="text-danger" id="ifsc_code-error"></div>
        </div>

        <!-- Branch Name -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Branch Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-bank fs-5"></i></span>
                <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="Branch Name" />
            </div>
            <div class="text-danger" id="branch_name-error"></div>
        </div>
    </div>

    <div class="row gy-3 mt-1">
        <!-- Branch Code -->
        <div class="col-md-6">
            <label class="form-label leave-sm-emp">Branch Code</label>
            <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-code-tags fs-5"></i></span>
                <input type="text" class="form-control" name="branch_code" id="branch_code" placeholder="Enter Branch Code" />
            </div>
            <div class="text-danger" id="branch_code-error"></div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="text-end mt-4">
        <a href="<?= base_url(
            "/account-detail-view",
        ) ?>" class="btn hr-btnbg interviewsmbtn me-2">
            Back
        </a>
        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn">Submit</button>
    </div>

    <div id="responseMessage"></div>
</form>

        </div>
    </div>
</div>
<script>
    $('#accountdetail').on('submit', function(e) {
        e.preventDefault();
        const token = localStorage.getItem('token'); // JWT token
        const formData = new FormData(this);
        $('#submitBtn').text('Submitting...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url("api/account-detail/store") ?>',
            type: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Account detail saved successfully!',
                    timer: 1500,
                    showConfirmButton: false,
                    buttonsStyling: false,
                                    customClass: {
                                    confirmButton: 'hr-btnbg',

                                    }
                });

                $('#submitBtn').text('Submit').prop('disabled', false);
                $('#accountdetail')[0].reset();
                window.location.href = "<?= base_url("account-detail-view") ?>";
            },
            error: function(xhr) {
                $('#submitBtn').text('Submit').prop('disabled', false);
                if (xhr.status === 409) {
            // Duplicate record
            Swal.fire({
                icon: 'warning',
                title: 'Duplicate Entry',
                text: xhr.responseJSON.message,
                buttonsStyling: false,
                                    customClass: {
                                    confirmButton: 'hr-btnbg',

                                    }
            });
        }
               else if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    $('.text-danger').text(''); // Clear old errors

                    $.each(response.errors, function(key, val) {
                        $('#' + key + '-error').text(val);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again!',
                        buttonsStyling: false,
                                    customClass: {
                                    confirmButton: 'hr-btnbg',

                                    }
                    });
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
