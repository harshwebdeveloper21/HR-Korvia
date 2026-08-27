<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<div class="main-container container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom-0 p-2">
                    <h5 class="card-title fw-bold mb-0">Manage Announcements</h5>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportAnnouncements" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/announcements/create" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Announcement
                        </a>
                    </div>
                </div>
                <div class="card-body ">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <span>Show</span>
                            <select class="form-select form-select-sm mx-2" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span>entries</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control form-control-sm" placeholder="Search" style="border-radius: 4px;">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap table-bordered table-striped" id="announcementsTable">
                            <thead style="background-color: #000; color: #fff;">
                                <tr>
                                    <th class="sort-header">TITLE <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                    <th class="sort-header">TYPE <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                    <th class="sort-header">AUDIENCE <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                    <th class="sort-header">DATE RANGE <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                    <th class="sort-header">STATUS <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                    <th class="sort-header">ACTION <i class="mdi mdi-unfold-more-horizontal float-end fs-12 opacity-50"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($announcements as $announcement): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= esc($announcement['title']) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'border-info text-info';
                                            switch ($announcement['type']) {
                                                case 'Warning': $badgeClass = 'border-warning text-warning'; break;
                                                case 'Success': $badgeClass = 'border-success text-success'; break;
                                                case 'Urgent': $badgeClass = 'border-danger text-danger'; break;
                                                case 'Event': $badgeClass = 'border-primary text-primary'; break;
                                            }
                                            ?>
                                            <span class="badge bg-transparent border <?= $badgeClass ?>" style="padding: 5px 10px; font-weight: 500;">
                                                <?= $announcement['type'] ?>
                                            </span>
                                        </td>
                                        <td><span class="text-muted"><?= $announcement['target_audience'] ?></span></td>
                                        <td>
                                            <div class="text-muted" style="font-size: 0.85rem;">
                                                <?= date('d M', strtotime($announcement['start_date'])) ?> - <?= date('d M, Y', strtotime($announcement['end_date'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($announcement['status'] == 'Active'): ?>
                                                <span class="text-success" style="font-weight: 500;">Active</span>
                                            <?php else: ?>
                                                <span class="text-danger" style="font-weight: 500;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="javascript:void(0)" class="btn btn-sm btn-icon btn-view-light" onclick="showAnnouncement(<?= esc(json_encode($announcement)) ?>)" title="View Details">
                                                    <i class="mdi mdi-eye text-dark fs-18"></i>
                                                </a>
                                                <a href="/announcements/edit/<?= $announcement['id'] ?>" class="btn btn-sm btn-icon btn-warning-light" title="Edit">
                                                    <i class="mdi mdi-pencil fs-18"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-danger-light delete-btn" data-id="<?= $announcement['id'] ?>" title="Delete">
                                                    <i class="mdi mdi-delete fs-18"></i>
                                                </a>
                                            </div>
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
</div>

<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        // Get CSRF from meta tags (defined in header_link)
        var csrfName = $('meta[name="csrf-token"]').attr('data-name');
        var csrfHash = $('meta[name="csrf-token"]').attr('content');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/announcements/delete/' + id,
                    type: 'POST', // Use POST for wider server compatibility
                    data: {
                        _method: 'DELETE', // Method spoofing for CI4
                        [csrfName]: csrfHash
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            // If CSRF expired or other error, refresh hash for next try
                            if (response.csrfHash) {
                                $('meta[name="csrf-token"]').attr('content', response.csrfHash);
                            }
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Permission denied or security token expired (403). Please refresh the page.', 'error');
                    }
                });
            }
        });
    });

    // 📥 Export to Excel functionality
    $('#btnExportAnnouncements').on('click', function () {
        const $btn = $(this);
        const token = localStorage.getItem('token');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

        fetch(`<?= base_url('api/announcements/export') ?>`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(async response => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
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
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Announcements exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
            Swal.fire('Export Error', error.message || 'Failed to export announcements', 'error');
        });
    });
});

/**
 * Show Announcement Details in a Bootstrap/Premium Styled Modal
 */
function showAnnouncement(announcement) {
    Swal.fire({
        title: '',
        html: `
            <div class="text-start">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h4 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1.2rem;">Announcement Details</h4>
                    <button type="button" class="btn-close" onclick="Swal.close()" aria-label="Close"></button>
                </div>
                <div class="mb-3">
                    <h5 class="fw-bold mb-1" style="color: #334155;">${announcement.title}</h5>
                    <p class="text-muted" style="font-size: 0.9rem;">${announcement.description}</p>
                </div>
                <div class="d-flex justify-content-end mt-4 pt-3">
                    <button type="button" class="btn btn-info text-white px-4" style="background-color: #00c4ff; border: none; font-weight: 600;" onclick="Swal.close()">Close</button>
                </div>
            </div>
        `,
        showConfirmButton: false,
        width: '500px',
        padding: '1.5rem',
        customClass: {
            popup: 'rounded-3 shadow-lg border-0'
        },
        showCloseButton: false,
        backdrop: `rgba(0,0,0,0.4)`
    });
}
</script>

<style>
    #announcementsTable {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
        margin-top: 20px !important;
    }
    #announcementsTable thead th {
        background-color: #000 !important;
        color: #fff !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px;
        border: none;
        vertical-align: middle;
        letter-spacing: 0.5px;
    }
    #announcementsTable tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: #475569;
    }
    #announcementsTable tbody tr:hover {
        background-color: #f8fafc;
    }
    .sort-header {
        cursor: pointer;
        white-space: nowrap;
    }
    .badge {
        font-weight: 500;
        border-radius: 6px;
        padding: 6px 12px;
    }
    
    /* Action Buttons Styling */
    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%; /* Pure Circle */
        padding: 0;
        transition: all 0.2s ease;
        border: none;
    }
    .fs-18 {
        font-size: 1.15rem !important;
    }
    .btn-view-light {
        background-color: #f1f5f9;
        color: #334155;
    }
    .btn-view-light:hover {
        background-color: #334155;
        color: #fff !important;
    }
    .btn-view-light:hover i {
        color: #fff !important;
    }
    .btn-warning-light {
        background-color: #fffbeb;
        color: #f59e0b;
    }
    .btn-warning-light:hover {
        background-color: #f59e0b;
        color: #fff !important;
    }
    .btn-danger-light {
        background-color: #fef2f2;
        color: #ef4444;
    }
    .btn-danger-light:hover {
        background-color: #ef4444;
        color: #fff !important;
    }
    
    .avatar.avatar-sm {
        width: 32px;
        height: 32px;
        line-height: 32px;
        border-radius: 4px;
    }
</style>
<?= $this->endSection(); ?>
