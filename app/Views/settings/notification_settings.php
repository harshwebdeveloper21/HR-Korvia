<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #E66136;
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    .slider.round {
        border-radius: 34px;
    }
    .slider.round:before {
        border-radius: 50%;
    }
    @media (max-width: 767px) {
        .notification-sm-btn {
            font-size: 12px !important;
            padding: 8px 16px !important;
            margin-top: 10px !important;
        }
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-bell-outline me-2"></i>Push Notification Settings
                </h4>
                <p class="text-muted mb-4">Control whether admins receive push notifications for employee activities.</p>

                <form id="notificationSettingsForm">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken">

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label">
                                    <i class="mdi mdi-clock-in me-2"></i>Attendance Notifications
                                </label>
                                <div class="col-sm-8">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="attendance_notifications_enabled" name="attendance_notifications_enabled">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="ms-3" id="attendanceStatusText">Loading...</span>
                                </div>
                            </div>
                            <p class="text-muted small ms-4">Receive notifications when employees check in or check out</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label">
                                    <i class="mdi mdi-calendar-clock me-2"></i>Leave Notifications
                                </label>
                                <div class="col-sm-8">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="leave_notifications_enabled" name="leave_notifications_enabled">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="ms-3" id="leaveStatusText">Loading...</span>
                                </div>
                            </div>
                            <p class="text-muted small ms-4">Receive notifications when employees apply for leave</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group row align-items-center">
                                <label class="col-sm-4 col-form-label">
                                    <i class="mdi mdi-cake-variant me-2"></i>Birthday Notifications
                                </label>
                                <div class="col-sm-8">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="birthday_notifications_enabled" name="birthday_notifications_enabled">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="ms-3" id="birthdayStatusText">Loading...</span>
                                </div>
                            </div>
                            <p class="text-muted small ms-4">Receive daily notifications at 12 AM for employees whose birthday is today</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <i class="mdi mdi-information-outline me-2"></i>
                                <strong>Note:</strong> When enabled, admins will receive push notifications on their devices (mobile/desktop). These settings only affect push notifications, not email notifications.
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary notification-sm-btn" id="saveBtn" style="background: linear-gradient(135deg, #e66136, #ff7b4a); border: none;">
                                <i class="mdi mdi-content-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load current settings
    loadSettings();

    function loadSettings() {
        const token = localStorage.getItem('token');
        if (!token) {
            Swal.fire({
                title: 'Error',
                text: 'Please login to access this page.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '/login';
            });
            return;
        }

        $.ajax({
            url: '/api/notification-settings/get',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                if (response.status === 'success') {
                    const attendanceEnabled = response.data.attendance_notifications_enabled;
                    const leaveEnabled = response.data.leave_notifications_enabled;
                    const birthdayEnabled = response.data.birthday_notifications_enabled;

                    $('#attendance_notifications_enabled').prop('checked', attendanceEnabled);
                    $('#leave_notifications_enabled').prop('checked', leaveEnabled);
                    $('#birthday_notifications_enabled').prop('checked', birthdayEnabled);

                    updateAttendanceStatusText(attendanceEnabled);
                    updateLeaveStatusText(leaveEnabled);
                    updateBirthdayStatusText(birthdayEnabled);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to load settings',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    Swal.fire({
                        title: 'Unauthorized',
                        text: 'Please login again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '/login';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load notification settings',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }

    function updateAttendanceStatusText(enabled) {
        if (enabled) {
            $('#attendanceStatusText').html('<span class="badge bg-success">Enabled</span>');
        } else {
            $('#attendanceStatusText').html('<span class="badge bg-secondary">Disabled</span>');
        }
    }

    function updateLeaveStatusText(enabled) {
        if (enabled) {
            $('#leaveStatusText').html('<span class="badge bg-success">Enabled</span>');
        } else {
            $('#leaveStatusText').html('<span class="badge bg-secondary">Disabled</span>');
        }
    }

    function updateBirthdayStatusText(enabled) {
        if (enabled) {
            $('#birthdayStatusText').html('<span class="badge bg-success">Enabled</span>');
        } else {
            $('#birthdayStatusText').html('<span class="badge bg-secondary">Disabled</span>');
        }
    }

    // Update status text when toggles change
    $('#attendance_notifications_enabled').change(function() {
        updateAttendanceStatusText($(this).is(':checked'));
    });

    $('#leave_notifications_enabled').change(function() {
        updateLeaveStatusText($(this).is(':checked'));
    });

    $('#birthday_notifications_enabled').change(function() {
        updateBirthdayStatusText($(this).is(':checked'));
    });

    // Handle form submission
    $('#notificationSettingsForm').submit(function(e) {
        e.preventDefault();

        const token = localStorage.getItem('token');
        if (!token) {
            Swal.fire({
                title: 'Error',
                text: 'Please login to access this page.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        const attendanceEnabled = $('#attendance_notifications_enabled').is(':checked');
        const leaveEnabled = $('#leave_notifications_enabled').is(':checked');
        const birthdayEnabled = $('#birthday_notifications_enabled').is(':checked');

        // Disable button during save
        $('#saveBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i>Saving...');

        $.ajax({
            url: '/api/notification-settings/update',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            data: JSON.stringify({
                attendance_notifications_enabled: attendanceEnabled,
                leave_notifications_enabled: leaveEnabled,
                birthday_notifications_enabled: birthdayEnabled
            }),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: 'Notification settings saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'hr-btnbg'
                        }
                    });
                    updateAttendanceStatusText(attendanceEnabled);
                    updateLeaveStatusText(leaveEnabled);
                    updateBirthdayStatusText(birthdayEnabled);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to save settings',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to save notification settings';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Re-enable button
                $('#saveBtn').prop('disabled', false).html('<i class="mdi mdi-content-save me-2"></i>Save Settings');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
