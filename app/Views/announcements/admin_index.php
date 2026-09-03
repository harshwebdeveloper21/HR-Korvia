<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Manage Announcements</h4>
                    <div class="d-md-flex gap-2 align-items-center mt-2 mt-md-0">
                        <button type="button" id="btnExportAnnouncements" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>
                        <a href="/announcements/create" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Announcement
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="announcementsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th class="desktop-only-col">Type</th>
                                <th class="desktop-only-col">Audience</th>
                                <th class="desktop-only-col">Date Range</th>
                                <th class="desktop-only-col">Status</th>
                                <th class="desktop-only-col action-column" style="width: 100px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $announcement): ?>
                                <?php
                                    $badgeClass = 'border-info text-info';
                                    switch ($announcement['type']) {
                                        case 'Warning': $badgeClass = 'border-warning text-warning'; break;
                                        case 'Success': $badgeClass = 'border-success text-success'; break;
                                        case 'Urgent': $badgeClass = 'border-danger text-danger'; break;
                                        case 'Event': $badgeClass = 'border-primary text-primary'; break;
                                    }
                                    $dateRange = date('d M', strtotime($announcement['start_date'])) . ' - ' . date('d M, Y', strtotime($announcement['end_date']));
                                ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                                            <div style="flex: 1;">
                                                <span class="fw-semibold text-dark"><?= esc($announcement['title']) ?></span>
                                                <div class="expanded-details" id="announcement-details-<?= $announcement['id'] ?>" onclick="event.stopPropagation();">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Type:</span>
                                                        <span class="detail-value">
                                                            <span class="badge bg-transparent border <?= $badgeClass ?>" style="padding: 3px 8px; font-size: 11px;">
                                                                <?= esc($announcement['type']) ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Audience:</span>
                                                        <span class="detail-value"><?= esc($announcement['target_audience']) ?></span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Date Range:</span>
                                                        <span class="detail-value"><?= $dateRange ?></span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Status:</span>
                                                        <span class="detail-value">
                                                            <?= $announcement['status'] == 'Active' ? '<span class="text-success fw-bold">Active</span>' : '<span class="text-danger fw-bold">Inactive</span>' ?>
                                                        </span>
                                                    </div>
                                                    <?php if (!empty($announcement['description'])): ?>
                                                        <div class="detail-row">
                                                            <span class="detail-label">Details:</span>
                                                            <span class="detail-value"><?= esc($announcement['description']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="detail-actions">
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-info text-white" onclick="showAnnouncement(<?= esc(json_encode($announcement)) ?>)"><i class="mdi mdi-eye"></i> View</a>
                                                        <a href="/announcements/edit/<?= $announcement['id'] ?>" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="deleteAnnouncement(<?= $announcement['id'] ?>);"><i class="mdi mdi-delete"></i> Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="desktop-only-col">
                                        <span class="badge bg-transparent border <?= $badgeClass ?>" style="padding: 4px 8px; font-weight: 500; font-size: 11px;">
                                            <?= esc($announcement['type']) ?>
                                        </span>
                                    </td>
                                    <td class="desktop-only-col"><span class="text-muted"><?= esc($announcement['target_audience']) ?></span></td>
                                    <td class="desktop-only-col text-muted" style="font-size: 0.85rem;"><?= $dateRange ?></td>
                                    <td class="desktop-only-col">
                                        <?php if($announcement['status'] == 'Active'): ?>
                                            <span class="badge bg-success-subtle text-success" style="font-weight: 600;">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger" style="font-weight: 600;">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="desktop-only-col">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="javascript:void(0)" class="text-primary me-1" onclick="showAnnouncement(<?= esc(json_encode($announcement)) ?>)" title="View Details">
                                                <i class="mdi mdi-eye fs-5"></i>
                                            </a>
                                            <a href="/announcements/edit/<?= $announcement['id'] ?>" class="text-warning me-1" title="Edit">
                                                <i class="mdi mdi-pencil fs-5"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="text-danger delete-btn" onclick="deleteAnnouncement(<?= $announcement['id'] ?>);" title="Delete">
                                                <i class="mdi mdi-delete fs-5"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="mobile-expand-col text-center">
                                        <button type="button" class="expand-toggle" data-target="announcement-details-<?= $announcement['id'] ?>" aria-label="Expand details"></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#announcementsTable').DataTable({
        order: [[3, 'desc']],
        columnDefs: [
            {
                targets: [5, 6], // Action and Expand button
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search...'
        }
    });

    table.on('draw', function() {
        if (typeof applyMobileTableVisibility === 'function') {
            applyMobileTableVisibility();
        }
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        var csrfName = $('meta[name="csrf-token"]').attr('data-name') || '<?= csrf_token() ?>';
        var csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
        
        Swal.fire({
            title: 'Delete Announcement?',
            text: "This action cannot be reverted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary ms-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("announcements/delete") ?>/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            if (response.csrfHash) {
                                $('meta[name="csrf-token"]').attr('content', response.csrfHash);
                            }
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Permission denied or session expired. Please refresh the page.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            }
        });
    });

    // Export to Excel
    $('#btnExportAnnouncements').on('click', function () {
        const $btn = $(this);
        const token = localStorage.getItem('token');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

        fetch(`<?= base_url('api/announcements/export') ?>`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(async response => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export');
            if (!response.ok) {
                const err = await response.json().catch(() => ({ message: 'Export failed' }));
                throw new Error(err.message || 'Export failed');
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const dateStr = new Date().toISOString().slice(0, 10);
            a.download = `Announcements_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export');
            Swal.fire('Export Error', error.message || 'Failed to export announcements', 'error');
        });
    });
});

function showAnnouncement(announcement) {
    function safeEscape(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    Swal.fire({
        title: '',
        html: `
            <div class="text-start">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">Announcement Details</h5>
                    <button type="button" class="btn-close" onclick="Swal.close()" aria-label="Close"></button>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-1 text-primary">${safeEscape(announcement.title)}</h6>
                    <div class="mb-2"><span class="badge bg-secondary">${announcement.type}</span> <small class="text-muted ms-2">${announcement.start_date} to ${announcement.end_date}</small></div>
                    <div class="text-muted" style="font-size: 0.9rem; white-space: pre-wrap; word-break: break-word; line-height: 1.6; text-align: left;">${safeEscape((announcement.description || '').trim()) || 'No additional description provided.'}</div>
                </div>
                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="Swal.close()">Close</button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        width: '500px',
        padding: '1.5rem',
        customClass: {
            popup: 'rounded-3 shadow-lg border-0'
        },
        showCloseButton: false
    });
}

window.deleteAnnouncement = function(id) {
    if (!id) return;
    var csrfName = $('meta[name="csrf-token"]').attr('data-name') || '<?= csrf_token() ?>';
    var csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    Swal.fire({
        title: 'Delete Announcement?',
        text: "This action cannot be reverted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary ms-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("api/announcements/delete") ?>/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        if (response.csrfHash) {
                            $('meta[name="csrf-token"]').attr('content', response.csrfHash);
                        }
                        Swal.fire('Error!', response.message || 'Failed to delete announcement.', 'error');
                    }
                },
                error: function(xhr) {
                    $.ajax({
                        url: '<?= base_url("announcements/delete") ?>/' + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            [csrfName]: csrfHash
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', res.message || 'Failed to delete announcement.', 'error');
                            }
                        },
                        error: function(err) {
                            let msg = 'Permission denied or session expired. Please refresh the page.';
                            if (err.responseJSON && err.responseJSON.message) {
                                msg = err.responseJSON.message;
                            }
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                }
            });
        }
    });
};
</script>
<?= $this->endSection(); ?>
