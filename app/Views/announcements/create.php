<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="main-container container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card custom-card rounded-3 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2 py-3 px-4">
                    <span style="background-color:#E66136; color:#fff; border-radius:8px; width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                        <i class="mdi mdi-bullhorn fs-18"></i>
                    </span>
                    <div class="card-title fw-bold mb-0" style="font-size: 1.1rem;">Add Announcement</div>
                </div>
                <div class="card-body p-4">
                    <form id="createAnnouncementForm" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="row gy-4">

                            <!-- Title -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Title</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-bullhorn"></i>
                                    </span>
                                    <input type="text" class="form-control" name="title" required placeholder="Enter announcement title">
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Announcement Type</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-flag"></i>
                                    </span>
                                    <select class="form-select" name="type" required>
                                        <option value="Info">ℹ️ Info (Blue)</option>
                                        <option value="Warning">⚠️ Warning (Yellow)</option>
                                        <option value="Success">✅ Success (Green)</option>
                                        <option value="Event">📅 Event (Purple)</option>
                                        <option value="Urgent">🚨 Urgent (Red)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xl-12">
                                <label class="form-label text-dark fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3" required placeholder="Enter announcement details..."></textarea>
                            </div>

                            <!-- Start Date -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-calendar-range"></i>
                                    </span>
                                    <input type="date" class="form-control" name="start_date" required value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-calendar-check"></i>
                                    </span>
                                    <input type="date" class="form-control" name="end_date" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                </div>
                            </div>

                            <!-- Target Audience -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Target Audience</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-account-group"></i>
                                    </span>
                                    <select class="form-select" id="target_audience" name="target_audience" required>
                                        <option value="All Users">All Users</option>
                                        <option value="Specific Role">Specific Role</option>
                                        <option value="Specific Users">Specific Users</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-check-circle"></i>
                                    </span>
                                    <select class="form-select" name="status">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Select Roles (styled pill checkboxes) -->
                            <div class="col-xl-12 target-roles-div" style="display:none;">
                                <label class="form-label text-dark fw-semibold">Select Roles</label>
                                <div class="input-group align-items-stretch">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-shield-account"></i>
                                    </span>
                                    <div class="form-control d-flex flex-wrap align-items-center gap-3" style="height:auto; min-height:50px; background:#fff;">
                                        <label class="role-pill-label" for="roleAdmin">
                                            <input type="checkbox" class="role-pill-input" name="target_roles[]" value="admin" id="roleAdmin">
                                            <span class="role-pill"><i class="mdi mdi-shield-crown-outline me-1"></i>Admin</span>
                                        </label>
                                        <label class="role-pill-label" for="roleHr">
                                            <input type="checkbox" class="role-pill-input" name="target_roles[]" value="hr" id="roleHr">
                                            <span class="role-pill"><i class="mdi mdi-account-tie-outline me-1"></i>HR</span>
                                        </label>
                                        <label class="role-pill-label" for="roleEmployee">
                                            <input type="checkbox" class="role-pill-input" name="target_roles[]" value="employee" id="roleEmployee">
                                            <span class="role-pill"><i class="mdi mdi-account-outline me-1"></i>Employee</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Select Users (Select2 dropdown) -->
                            <div class="col-xl-12 target-users-div" style="display:none;">
                                <label class="form-label text-dark fw-semibold">Select Users</label>
                                <div class="input-group align-items-start">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white; min-height: 50px;">
                                        <i class="mdi mdi-account-multiple-check"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <select class="form-control select2-users" id="target_users" name="target_users[]" multiple>
                                            <?php foreach ($users as $u): ?>
                                                <option value="<?= $u['id'] ?>"><?= esc($u['username']) ?> (<?= ucfirst($u['role']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachment -->
                            <div class="col-xl-12">
                                <label class="form-label text-dark fw-semibold">Attachment <span class="text-muted fw-normal">(Optional)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-paperclip"></i>
                                    </span>
                                    <input type="file" class="form-control" name="attachment">
                                </div>
                            </div>

                            <!-- Buttons -->
                             <div class="col-xl-12 d-flex justify-content-end align-items-center gap-3 mt-4 pt-3 border-top">
                                 <a href="/announcements/admin" class="btn px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #E66136; border-radius: 8px;">
                                     Back
                                 </a>
                                 <button type="submit" id="submitBtn" class="btn px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #E66136; border-radius: 8px;">
                                     Submit
                                 </button>
                             </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Input Group */
    .input-group-text {
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
        min-width: 46px;
        justify-content: center;
    }
    .form-control, .form-select {
        border-radius: 8px !important;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
    }
    .input-group .form-control:not(:first-child),
    .input-group .form-select:not(:first-child) {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #E66136;
        box-shadow: 0 0 0 0.2rem rgba(230, 97, 54, 0.1);
    }

    /* Role Pill Checkboxes */
    .role-pill-label { cursor: pointer; margin: 0; }
    .role-pill-input { display: none; }
    .role-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        border-radius: 50px;
        border: 2px solid #dee2e6;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }
    .role-pill:hover { border-color: #E66136; color: #E66136; background: #fff5f2; }
    .role-pill-input:checked + .role-pill {
        background-color: #E66136;
        border-color: #E66136;
        color: #fff;
        box-shadow: 0 2px 8px rgba(230, 97, 54, 0.3);
    }

    /* Select2 */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 0 8px 8px 0;
        min-height: 50px;
        padding: 6px 8px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #E66136;
        box-shadow: 0 0 0 0.2rem rgba(230, 97, 54, 0.1);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #E66136;
        border: none;
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-weight: 500;
        font-size: 0.8rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255,255,255,0.7);
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #fff; }
    .select2-dropdown { border-color: #E66136; border-radius: 8px; }
    .select2-container--default .select2-results__option--highlighted { background-color: #E66136; }

    /* Card */
    .card { border: none; overflow: visible; }
    .card-header { background-color: #fff; border-bottom: 1px solid #f1f5f9; padding: 16px 20px; }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {

    // Initialize Select2 for Users
    if ($('.select2-users').length) {
        $('.select2-users').select2({
            placeholder: "Type to search and select users...",
            allowClear: true,
            width: '100%'
        });
    }

    // Show/hide conditional sections
    $('#target_audience').on('change', function() {
        var val = $(this).val();
        $('.target-roles-div').hide();
        $('.target-users-div').hide();
        if (val === 'Specific Role') {
            $('.target-roles-div').fadeIn(200);
        } else if (val === 'Specific Users') {
            $('.target-users-div').fadeIn(200);
        }
    });

    // Form submit
    $('#createAnnouncementForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Create form submitted');

        var $btn = $('#submitBtn');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Submitting...');

        var formData = new FormData(this);
        $.ajax({
            url: '<?= base_url("announcements/store") ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Response:', response);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Announcement Created!',
                        text: response.message || 'Your announcement has been saved.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '<?= base_url("announcements/admin") ?>';
                    });
                } else {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-send me-1"></i>Submit');
                    var errorMsg = response.message || 'Something went wrong.';
                    if (response.messages && typeof response.messages === 'object') {
                        errorMsg = Object.values(response.messages).join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', html: errorMsg });
                }
            },
            error: function(xhr) {
                console.error('Ajax error:', xhr.status, xhr.responseText);
                $btn.prop('disabled', false).html('<i class="mdi mdi-send me-1"></i>Submit');
                var msg = 'Server error (' + xhr.status + '). Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });

});
</script>
<?= $this->endSection(); ?>
