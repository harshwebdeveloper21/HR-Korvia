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
                                <input type="text" class="form-control" name="title" id="title" placeholder="Enter template title (e.g. JOINING LETTER, Offer Letter)" />
                            </div>
                            <small id="title-error" class="text-danger"></small>
                        </div>
                    </div>



                    <!-- Template Header File (Shows on top of all PDF pages) -->
                    <div class="form-group row mt-3">
                        <label class="col-sm-2 col-form-label">Template Header</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="template_header" id="template_header" accept="image/*" />
                            <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline"></i> Upload header banner/image to display at the top of every page in the generated PDF.</small>
                            <small id="template_header-error" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Template Footer File (Shows on bottom of all PDF pages) -->
                    <div class="form-group row mt-3">
                        <label class="col-sm-2 col-form-label">Template Footer</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="template_footer" id="template_footer" accept="image/*" />
                            <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline"></i> Upload footer banner/image to display at the bottom of every page in the generated PDF.</small>
                            <small id="template_footer-error" class="text-danger"></small>
                        </div>
                    </div>

                    <!-- Dynamic Template Pages Container -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label">
                            <strong>Design Template</strong>
                            <small class="d-block text-muted">Page-wise document editor</small>
                        </label>
                        <div class="col-sm-10">
                            <div id="template-pages-container">
                                <!-- Dynamic pages will be populated here -->
                            </div>

                            <!-- Add More Page Button -->
                            <div class="mt-2 mb-4">
                                <button type="button" class="btn btn-outline-primary fw-bold d-inline-flex align-items-center gap-2 py-2 px-3" id="addPageBtn" style="border-radius: 8px;">
                                    <i class="mdi mdi-plus-circle-outline fs-5"></i> Add More Page
                                </button>
                                <span class="text-muted small ms-2">Click to add Page 3, Page 4 or more pages to the document</span>
                            </div>
                        </div>
                    </div>

                    <!-- Display dynamic fields below the content -->
                    <div class="form-group row mt-4">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="alert alert-info mt-4">
                                <strong>Note:</strong> You can use dynamic tags (placeholders) inside the content. These tags will be replaced with actual employee/candidate data when the letter is generated.
                                <ul class="mb-0 mt-2">
                                    <li><code>{{candidate_name}}</code> or <code>{{employee_name}}</code> → Candidate's full name</li>
                                    <li><code>{{job_title}}</code> or <code>{{designation}}</code> → Job title / designation</li>
                                    <li><code>{{department_name}}</code> or <code>{{department}}</code> → Department name</li>
                                    <li><code>{{start_date}}</code> or <code>{{joining_date}}</code> → Candidate's start date from onboarding</li>
                                    <li><code>{{salary}}</code> → Salary / compensation terms</li>
                                    <li><code>{{docu_submitted}}</code> → Submitted documents list from onboarding</li>
                                    <li><code>{{reporting_to}}</code> or <code>{{supervisor}}</code> → Reporting manager / supervisor</li>
                                    <li><code>{{working_hours}}</code> → Working hours (e.g. 09:30 AM till 06:15 PM)</li>
                                    <li><code>{{today_date}}</code> or <code>{{current_date}}</code> → Current date</li>
                                    <li><code>{{company_name}}</code> → Company name</li>
                                    <li><code>{{company_address}}</code> → Company address</li>
                                    <li><code>{{company_phone}}</code> / <code>{{company_email}}</code> → Company contact details</li>
                                    <li><code>{{created_by}}</code> or <code>{{signer_name}}</code> → Signer / HR / Creator name</li>
                                    <li><code>{{creator_designation}}</code> or <code>{{signer_designation}}</code> → Signer designation</li>
                                    <li><code>{{email}}</code> / <code>{{phone_number}}</code> → Candidate's email & phone</li>
                                </ul>
                                ✨ Example usage: <br>
                                <em>"We are pleased to offer <code>{{candidate_name}}</code> the role of <code>{{job_title}}</code> starting from <code>{{start_date}}</code>."</em>
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
    if (!CKEDITOR.stylesSet.get('custom_offer_styles')) {
        CKEDITOR.stylesSet.add('custom_offer_styles', [
            { name: 'Arrow Bullet List (➤)', element: 'ul', attributes: { 'class': 'arrow-list' } },
            { name: 'Arrow Item (➤)', element: 'li', attributes: { 'class': 'arrow-item' } },
            { name: 'Document Title', element: 'h3', styles: { 'text-decoration': 'underline', 'text-transform': 'uppercase' } }
        ]);
    }

    const toolbarConfig = [
        { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
        { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
        { name: 'links', items: ['Link', 'Unlink'] },
        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
        '/',
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
        { name: 'tools', items: ['Maximize', 'ShowBlocks', 'Source'] }
    ];

    let pageCounter = 0;

    function createPageBlock(pageNumber, initialContent) {
        pageCounter++;
        const pageId = pageCounter;
        const isFirstPage = (pageNumber === 1);
        const textareaId = 'page_editor_' + pageId;

        const blockHtml = `
            <div class="template-page-card mb-4 p-3 border rounded bg-white shadow-sm" id="page-card-${pageId}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="mdi mdi-file-document-outline me-1 text-primary"></i> Design Template Page <span class="page-num-text">${pageNumber}</span>
                    </h6>
                    ${!isFirstPage ? `
                        <button type="button" class="btn btn-sm btn-outline-danger remove-page-btn" data-page-id="${pageId}">
                            <i class="mdi mdi-delete-outline"></i> Remove Page
                        </button>
                    ` : '<span class="badge bg-light text-muted border">Page 1 (Main)</span>'}
                </div>
                <div>
                    <textarea id="${textareaId}" class="page-editor-textarea form-control"></textarea>
                </div>
            </div>
        `;

        $('#template-pages-container').append(blockHtml);

        // Initialize CKEditor
        CKEDITOR.replace(textareaId, {
            height: 380,
            removePlugins: 'elementspath',
            removeButtons: '',
            resize_enabled: false,
            stylesSet: 'custom_offer_styles',
            toolbar: toolbarConfig,
            on: {
                instanceReady: function (evt) {
                    // Inject live styling into CKEditor editable iframe so bullets render as arrows
                    const customCss = `
                        body { font-family: "Times New Roman", Times, serif; font-size: 15px; line-height: 1.45; color: #000; }
                        ul.arrow-list, ul {
                            list-style: none !important;
                            padding-left: 0 !important;
                            margin: 6px 0 10px 0 !important;
                        }
                        ul.arrow-list li, ul li {
                            position: relative !important;
                            margin-bottom: 6px !important;
                            padding-left: 20px !important;
                            text-indent: -20px !important;
                        }
                        ul.arrow-list li::before, ul li::before {
                            content: "➤ " !important;
                            color: #000 !important;
                            font-size: 13px !important;
                            margin-right: 4px !important;
                            font-family: "DejaVu Sans", Arial, sans-serif !important;
                        }
                    `;
                    const styleEl = evt.editor.document.createElement('style');
                    styleEl.setAttribute('type', 'text/css');
                    styleEl.setText(customCss);
                    evt.editor.document.getHead().append(styleEl);

                    if (initialContent) {
                        evt.editor.setData(initialContent);
                    }
                }
            }
        });

        renumberPages();
        return textareaId;
    }

    function renumberPages() {
        $('#template-pages-container .template-page-card').each(function (index) {
            $(this).find('.page-num-text').text(index + 1);
        });
    }

    // Remove page click
    $(document).on('click', '.remove-page-btn', function () {
        const pageId = $(this).data('page-id');
        const textareaId = 'page_editor_' + pageId;
        if (confirm('Are you sure you want to remove this page?')) {
            if (CKEDITOR.instances[textareaId]) {
                CKEDITOR.instances[textareaId].destroy();
            }
            $('#page-card-' + pageId).remove();
            renumberPages();
        }
    });

    // Add More Page click
    $('#addPageBtn').on('click', function () {
        const nextNum = $('#template-pages-container .template-page-card').length + 1;
        createPageBlock(nextNum, '');
    });

    // Initialize default Page 1 and Page 2
    $(document).ready(function () {
        createPageBlock(1, '');
        createPageBlock(2, '');
    });
</script>

<script>
    $('#offerTemplateForm').on('submit', function (e) {
        e.preventDefault();

        // Update textarea with CKEditor content
        for (const instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        const formData = new FormData(this);
        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfTokenValue = $('#csrfToken').val();
        formData.append(csrfTokenName, csrfTokenValue);

        // Collect all dynamic pages in order
        const pages = [];
        $('#template-pages-container .template-page-card').each(function () {
            const editorId = $(this).find('.page-editor-textarea').attr('id');
            if (CKEDITOR.instances[editorId]) {
                pages.push(CKEDITOR.instances[editorId].getData());
            }
        });

        // Append pages array
        pages.forEach(function (pageContent, index) {
            formData.append('pages[' + index + ']', pageContent);
        });
        formData.set('content', pages[0] || '');
        formData.set('content_page2', pages[1] || '');

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
                $('#template_header-error').text('');
                $('#template_footer-error').text('');
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
                    $('#template_header-error').text('');
                    $('#template_footer-error').text('');
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