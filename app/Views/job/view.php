<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .main-dec-div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filterdept select {
        margin-top: .5rem;
    }

    .filterbtn {
        margin-top: 0.1rem;
    }

    .filtermenu {
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .departmrgin {
        margin-right: 10px !important;
    }

    .form-select {
        height: 2.44rem;
    }

    /* select.form-select{
        padding: 0px !important;
    }  */

    @media (max-width: 767.98px) {
        .filter-sm-res {
            flex-wrap: wrap !important;
        }

        .flex-direction-column {
            flex-direction: column;
        }

        /* .filterdept{
    margin-right: 41px !important;
  } */

        .filter-sm-res h4 {
            flex: 1 1 100%;
            margin-bottom: 10px;
        }

        .filter-sm-res>div {
            flex: 1 1 100%;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-sm-res select {
            width: 100% !important;
            min-width: unset !important;
        }

        .filter-sm-res a {
            width: 100%;
        }

        .btnpdingam {
            padding: 5px !important;
            font-size: 12px !important;
            margin: 7px !important;
        }

        .filterbtnpadd {
            padding: 2px !important;
            /* margin-left: 112px !important; */

        }

        .filtermenu {
            margin-bottom: 12px !important;
            /* margin-left:-54px !important; */
            justify-content: space-between;
            width: 100%;
        }

        #departmentjobFilter {
            max-width: 150px;
            font-size: 14px;
            padding: 4px 8px;
        }

        .form-select {
            height: 27px !important;
        }

        .fontsmfiltertitle {
            font-size: 13px !important;
        }

        .departmrgin {
            font-size: 11px !important;
        }

        .filterbtn {
            margin-top: -0.5rem !important;
        }

        .filtermarginjob {
            margin-bottom: 10px !important;
        }

        .dataTables_length {
            margin-left: .1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            /* margin-left: -3rem !important;  */
            float: left !important;
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
            /* Hide "Search:" label text */
        }



        #jobs-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #jobs-Table_length::first-text,
        #jobs-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #jobs-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #jobs-Table_filter label {
            font-size: 0;
        }

        #jobs-Table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #jobs-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #jobs-Table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #jobs-Table_length label select {
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

    .capitalize-text {
        text-transform: capitalize;
    }

    @media (min-width: 767px) {
        div.dataTables_wrapper div.dataTables_filter label input {
            width: 278px !important;
        }
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Jobs</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="statusFilter" class="form-label mb-0">Filter by Department:</label>
                        <select class="form-select w-25" id="departmentjobFilter" style="min-width: 200px;margin:0.1rem">
                            <option value="">All Departments</option>
                          
                        </select>

                        <a href="/job" class="btn hr-btnbg">
                            <i class="mdi mdi-plus"></i> Add job
                        </a>
                    </div>

                </div> -->

                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Jobs</h4>
                    <div class="d-md-flex gap-2 align-items-center">
                        <select class="form-select" id="departmentjobFilter" style="min-width: 180px; width: auto;">
                            <option value="">All Departments</option>
                            <!-- Departments will be populated dynamically -->
                        </select>
                        <button type="button" id="btnExportJobs" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/job" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Job
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="jobs-Table">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th class="desktop-only-col">Department</th>
                                <th class="desktop-only-col">Job Type</th>
                                <th class="desktop-only-col">Salary</th>
                                <th class="desktop-only-col">Post Date</th>
                                <th class="desktop-only-col action-column" style="width: 100px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="jobs-Table-Body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');

        // ✅ Fetch and display jobs
        function fetchJobs(departmentId = '') {
            const token = localStorage.getItem('token');

            $.ajax({
                url: `<?= base_url('api/job') ?>`,
                method: 'GET',
                data: departmentId ? {
                    department_id: departmentId
                } : {},
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(responseData) {
                    if (responseData.status === 'success') {
                        const jobs = responseData.data;
                        let tableRows = '';

                        jobs.forEach((job, index) => {
                            const jobTypeFormatted = job.job_type ? (job.job_type.charAt(0).toUpperCase() + job.job_type.slice(1).toLowerCase()) : '';
                            const row = `
                        <tr data-id="${job.id}">
                            <td class="capitalize-text">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <div style="flex: 1;">
                                        <a href="/job/display/${job.id}" class="text-decoration-none text-dark fw-bold" title="View">${job.job_title}</a>
                                        <div class="expanded-details" id="job-details-${job.id}" onclick="event.stopPropagation();">
                                            <div class="detail-row">
                                                <span class="detail-label">Department:</span>
                                                <span class="detail-value">${job.department_name || 'N/A'}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Job Type:</span>
                                                <span class="detail-value">${jobTypeFormatted}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Salary:</span>
                                                <span class="detail-value">${job.salary_range || 'N/A'}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Post Date:</span>
                                                <span class="detail-value">${job.post_date || 'N/A'}</span>
                                            </div>
                                            <div class="detail-actions">
                                                <a href="/job/display/${job.id}" class="btn btn-sm btn-info text-white"><i class="mdi mdi-eye"></i> View</a>
                                                <a href="/job?id=${job.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                <a href="#" class="btn btn-sm btn-danger" data-id="${job.id}" onclick="deleteLeave(event)"><i class="mdi mdi-delete"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="desktop-only-col capitalize-text">${job.department_name || 'N/A'}</td>
                            <td class="desktop-only-col capitalize-text"><span class="badge badge-outline-secondary" style="font-size: 11px;">${jobTypeFormatted}</span></td>
                            <td class="desktop-only-col">${job.salary_range || 'N/A'}</td>
                            <td class="desktop-only-col">${job.post_date || 'N/A'}</td>
                            <td class="desktop-only-col">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <a href="/job/display/${job.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    <a href="/job?id=${job.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <a href="#" class="text-danger fs-5" title="Delete" data-id="${job.id}" onclick="deleteLeave(event)"><i class="mdi mdi-delete"></i></a>
                                </div>
                            </td>
                            <td class="mobile-expand-col text-center">
                                <button type="button" class="expand-toggle" data-target="job-details-${job.id}" aria-label="Expand details"></button>
                            </td>
                        </tr>
                    `;
                            tableRows += row;
                        });

                        $('#jobs-Table-Body').html(tableRows);
                        if ($.fn.DataTable.isDataTable('#jobs-Table')) {
                            $('#jobs-Table').DataTable().clear().destroy();
                        }
                        $('#jobs-Table').DataTable({
                            order: [[4, 'desc']],
                            columnDefs: [
                                {
                                    targets: [5, 6],
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            }
                        });
                        // $('#jobs-Table').DataTable(); // Initialize the DataTable

                    } else {
                        console.error('Failed to fetch jobs:', responseData.message);
                    }
                },
                error: function(error) {
                    console.error('Error fetching jobs:', error);
                }
            });
        }

        function fetchDepartments() {
            $.ajax({
                url: '<?= base_url('/api/getdepartments') ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function(response) {
                    if (response.status && response.departments) {
                        let options = `<option value="">All Departments</option>`;
                        response.departments.forEach((dept) => {
                            options += `<option value="${dept.id}">${dept.department_name}</option>`;
                        });
                        $('#departmentjobFilter').html(options);
                    }
                },
                error: function() {
                    console.error('Failed to load departments');
                }
            });
        }

        // ✅ Department filter change event
        $('#departmentjobFilter').on('change', function() {
            const selectedDeptId = $(this).val();
            fetchJobs(selectedDeptId); // Pass selected department ID
        });

        // 🚀 Initial calls
        fetchDepartments();
        fetchJobs(); // Load all employees initially
    });

    // ✅ Fetch department list

    // Function to handle delete action
    function deleteLeave(event) {
        event.preventDefault();
        const jobId = $(event.target).closest('a').data('id'); // Get job ID

        // First confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to revert this!',
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
                $.ajax({
                    url: "<?= base_url('api/job/') ?>" + jobId,
                    type: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $(`tr[data-id="${jobId}"]`).remove(); // Remove row

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The job has been deleted successfully.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn hr-btnbg'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to delete the job.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn hr-btnbg'
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'An error occurred while deleting the job.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn hr-btnbg'
                            }
                        });
                    }
                });
            }
        });
    }

    // 📥 Export to Excel functionality
    $('#btnExportJobs').on('click', function () {
        const $btn = $(this);
        const departmentId = $('#departmentjobFilter').val() || '';
        const search = $('#jobs-Table_filter input').val() || '';
        const token = localStorage.getItem('token');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

        const queryParams = new URLSearchParams({
            department_id: departmentId,
            search: search
        });

        fetch(`<?= base_url('api/job/export') ?>?${queryParams.toString()}`, {
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
            a.download = `Job_Openings_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Job list exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
            Swal.fire('Export Error', error.message || 'Failed to export jobs', 'error');
        });
    });
</script>

<?= $this->endSection(); ?>