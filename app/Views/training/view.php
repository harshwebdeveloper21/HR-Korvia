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
            display: none !important;
        }

        #training-table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #training-table_length label::first-text,
        #training-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #training-table_length label {
            font-size: 0;
            /* hide text */
        }

        #training-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #training-table_filter label {
            font-size: 0;
        }

        #training-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }


        #training-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #training-table_length label select {
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
                    <h4 class="card-title">Manage Trainings</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportTrainings" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <?php $role = session()->get("role"); ?>
                        <?php if ($role !== "employee"): ?>
                            <a href="<?= base_url(
                                "/training",
                            ) ?>" class="btn hr-btnbg attendenceall text-nowrap">
                                <i class="mdi mdi-plus iconfontsize"></i> Add Training
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="training-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Training Title</th>
                                <th class="desktop-only-col">Start Date</th>
                                <th class="desktop-only-col">End Date</th>
                                <th class="desktop-only-col">Location</th>
                                <th style="display: none;">Created At</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="training-table tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token
        // Get user role from the JWT payload
        function getUserRole() {
            let payload = JSON.parse(atob(token.split('.')[1])); // Decode JWT
            return payload.role; // Extract user role
        }

        function fetchTrainings() {
            $.ajax({
                url: '<?= base_url("/api/training/getAll") ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const trainings = response.data;
                        let tableRows = '';
                        let userRole = getUserRole(); // Get role from JWT
                        var baseImagePath = "<?= base_url(env("ImagePath")) ?>";
                        trainings.forEach((training) => {
                            let imageUrl = training.profile_image ?
                                `/upload/${training.profile_image}` :
                                `${baseImagePath}upload/default-profile.jpg`;
                            let actionButtons = '';
                            let mobileActionsHtml = '';

                            if (userRole !== 'employee') {
                                actionButtons = `
                                    <a href="/training/profile/${training.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    <a href="/training/get/${training.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <a href="#" class="text-danger fs-5 delete-training" data-id="${training.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/training/profile/${training.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                        <a href="/training/get/${training.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-training" data-id="${training.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                    </div>
                                `;
                            } else {
                                actionButtons = `
                                    <a href="/training/profile/${training.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/training/profile/${training.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                    </div>
                                `;
                            }

                            tableRows += `
                            <tr data-id="${training.id}">
                                <td class="py-1">
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <a href="/training/profile/${training.id}" class="text-decoration-none">
                                            <img src="${imageUrl}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        </a>
                                        <div style="flex: 1;">
                                            <a href="/training/profile/${training.id}" class="text-decoration-none text-dark">
                                                <span class="capitalize-text">${training.employee_name}</span>
                                            </a>
                                            <div class="expanded-details" id="training-details-${training.id}" onclick="event.stopPropagation();">
                                                <div class="detail-row">
                                                    <span class="detail-label">Training Title:</span>
                                                    <span class="detail-value">${training.training_title}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Start Date:</span>
                                                    <span class="detail-value">${training.start_date}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">End Date:</span>
                                                    <span class="detail-value">${training.end_date}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Location:</span>
                                                    <span class="detail-value">${training.location}</span>
                                                </div>
                                                ${mobileActionsHtml}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="desktop-only-col capitalize-text">${training.training_title}</td>
                                <td class="desktop-only-col capitalize-text">${training.start_date}</td>
                                <td class="desktop-only-col capitalize-text">${training.end_date}</td>
                                <td class="desktop-only-col capitalize-text">${training.location}</td>
                                <td style="display: none;">${training.created_at}</td>
                                <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                    ${actionButtons}
                                </td>
                                <td class="mobile-expand-col text-center">
                                    <button type="button" class="expand-toggle" data-target="training-details-${training.id}" aria-label="Expand details"></button>
                                </td>
                            </tr>
                        `;
                        });

                        $('#training-table tbody').html(tableRows); // Update the table body
                        // $('#training-table').DataTable(); // Initialize DataTable for the updated rows
                        $('#training-table').DataTable({
                            order: [
                                [5, 'desc']
                            ],
                            columnDefs: [
                                {
                                    targets: 5,
                                    visible: false
                                },
                                {
                                    targets: 7, // mobile expand column
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
                        Swal.fire('Error', 'Failed to load training records', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch training records', 'error');
                }
            });
        }

        // Call the fetchTrainings function on page load
        fetchTrainings();

        $(document).on('click', '.delete-training', function(e) {
            e.preventDefault();
            const trainingId = $(this).data('id');

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
                    cancelButton: 'btn hr-btnbg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/training/${trainingId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The training record has been deleted.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn hr-btnbg'
                                    }
                                }).then(() => {
                                    $(`tr[data-id="${trainingId}"]`).remove();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete the training record.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn hr-btnbg'
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error deleting the training record.',
                                icon: 'error',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn hr-btnbg'
                                }
                            });
                        }
                    });
                }
            });
        });

        // 📥 Export to Excel functionality
        $('#btnExportTrainings').on('click', function () {
            const $btn = $(this);
            const search = $('#training-table_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/training/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Trainings_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Trainings exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export trainings', 'error');
            });
        });

    });
</script>

<?= $this->endSection() ?>
