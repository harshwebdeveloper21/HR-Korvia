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
                <h4 class="card-title">Add Template</h4>
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
                                <input type="text" class="form-control" name="title" id="title" placeholder="Enter template title" />
                            </div>
                            <small id="title-error" class="text-danger"></small>
                        </div>
                    </div>
                    <!-- Template Image Upload -->
                    <div class="form-group row mt-3">
                        <label class="col-sm-2 col-form-label">Template Image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="template_img" id="template_img" accept="image/*" />
                            <small id="template_img-error" class="text-danger"></small>

                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label">Content</label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content" class="form-control"></textarea>
                            <small id="content-error" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Display dynamic fields below the content -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="alert alert-info mt-4">
                                <strong>Note:</strong> You can use dynamic tags (placeholders) inside the content. These tags will be replaced with actual employee/candidate data when the letter is generated.
                                <ul class="mb-0 mt-2">
                                    <li><code>{{logo_img}}</code> → Will be replaced with the company logo image</li>
                                    <li><code>{{company_name}}</code> → Will be replaced with your company name</li>
                                    <li><code>{{company_address}}</code> → Will be replaced with your company address</li>
                                    <li><code>{{company_phone}}</code> → Will be replaced with your company phone</li>
                                    <li><code>{{company_email}}</code> → Will be replaced with your company email</li>
                                    <li><code>{{today_date}}</code> → Will be replaced with today’s date</li>
                                    <li><code>{{candidate_name}}</code> → Will be replaced with the candidate’s full name</li>
                                    <li><code>{{job_title}}</code> → Will be replaced with the candidate's job title</li>
                                    <li><code>{{start_date}}</code> → Will be replaced with the job posting date</li>
                                    <li><code>{{department_name}}</code> → Will be replaced with the department name</li>
                                    <li><code>{{created_by}}</code> → Will be replaced with the username of the person creating the letter</li>
                                    <li><code>{{creator_email}}</code> → Will be replaced with the creator’s email</li>
                                    <li><code>{{creator_designation}}</code> → Will be replaced with the creator’s designation</li>
                                    <li><code>{{joining_date}}</code> → Will be replaced with the candidate’s joining date</li>
                                    <li><code>{{salary}}</code> → Will be replaced with the offered salary</li>
                                    <li><code>{{docu_submitted}}</code> → Will be replaced with submitted documents info</li>
                                </ul>
                                ✨ Example usage: <br>
                                <em>"We are pleased to offer <code>{{candidate_name}}</code> the role of <code>{{job_title}}</code> starting from <code>{{joining_date}}</code>."</em>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-end mt-4">
                        <a href="<?= site_url('offer-templates-view') ?>" class="btn hr-btnbg">Back</a>
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
    CKEDITOR.replace('content', {
        height: 500,
        removePlugins: 'elementspath',
        resize_enabled: false,
        on: {
            instanceReady: function (evt) {
                const bgImage = '<?= isset($emp_image_url) ? $emp_image_url : '' ?>';
                if (bgImage) {
                    const body = evt.editor.document.getBody();
                    body.setStyle('background-image', 'url(' + bgImage + ')');
                    body.setStyle('background-size', 'cover');
                    body.setStyle('background-repeat', 'no-repeat');
                    body.setStyle('background-position', 'center center');
                    body.setStyle('color', '#000'); // Optional: improve text visibility
                }
            }
        }
    });
</script>

<script>
    $('#offerTemplateForm').on('submit', function (e) {
        e.preventDefault();

        // Update textarea with CKEditor content
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
        $('#loader').show();

        $.ajax({
            url: '<?= site_url("api/offer-template/save") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#submitBtn').text('Saving...').prop('disabled', true);
                $('#title-error').text('');
                $('#template_img-error').text('');
                $('#content-error').text('');
                $('#responseMessage').html('');
            },
            success: function (response) {
                $('#loader').hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Offer letter template saved successfully!',
                    timer: 1500,
                    showConfirmButton: false,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg'
                    }
                });

                $('#submitBtn').text('Submit').prop('disabled', false);

                setTimeout(() => {
                    window.location.href = '<?= site_url("offer-templates-view") ?>';
                }, 1600);
            },
            error: function (xhr) {
                $('#loader').hide();
                $('#submitBtn').text('Submit').prop('disabled', false);

                if (xhr.status === 422) {
                    const response = xhr.responseJSON;
                    $('#title-error').text('');
                    $('#template_img-error').text('');
                    $('#content-error').text('');

                    $.each(response.errors, function (key, val) {
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
</script>
<?= $this->endSection(); ?>