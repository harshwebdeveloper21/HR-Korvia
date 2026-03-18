<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-4 border-bottom-0">
                <div>
                    <h4 class="card-title mb-1 fw-bold">Admin: Manage Complaints & Feedback</h4>
                    <p class="text-muted small mb-0">Monitor, categorize, and resolve employee feedback and complaints.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('complaints/create') ?>" class="btn btn-sm text-white fw-bold" style="background-color: #E66136; border-radius: 4px; padding: 5px 15px;">
                        + Add Complaint
                    </a>
                    <button class="btn btn-sm btn-outline-primary border refresh-table" style="padding: 5px 15px;">
                        <i class="mdi mdi-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="card-body p-0 border-top bg-light bg-opacity-10 py-3">
                <div class="row px-4 g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Filter by Status</label>
                        <select id="filterStatus" class="form-select border-0 shadow-sm py-2">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Filter by Type</label>
                        <select id="filterType" class="form-select border-0 shadow-sm py-2">
                            <option value="">All Types</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Feedback">Feedback</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Request Date From</label>
                        <input type="date" id="filterDateFrom" class="form-control border-0 shadow-sm py-2">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Request Date To</label>
                        <input type="date" id="filterDateTo" class="form-control border-0 shadow-sm py-2">
                    </div>
                </div>
            </div>

            <div class="card-body pt-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-bottom" id="complaintsAdminTable">
                        <thead style="background-color: #000; color: #fff;">
                            <tr>
                                <th class="py-3 ps-4">ID</th>
                                <th class="py-3">EMPLOYEE INFO</th>
                                <th class="py-3">TYPE</th>
                                <th class="py-3">SUBJECT</th>
                                <th class="py-3 text-center">STATUS</th>
                                <th class="py-3">SUBMISSION DATE</th>
                                <th class="py-3 text-end pe-4">ACTION</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let table = $('#complaintsAdminTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= base_url('api/complaints/list') ?>',
            data: function(d) {
                d.type = $('#filterType').val();
                d.status = $('#filterStatus').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { 
                data: 'id',
                className: 'ps-4 fw-bold'
            },
            { 
                data: null,
                render: function(data) {
                    return `
                        <div>
                            <span class="fw-bold d-block text-dark text-uppercase" style="font-size: 0.85rem;">${data.name}</span>
                            <span class="small text-muted d-block">${data.email}</span>
                        </div>
                    `;
                }
            },
            { 
                data: 'type',
                render: function(data) {
                    let badgeColor = data === 'Complaint' ? '#fe5e5e' : '#00c8ff';
                    return `<span class="badge bg-transparent border" style="border-color: ${badgeColor} !important; color: ${badgeColor}; padding: 5px 10px;">${data}</span>`;
                }
            },
            { 
                data: 'subject',
                render: function(data) {
                    return `<span class="small fw-semibold text-wrap d-inline-block" style="min-width: 150px; color: #555;">${data}</span>`;
                }
            },
            { 
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    let statusColor = '#ffbc07';
                    if (data === 'In Progress') statusColor = '#4B49AC';
                    if (data === 'Resolved') statusColor = '#28a745';
                    return `<span style="color: ${statusColor}; font-weight: 600;">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    let d = new Date(data);
                    return `<span class="text-muted small">${d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                render: function(data) {
                    return `
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('complaints/update') ?>/${data.id}" class="text-warning fs-18" title="Edit/Update">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" class="text-danger fs-18 delete-btn" data-id="${data.id}" title="Delete">
                                <i class="mdi mdi-delete"></i>
                            </a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search here...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filterType, #filterStatus, #filterDateFrom, #filterDateTo').on('change', function() {
        table.ajax.reload();
    });

    $('.refresh-table').on('click', function() {
        table.ajax.reload();
    });

    $('#complaintsAdminTable').on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this record!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('api/complaints/delete') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success');
                            table.ajax.reload();
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
    #complaintsAdminTable thead th { 
        background-color: #000 !important; 
        color: #fff !important; 
        font-weight: 500; 
        text-transform: uppercase;
        font-size: 0.8rem; 
        padding: 12px 15px;
        border: none;
    }
    #complaintsAdminTable tbody td { 
        padding: 12px 15px;
        border-bottom: 1px solid #f3f3f3;
        font-size: 0.85rem;
    }
    .fs-18 { font-size: 1.15rem; }
    .badge { border-radius: 4px; font-weight: 500; }
</style>
<?= $this->endSection(); ?>
