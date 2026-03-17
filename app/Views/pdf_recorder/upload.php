<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<style>
    .upload-area {
        border: 2px dashed #E66136;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .upload-area:hover {
        background-color: #fff;
        border-color: #d4552a;
    }
    .upload-area.dragover {
        background-color: #fff3f0;
        border-color: #E66136;
    }
    .upload-icon {
        font-size: 48px;
        color: #E66136;
        margin-bottom: 20px;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="card-title">PDF Statement - Upload Bank Statement</h4>
                </div>

                <div id="alertContainer"></div>

                <form id="pdfUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Upload PDF File</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="mdi mdi-file-pdf-box upload-icon"></i>
                                <h5>Drag & Drop PDF File Here</h5>
                                <p class="text-muted">or</p>
                                <input type="file" class="form-control d-none" name="pdf_file" id="pdf_file" accept=".pdf" required>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('pdf_file').click()">
                                    <i class="mdi mdi-upload me-2"></i>Browse Files
                                </button>
                                <p class="text-muted mt-2 mb-0">Maximum file size: 10MB</p>
                                <p class="text-muted" id="fileName"></p>
                            </div>
                        </div>
                    </div>

                    <?php if (
                        isset($passwordSupportAvailable) &&
                        $passwordSupportAvailable
                    ): ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label">PDF Password (if protected)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-lock fs-5"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter PDF password (optional)">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="mdi mdi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Enter the password if your PDF is password-protected. The system will unlock it automatically.</small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="mdi mdi-alert-circle me-2 fs-5"></i>
                                <div>
                                    <strong>Note:</strong> Do not upload password-protected files here. Please unlock your PDF file before uploading.
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn hr-btnbg" id="uploadBtn">
                                <i class="mdi mdi-upload me-2"></i>Upload & Parse PDF
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" id="resetBtn" style="display: none;">
                                <i class="mdi mdi-refresh me-2"></i>Upload Another PDF
                            </button>
                        </div>
                    </div>
                </form>

                <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Processing PDF, please wait...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('pdf_file');
    const fileName = document.getElementById('fileName');
    const uploadForm = document.getElementById('pdfUploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const resetBtn = document.getElementById('resetBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    // Toggle password visibility (only if password field exists)
    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('mdi-eye');
            eyeIcon.classList.toggle('mdi-eye-off');
        });
    }

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            fileInput.files = files;
            updateFileName(files[0].name);
        } else {
            showAlert('Please drop a valid PDF file.', 'danger');
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            updateFileName(e.target.files[0].name);
        }
    });

    function updateFileName(name) {
        fileName.textContent = 'Selected: ' + name;
        fileName.style.color = '#28a745';
    }

    // Form submission
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!fileInput.files.length) {
            showAlert('Please select a PDF file.', 'danger');
            return;
        }

        const formData = new FormData(uploadForm);
        const csrfToken = document.getElementById('csrfToken').value;
        formData.append('<?= csrf_token() ?>', csrfToken);

        uploadBtn.disabled = true;
        loadingIndicator.style.display = 'block';

        fetch('<?= base_url("api/pdf-recorder/upload") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            uploadBtn.disabled = false;

            if (data.status === 'success') {
                showAlert('PDF uploaded and parsed successfully! Redirecting to preview...', 'success');
                setTimeout(() => {
                    window.location.href = '<?= base_url(
                        "pdf-recorder/preview",
                    ) ?>';
                }, 1500);
            } else {
                showAlert(data.message || 'Failed to upload PDF.', 'danger');
            }
        })
        .catch(error => {
            loadingIndicator.style.display = 'none';
            uploadBtn.disabled = false;
            showAlert('An error occurred while uploading the PDF.', 'danger');
            console.error('Error:', error);
        });
    });

    // Reset button
    resetBtn.addEventListener('click', function() {
        uploadForm.reset();
        fileName.textContent = '';
        fileInput.value = '';
        resetBtn.style.display = 'none';
    });

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
});
</script>

<?= $this->endSection() ?>
