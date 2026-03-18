<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <!-- Premium Header -->
            <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #4B49AC 0%, #7da2fb 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-white">
                        <h4 class="card-title mb-1 fw-bold text-white">My Complaints & Feedback</h4>
                        <p class="mb-0 opacity-75 small">Track, manage and check status of your submitted requests.</p>
                    </div>
                    <a href="<?= base_url('complaints/create') ?>" class="btn btn-white text-primary fw-bold shadow-sm d-flex align-items-center gap-2 py-2 px-3 bg-white" style="border-radius: 8px;">
                        <i class="mdi mdi-plus-circle fs-5"></i> Submit New Request
                    </a>
                </div>
            </div>
            
            <div class="card-body pt-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="userComplaintsTable">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="py-3 border-0 ps-4">ID</th>
                                <th class="py-3 border-0">SUBJECT & DETAILS</th>
                                <th class="py-3 border-0 text-center">CATEGORY</th>
                                <th class="py-3 border-0 text-center">STATUS</th>
                                <th class="py-3 border-0">DATE SUBMITTED</th>
                                <th class="py-3 border-0 text-end pe-4">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($complaints)): ?>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?= $complaint['id'] ?></td>
                                        <td>
                                            <div class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;"><?= esc($complaint['subject']) ?></div>
                                            <small class="text-muted d-block" style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                <?= esc(substr($complaint['message'], 0, 80)) ?>...
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($complaint['type'] == 'Complaint'): ?>
                                                <span class="badge border border-danger text-danger bg-danger bg-opacity-10 px-3 py-2" style="border-radius:60px;">
                                                    <i class="mdi mdi-alert-circle-outline me-1 small"></i> Complaint
                                                </span>
                                            <?php else: ?>
                                                <span class="badge border border-info text-info bg-info bg-opacity-10 px-3 py-2" style="border-radius:60px;">
                                                    <i class="mdi mdi-lightbulb-outline me-1 small"></i> Feedback
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $statusClass = 'warning';
                                            $statusIcon = 'clock-outline';
                                            if ($complaint['status'] == 'In Progress') { $statusClass = 'primary'; $statusIcon = 'progress-clock'; }
                                            if ($complaint['status'] == 'Resolved') { $statusClass = 'success'; $statusIcon = 'check-circle-outline'; }
                                            ?>
                                            <div class="d-inline-flex align-items-center text-<?= $statusClass ?> fw-bold small">
                                                <i class="mdi mdi-<?= $statusIcon ?> me-1 fs-6"></i> <?= $complaint['status'] ?>
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('d M, Y', strtotime($complaint['created_at'])) ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-icon border-0 view-details" 
                                                        data-id="<?= $complaint['id'] ?>"
                                                        data-subject="<?= esc($complaint['subject']) ?>" 
                                                        data-message="<?= esc($complaint['message']) ?>"
                                                        data-remark="<?= esc($complaint['admin_remark'] ?? 'Our HR team will review your submission shortly.') ?>"
                                                        data-status="<?= $complaint['status'] ?>"
                                                        data-file="<?= $complaint['file'] ?>"
                                                        title="View Details"
                                                        style="background-color: #f0f7ff; color: #4B49AC;">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon border-0 delete-btn" 
                                                        data-id="<?= $complaint['id'] ?>" 
                                                        title="Delete permanently"
                                                        style="background-color: #fdf2f2; color: #fe5e5e;">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="mdi mdi-card-search-outline text-muted" style="font-size: 4rem;"></i>
                                            <h5 class="text-muted mt-3">No submissions found yet.</h5>
                                            <p class="text-muted small">Your submitted complaints and feedback will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold">Record #<span id="modalId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-4 d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                    <div>
                        <label class="small text-muted d-block mb-1">Status</label>
                        <span id="modalStatus" class="fw-bold fs-14"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small text-muted d-block mb-1">Subject</label>
                    <div id="modalSubject" class="fw-bold fs-5 text-dark"></div>
                </div>
                <div class="mb-4">
                    <label class="small text-muted d-block mb-1">Content Detail</label>
                    <div id="modalMessage" class="p-3 border rounded bg-white small shadow-none" style="white-space: pre-wrap; letter-spacing: 0.3px;"></div>
                </div>
                <div class="mb-4" id="attachmentArea" style="display:none;">
                    <label class="small text-muted d-block mb-2">Original Attachment</label>
                    <a id="modalFile" href="#" target="_blank" class="btn btn-sm btn-outline-primary py-2 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="mdi mdi-attachment"></i> Open Attached Document
                    </a>
                </div>
                <div class="mt-4 p-3 border-start border-primary border-4 rounded" style="background-color: #f0f7ff; color: #4B49AC;">
                    <label class="small text-muted d-block mb-1 uppercase fw-bold" style="font-size: 0.7rem;">Official Resolution Remark</label>
                    <div id="modalRemark" class="fw-bold italic" style="font-style: italic;"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Close View</button>
            </div>
        </div>
    </div>
</div>

<style>
    #userComplaintsTable thead th { 
        text-transform: uppercase;
        font-size: 0.75rem; 
        letter-spacing: 0.5px;
    }
    #userComplaintsTable tbody td { 
        padding: 15px;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.85rem;
    }
    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items:center;
        justify-content:center;
        border-radius: 8px;
    }
    .uppercase { text-transform: uppercase; }
    .fs-14 { font-size: 0.85rem; }
    
    .table-hover tbody tr:hover {
        background-color: #fbfcfe;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('.view-details').on('click', function() {
        let btn = $(this);
        $('#modalId').text(btn.data('id'));
        $('#modalSubject').text(btn.data('subject'));
        $('#modalMessage').text(btn.data('message'));
        $('#modalRemark').text(btn.data('remark'));
        
        let status = btn.data('status');
        let statusColor = '#ffbc07';
        if (status === 'In Progress') statusColor = '#4B49AC';
        if (status === 'Resolved') statusColor = '#28a745';
        
        $('#modalStatus').text(status).css('color', statusColor);
        
        let file = btn.data('file');
        if(file) {
            $('#modalFile').attr('href', '<?= base_url('uploads/complaints') ?>/' + file);
            $('#attachmentArea').show();
        } else {
            $('#attachmentArea').hide();
        }
        
        $('#userViewModal').modal('show');
    });

    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Confirm Removal',
            text: "Are you sure you want to delete this case forever?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#fe5e5e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('api/complaints/delete') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', 'Case file has been cleaned up.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
