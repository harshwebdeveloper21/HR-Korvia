<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>
<style>
    .filterbtn {
        margin-top: -0.5rem !important;
    }

    .bg-light-stripe {
        background-color: #f2f2f2 !important;
    }

    #complaintsAdminTable thead tr {
        background-color: #000 !important;
        color: #fff !important;
    }

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
                        <select class="form-select shadow-none d-none" id="filterType" style="max-width: 130px;">
                            <option value="">All Types</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Feedback">Feedback</option>
                        </select>
                        <button type="button" id="btnExportComplaints" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="<?= base_url('complaints/create') ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add New Request
                        </a>
                    </div>
                </div>

                <!-- Complaints & Feedback Tabs -->
                <ul class="nav nav-tabs mb-3" id="complaintTabs" role="tablist" style="border-bottom: 2px solid #E66136;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-type="" type="button" role="tab"
                            style="color:#E66136; border-bottom: 3px solid #E66136; font-weight:600;">All Requests</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="complaints-tab" data-type="Complaint" type="button" role="tab"
                            style="color:#6c757d; font-weight:600;"><i class="mdi mdi-alert-circle-outline me-1"></i> Complaints</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="feedback-tab" data-type="Feedback" type="button" role="tab"
                            style="color:#6c757d; font-weight:600;"><i class="mdi mdi-comment-text-outline me-1"></i> Feedback</button>
                    </li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="complaintsAdminTable">
                        <thead class="table-light">
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Name</th>
                                <th class="desktop-only-col">Category</th>
                                <th class="desktop-only-col">Subject</th>
                                <th class="desktop-only-col text-center">Status</th>
                                <th class="desktop-only-col">Submitted</th>
                                <th class="desktop-only-col action-column" style="width: 100px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
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
                    <div id="modalMessage" class="p-3 border rounded bg-white small shadow-none"
                        style="white-space: pre-wrap; letter-spacing: 0.3px;"></div>
                </div>
                <div class="mb-4" id="attachmentArea" style="display:none;">
                    <label class="small text-muted d-block mb-2">Original Attachment</label>
                    <a id="modalFile" href="#" target="_blank"
                        class="btn btn-sm btn-outline-primary py-2 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="mdi mdi-attachment"></i> Open Attached Document
                    </a>
                </div>
                <div class="mt-4 p-3 border-start border-primary border-4 rounded"
                    style="background-color: #f0f7ff; color: #4B49AC;">
                    <label class="small text-muted d-block mb-1 uppercase fw-bold" style="font-size: 0.7rem;">Official
                        Resolution Remark</label>
                    <div id="modalRemark" class="fw-bold italic" style="font-style: italic; margin-bottom: 10px;"></div>

                    <div id="resolutionAttachmentArea" style="display:none;">
                        <label class="small text-muted d-block mb-2 uppercase fw-bold"
                            style="font-size: 0.65rem;">Resolution Proof / Document</label>
                        <a id="modalResolutionFile" href="#" target="_blank"
                            class="btn btn-sm btn-outline-success py-2 w-100 d-flex align-items-center justify-content-center gap-2"
                            style="border-radius: 8px;">
                            <i class="mdi mdi-check-circle-outline"></i> Open Resolution Attachment
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">Close View</button>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-14 {
        font-size: 0.85rem;
    }

    .view-details-btn:hover {
        opacity: 0.8;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    $(document).ready(function () {
        let table = $('#complaintsAdminTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= base_url('api/complaints/list') ?>',
                data: function (d) {
                    d.type = $('#filterType').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'id', visible: false },
                {
                    data: null,
                    render: function (data, type, row) {
                        let color = row.type === 'Complaint' ? '#ef4444' : '#0ea5e9';
                        let typeBadge = `<span class="badge border-0 px-2 py-1 fw-semibold text-white" style="background-color: ${color}; border-radius: 4px; font-size: 0.7rem;">${(row.type || '').toUpperCase()}</span>`;
                        let statusColor = '#E66136';
                        if (row.status === 'In Progress') statusColor = '#4B49AC';
                        if (row.status === 'Resolved') statusColor = '#34B1AA';
                        let statusDisplay = `<span class="fw-bold" style="color: ${statusColor}; font-size: 0.75rem;">${(row.status || '').toUpperCase()}</span>`;
                        let d = new Date(row.created_at);
                        let dateStr = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

                        return `
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="flex: 1;">
                                <span class="fw-bold d-block text-dark">${row.name || 'Anonymous'}</span>
                                <span class="text-muted small d-block">${row.email || ''}</span>
                                <div class="expanded-details" id="complaint-details-${row.id}" onclick="event.stopPropagation();">
                                    <div class="detail-row">
                                        <span class="detail-label">Type:</span>
                                        <span class="detail-value">${typeBadge}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Subject:</span>
                                        <span class="detail-value">${row.subject}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">${statusDisplay}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Submitted:</span>
                                        <span class="detail-value">${dateStr}</span>
                                    </div>
                                    <div class="detail-actions">
                                        <a href="#" class="btn btn-sm btn-info text-white view-details-btn" 
                                           data-id="${row.id}"
                                           data-subject="${row.subject}" 
                                           data-message="${row.message}"
                                           data-remark="${row.admin_remark || 'Not provided yet.'}"
                                           data-status="${row.status}"
                                           data-file="${row.file || ''}"
                                           data-res-file="${row.resolution_file || ''}">
                                           <i class="mdi mdi-eye"></i> View
                                        </a>
                                        <a href="<?= base_url('complaints/update') ?>/${row.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-btn" data-id="${row.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    }
                },
                {
                    data: 'type',
                    className: 'desktop-only-col',
                    render: function (data) {
                        let color = data === 'Complaint' ? '#ef4444' : '#0ea5e9';
                        return `<span class="badge border-0 px-2 py-1 fw-semibold text-white" style="background-color: ${color}; border-radius: 4px; font-size: 0.7rem;">${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'subject',
                    className: 'desktop-only-col',
                    render: function (data) {
                        return `<span class="text-muted small lh-sm d-inline-block text-wrap" style="max-width: 200px;">${data}</span>`;
                    }
                },
                {
                    data: 'status',
                    className: 'desktop-only-col text-center',
                    render: function (data) {
                        let color = '#E66136';
                        if (data === 'In Progress') color = '#4B49AC';
                        if (data === 'Resolved') color = '#34B1AA';
                        return `<span class="fw-bold" style="color: ${color}; font-size: 0.75rem;">${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    className: 'desktop-only-col',
                    render: function (data) {
                        let d = new Date(data);
                        return `<span class="text-muted small">${d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'desktop-only-col',
                    orderable: false,
                    render: function (data) {
                        return `
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a href="#" class="view-details-btn text-primary fs-5" 
                               data-id="${data.id}"
                               data-subject="${data.subject}" 
                               data-message="${data.message}"
                               data-remark="${data.admin_remark || 'Not provided yet.'}"
                               data-status="${data.status}"
                               data-file="${data.file || ''}"
                               data-res-file="${data.resolution_file || ''}"
                               title="View Details">
                               <i class="mdi mdi-eye"></i>
                            </a>
                            <a href="<?= base_url('complaints/update') ?>/${data.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                            <a href="#" class="text-danger fs-5 delete-btn" data-id="${data.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                        </div>
                    `;
                    }
                },
                {
                    data: null,
                    className: 'mobile-expand-col text-center',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `<button type="button" class="expand-toggle" data-target="complaint-details-${data.id}" aria-label="Expand details"></button>`;
                    }
                }
            ],
            order: [[0, 'desc']],
            columnDefs: [
                {
                    targets: [6, 7],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        $('#filterType, #filterStatus').on('change', function () { table.ajax.reload(); });

        $('#complaintTabs .nav-link').on('click', function () {
            $('#complaintTabs .nav-link')
                .removeClass('active')
                .css({'color': '#6c757d', 'border-bottom': 'none', 'font-weight': '600'});
            $(this)
                .addClass('active')
                .css({'color': '#E66136', 'border-bottom': '3px solid #E66136'});

            const selectedType = $(this).data('type');
            $('#filterType').val(selectedType);
            table.ajax.reload();
        });

        // Delegation for View Details button
        $('#complaintsAdminTable').on('click', '.view-details-btn', function (e) {
            e.preventDefault();
            let btn = $(this);
            $('#modalId').text(btn.data('id'));
            $('#modalSubject').text(btn.data('subject'));
            $('#modalMessage').text(btn.data('message'));
            $('#modalRemark').text(btn.data('remark'));

            let status = btn.data('status');
            let statusColor = '#E66136';
            if (status === 'In Progress') statusColor = '#4B49AC';
            if (status === 'Resolved') statusColor = '#34B1AA';

            $('#modalStatus').text(status).css('color', statusColor);

            let file = btn.data('file');
            if (file) {
                $('#modalFile').attr('href', '<?= base_url('uploads/complaints') ?>/' + file);
                $('#attachmentArea').show();
            } else {
                $('#attachmentArea').hide();
            }

            let resFile = btn.data('res-file');
            if (resFile) {
                $('#modalResolutionFile').attr('href', '<?= base_url('uploads/complaints') ?>/' + resFile);
                $('#resolutionAttachmentArea').show();
            } else {
                $('#resolutionAttachmentArea').hide();
            }
            $('#userViewModal').modal('show');
        });

        $('#complaintsAdminTable').on('click', '.delete-btn', function (e) {
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
                        success: function (response) {
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

        // 📥 Export to Excel functionality
        $('#btnExportComplaints').on('click', function () {
            const $btn = $(this);
            const type = $('#complaintTabs .nav-link.active').data('type') || $('#filterType').val() || '';
            const status = $('#filterStatus').val() || '';
            const search = $('#complaintsAdminTable_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({
                type: type,
                status: status,
                search: search
            });

            fetch(`<?= base_url('api/complaints/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Complaints_Feedback_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Complaints & Feedback exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export complaints', 'error');
            });
        });
    });
</script>
<?= $this->endSection(); ?>