<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }

        .cart-sm-titles {
            font-size: 15px !important;
            margin-bottom: 5px !important;
        }
    }
    .cke_notifications_area{
    display: none !important;
}
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Employee Of The Month Template</h4>
                <form id="offerTemplateForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" name="id" id="id" /> <!-- For editing -->


                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label leave-sm-emp">Template Title</label>
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
                        <label class="col-sm-2 col-form-label leave-sm-emp">Template Image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="emp_image" id="emp_image" accept="image/*" />
                            <small id="emp_image-error" class="text-danger"></small>

                        </div>
                    </div>

                    <!-- CKEditor -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label leave-sm-emp">Content</label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content"></textarea>
                            <small id="content-error" class="text-danger"></small>
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
                        <a href="<?= site_url(
                            "emp-month-view",
                        ) ?>" class="btn hr-btnbg interviewsmbtn">Back</a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn">Submit</button>
                    </div>
                </form>

                <div id="responseMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

<!-- TinyMCE Init -->
<script>
    CKEDITOR.replace('content', {
        height: 500,
        removePlugins: 'elementspath',
        resize_enabled: false,
        on: {
            instanceReady: function(evt) {
                const bgImage = '<?= isset($emp_image_url)
                    ? $emp_image_url
                    : "" ?>';
                if (bgImage) {
                    const editorDocument = evt.editor.document;
                    const body = editorDocument.getBody();
                    body.setStyle('background-image', 'url(' + bgImage + ')');
                    body.setStyle('background-size', 'cover');
                    body.setStyle('background-repeat', 'no-repeat');
                    body.setStyle('background-position', 'center center');
                    body.setStyle('color', '#000'); // Adjust text color for contrast
                }
            }
        }
    });
    $('#offerTemplateForm').on('submit', function(e) {
        e.preventDefault();

        $('#loader').show();

         const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);

        $.ajax({
            url: '<?= site_url("api/empof-month/SaveEmpMonth") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitBtn').text('Saving...').prop('disabled', true);
                $('#title-error').text('');
                $('#emp_image-error').text('');
                $('#content-error').text('');
                $('#responseMessage').html('');
            },
            success: function(response) {
                $('#loader').hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Template saved successfully!',
                    timer: 1500,
                    showConfirmButton: false,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg',

                    }
                });

                // ✅ Set background image if available
                if (response.image_url) {
                    const editor = tinymce.get('content');
                    editor.getBody().style.backgroundImage = `url(${response.image_url})`;
                    editor.getBody().style.backgroundSize = 'cover';
                    editor.getBody().style.backgroundRepeat = 'no-repeat';
                    editor.getBody().style.backgroundPosition = 'center center';
                }

                $('#submitBtn').text('Submit').prop('disabled', false);

                setTimeout(() => {
                    window.location.href = '<?= site_url("emp-month-view") ?>';
                }, 1600);
            },

            error: function(xhr) {
                $('#loader').hide();

                $('#submitBtn').text('Submit').prop('disabled', false);

                if (xhr.status === 422) {
                    const response = xhr.responseJSON;

                    $('#title-error').text('');
                    $('#emp_image-error').text('');
                    $('#content-error').text('');

                    $.each(response.errors, function(key, val) {
                        $('#' + key + '-error').text(val);
                    });
                 } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong. Please try again.',
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
