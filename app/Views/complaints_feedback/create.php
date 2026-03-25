<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <!-- Header Banner - Compact Version -->
            <div class="card-header p-0 position-relative" style="background: linear-gradient(135deg, #E66136 0%, #ff8c69 100%); min-height: 100px;">
                <div class="position-absolute top-50 start-0 translate-middle-y ps-4 text-white">
                    <h4 class="fw-bold mb-0 text-white">New Submission</h4>
                    <p class="mb-0 opacity-75" style="font-size: 0.75rem;">Tell us what's on your mind. We're here to listen.</p>
                </div>
                <!-- Abstract Design Elements -->
                <div class="position-absolute top-0 end-0 p-3 opacity-25 text-white">
                    <i class="mdi mdi-message-draw" style="font-size: 6rem; line-height: 1; transform: rotate(-15deg);"></i>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                <form id="complaintForm" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <!-- Compact Category Selection -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block ps-1" style="letter-spacing: 1px; font-size: 0.65rem;">Record Category</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="category-item h-100">
                                        <input type="radio" name="type" id="typeComplaint" value="Complaint" checked class="category-radio d-none">
                                        <label for="typeComplaint" class="category-card complaint-card d-flex align-items-center p-3 h-100">
                                            <div class="category-visual-box me-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-bullhorn"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-0 card-title">Complaint</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.7rem;">Report issues requiring resolution.</p>
                                            </div>
                                            <div class="check-mark ms-auto opacity-0">
                                                <i class="mdi mdi-check-circle fs-5"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="category-item h-100">
                                        <input type="radio" name="type" id="typeFeedback" value="Feedback" class="category-radio d-none">
                                        <label for="typeFeedback" class="category-card feedback-card d-flex align-items-center p-3 h-100">
                                            <div class="category-visual-box me-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-message-reply-text"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-0 card-title">Feedback</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.7rem;">Suggest improvements or share ideas.</p>
                                            </div>
                                            <div class="check-mark ms-auto opacity-0">
                                                <i class="mdi mdi-check-circle fs-5"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Employee Selection Card -->
                        <?php if (in_array($role, ['admin', 'hr'])): ?>
                        <div class="col-12">
                            <div class="employee-link-card p-3 rounded-4 border shadow-sm" style="background-color: #fcfcfc;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="link-icon-circle me-3 d-flex align-items-center justify-content-center shadow-sm">
                                        <i class="mdi mdi-account-switch-outline"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark small">Employee Registration Control</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem;">Select the person on whose behalf this is being submitted.</p>
                                    </div>
                                </div>
                                <div class="select2-modern-wrapper">
                                    <select class="form-select select2 w-100" name="user_id">
                                        <option value="">Start typing name or role to search...</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= $u['id'] == $user->sub ? 'selected' : '' ?>>
                                                <?= esc($u['username']) ?> (<?= ucfirst($u['role']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Standard Fields -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Subject</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-format-title"></i></span>
                                <input type="text" class="form-control border-start-0 py-2" name="subject" required placeholder="A brief heading for your request">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Contact Name</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-account-outline"></i></span>
                                <input type="text" class="form-control border-start-0 py-2" name="name" required value="<?= esc($user->username ?? '') ?>" placeholder="Full Name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Contact Email</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-email-open-outline"></i></span>
                                <input type="email" class="form-control border-start-0 py-2" name="email" required value="<?= esc($user->email ?? '') ?>" placeholder="email@address.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-phone"></i></span>
                                <input type="text" class="form-control border-start-0 py-2" name="mobile" required placeholder="Phone number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Attachment</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-paperclip"></i></span>
                                <input type="file" class="form-control border-start-0 py-2" name="file">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Your Message</label>
                            <textarea class="form-control py-3" name="message" rows="5" required placeholder="Provide as much detail as possible to help us understand..." style="border-radius: 10px;"></textarea>
                        </div>

                        <!-- Status (Admin/HR Only) -->
                        <?php if (in_array($role, ['admin', 'hr'])): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Initial Status</label>
                            <div class="input-group">
                                <span class="input-group-text text-white border-end-0" style="background-color: #E66136;"><i class="mdi mdi-shield-check-outline"></i></span>
                                <select class="form-select border-start-0 py-2" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end align-items-center gap-3 mt-5 pt-4 border-top">
                        <a href="<?= in_array($role, ['admin', 'hr']) ? base_url('complaints/admin') : base_url('complaints') ?>" class="btn px-4 py-2 text-white shadow-sm fw-bold" style="background-color: #E66136; border-radius: 8px;">
                            Back
                        </a>
                        <button type="submit" id="submitBtn" class="btn px-4 py-2 text-white shadow-sm fw-bold" style="background-color: #E66136; border-radius: 8px;">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .category-card {
        cursor: pointer;
        background-color: #f8fafc;
        border: 2px solid #f1f5f9 !important;
        border-radius: 16px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .category-card:hover {
        background-color: #fff;
        border-color: #e2e8f0 !important;
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.05);
    }
    .category-visual-box {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        font-size: 28px;
        transition: all 0.3s ease;
    }
    
    /* Complaint Specific */
    .complaint-card .category-visual-box { background-color: #feedef; color: #ef4444; }
    .complaint-card .check-mark { color: #ef4444; }
    
    /* Feedback Specific */
    .feedback-card .category-visual-box { background-color: #e0f2fe; color: #0ea5e9; }
    .feedback-card .check-mark { color: #0ea5e9; }

    /* Checked States */
    #typeComplaint:checked + .complaint-card {
        border-color: #ef4444 !important;
        background-color: #fffafa;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.1);
    }
    #typeFeedback:checked + .feedback-card {
        border-color: #0ea5e9 !important;
        background-color: #f0f9ff;
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.1);
    }
    .category-radio:checked + .category-card .check-mark {
        opacity: 1;
        transform: scale(1.1);
    }
    .category-radio:checked + .category-card .category-visual-box {
        transform: scale(1.1) rotate(-5deg);
    }
    .employee-link-card {
        background-color: #f8fafc;
        border-color: #f1f5f9 !important;
        transition: all 0.3s ease;
    }
    .employee-link-card:hover {
        border-color: #e2e8f0 !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03) !important;
    }
    .link-icon-circle {
        width: 48px;
        height: 48px;
        background: #fff;
        border-radius: 50%;
        color: #E66136;
        font-size: 22px;
    }
    .select2-modern-wrapper .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        height: 44px !important;
        background-color: #fff !important;
    }
    .select2-modern-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 44px !important;
        padding-left: 15px !important;
        color: #334155 !important;
        font-weight: 500 !important;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    if ($('.select2').length) {
        $('.select2').each(function() {
            $(this).select2({
                placeholder: $(this).find('option[value=""]').text() || "Search...",
                allowClear: true,
                width: '100%'
            });
        });
    }

    $('#complaintForm').on('submit', function(e) {
        e.preventDefault();
        
        // Manual validation for Select2 if Admin/HR
        <?php if (in_array($role, ['admin', 'hr'])): ?>
        if ($('select[name="user_id"]').val() === "") {
            Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select an employee first.' });
            return false;
        }
        <?php endif; ?>

        let $btn = $('#submitBtn');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i> Processing...');

        let formData = new FormData(this);
        $.ajax({
            url: '<?= base_url("api/complaints/store") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted Successfully!',
                        text: response.message,
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= in_array($role, ['admin', 'hr']) ? base_url('complaints/admin') : base_url('complaints') ?>';
                    });
                } else {
                    $btn.prop('disabled', false).html(originalText);
                    let errors = response.messages ? Object.values(response.messages).join("<br>") : response.message;
                    Swal.fire({ icon: 'error', title: 'Action Required', html: errors });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                let msg = 'Communication failed. Please check your connection.';
                if (xhr.status === 400 && xhr.responseJSON) {
                    msg = xhr.responseJSON.messages ? Object.values(xhr.responseJSON.messages).join("<br>") : xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
