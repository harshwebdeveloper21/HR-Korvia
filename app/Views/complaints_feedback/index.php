<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-4 border-bottom-0">
                <div>
                    <h4 class="card-title mb-1 fw-bold">My Complaints & Feedback</h4>
                    <p class="text-muted small mb-0">List of your all submitted feedback and complaints with status.</p>
                </div>
                <a href="<?= base_url('complaints/create') ?>" class="btn btn-primary d-flex align-items-center gap-2" style="background-color: #4B49AC; border-radius: 4px; padding: 8px 18px;">
                    <i class="mdi mdi-plus-circle fs-5"></i> Submit New Request
                </a>
            </div>
            
            <div class="card-body pt-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-bottom" id="userComplaintsTable">
                        <thead style="background-color: #000; color: #fff;">
                            <tr>
                                <th class="py-3 ps-4">ID</th>
                                <th class="py-3">SUBJECT & DETAILS</th>
                                <th class="py-3">CATEGORY</th>
                                <th class="py-3">STATUS</th>
                                <th class="py-3">DATE SUBMITTED</th>
                                <th class="py-3 text-end pe-4">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($complaints)): ?>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted"><?= $complaint['id'] ?></td>
                                        <td>
                                            <div class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;"><?= esc($complaint['subject']) ?></div>
                                            <small class="text-muted d-block" style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                <?= esc(substr($complaint['message'], 0, 60)) ?>...
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($complaint['type'] == 'Complaint'): ?>
                                                <span class="badge border border-danger text-danger bg-transparent px-3 py-2 fw-semibold">
                                                    Complaint
                                                </span>
                                            <?php else: ?>
                                                <span class="badge border border-info text-info bg-transparent px-3 py-2 fw-semibold">
                                                    Feedback
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColor = '#ffbc07';
                                            if ($complaint['status'] == 'In Progress') $statusColor = '#4B49AC';
                                            if ($complaint['status'] == 'Resolved') $statusColor = '#28a745';
                                            ?>
                                            <span style="color: <?= $statusColor ?>; font-weight: 600; font-size: 0.85rem;">
                                                <?= $complaint['status'] ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('d M, Y', strtotime($complaint['created_at'])) ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="javascript:void(0);" class="text-info fs-18 view-details" 
                                                        data-id="<?= $complaint['id'] ?>"
                                                        data-subject="<?= esc($complaint['subject']) ?>" 
                                                        data-message="<?= esc($complaint['message']) ?>"
                                                        data-remark="<?= esc($complaint['admin_remark'] ?? 'No response yet.') ?>"
                                                        data-status="<?= $complaint['status'] ?>"
                                                        data-file="<?= $complaint['file'] ?>"
                                                        title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="text-danger fs-18 delete-btn" data-id="<?= $complaint['id'] ?>" title="Delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <h5 class="text-muted">No records found.</h5>
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
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Case Details #<span id="modalId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="small text-muted d-block mb-1">Status</label>
                    <span id="modalStatus" class="fw-bold fs-14"></span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block mb-1">Subject</label>
                    <div id="modalSubject" class="fw-bold fs-5 text-dark"></div>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block mb-1">Message</label>
                    <div id="modalMessage" class="p-3 border rounded bg-light small shadow-none" style="white-space: pre-wrap; letter-spacing: 0.3px;"></div>
                </div>
                <div class="mb-3" id="attachmentArea" style="display:none;">
                    <label class="small text-muted d-block mb-1">Attachment</label>
                    <a id="modalFile" href="#" target="_blank" class="btn btn-sm btn-outline-primary py-2 w-100">
                        <i class="mdi mdi-attachment me-1"></i> View Attachment
                    </a>
                </div>
                <div class="mt-4">
                    <label class="small text-muted d-block mb-1 uppercase fw-bold">Resolution Remark</label>
                    <div id="modalRemark" class="p-3 border-start border-primary border-4 rounded" style="background-color: #f0f7ff; color: #4B49AC; font-weight: 500;"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
            title: 'Delete recorded case?',
            text: "This action cannot be undone.",
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
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
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

<style>
    #userComplaintsTable thead th { 
        background-color: #000 !important; 
        color: #fff !important; 
        font-weight: 500; 
        text-transform: uppercase;
        font-size: 0.8rem; 
        padding: 12px 15px;
        border: none;
    }
    #userComplaintsTable tbody td { 
        padding: 12px 15px;
        border-bottom: 1px solid #f3f3f3;
        font-size: 0.85rem;
    }
    .fs-18 { font-size: 1.15rem; }
    .badge { border-radius: 4px; font-weight: 500; }
    .uppercase { text-transform: uppercase; }
    .fs-14 { font-size: 0.85rem; }
</style>
<?= $this->endSection(); ?>
