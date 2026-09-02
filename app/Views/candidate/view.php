<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
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

        #candidates-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #candidates-Table_length label::first-text,
        #candidates-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #candidates-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #candidates-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

   #candidates-Tabl_filter label {
    font-size: 0;
  }
  #candidates-Tabl_filter input {
    font-size: 14px; /* Keep input font size normal */
  }
        #candidates-Table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #candidates-Table_length label select {
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
    height:29px !important

}
 .custom-select{
            height: 26px !important;
            width: 57px !important;
        }

    }
     .capitalize-text {
        text-transform: capitalize;
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Candidates</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportCandidates" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>
                        <a href="<?= base_url(
                            "/candidate",
                        ) ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Candidate
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="candidates-Table">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th class="desktop-only-col">Email</th>
                                <th class="desktop-only-col">Job Title</th>
                                <th class="desktop-only-col">Status</th>
                                <th class="desktop-only-col action-column" style="width: 100px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="candidates-Table-Body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('token'); // JWT token from login

        // Fetch candidates when the page loads
        fetch('/api/candidate', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            })
            .then((response) => response.json())
            .then((responseData) => {
                if (responseData.status === 'success') {
                    const candidates = responseData.data;
                    let tableRows = '';

                    candidates.forEach((candidate, index) => {
                        tableRows += `
                        <tr data-id="${candidate.id}">
                            <td class="capitalize-text">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <div style="flex: 1;">
                                        <a href="/candidate/display/${candidate.id}" class="text-decoration-none text-dark fw-bold">
                                            ${candidate.candidate_name}
                                        </a>
                                        <div class="expanded-details" id="candidate-details-${candidate.id}" onclick="event.stopPropagation();">
                                            <div class="detail-row">
                                                <span class="detail-label">Email:</span>
                                                <span class="detail-value">${candidate.email || 'N/A'}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Job:</span>
                                                <span class="detail-value">${candidate.job_title || 'N/A'}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Status:</span>
                                                <span class="detail-value">${candidate.status || 'N/A'}</span>
                                            </div>
                                            <div class="detail-actions">
                                                <a href="/candidate/display/${candidate.id}" class="btn btn-sm btn-info text-white"><i class="mdi mdi-eye"></i> View</a>
                                                <a href="/candidate/${candidate.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                <a href="#" class="btn btn-sm btn-danger" data-id="${candidate.id}" data-status="${candidate.status}" onclick="deleteCandidate(event)"><i class="mdi mdi-delete"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="desktop-only-col">${candidate.email || 'N/A'}</td>
                            <td class="desktop-only-col capitalize-text">${candidate.job_title || 'N/A'}</td>
                            <td class="desktop-only-col capitalize-text">
                                <span class="badge badge-outline-secondary" style="font-size: 11px;">${candidate.status || 'N/A'}</span>
                            </td>
                            <td class="desktop-only-col">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <a href="/candidate/display/${candidate.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    <a href="/candidate/${candidate.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <a href="#" class="text-danger fs-5" title="Delete"
                                       data-id="${candidate.id}"
                                       data-status="${candidate.status}"
                                       onclick="deleteCandidate(event)">
                                       <i class="mdi mdi-delete"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="mobile-expand-col text-center">
                                <button type="button" class="expand-toggle" data-target="candidate-details-${candidate.id}" aria-label="Expand details"></button>
                            </td>
                        </tr>
                    `;
                    });

                    document.getElementById('candidates-Table-Body').innerHTML = tableRows;

                    if ($.fn.DataTable.isDataTable('#candidates-Table')) {
                        $('#candidates-Table').DataTable().clear().destroy();
                    }
                    $('#candidates-Table').DataTable({
                        columnDefs: [
                            {
                                targets: [4, 5],
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            search: "",
                            searchPlaceholder: "Search"
                        }
                    });
                } else {
                    console.error('Failed to fetch candidates:', responseData.message);
                }
            })
            .catch((error) => {
                console.error('Error fetching candidates:', error);
            });
    });

    // Delete candidate — warns if candidate has a completed interview or is hired
    function deleteCandidate(event) {
        event.preventDefault();
        const anchor      = event.target.closest('a');
        const candidateId = anchor.getAttribute('data-id');
        const status      = (anchor.getAttribute('data-status') || '').toLowerCase();

        // High-risk statuses that warrant an extra warning
        const isHighRisk = ['hired', 'completed', 'scheduled'].includes(status);

        function performDelete() {
            fetch(`/api/candidate/${candidateId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Content-Type': 'application/json',
                },
            })
            .then(r => r.json())
            .then(responseData => {
                if (responseData.status === 'success') {
                    const row = document.querySelector(`tr[data-id="${candidateId}"]`);
                    if (row) row.remove();
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The candidate has been deleted successfully.',
                        icon: 'success',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' },
                        confirmButtonText: 'OK',
                    });
                } else {
                    const msg = responseData.messages?.error
                        || responseData.message
                        || 'Failed to delete the candidate. They may have associated interviews.';
                    Swal.fire({
                        title: 'Cannot Delete',
                        text: msg,
                        icon: 'error',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' },
                        confirmButtonText: 'OK',
                    });
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'An error occurred while deleting the candidate.', 'error');
            });
        }

        if (isHighRisk) {
            // ⚠️ Strong warning for candidates with interviews / already hired
            Swal.fire({
                title: '⚠️ Warning: Active Candidate',
                html: `<p>This candidate's status is <strong>${status}</strong>.</p>
                       <p>They may have <strong>interview or onboarding records</strong> linked to their profile.</p>
                       <p>Deleting this candidate will <strong>not</strong> delete associated user accounts, but their recruitment history will be lost.</p>
                       <p><strong>Are you absolutely sure?</strong></p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete Candidate',
                cancelButtonText: 'No, Keep It',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn hr-btnbg'
                }
            }).then(result => {
                if (result.isConfirmed) performDelete();
            });
        } else {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This candidate and their application data will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                    cancelButton: 'hr-btnbg',
                }
            }).then(result => {
                if (result.isConfirmed) performDelete();
            });
        }
    }

    // 📥 Export to Excel functionality
    document.getElementById('btnExportCandidates')?.addEventListener('click', function () {
        const btn = this;
        const search = $('#candidates-Table_filter input').val() || '';
        const token = localStorage.getItem('token');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...';

        const queryParams = new URLSearchParams({ search: search });

        fetch(`<?= base_url('api/candidate/export') ?>?${queryParams.toString()}`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(async response => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-file-excel iconfontsize"></i> Export';
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
            a.download = `Candidates_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Candidate list exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-file-excel iconfontsize"></i> Export';
            Swal.fire('Export Error', error.message || 'Failed to export candidates', 'error');
        });
    });
</script>

<?= $this->endSection() ?>
