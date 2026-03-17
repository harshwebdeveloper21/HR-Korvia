<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<div class="row mt-4">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">All Notifications</h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="notificationTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>By</th>
                                <th>Employee</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajax({
            url: '<?= base_url('api/notifications/getNotificationsAll') ?>',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            success: function(response) {
                const tbody = $('#notificationTable tbody');
                tbody.empty(); // Clear previous rows

                if (!response.notifications || response.notifications.length === 0) {
                    tbody.append(`<tr><td colspan="6" class="text-center">No notifications available</td></tr>`);
                    return;
                }

                response.notifications.forEach((notification, index) => {
                    let parsedData;
                    try {
                        parsedData = JSON.parse(notification.data);
                    } catch (e) {
                        parsedData = {
                            message: 'Invalid data format'
                        };
                    }

                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${parsedData.type || '-'}</td>
                            <td>${parsedData.message || '-'}</td>
                            <td>${parsedData.username || '-'}</td>
                            <td>${parsedData.employee || '-'}</td>
                            <td>${notification.created_at}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                $('#notificationTable').DataTable();

            },
            error: function(xhr, status, error) {
                console.error('Error loading notifications:', error);
                $('#notificationTable tbody').html(
                    `<tr><td colspan="6" class="text-danger text-center">Failed to load notifications.</td></tr>`
                );
            }
        });
    });
</script>

<?= $this->endSection(); ?>