<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<style>
    .category-card {
        cursor: pointer;
        background-color: #f8fafc;
        border: 2px solid #f1f5f9 !important;
        border-radius: 16px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .check-mark { opacity: 0; transform: scale(0.5); transition: all 0.3s ease; }
    
    .category-visual-box { width: 60px; height: 60px; border-radius: 14px; font-size: 28px; transition: all 0.3s ease; }
    
    .complaint-card .category-visual-box { background-color: #feedef; color: #ef4444; }
    .complaint-card .check-mark { color: #ef4444; }
    .feedback-card .category-visual-box { background-color: #e0f2fe; color: #0ea5e9; }
    .feedback-card .check-mark { color: #0ea5e9; }

    #typeComplaint:checked + .complaint-card { border-color: #ef4444 !important; background-color: #fffafa; box-shadow: 0 8px 20px rgba(239,68,68,0.1); }
    #typeFeedback:checked + .feedback-card { border-color: #0ea5e9 !important; background-color: #f0f9ff; box-shadow: 0 8px 20px rgba(14,165,233,0.1); }
    
    .category-radio:checked + .category-card .check-mark { opacity: 1; transform: scale(1); }
    .category-radio:checked + .category-card .category-visual-box { transform: scale(1.1) rotate(-5deg); }

    .employee-link-card { border-color: #f1f5f9 !important; transition: all 0.3s ease; }
    .link-icon-circle { width: 48px; height: 48px; background: #fff; border-radius: 50%; color: #E66136; font-size: 22px; }

    .select2-modern-wrapper .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important; border-radius: 10px !important; height: 44px !important;
    }
    .select2-modern-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px !important; padding-left: 15px !important; color: #334155 !important;
    }
    
    .btn-check:checked + .btn-outline-primary { background-color: #4B49AC !important; border-color: #4B49AC !important; color: white !important; }
    .btn-check:checked + .btn-outline-success { background-color: #28a745 !important; border-color: #28a745 !important; color: white !important; }
    .btn-check:checked + .btn-outline-secondary { background-color: #6c757d !important; border-color: #6c757d !important; color: white !important; }
</style>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <!-- Header Banner - Compact Version -->
            <div class="card-header p-0 position-relative" style="background: linear-gradient(135deg, #E66136 0%, #ff8c69 100%); min-height: 100px;">
                <div class="position-absolute top-50 start-0 translate-middle-y ps-4 text-white">
                    <h4 class="fw-bold mb-0 text-white">Update Record #<?= $complaint['id'] ?></h4>
                    <p class="mb-0 opacity-75 small text-white" style="font-size: 0.75rem;">Manage resolution and case details for this submission.</p>
                </div>
                <!-- Abstract Design Elements -->
                <div class="position-absolute top-0 end-0 p-3 opacity-25 text-white">
                    <i class="mdi mdi-shield-edit" style="font-size: 6rem; line-height: 1; transform: rotate(-10deg);"></i>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                <form id="updateComplaintForm" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $complaint['id'] ?>">
                    
                    <div class="row g-4">
                        <!-- Compact Category Selection -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block ps-1" style="letter-spacing: 1px; font-size: 0.65rem;">Selected Category</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="category-item h-100">
                                        <input type="radio" name="type" id="typeComplaint" value="Complaint" <?= $complaint['type'] == 'Complaint' ? 'checked' : '' ?> class="category-radio d-none">
                                        <label for="typeComplaint" class="category-card complaint-card d-flex align-items-center p-3 h-100">
                                            <div class="category-visual-box me-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-bullhorn"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-0 card-title">Complaint</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.7rem;">Issues requiring formal resolution.</p>
                                            </div>
                                            <div class="check-mark ms-auto">
                                                <i class="mdi mdi-check-circle fs-5"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="category-item h-100">
                                        <input type="radio" name="type" id="typeFeedback" value="Feedback" <?= $complaint['type'] == 'Feedback' ? 'checked' : '' ?> class="category-radio d-none">
                                        <label for="typeFeedback" class="category-card feedback-card d-flex align-items-center p-3 h-100">
                                            <div class="category-visual-box me-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-message-reply-text"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-0 card-title">Feedback</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.7rem;">Improvements or helpful ideas.</p>
                                            </div>
                                            <div class="check-mark ms-auto">
                                                <i class="mdi mdi-check-circle fs-5"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Employee Selection Card -->
                        <div class="col-12">
                            <div class="employee-link-card p-3 rounded-4 border shadow-sm" style="background-color: #fcfcfc;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="link-icon-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                                        <i class="mdi mdi-account-switch-outline"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark small">Employee Data Link</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem;">Record is associated with the selected employee below.</p>
                                    </div>
                                </div>
                                <div class="select2-modern-wrapper">
                                    <select class="form-select select2 w-100" name="user_id">
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= $u['id'] == $complaint['user_id'] ? 'selected' : '' ?>>
                                                <?= esc($u['username']) ?> (<?= ucfirst($u['role']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Standard Information -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Update Subject</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-format-title text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 py-2" name="subject" required value="<?= esc($complaint['subject']) ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Contact Person</label>
                            <input type="text" class="form-control py-2 ps-3" name="name" value="<?= esc($complaint['name']) ?>" style="border-radius: 8px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Primary Mobile</label>
                            <input type="text" class="form-control py-2 ps-3" name="mobile" value="<?= esc($complaint['mobile']) ?>" style="border-radius: 8px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Request Content</label>
                            <textarea class="form-control py-3" name="message" rows="5" required style="border-radius: 10px;"><?= esc($complaint['message']) ?></textarea>
                        </div>

                        <!-- Resolution Section (Admin/HR Only) -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="text-uppercase text-muted fw-bold small mb-4" style="letter-spacing: 1px;">Workflow & Resolution</h6>
                            
                            <div class="p-4 rounded-4 shadow-sm border" style="background-color: #f8fafc;">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark mb-3">Workflow Status</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <input type="radio" class="btn-check" name="status" id="st_pending" value="Pending" <?= $complaint['status'] == 'Pending' ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-secondary rounded-pill px-4" for="st_pending">Pending</label>

                                            <input type="radio" class="btn-check" name="status" id="st_progress" value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-primary rounded-pill px-4" for="st_progress">In Progress</label>

                                            <input type="radio" class="btn-check" name="status" id="st_resolved" value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'checked' : '' ?>>
                                            <label class="btn btn-outline-success rounded-pill px-4" for="st_resolved">Resolved</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-dark">Official Resolution Remark</label>
                                        <textarea class="form-control py-3 bg-white" name="admin_remark" rows="4" placeholder="Enter resolution details, action taken, etc..." style="border-radius: 10px; border: 1px solid #e2e8f0;"><?= esc($complaint['admin_remark'] ?? '') ?></textarea>
                                        <small class="text-muted d-block mt-2"><i class="mdi mdi-information-outline me-1"></i> Visible to employee upon submission.</small>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-dark">Resolution Attachment (Optional)</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="resolution_file" accept="image/*,.pdf">
                                            <?php if (!empty($complaint['resolution_file'])): ?>
                                                <a href="<?= base_url('uploads/complaints/' . $complaint['resolution_file']) ?>" target="_blank" class="btn btn-outline-info">
                                                    <i class="mdi mdi-eye"></i> View Current
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">Upload a screenshot or document of the resolution.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                        <a href="<?= base_url('complaints/admin') ?>" class="btn btn-link text-muted text-decoration-none fw-semibold">
                            <i class="mdi mdi-arrow-left me-1"></i> Cancel and go back
                        </a>
                        <button type="submit" id="submitBtn" class="btn btn-lg px-5 text-white shadow-sm fw-bold" style="background-color: #E66136; border-radius: 10px;">
                           Update <i class="mdi mdi-content-save-check ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    if ($('.select2').length) {
        $('.select2').each(function() {
            $(this).select2({
                placeholder: "Search employee...",
                allowClear: true,
                width: '100%'
            });
        });
    }

    $('#updateComplaintForm').on('submit', function(e) {
        e.preventDefault();
        
        let $btn = $('#submitBtn');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i> Saving Updates...');

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
                        showConfirmButton: false
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
                Swal.fire('Error', 'Communication failed. Please check your connection.', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
