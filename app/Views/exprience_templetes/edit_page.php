<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .cke_notifications_area {
        display: none !important;
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Template</h4>

                <!-- <form id="offerTemplateForm" method="post"> -->
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
                            <input type="file" class="form-control" name="template_img" id="template_img"
                                accept="image/*" />
                            <small id="template_img-error" class="text-danger"></small>

                            <!-- Preview the uploaded image here -->
                            <div class="mt-3">
                                <img id="existingImagePreview" src="" alt="Template Image" class="img-fluid rounded"
                                    style="max-height: 150px;" />
                            </div>
                        </div>
                    </div>


                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label">Design Template</label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content" class="d-none"></textarea>
                            <small id="content-error" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-end mt-4">
                        <a href="<?= site_url('exprience-templates-view') ?>" class="btn hr-btnbg">Back</a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                </form>
                <div class="alert alert-info mt-4">
                    <strong>Note:</strong> You can use dynamic tags (placeholders) inside the content. These tags will be replaced with actual employee data when the letter is generated.
                    <ul class="mb-0 mt-2">
                        <li><code>{{employee_name}}</code> → Will be replaced with the employee's full name</li>
                        <li><code>{{company_name}}</code> → Will be replaced with your company name</li>
                        <li><code>{{joining_date}}</code> → Will be replaced with the employee's From date</li>
                        <li><code>{{leaving_date}}</code> → Will be replaced with the employee's To date</li>
                        <li><code>{{designation}}</code> → Will be replaced with the employee's job title/designation</li>
                        <li><code>{{department}}</code> → Will be replaced with the employee's job title/departemnt</li>
                        <li><code>{{company_address}}</code> → Will be replaced with your company address</li>
                        <li><code>{{company_phone}}</code> → Will be replaced with your company phone</li>
                        <li><code>{{company_email}}</code> → Will be replaced with your company email</li>
                        <li><code>{{created_by}}</code> → Will be replaced with your created name</li>
                    </ul>
                    ✨ Example usage: <br>
                    <em>"<code>{{employee_name}}</code> worked with us from <code>{{joining_date}}</code> to <code>{{leaving_date}}</code>."</em>
                </div>
                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    const templateId = <?= json_encode($id) ?>;

    $(document).ready(function() {
        CKEDITOR.replace('content', {
            height: 400,
            removePlugins: 'elementspath',
            resize_enabled: false
        });

        // ✅ Fetch existing template data
        $.ajax({
            url: "<?= site_url('api/exprience-templates/get-data') ?>/" + templateId,
            method: "GET",
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    $('#id').val(templateId);
                    $('#title').val(response.data.title);
                    CKEDITOR.instances['content'].setData(response.data.content);

                    if (response.data.template_img) {
                        $('#existingImagePreview')
                            .attr('src', '<?= base_url("upload/templates/") ?>' + response.data.template_img)
                            .show();
                    } else {
                        $('#existingImagePreview').hide();
                    }
                } else {
                    $('#responseMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                $('#responseMessage').html(`<div class="alert alert-danger">Error fetching template data.</div>`);
            }
        });

        // ✅ Form submission
        $('#offerTemplateForm').on('submit', function(e) {
            e.preventDefault();

            // Sync CKEditor content to textarea
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
                url: '<?= site_url("api/exprience-template/update-data") ?>/' + templateId,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    $('#loader').hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Experience Letter template updated successfully!',
                        timer: 1500,
                        showConfirmButton: false,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg'
                        }
                    });

                    setTimeout(() => {
                        window.location.href = '<?= site_url("exprience-templates-view") ?>';
                    }, 1600);
                },
                error: function(xhr) {
                    $('#loader').hide();
                    $('#submitBtn').text('Submit').prop('disabled', false);

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        $('#title-error, #template_img-error, #content-error').text('');

                        $.each(errors, function(key, val) {
                            $('#' + key + '-error').text(val);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg'
                            }
                        });
                    }
                }
            });
        });
    });
</script>


<?= $this->endSection(); ?>