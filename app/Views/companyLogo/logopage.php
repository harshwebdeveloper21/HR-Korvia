<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Edit Company Logo</h4>

                <!-- Display Current Logo -->
                <div class="text-center mb-3">
                    <img src="<?= getCompanyLogo(); ?>" id="previewLogo" alt="Company Logo" class="img-thumbnail" style="max-height: 100px;">
                </div>

                <form id="logoForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload New Logo</label>
                        <input type="file" class="form-control" name="logo" id="logoInput" accept="image/*" required>
                    </div>

                    <div class="text-end mt-3">
                        <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Update Logo</button>
                    </div>
                </form>

                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const token = localStorage.getItem('token'); // JWT token from login

    // Show image preview when file is selected
    $('#logoInput').change(function (event) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#previewLogo').attr('src', e.target.result);
        }
        reader.readAsDataURL(event.target.files[0]);
    });

    // Submit form via AJAX
    $('#logoForm').submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "<?= base_url('api/company-logo/update'); ?>",
            type: "POST",
            headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#submitBtn').prop('disabled', true).text('Updating...');
            },
            success: function (response) {
                $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: function (xhr) {
                $('#responseMessage').html('<div class="alert alert-danger">Error updating logo. Please try again.</div>');
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Update Logo');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>