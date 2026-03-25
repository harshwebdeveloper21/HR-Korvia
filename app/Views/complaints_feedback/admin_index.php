<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<style>
    .filterbtn { margin-top: -0.5rem !important; }
    .bg-light-stripe { background-color: #f2f2f2 !important; }
    #complaintsAdminTable thead tr { background-color: #000 !important; color: #fff !important; }
    #complaintsAdminTable thead th { 
        color: #fff !important; 
        font-weight: 700 !important; 
        text-transform: uppercase !important; 
        font-size: 0.8rem !important;
        border: none !important;
        padding: 15px 10px !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 321px !important;
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 5px 10px;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 5px;
        border: 1px solid #ddd;
        padding: 3px 5px;
    }
    .hr-btnbg {
        background-color: #E66136 !important;
        border: 2px solid #F05929 !important;
        border-radius: 4px !important;
        color: #fff !important;
        font-family: Poppins, sans-serif !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }
</style>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="align-items-center d-md-flex justify-content-between mb-3">
                    <h4 class="card-title">Manage Complaints & Feedback</h4>
                    <div class="d-md-flex gap-2">
                        <select class="form-select shadow-none" id="filterStatus" style="max-width: 150px;">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                        <select class="form-select shadow-none" id="filterType" style="max-width: 130px;">
                            <option value="">All Types</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Feedback">Feedback</option>
                        </select>
                        <a href="<?= base_url('complaints/create') ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add New Request
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="complaintsAdminTable">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Subject</th>
                                <th class="text-center">Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
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
            }
        },
        columns: [
            { data: 'id', visible: false },
            { 
                data: null,
                render: function(data) {
                    return `
                        <div class="py-1">
                            <span class="fw-bold d-block text-dark">${data.name}</span>
                            <span class="text-muted small d-block">${data.email}</span>
                        </div>
                    `;
                }
            },
            { 
                data: 'type',
                render: function(data) {
                    let color = data === 'Complaint' ? '#ef4444' : '#0ea5e9';
                    return `<span class="badge border-0 px-2 py-1 fw-semibold text-white" style="background-color: ${color}; border-radius: 4px; font-size: 0.7rem;">${data.toUpperCase()}</span>`;
                }
            },
            { 
                data: 'subject',
                render: function(data) {
                    return `<span class="text-muted small lh-sm d-inline-block text-wrap" style="max-width: 200px;">${data}</span>`;
                }
            },
            { 
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    let color = '#E66136';
                    if (data === 'In Progress') color = '#4B49AC';
                    if (data === 'Resolved') color = '#34B1AA';
                    return `<span class="fw-bold" style="color: ${color}; font-size: 0.75rem;">${data.toUpperCase()}</span>`;
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
                className: 'desktop-only-col',
                orderable: false,
                render: function(data) {
                    return `
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a href="<?= base_url('complaints/update') ?>/${data.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                            <a href="#" class="text-danger fs-5 delete-btn" data-id="${data.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            search: "",
            searchPlaceholder: "Search",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    $('#filterType, #filterStatus').on('change', function() { table.ajax.reload(); });

    $('#complaintsAdminTable').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn hr-btnbg me-2',
                cancelButton: 'btn hr-btnbg',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('api/complaints/delete') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: { confirmButton: 'btn hr-btnbg' }
                            }).then(() => { table.ajax.reload(); });
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
