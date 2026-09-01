<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .capitalize-text {
        text-transform: capitalize;
    }

    @media (max-width: 767px) {
        .attendenceall {
            font-size: 9px !important;
            padding: 5.2px !important;
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

        #interviews-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #interviews-Table_length label::first-text,
        #interviews-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #interviews-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #interviews-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #interviews-Table_filter label {
            font-size: 0;
        }

        #interviews-Table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #interviews-Table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #interviews-Table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        /* .form-control {
            height: 0px !important;
        } */

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
    }

</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Interviews</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportInterviews" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="<?= base_url(
                            "/interviews",
                        ) ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Interview
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="interviews-Table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Interviewer Name</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Schedule Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="interviews-Table-Body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const token = localStorage.getItem('token'); // JWT token from login

        // Fetch leave types when the page loads
        fetch('/api/interviews', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((responseData) => {
                if (responseData.status === 'success') {
                    const interviews = responseData.data; // Get the leave types data
                    let tableRows = '';

                    interviews.forEach((interview, index) => {
                        // Create a row for each leave type
                        const row = document.createElement('tr');

                        tableRows += `
                     <tr data-id="${interview.id}">
                        <td>${index + 1}</td>
                        <td class="capitalize-text">${interview.candidate_name}</td>
                        <td class="capitalize-text">${interview.job_title}</td>
                          <td class="py-1">
                            <select class="form-select status-select capitalize-text" data-id="${interview.id}">
                                <option value="scheduled" ${interview.status === 'scheduled' ? 'selected' : ''}>Scheduled</option>
                                <option value="completed" ${interview.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${interview.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                           <td>${interview.schedule_date}</td>
                        <td style="display: flex; align-items: center; gap: 8px;">
                            <a href="/interview/display/${interview.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>

                            <a href="#" class="text-danger fs-5" title="Delete"
                               data-id="${interview.id}"
                               data-status="${interview.status}"
                               onclick="deleteInterview(event)">
                               <i class="mdi mdi-delete"></i>
                            </a>
                        </td>
                        </tr>
                    `;
                    });
                    $('#interviews-Table-Body').html(tableRows);
                    // $('#interviews-Table').DataTable(); // Append the row to the table body
                    if ($.fn.DataTable.isDataTable('#interviews-Table')) {
                        $('#interviews-Table').DataTable().clear().destroy();
                    }
                    $('#interviews-Table').DataTable({
                        language: {
                            search: "",
                            searchPlaceholder: "Search"
                        }
                    });

                } else {
                    console.error('Failed to fetch leave types:', responseData.message);
                }
            })
            .catch((error) => {
                console.error('Error fetching leave types:', error);
            });
    });
    $(document).on('change', '.status-select', function () {
        const token = localStorage.getItem('token'); // JWT token from login
        const interviewId = $(this).data('id');
        const newStatus = $(this).val();
        const $select = $(this);
        const prevStatus = $select.data('prev-status') || $select.find('option[selected]').val(); // Store previous status

        // If the previous status was already "completed", prevent changes
        if (prevStatus === 'completed') {
            Swal.fire({
                icon: 'warning',
                title: 'Already Completed!',
                text: 'This interview has already been marked as completed and cannot be changed.',
                timer: 3000,
                showConfirmButton: false,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',

                }
            });

            // Revert dropdown to "completed"
            $select.val('completed');
            return;
        }

        // If changing status to "completed", show confirmation popup (only first time)
        if (newStatus === 'completed') {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to mark this interview as completed? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, complete it!',
                cancelButtonText: 'No, cancel',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                    cancelButton: 'hr-btnbg',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with status update
                    updateInterviewStatus(interviewId, newStatus, token, $select);
                } else {
                    // Revert dropdown back to previous value if cancelled
                    $select.val(prevStatus);
                }
            });
        } else {
            // Directly update status for other values
            updateInterviewStatus(interviewId, newStatus, token, $select);
        }
    });

    // Function to update interview status
    function updateInterviewStatus(interviewId, newStatus, token, $select) {
        const csrfName = $('meta[name="csrf-token"]').attr('data-name');
        const csrfHash = $('meta[name="csrf-token"]').attr('content');
        const payload = {
            status: newStatus
        };
        payload[csrfName] = csrfHash; // Add CSRF token to body
        fetch(`/api/interviews/${interviewId}/status`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Interview status updated successfully!',
                        timer: 2000,
                        showConfirmButton: false,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });

                    // If status is changed to "completed", prevent further changes
                    if (newStatus === 'completed') {
                        setTimeout(() => location.reload(), 2000); // Reload page after success
                    }

                    // Store the new status as the previous status
                    $select.data('prev-status', newStatus);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to update status'
                    });

                    // Revert to previous status in case of failure
                    $select.val($select.data('prev-status'));
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating the status.'
                });

                // Revert to previous status in case of error
                $select.val($select.data('prev-status'));
            });
    }



    // Function to handle interview delete — shows extra warning for completed interviews
    function deleteInterview(event) {
        event.preventDefault();

        const anchor = event.target.closest('a');
        const interviewId = anchor.getAttribute('data-id');
        const status = (anchor.getAttribute('data-status') || '').toLowerCase();
        const csrfName = $('meta[name="csrf-token"]').attr('data-name');
        const csrfHash = $('meta[name="csrf-token"]').attr('content');

        // Helper: actually call the delete API
        function performDelete() {
            fetch(`/api/interviews/${interviewId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ [csrfName]: csrfHash })
            })
                .then(r => r.json())
                .then(responseData => {
                    if (responseData.status === 'success') {
                        const row = document.querySelector(`tr[data-id="${interviewId}"]`);
                        if (row) row.remove();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'The interview has been deleted successfully.',
                            icon: 'success',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn hr-btnbg' }
                        });
                    } else {
                        const errorMsg = responseData.message || responseData.messages?.error || 'Only admins can delete interviews.';
                        Swal.fire({
                            title: 'Cannot Delete',
                            text: errorMsg,
                            icon: 'error',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn hr-btnbg' }
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the interview.',
                        icon: 'error',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'btn hr-btnbg' }
                    });
                });
        }

        if (status === 'completed') {
            // ⚠️ Completed interview — show strong two-step warning
            Swal.fire({
                title: '⚠️ Warning: Completed Interview',
                html: `<p>This interview has already been <strong>completed</strong>.</p>
                       <p>Deleting it may affect onboarding and candidate records linked to this interview.</p>
                       <p><strong>Are you absolutely sure you want to proceed?</strong></p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, I understand — Delete',
                cancelButtonText: 'No, Keep It',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn hr-btnbg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete();
                }
            });
        } else {
            // Standard confirmation for scheduled / cancelled
            Swal.fire({
                title: 'Are you sure?',
                text: 'This interview record will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn hr-btnbg me-2',
                    cancelButton: 'btn hr-btnbg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete();
                }
            });
        }
    }

    // 📥 Export to Excel functionality
    document.getElementById('btnExportInterviews')?.addEventListener('click', function () {
        const btn = this;
        const search = $('#interviews-Table_filter input').val() || '';
        const token = localStorage.getItem('token');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...';

        const queryParams = new URLSearchParams({ search: search });

        fetch(`<?= base_url('api/interview/export') ?>?${queryParams.toString()}`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(async response => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel';
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
            a.download = `Interviews_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Interview list exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel';
            Swal.fire('Export Error', error.message || 'Failed to export interviews', 'error');
        });
    });
</script>
<?= $this->endSection() ?>