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

        #leaveTypes-Table_length label {
            display: flex;
            align-items: center;
            margin-top: 1px;
        }

        /* Hide the text inside the label */
        #leaveTypes-Table_length::first-text,
        #leaveTypes-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #leaveTypes-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #leaveTypes-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #leaveTypes-Table_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }

        #leaveTypes-Table_filter label {
            font-size: 0;
        }

        #leaveTypes-Table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #leaveTypes-Table_length label select {
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


        /* .form-control {
            height: 0px !important;
        }  */
    }
</style>
<div class="row">
    <div class="col-lg-13 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Leave Types</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportLeaveType" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/leave_type" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Leave Type
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="leaveTypes-Table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Leave Type</th>
                                <th class="desktop-only-col">Number of leaves</th>
                                <th class="desktop-only-col">Allow Half Day</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="leaveTypes-Table-Body">
                            <!-- Data dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('token'); // JWT token from login

        // Fetch leave types when the page loads
        fetch('/api/leavetype', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            })
            .then((response) => response.json())
            .then((responseData) => {
                if (responseData.status === 'success') {                    const leaveTypes = responseData.data; // Get the leave types data
                    let tableRows = '';

                    leaveTypes.forEach((leaveType, index) => {
                        const formattedName = leaveType.leave_type.charAt(0).toUpperCase() + leaveType.leave_type.slice(1).toLowerCase();
                        const allowHalfDay = leaveType.allow_half_day == 1 ? 'Yes' : 'No';
                        tableRows += `
                     <tr data-id="${leaveType.id}">
                        <td>${index + 1}</td>
                        <td>
                            ${formattedName}
                            <div class="expanded-details" id="leavetype-details-${leaveType.id}" onclick="event.stopPropagation();">
                                <div class="detail-row">
                                    <span class="detail-label">Number of leaves:</span>
                                    <span class="detail-value">${leaveType.number_of_leaves ? leaveType.number_of_leaves : 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Allow Half Day:</span>
                                    <span class="detail-value">${allowHalfDay}</span>
                                </div>
                                <div class="detail-actions">
                                    <a href="/leave_type?id=${leaveType.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger" data-id="${leaveType.id}" onclick="deleteLeave(event)"><i class="mdi mdi-delete"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                        <td class="desktop-only-col">${leaveType.number_of_leaves ? leaveType.number_of_leaves : 'N/A'}</td>
                        <td class="desktop-only-col">${allowHalfDay}</td>
                        <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                            <a href="/leave_type?id=${leaveType.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                            <a href="#" class="text-danger fs-5" title="Delete" data-id="${leaveType.id}" onclick="deleteLeave(event)"><i class="mdi mdi-delete"></i></a>
                        </td>
                        <td class="mobile-expand-col text-center">
                            <button type="button" class="expand-toggle" data-target="leavetype-details-${leaveType.id}" aria-label="Expand details"></button>
                        </td>
                        </tr>
                    `;
                    });
                    $('#leaveTypes-Table-Body').html(tableRows);
                    if ($.fn.DataTable.isDataTable('#leaveTypes-Table')) {
                        $('#leaveTypes-Table').DataTable().clear().destroy();
                    }
                    $('#leaveTypes-Table').DataTable({
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
                    console.error('Failed to fetch leave types:', responseData.message);
                }
            })
            .catch((error) => {
                console.error('Error fetching leave types:', error);
            });
    });

    // Function to handle delete action
    function deleteLeave(event) {
        event.preventDefault(); // Prevent the default link behavior
        const leaveId = event.target.closest('a').getAttribute('data-id'); // Get the leave ID

        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to revert this!',
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
                // Perform the delete action (AJAX call)
                fetch(`/api/leavetype/${leaveId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json',
                        },
                    })
                    .then((response) => response.json())
                    .then((responseData) => {
                        if (responseData.status === 'success') {
                            // Dynamically remove the row from the table
                            const row = document.querySelector(`tr[data-id="${leaveId}"]`);
                            if (row) row.remove();

                            // Show success message
                            Swal.fire('Deleted!', 'The leave has been deleted successfully.', 'success');
                        } else {
                            // Show error message if deletion fails
                            Swal.fire(
                                'Error!',
                                responseData.message, // Show the actual message from server
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        let errorMessage = 'There was an error deleting the leave. Please try again.';
                        if (error.message) {
                            errorMessage = error.message; // Get actual error message
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    });
            }
        });
    }

    $(document).ready(function() {
        // 📥 Export to Excel functionality
        $('#btnExportLeaveType').on('click', function () {
            const $btn = $(this);
            const search = $('#leaveTypes-Table_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/leavetype/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Leave_Types_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Leave types exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export leave types', 'error');
            });
        });
    });
</script>

<?= $this->endSection() ?>
