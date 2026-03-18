<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="main-container container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Select2 CSS only (JS loaded below after jQuery) -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

            <div class="card custom-card rounded-3 shadow-sm">
                <div class="card-header">
                    <div class="card-title fw-bold" style="font-size: 1.1rem;">Edit Announcement</div>
                </div>
                <div class="card-body p-4">
                    <form id="editAnnouncementForm" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                        <div class="row gy-4">
                            <!-- Title -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Title</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-bullhorn"></i>
                                    </span>
                                    <input type="text" class="form-control" name="title" required value="<?= esc($announcement['title']) ?>">
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
                                        <option value="Info" <?= $announcement['type'] == 'Info' ? 'selected' : '' ?>>Info (Blue)</option>
                                        <option value="Warning" <?= $announcement['type'] == 'Warning' ? 'selected' : '' ?>>Warning (Yellow)</option>
                                        <option value="Success" <?= $announcement['type'] == 'Success' ? 'selected' : '' ?>>Success (Green)</option>
                                        <option value="Event" <?= $announcement['type'] == 'Event' ? 'selected' : '' ?>>Event (Primary)</option>
                                        <option value="Urgent" <?= $announcement['type'] == 'Urgent' ? 'selected' : '' ?>>Urgent (Red)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-xl-12">
                                <label class="form-label text-dark fw-semibold">Description</label>
                                <textarea class="form-control" name="description" rows="3" required><?= esc($announcement['description']) ?></textarea>
                            </div>

                            <!-- Start Date -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-calendar-range"></i>
                                    </span>
                                    <input type="date" class="form-control" name="start_date" required value="<?= $announcement['start_date'] ?>">
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-xl-6">
                                <label class="form-label text-dark fw-semibold">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" style="background-color: #E66136; color: white;">
                                        <i class="mdi mdi-calendar-check"></i>
                                    </span>
                                    <input type="date" class="form-control" name="end_date" required value="<?= $announcement['end_date'] ?>">
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
                                        <option value="All Users" <?= $announcement['target_audience'] == 'All Users' ? 'selected' : '' ?>>All Users</option>
                                        <option value="Specific Role" <?= $announcement['target_audience'] == 'Specific Role' ? 'selected' : '' ?>>Specific Role</option>
                                        <option value="Specific Users" <?= $announcement['target_audience'] == 'Specific Users' ? 'selected' : '' ?>>Specific Users</option>
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
                                        <option value="Active" <?= $announcement['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Inactive" <?= $announcement['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Specific Role Selection -->
                            <?php $targetRoles = explode(',', $announcement['target_roles'] ?? ''); ?>
                            <div class="col-xl-12 target-roles-div" style="<?= $announcement['target_audience'] == 'Specific Role' ? '' : 'display:none;' ?>">
                                <label class="form-label text-dark fw-semibold">Select Roles</label>
                                <div class="d-flex gap-4 p-2 border rounded bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="target_roles[]" value="admin" id="roleAdmin" <?= in_array('admin', $targetRoles) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="roleAdmin">Admin</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="target_roles[]" value="hr" id="roleHr" <?= in_array('hr', $targetRoles) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="roleHr">HR</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="target_roles[]" value="employee" id="roleEmployee" <?= in_array('employee', $targetRoles) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="roleEmployee">Employee</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Specific User Selection -->
                            <?php $targetUsers = explode(',', $announcement['target_users'] ?? ''); ?>
                            <div class="col-xl-12 target-users-div" style="<?= $announcement['target_audience'] == 'Specific Users' ? '' : 'display:none;' ?>">
                                <label class="form-label text-dark fw-semibold">Select Users</label>
                                <select class="form-control select2" id="target_users" name="target_users[]" multiple>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= in_array($u['id'], $targetUsers) ? 'selected' : '' ?>><?= esc($u['username']) ?> (<?= $u['role'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Attachment -->
                            <div class="col-xl-12">
                                <label class="form-label text-dark fw-semibold">Attachment (Keep empty to retain current)</label>
                                <?php if($announcement['attachment']): ?>
                                    <div class="mb-2 small text-muted">Current: <a href="/uploads/announcements/<?= $announcement['attachment'] ?>" target="_blank" class="text-primary"><?= $announcement['attachment'] ?></a></div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="attachment">
                            </div>

                             <!-- Buttons -->
                             <div class="col-xl-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                 <a href="/announcements/admin" class="btn btn-light px-4 fw-semibold" style="border-radius: 8px;">Back</a>
                                 <button type="submit" id="editSubmitBtn" class="btn px-4 text-white fw-semibold" style="background-color: #E66136; border-radius: 8px; box-shadow: 0 4px 12px rgba(230, 97, 54, 0.2);">Submit Changes</button>
                             </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Select2 JS after jQuery (which is in footer_link.php) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    if (typeof $.fn.select2 !== 'undefined' && $('.select2').length) {
        $('.select2').select2({
            placeholder: "Select Users",
            allowClear: true,
            width: '100%'
        });
    }

    $('#target_audience').on('change', function() {
        var val = $(this).val();
        $('.target-roles-div, .target-users-div').hide();
        if (val === 'Specific Role') {
            $('.target-roles-div').show();
        } else if (val === 'Specific Users') {
            $('.target-users-div').show();
        }
    });

    $('#editAnnouncementForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Edit form submitted');

        var id = $('input[name="id"]').val();
        var $submitBtn = $('#editSubmitBtn');
        $submitBtn.prop('disabled', true).text('Saving...');

        var formData = new FormData(this);
        $.ajax({
            url: '<?= base_url("announcements/update/") ?>' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Response:', response);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '<?= base_url("announcements/admin") ?>';
                    });
                } else {
                    $submitBtn.prop('disabled', false).text('Submit Changes');
                    var errorMsg = response.message || 'Something went wrong';
                    if (response.messages && typeof response.messages === 'object') {
                        errorMsg = Object.values(response.messages).join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html: errorMsg });
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', xhr.status, xhr.responseText);
                $submitBtn.prop('disabled', false).text('Submit Changes');
                var msg = 'Server error (' + xhr.status + ')';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });
});
</script>
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-height: 45px;
        padding: 4px 8px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #E66136;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgba(230, 97, 54, 0.1);
        border: 1px solid rgba(230, 97, 54, 0.2);
        color: #E66136;
        border-radius: 4px;
        padding: 2px 8px;
        font-weight: 500;
    }
    .input-group-text {
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
        min-width: 45px;
        justify-content: center;
    }
    .form-control, .form-select {
        border-radius: 8px !important;
        padding: 10px 15px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus, .form-select:focus {
        border-color: #E66136;
        box-shadow: 0 0 0 0.2rem rgba(230, 97, 54, 0.1);
    }
    .card {
        border: none;
        overflow: hidden;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 25px;
    }
</style>
<?= $this->endSection(); ?>
