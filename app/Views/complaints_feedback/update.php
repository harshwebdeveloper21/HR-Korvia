<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <!-- Header Banner with Case Info -->
            <div class="card-header p-0" style="background: linear-gradient(135deg, #020617 0%, #1e293b 100%); min-height: 140px;">
                <div class="container-fluid py-4 px-4 h-100 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-<ctrl94>">
                        <div class="text-white">
                            <span class="badge text-uppercase mb-2" style="background-color: #E66136; padding: 6px 12px; letter-spacing: 1px;">
                                <i class="mdi mdi-shield-edit me-1"></i> Management Dashboard
                            </span>
                            <h3 class="fw-bold mb-1">Case #<?= $complaint['id'] ?> Details</h3>
                            <p class="mb-0 text-white-50 small">Manage, update, and resolve employee feedback or complaints.</p>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50 small mb-1">Creation Date</div>
                            <div class="text-white fw-bold"><?= date('d F, Y', strtotime($complaint['created_at'])) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="card-body p-4 p-md-5 bg-white">
                <form id="updateComplaintForm" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $complaint['id'] ?>">
                    
                    <div class="row g-4">
                        <!-- Left Column: Case Information -->
                        <div class="col-lg-7">
                            <h6 class="text-uppercase text-muted fw-bold small mb-4 border-bottom pb-2">Record Information</h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-dark">Subject</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-0"><i class="mdi mdi-information-outline"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2" name="subject" required value="<?= esc($complaint['subject']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-dark">Employee Record Owner</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-0"><i class="mdi mdi-account-cog"></i></span>
                                        <select class="form-select bg-light border-0 py-2 select2" name="user_id">
                                            <?php foreach ($users as $u): ?>
                                                <option value="<?= $u['id'] ?>" <?= $u['id'] == $complaint['user_id'] ? 'selected' : '' ?>>
                                                    <?= esc($u['username']) ?> (<?= ucfirst($u['role']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Contact Person Name</label>
                                    <input type="text" class="form-control bg-light border-0 py-2" name="name" value="<?= esc($complaint['name']) ?>" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Email Registered</label>
                                    <input type="email" class="form-control bg-light border-0 py-2" name="email" value="<?= esc($complaint['email']) ?>" placeholder="email@address.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Mobile Contact</label>
                                    <input type="text" class="form-control bg-light border-0 py-2" name="mobile" value="<?= esc($complaint['mobile']) ?>" placeholder="Phone number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Record Type</label>
                                    <select class="form-select bg-light border-0 py-2" name="type">
                                        <option value="Complaint" <?= $complaint['type'] == 'Complaint' ? 'selected' : '' ?>>Complaint</option>
                                        <option value="Feedback" <?= $complaint['type'] == 'Feedback' ? 'selected' : '' ?>>Feedback</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold small text-dark">Detailed Content</label>
                                <div class="p-3 bg-light rounded-3 border-0" style="min-height: 150px;">
                                    <textarea class="form-control bg-transparent border-0 p-0" name="message" rows="6" required style="resize:none;"><?= esc($complaint['message']) ?></textarea>
                                </div>
                            </div>

                            <?php if ($complaint['file']): ?>
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-white shadow-sm border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-orange bg-opacity-10 p-2" style="background-color: #fef3ef;">
                                        <i class="mdi mdi-file-document-outline fs-4" style="color: #E66136;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Attachment Uploaded</div>
                                        <small class="text-muted"><?= $complaint['file'] ?></small>
                                    </div>
                                </div>
                                <a href="<?= base_url('uploads/complaints/' . $complaint['file']) ?>" target="_blank" class="btn btn-sm text-white px-3 fw-bold" style="background-color: #E66136;">
                                    View File
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Column: Resolution & Status -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm bg-light" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-4 border-bottom pb-2">Status & Resolution</h6>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small">Current Workflow Status</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <input type="radio" class="btn-check" name="status" id="st_pending" value="Pending" <?= $complaint['status'] == 'Pending' ? 'selected' : '' ?>>
                                            <label class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" for="st_pending">Pending</label>

                                            <input type="radio" class="btn-check" name="status" id="st_progress" value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'selected' : '' ?>>
                                            <label class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold" for="st_progress">In Progress</label>

                                            <input type="radio" class="btn-check" name="status" id="st_resolved" value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'selected' : '' ?>>
                                            <label class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold" for="st_resolved">Resolved</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold small">Resolution Remarks / HR Notes</label>
                                        <textarea class="form-control border-0 shadow-sm py-3 px-3" name="admin_remark" rows="8" placeholder="Enter resolution details, action taken, or explanation for the employee..." style="border-radius: 10px;"><?= esc($complaint['admin_remark'] ?? '') ?></textarea>
                                        <div class="mt-2 d-flex align-items-center text-muted">
                                            <i class="mdi mdi-information-outline me-1 fs-6"></i>
                                            <small>Employee will see this comment when they log in.</small>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" id="submitBtn" class="btn btn-lg text-white shadow fw-bold border-0 py-3" style="background-color: #E66136; border-radius: 12px;">
                                            Apply Changes <i class="mdi mdi-content-save-check ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <a href="<?= base_url('complaints/admin') ?>" class="btn btn-light border-0 w-100 mt-3 py-3 fw-semibold text-muted" style="border-radius: 12px;">
                                <i class="mdi mdi-arrow-left me-1"></i> Cancel and go back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(230, 97, 54, 0.08);
    }
    .btn-check:checked + .btn-outline-secondary { background-color: #6c757d !important; color: white !important; }
    .btn-check:checked + .btn-outline-primary { background-color: #4B49AC !important; color: white !important; border-color: #4B49AC !important; }
    .btn-check:checked + .btn-outline-success { background-color: #28a745 !important; color: white !important; border-color: #28a745 !important; }
    
    /* Input transition */
    .form-control, .form-select {
        transition: all 0.2s ease;
    }
    
    .select2-container--default .select2-selection--single {
        background-color: #f8fafc !important;
        border: none !important;
        height: 42px !important;
        border-radius: 8px !important;
        padding-top: 5px !important;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    if ($('.select2').length) {
        $('.select2').select2({
            placeholder: "Search employee...",
            allowClear: true,
            width: '100%'
        });
    }

    $('#updateComplaintForm').on('submit', function(e) {
        e.preventDefault();
        
        let $btn = $('#submitBtn');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i> Saving...');

        let formData = new FormData(this);
        $.ajax({
            url: '<?= base_url("api/complaints/update/" . $complaint['id']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Updated!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fff',
                        iconColor: '#E66136'
                    }).then(() => {
                        window.location.href = '<?= base_url("complaints/admin") ?>';
                    });
                } else {
                    $btn.prop('disabled', false).html(originalText);
                    Swal.fire({ icon: 'error', title: 'Update Failed', text: response.message });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                Swal.fire('Error', 'Communication failed. Please try again.', 'error');
            }
        });
    });

    // Handle initial state of radio buttons (manual fix because btn-check logic)
    $('input[name="status"][value="<?= $complaint['status'] ?>"]').prop('checked', true);
});
</script>
<?= $this->endSection(); ?>
