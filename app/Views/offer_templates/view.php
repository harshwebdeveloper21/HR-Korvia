<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 9px !important;
            padding: 5.3px !important;
        }

        .iconfontsize {
            font-size: 11px !important;
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
            /* margin-left: -5rem !important;  */
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
        }

        #templateTable_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #templateTable_length::first-text,
        #templateTable_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #templateTable_length label {
            font-size: 0;
            /* hide text */
        }

        #templateTable_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #templateTable_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }

        #templateTable_filter label {
            font-size: 0;
        }

        #templateTable_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #templateTable_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 29px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }

        /* .form-control {
            height: 0px !important;
        }  */
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">All Templates</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportOfferTemplates" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>
                        <a href="/add-offer-templates" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Template
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="templateTable">
                        <thead class="table-light">
                            <tr>
                                <th>Template Title</th>
                                <th class="desktop-only-col action-column" style="width: 100px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="templateTableBody">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>

                <div id="noTemplates" class="text-center text-muted mt-4" style="display: none;">
                    No templates found.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Script -->
<script>
    function loadTemplates() {
        $.ajax({
            url: '<?= site_url("api/offer-template/list") ?>',
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((template, index) => {
                        html += `
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <div style="flex: 1;">
                                            <span class="fw-bold text-dark">${template.title}</span>
                                            <div class="expanded-details" id="template-details-${template.id}" onclick="event.stopPropagation();">
                                                <div class="detail-actions">
                                                    <a href="/template/view/${template.id}" class="btn btn-sm btn-info text-white"><i class="mdi mdi-eye"></i> View</a>
                                                    <a href="/template/${template.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                    <a href="#" class="btn btn-sm btn-danger delete-template" data-id="${template.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="desktop-only-col">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <a href="/template/view/${template.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                        <a href="/template/${template.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="text-danger fs-5 delete-template" data-id="${template.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                                    </div>
                                </td>
                                <td class="mobile-expand-col text-center">
                                    <button type="button" class="expand-toggle" data-target="template-details-${template.id}" aria-label="Expand details"></button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#templateTableBody').html(html);

                    // Initialize DataTable
                    if ($.fn.DataTable.isDataTable('#templateTable')) {
                        $('#templateTable').DataTable().destroy();
                    }
                    $('#templateTable').DataTable({
                        columnDefs: [
                            {
                                targets: [1, 2],
                                orderable: false,
                                searchable: false
                            }
                        ],
                        pageLength: 10,
                        language: {
                            searchPlaceholder: "Search",
                            search: ""
                        }
                    });

                    $('#noTemplates').hide();
                } else {
                    $('#templateTableBody').html('');
                    $('#noTemplates').show();
                }
            },
            error: function() {
                $('#templateTableBody').html('');
                $('#noTemplates').text('Error loading templates.').show();
            }
        });
    }

    $(document).ready(function() {
        loadTemplates();
    });

    $(document).on('click', '.delete-template', function(e) {
        e.preventDefault();
        const button = $(this); // store the button reference
        const templateId = button.data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'hr-btnbg',
                cancelButton: 'hr-btnbg',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url(
                        "api/offer-template/delete/",
                    ) ?>' + templateId,
                    type: 'DELETE',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Deleted!', res.message, 'success');

                            // Remove the row directly without reloading the table
                            const row = button.closest('tr');
                            $('#templateTable').DataTable().row(row).remove().draw();

                            // If no rows left, show the "noTemplates" message
                            if ($('#templateTableBody tr').length === 0) {
                                $('#noTemplates').show();
                            }

                        } else {
                            Swal.fire('Error!', res.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });

    // 📥 Export to Excel functionality
    $('#btnExportOfferTemplates').on('click', function () {
        const $btn = $(this);
        const search = $('#templateTable_filter input').val() || '';
        const token = localStorage.getItem('token');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

        const queryParams = new URLSearchParams({ search: search });

        fetch(`<?= base_url('api/offer-template/export') ?>?${queryParams.toString()}`, {
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
            a.download = `Offer_Templates_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Offer templates exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export');
            Swal.fire('Export Error', error.message || 'Failed to export templates', 'error');
        });
    });
</script>

<?= $this->endSection() ?>
