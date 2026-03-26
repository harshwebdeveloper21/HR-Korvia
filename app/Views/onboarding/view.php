<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
     .capitalize-text {
        text-transform: capitalize;
    }
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
   .form-select{
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

        .filterdept {
            margin-left: -13px !important;
        }

        .filter-sm-res a {
            width: 100%;
        }

        .btnpdingam {
            padding: 5.3px !important;
            font-size: 9px !important;
            /* margin: 7px !important; */
        }

        .filterbtnpadd {
            padding: 2px !important;

        }

        .filtermenu {
            margin-bottom: 12px !important;
            margin-left: 7px !important;
        }

        #departmentonbordingFilter {
            max-width: 150px;
            font-size: 14px;
            padding: 4px 8px;
        }

        .form-select {
            height: 1.75rem !important;
        }

        .fontsmfiltertitle {
            font-size: 11px !important;
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

        #onboaring-Table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #onboaring-Table_length label::first-text,
        #onboaring-Table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #onboaring-Table_length label {
            font-size: 0;
            /* hide text */
        }

        #onboaring-Table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #onboaring-Table_filter label {
            font-size: 0;
        }

        #onboaring-Table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #onboaring-Table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #onboaring-Table_length label select {
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
    @media (min-width: 767px) {
        div.dataTables_wrapper div.dataTables_filter label input {
            width: 338px !important;
        }
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Employee OnBoarding</h4>
                  
                     <div class="d-flex justify-content-between align-items-center">
                        <label for="statusFilter" class="form-label mb-0">Filter by Department:</label>
                        <select class="form-select w-25" id="departmentonbordingFilter" style="min-width: 200px;">
                            <option value="">All Departments</option>
                            
                        </select>

                         <a href="/onboarding" class="btn hr-btnbg">
                        <i class="mdi mdi-plus"></i> Add OnBoarding
                    </a>
                    </div>
                </div> -->

                <div class="main-dec-div flex-direction-column">

                    <h4 class="card-title filtermarginjob">Manage Employee OnBoarding</h4>

                    <div class="filtermenu">
                        <div class="filterdept" style="margin-right:10px">
                         
                            <select class="form-select form-select-sm departmrgin mb-3" id="departmentonbordingFilter" style="max-width: 150px; font-size: 14px;">

                                <option value="">All Departments</option>
                                <!-- Departments will be populated dynamically -->
                            </select>
                        </div>
                        <div class="filterbtn filterbtnpadd">
                            <a href="/onboarding" class="btn hr-btnbg btnpdingam mb-2">
                                <i class="mdi mdi-plus iconfontsize"></i> Add OnBoarding
                            </a>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="onboaring-Table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th> Candidate Name</th>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Start Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="onboaring-Table-Body">

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

        function fetchOnboarding(departmentId = '') {
            $.ajax({
                url: '/api/onboarding',
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
                        const onboardings = responseData.data;
                        let tableRows = '';

                        onboardings.forEach((onboarding, index) => {
                            tableRows += `
                        <tr data-id="${onboarding.id}">
                            <td>${index + 1}</td>
                            <td>
                                <a href="/onboarding/display/${onboarding.id}" class="text-decoration-none text-dark capitalize-text">
                                    ${onboarding.candidate_name}
                                </a>
                            </td>
                            <td class="capitalize-text">${onboarding.department_name}</td>
                            <td class="capitalize-text">${onboarding.job_title}</td>
                            <td>${onboarding.start_date}</td>
                            <td style="display: flex; align-items: center; gap: 8px;">
                                <a href="/onboarding/display/${onboarding.id}" class="text-primary fs-5" title="View">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="/onboarding/${onboarding.id}" class="text-warning fs-5" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <a href="#" class="text-danger fs-5" title="Delete" data-id="${onboarding.id}" onclick="deleteLeave(event)">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                                <a href="#" 
                                    class="fs-5 download-offer-letter" 
                                    title="Download Offer Letter" 
                                    data-candidate-id="${onboarding.candidate_id}" 
                                    data-offer-letter-id="${onboarding.offer_later_id}"
                                    data-candidate-name="${onboarding.candidate_name}" style="color:black">
                                    <i class="mdi mdi-download"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                        });

                        // $('#onboaring-Table-Body').html(tableRows);
                        // $('#onboaring-Table').DataTable();

                        const $table = $('#onboaring-Table');

                        // Destroy if already initialized
                        if ($.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().clear().destroy();
                        }

                        // Inject rows into <tbody>
                        $('#onboaring-Table-Body').html(tableRows);

                        // Delay to let DOM fully update
                        setTimeout(() => {
                            $table.DataTable({
                                order: [
                                    [5, 'desc']
                                ],
                                columnDefs: [{
                                    targets: 0,
                                    visible: false,
                                    searchable: false
                                }],
                                language: {
                                    search: "",
                                    searchPlaceholder: "Search"
                                }
                            });
                        }, 10);
                    } else {
                        console.error('Failed to fetch onboarding data:', responseData.message);
                    }
                },
                error: function(error) {
                    console.error('Error fetching onboarding data:', error);
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
                        $('#departmentonbordingFilter').html(options);
                    }
                },
                error: function() {
                    console.error('Failed to load departments');
                }
            });
        }

        // ✅ Department filter change event
        $('#departmentonbordingFilter').on('change', function() {
            const selectedDeptId = $(this).val();
            fetchOnboarding(selectedDeptId); // Pass selected department ID
        });

        // 🚀 Initial calls
        fetchDepartments();
        fetchOnboarding(); // Load all employees initially
    });


    // Function to handle delete action
    function deleteLeave(event) {
        event.preventDefault(); // Prevent the default link behavior
        const onboardingId = event.target.closest('a').getAttribute('data-id'); // Get the leave ID

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
                fetch(`/api/onboarding/${onboardingId}`, {
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
                            const row = document.querySelector(`tr[data-id="${onboardingId}"]`);
                            if (row) row.remove();

                            // Show success message
                            Swal.fire('Deleted!', 'The onboarding has been deleted successfully.', 'success');
                        } else {
                            // Show error message if deletion fails
                            Swal.fire('Error!', responseData.message || 'Only admins can delete onboarding.', 'error');
                        }
                    })
                    .catch((error) => {
                        console.error('Error deleting onboarding:', error);
                        Swal.fire('Error!', 'An error occurred while deleting the onboarding.', 'error');
                    });
            }
        });
    }
    $(document).on('click', '.download-offer-letter', function(e) {
        e.preventDefault();

        const $button = $(this); // <-- capture the clicked button
        const candidateId = $button.data('candidate-id');
        const offerLetterId = $button.data('offer-letter-id');
        const candidateName = $button.data('candidate-name') || 'Candidate'; // safely get candidate name

        $.ajax({
            url: `/offer-letter/generate/${candidateId}/${offerLetterId}`,
            method: 'GET',
            xhrFields: {
                responseType: 'blob'
            },
            success: function(data, status, xhr) {
                // Format current date and time
                const now = new Date();
                const formattedDate = now.toISOString().split('T')[0]; // yyyy-mm-dd
                const formattedTime = now.toTimeString().split(' ')[0].replace(/:/g, '-'); // hh-mm-ss

                // Create safe filename
                const safeName = candidateName.replace(/\s+/g, '_');
                const filename = `offerletter_${safeName}_${formattedDate}_${formattedTime}.pdf`;

                // Trigger file download
                const blob = new Blob([data], {
                    type: 'application/pdf'
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(link.href);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to download offer letter.',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>