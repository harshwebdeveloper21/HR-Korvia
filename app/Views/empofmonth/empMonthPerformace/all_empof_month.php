<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .capitalize-text {
        text-transform: capitalize;
    }
    @media (max-width: 767px) {
        .attendenceall {
            font-size: 8px !important;
            padding: 6px !important;
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
            font-size: 0px !important;
            float: left !important;
            /* margin-left: -5rem !important;  */
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 25%;
            max-width: 26%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
        }

        #templateTable_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #templateTable_length label::first-text,
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

        #templateTable_filter label {
            font-size: 0;
        }

        #templateTable_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #templateTable_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #templateTable_length label select {
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
    @media (min-width: 768px) {
  .btn.hr-btnbg.attendenceall {
    width: 191px !important;
  }
}

</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">All EOM Performance</h4>

                    <a href="<?= base_url(
                        "/addemp-month-performance",
                    ) ?>" class="btn hr-btnbg attendenceall">
                        <i class="mdi mdi-plus iconfontsize"></i> Generate EOM
                    </a>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="templateTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Employee Name</th>
                                <th>Template Name</th>
                                <th>Month & Year</th>
                                <th>Action</th>
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
<script>
    $(document).ready(function() {
        fetchTemplates();
    });
    const token = localStorage.getItem('token'); // JWT token from login

    function fetchTemplates() {
        $.ajax({
            url: '<?= base_url("api/employee-of-month/all") ?>',
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
            },
            dataType: 'json',
            success: function(response) {
                const tableBody = $('#templateTableBody');
                const noData = $('#noTemplates');

                // Destroy existing DataTable before repopulating
                if ($.fn.DataTable.isDataTable('#templateTable')) {
                    $('#templateTable').DataTable().destroy();
                }

                tableBody.empty();

                if (response.length === 0) {
                    noData.show();
                    return;
                }

                noData.hide();

                response.forEach((item, index) => {
                    tableBody.append(`
                    <tr>
                        <td>${index + 1}</td>
                          <td class="py-1">
                                 <a href="/performance/profile/${item.user_id}" class="text-decoration-none text-dark">
                                 <div style="display: flex; align-items: center; gap: 10px;">
            <img src="/upload/${item.profile_image || 'default-profile.jpg'}"
                 alt="Profile"
                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            <span class="capitalize-text">${item.user_name}</span>
        </div>
                        </a></td>

                        <td class="capitalize-text">${item.template_title}</td>
                        <td class="capitalize-text">${item.month_year}</td>
                        <td style="display: flex; align-items: center; gap: 8px;">
                            <a href="#" class="text-danger fs-5" title="Delete" onclick="deletePerformance(${item.id})">
                                <i class="mdi mdi-delete"></i>
                            </a>
                           <a href="#"
                                class="text-success fs-5 download-eom-letter"
                                title="Download EOM Letter"
                                data-employee-id="${item.user_id}"
                                data-template-id="${item.template_id}">
                                <i class="mdi mdi-download"></i>
                            </a>

                        </td>
                    </tr>
                `);
                });

                // Initialize DataTable
                $('#templateTable').DataTable({
                    responsive: true,

                    columnDefs: [{
                        targets: 0,
                        visible: false,
                        searchable: false
                    }],
                    language: {
                        emptyTable: "No performance records available",
                        search: "",
                        searchPlaceholder: "Search",

                    }
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
            }
        });
    }


    function deletePerformance(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/employee-of-month/delete/${id}`,
                    type: 'DELETE',
                    success: function() {
                        Swal.fire(
                            'Deleted!',
                            'Employee Of The Month Performance record has been deleted.',
                            'success'
                        );
                        fetchTemplates();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Failed to delete the performance record.',
                            'error'
                        );
                    }
                });
            }
        });
    }
    $(document).on('click', '.download-eom-letter', function(e) {
        e.preventDefault();

        const employeeId = $(this).data('employee-id');
        const templateId = $(this).data('template-id');
        const row = $(this).closest('tr'); // Get the closest table row

        if (!employeeId || !templateId) {
            Swal.fire('Error', 'Missing employee or template ID', 'error');
            return;
        }

        const employeeName = row.find('td').eq(1).text(); // Get the employee name from the second column (index 1)

        const url = `/api/employee-of-month/generate-pdf/${employeeId}/${templateId}?download=true`;


        $.ajax({
            url: url,
            method: 'GET',
            xhrFields: {
                responseType: 'blob' // We expect a blob (PDF file)
            },
            success: function(data, status, xhr) {
                const blob = new Blob([data], {
                    type: 'application/pdf'
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);

                // Get current date and time in a readable format
                const currentDate = new Date();
                const formattedDate = currentDate.toISOString().split('T')[0]; // Get yyyy-mm-dd format
                const formattedTime = currentDate.toTimeString().split(' ')[0]; // Get hh:mm:ss format

                // Set the dynamic filename with employee name and current timestamp
                const filename = `${employeeName}_Performance_${formattedDate}_${formattedTime}.pdf`;

                // Set the download attribute to trigger download with dynamic filename
                link.download = filename;
                link.click();

                // Optional: Clean up the blob URL after download
                window.URL.revokeObjectURL(link.href);
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Failed to generate or download the PDF.', 'error');
            }
        });
    });
</script>

<?= $this->endSection() ?>
