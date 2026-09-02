<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        /* .attendenceall {
            font-size: 12px !important;
            padding: 5.3px !important;
        } */

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
            max-width: 18%;
        }

        .dataTables_filter label:before {
            content: "" !important;
        }

        #designation-table_length label {
            display: flex;
            align-items: center;
            margin-top: 1px;
        }

        /* Hide the text inside the label */
        #designation-table_length::first-text,
        #designation-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #designation-table_length label {
            font-size: 0;
            /* hide text */
        }

        #designation-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }
        #designation-table_filter label {
            font-size: 0;
        }

        #designation-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }
        #designation-table_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }

        #designation-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

           div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 32px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }


    }
</style>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Designations</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportDesignation" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>
                        <a href="/designation" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Designation
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="designation-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Designation</th>
                                <th class="desktop-only-col">Department</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="designation-table-body">
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token

        function fetchDesignations() {
            $.ajax({
                url: '/api/designation', // API endpoint for designations
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.designations) {
                        const designations = response.designations;
                        let tableRows = '';
                        designations.forEach((designation, index) => {
                            const formattedNames = designation.department_name.charAt(0).toUpperCase() + designation.department_name.slice(1).toLowerCase();
                            const formattedName = designation.designation_name.charAt(0).toUpperCase() + designation.designation_name.slice(1).toLowerCase();

                            tableRows += `
                    <tr data-id="${designation.id}">
                        <td>${index + 1}</td>
                        <td>
                            <div style="flex: 1;">
                                <span>${formattedName}</span>
                                <div class="expanded-details" id="desig-details-${designation.id}" onclick="event.stopPropagation();">
                                    <div class="detail-row">
                                        <span class="detail-label">Department:</span>
                                        <span class="detail-value">${formattedNames}</span>
                                    </div>
                                    <div class="detail-actions">
                                        <a href="/designation?id=${designation.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-designation" data-id="${designation.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="desktop-only-col">${formattedNames}</td>
                        <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                            <a href="/designation?id=${designation.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                            <a href="#" class="text-danger fs-5 delete-designation" data-id="${designation.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                        </td>
                        <td class="mobile-expand-col text-center">
                            <button type="button" class="expand-toggle" data-target="desig-details-${designation.id}" aria-label="Expand details"></button>
                        </td>
                    </tr>
                `;
                        });
                        $('#designation-table-body').html(tableRows);
                         if ($.fn.DataTable.isDataTable('#designation-table')) {
                    $('#designation-table').DataTable().clear().destroy();
                }
                    $('#designation-table').DataTable({
                        columnDefs: [
                            {
                                targets: 4, // mobile expand column
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            search: "",
                            searchPlaceholder: "Search"
                        }
                    });
                    // Apply mobile visibility
                    if (typeof applyMobileTableVisibility === 'function') {
                        applyMobileTableVisibility();
                    }
                    } else {
                        Swal.fire('Error', 'Failed to load designations', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch designations', 'error');
                }
            });
        }


        // Call the fetchDesignations function
        fetchDesignations();

        $(document).on('click', '.delete-designation', function(e) {
            e.preventDefault();
            const designationId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
        confirmButton: 'hr-btnbg',
        cancelButton: 'hr-btnbg',
    }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/designation/${designationId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.message === 'Designation deleted successfully!') {
                                Swal.fire('Deleted!', 'The designation has been deleted.', 'success').then(() => {
                                    $(`tr[data-id="${designationId}"]`).remove();
                                });
                            } else {
                                Swal.fire(
                                'Error!',
                                responseData.message, // Show the actual message from server
                                'error'
                            );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error deleting the designation. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        });

        // 📥 Export to Excel functionality
        $('#btnExportDesignation').on('click', function () {
            const $btn = $(this);
            const search = $('#designation-table_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/designation/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Designations_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Designations exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export');
                Swal.fire('Export Error', error.message || 'Failed to export designations', 'error');
            });
        });
    });
</script>
<?= $this->endSection() ?>
