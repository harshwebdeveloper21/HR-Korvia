<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .cke_notifications_area{
    display: none !important;
}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Template</h4>
                  <form id="offerTemplateForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" name="id" id="id" /> <!-- For editing -->

                    <!-- Title -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Template Title</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-note-text-outline fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" name="title" id="title"
                                    placeholder="Enter template title" />
                            </div>
                        </div>
                    </div>
                    <!-- Template Image Upload -->
                    <div class="form-group row mt-3">
                        <label class="col-sm-2 col-form-label">Template Image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="emp_image" id="emp_image"
                                accept="image/*" />
                            <small id="emp_image-error" class="text-danger"></small>

                            <!-- Preview the uploaded image here -->
                            <div class="mt-3">
                                <img id="existingImagePreview" src="" alt="Template Image" class="img-fluid rounded"
                                    style="max-height: 150px;" />
                            </div>
                        </div>
                    </div>

                    <!-- TinyMCE Editor -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label">Design Template</label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content" class="d-none"></textarea>
                        </div>
                    </div>
                    <!-- Display dynamic fields below the content -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="alert alert-info mt-4">
                                <strong>Note:</strong> You can use dynamic tags (placeholders) inside the content. These tags will be replaced with actual employee data when the letter is generated.
                                <ul class="mb-0 mt-2">
                                    <li><code>{{username}}</code> → Will be replaced with the employee's full name</li>
                                    <li><code>{{logo_img}}</code> → Will be replaced with the company logo image</li>
                                    <li><code>{{company_name}}</code> → Will be replaced with your company name</li>
                                    <li><code>{{company_address}}</code> → Will be replaced with your company address</li>
                                    <li><code>{{company_phone}}</code> → Will be replaced with your company phone</li>
                                    <li><code>{{company_email}}</code> → Will be replaced with your company email</li>
                                    <li><code>{{today_date}}</code> → Will be replaced with your today date</li>
                                    <li><code>{{designation_name}}</code> → Will be replaced with the employee's job title/designation</li>
                                    <li><code>{{goals_achieved}}</code> → Will be replaced with the employee's goals achieved</li>
                                    <li><code>{{team_work}}</code> → Will be replaced with the employee's team work score</li>
                                    <li><code>{{management}}</code> → Will be replaced with the employee's management skill rating</li>
                                    <li><code>{{presentation_skill}}</code> → Will be replaced with the employee's presentation skill rating</li>
                                    <li><code>{{behaviour}}</code> → Will be replaced with the employee's behavior rating</li>
                                    <li><code>{{rating}}</code> → Will be replaced with the employee's overall performance rating</li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    <!-- Submit -->
                    <div class="text-end mt-4">
                        <a href="<?= site_url('emp-month-view') ?>" class="btn hr-btnbg">Back</a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                </form>

                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    const templateId = <?= json_encode($id) ?>;
    $(document).ready(function () {

        // ✅ Initialize CKEditor
        CKEDITOR.replace('content', {
            height: 400,
            removePlugins: 'elementspath',
            resize_enabled: false,
            on: {
                instanceReady: function (evt) {
                    // ✅ Fetch existing template data
                    $.ajax({
                        url: "<?= site_url('api/empof_month-templates/get-template') ?>/" + templateId,
                        method: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#id').val(templateId);
                                $('#title').val(response.data.title);

                                // Set content in CKEditor
                                CKEDITOR.instances['content'].setData(response.data.content);

                                // Show existing image if available
                                if (response.data.emp_image) {
                                    $('#existingImagePreview')
                                        .attr('src', '<?= base_url("upload/") ?>' + response.data.emp_image)
                                        .show();
                                } else {
                                    $('#existingImagePreview').hide();
                                }
                            } else {
                                $('#responseMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
                            }
                        },
                        error: function () {
                            $('#responseMessage').html(`<div class="alert alert-danger">Error fetching template data.</div>`);
                        }
                    });
                }
            }
        });
        $('#offerTemplateForm').on('submit', function (e) {
            e.preventDefault();

            // ✅ Sync CKEditor content to textarea
            for (const instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

         const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
            $('#submitBtn').text('Submitting...').prop('disabled', true);
            $('#loader').show();

            $.ajax({
                url: '<?= site_url("api/empof-month-template/update-template") ?>/' + templateId,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function () {
                    $('#loader').hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Template updated successfully!',
                        timer: 1500,
                        showConfirmButton: false,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',
                        }
                    });

                    setTimeout(() => {
                        window.location.href = '<?= site_url("emp-month-view") ?>';
                    }, 1600);
                },
                error: function (xhr) {
                    $('#loader').hide();
                    $('#submitBtn').text('Submit').prop('disabled', false);

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        $('#title-error, #template_img-error, #content-error').text('');

                        $.each(errors, function (key, val) {
                            $('#' + key + '-error').text(val);
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                        });
                    }
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>