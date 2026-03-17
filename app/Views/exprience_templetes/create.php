<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }
    }

    .cke_notifications_area {
        display: none !important;
    }
</style>
<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet" />
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Exprience</h4>

                <!-- <form id="offerTemplateForm" method="post"> -->
                <form id="exprinceTemplateForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">
                    <input type="hidden" name="id" id="id" /> <!-- For editing -->

                    <!-- Title -->
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
                            <input type="file" class="form-control" name="template_img" id="template_img" accept="image/*" />
                            <small id="template_img-error" class="text-danger"></small>

                        </div>
                    </div>

                    <!-- CKEditor Area -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label leave-sm-emp">Content</label>
                        <div class="col-sm-10">
                            <textarea id="content" name="content" class="d-none"></textarea>
                            <small id="content-error" class="text-danger"></small>
                        </div>
                    </div>


                    <!-- Submit -->
                    <div class="text-end mt-4">
                        <a href="<?= site_url(
                            "exprience-templates-view",
                        ) ?>" class="btn hr-btnbg interviewsmbtn">Back</a>
                        <button type="submit" class="btn hr-btnbg interviewsmbtn" id="submitBtn">Submit</button>
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
    CKEDITOR.on('instanceReady', function(evt) {
        const warningBox = document.querySelector('.cke_notification_warning');
        if (warningBox) warningBox.style.display = 'none';
    });

    // Initialize CKEditor
    CKEDITOR.replace('content', {
        height: 500,
        removePlugins: 'elementspath',
        resize_enabled: false
    });

    $('#exprinceTemplateForm').on('submit', function(e) {
        e.preventDefault();

        // ✅ Ensure CKEditor content is synced to textarea
        for (const instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);
        $('#loader').show();

        $.ajax({
            url: '<?= site_url("api/exprience-templates/savedata") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#submitBtn').text('Saving...').prop('disabled', true);
                $('#title-error').text('');
                $('#template_img-error').text('');
                $('#content-error').text('');
                $('#responseMessage').html('');
            },
            success: function(response) {
                $('#loader').hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Experience Letter template saved successfully!',
                    timer: 1500,
                    showConfirmButton: false,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'hr-btnbg',
                    }
                });

                $('#submitBtn').text('Submit').prop('disabled', false);

                setTimeout(() => {
                    window.location.href = '<?= site_url(
                        "exprience-templates-view",
                    ) ?>';
                }, 1600);
            },
            error: function(xhr) {
                $('#loader').hide();
                $('#submitBtn').text('Submit').prop('disabled', false);

                if (xhr.status === 422) {
                    const response = xhr.responseJSON;

                    $('#title-error').text('');
                    $('#template_img-error').text('');
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
