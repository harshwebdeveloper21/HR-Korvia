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
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                    <h4 class="card-title mb-0">All EOM Performance</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="btnExportEmpOfMonth" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>
                        <a href="<?= base_url(
                            "/addemp-month-performance",
                        ) ?>" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Generate EOM
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="templateTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Template Name</th>
                                <th class="desktop-only-col">Month & Year</th>
                                <th class="desktop-only-col">Action</th>
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
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <a href="/performance/profile/${item.user_id}" class="text-decoration-none">
                                    <img src="/upload/${item.profile_image || 'default-profile.jpg'}"
                                         alt="Profile"
                                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                </a>
                                <div style="flex: 1;">
                                    <a href="/performance/profile/${item.user_id}" class="text-decoration-none text-dark fw-bold">
                                        <span class="capitalize-text">${item.user_name}</span>
                                    </a>
                                    <div class="expanded-details" id="eom-details-${item.id}">
                                        <div class="detail-row">
                                            <span class="detail-label">Template:</span>
                                            <span class="detail-value capitalize-text">${item.template_title}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Month & Year:</span>
                                            <span class="detail-value">${item.month_year}</span>
                                        </div>
                                        <div class="detail-actions">
                                            <a href="javascript:void(0);"
                                               class="btn btn-sm btn-success text-white download-eom-letter"
                                               data-employee-id="${item.user_id}"
                                               data-template-id="${item.template_id}">
                                                <i class="mdi mdi-download"></i> Download EOM
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deletePerformance(${item.id})">
                                                <i class="mdi mdi-delete"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="desktop-only-col capitalize-text">${item.template_title}</td>
                        <td class="desktop-only-col capitalize-text">${item.month_year}</td>
                        <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                            <a href="javascript:void(0);"
                                class="text-success fs-5 download-eom-letter"
                                title="Download EOM Letter"
                                data-employee-id="${item.user_id}"
                                data-template-id="${item.template_id}">
                                <i class="mdi mdi-download"></i>
                            </a>
                            <a href="javascript:void(0);" class="text-danger fs-5" title="Delete" onclick="deletePerformance(${item.id})">
                                <i class="mdi mdi-delete"></i>
                            </a>
                        </td>
                        <td class="mobile-expand-col text-center">
                            <button type="button" class="expand-toggle" data-target="eom-details-${item.id}" aria-label="Expand details"></button>
                        </td>
                    </tr>
                `);
                });

                // Initialize DataTable
                const dt = $('#templateTable').DataTable({
                    responsive: false,
                    columnDefs: [
                        {
                            targets: 0,
                            visible: false,
                            searchable: false
                        },
                        {
                            targets: 5,
                            orderable: false,
                            searchable: false
                        }
                    ],
                    language: {
                        emptyTable: "No performance records available",
                        search: "",
                        searchPlaceholder: "Search"
                    }
                });

                dt.on('draw', function() {
                    if (typeof applyMobileTableVisibility === 'function') {
                        applyMobileTableVisibility();
                    }
                });

                if (typeof applyMobileTableVisibility === 'function') {
                    applyMobileTableVisibility();
                }
            },
            error: function(xhr, status, error) {
                console.error("Failed to load templates:", error);
                $('#noTemplates').text("Error loading data.").show();
            }
        });
    }

    window.deletePerformance = function(id) {
        if (!id) return;
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
                    type: 'POST',
                    data: { _method: 'DELETE' },
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
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
    };
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

    // 📥 Export to Excel functionality
    $('#btnExportEmpOfMonth').on('click', function () {
        const $btn = $(this);
        const search = $('#templateTable_filter input').val() || '';
        const token = localStorage.getItem('token');

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

        const queryParams = new URLSearchParams({ search: search });

        fetch(`<?= base_url('api/empofmonth/export') ?>?${queryParams.toString()}`, {
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
            a.download = `Employee_of_the_Month_${dateStr}.xlsx`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            Swal.fire({
                icon: 'success',
                title: 'Exported!',
                text: 'Employee of the Month awards exported to Excel successfully.',
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export');
            Swal.fire('Export Error', error.message || 'Failed to export Employee of the Month data', 'error');
        });
    });
</script>

<?= $this->endSection() ?>
