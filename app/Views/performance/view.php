<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .capitalize-text {
        text-transform: capitalize;
    }

    @media (min-width: 768px) {
        .attendenceall {
            width: 193px !important;
        }
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
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Performances</h4>
                    <?php $role = session()->get("role"); ?>
                    <?php if ($role !== "employee"): ?>
                        <a href="<?= base_url(
                            "/performance",
                        ) ?>" class="btn hr-btnbg attendenceall">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Performance
                        </a>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="performance-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Review Date</th>
                                <th class="desktop-only-col">Designation</th>
                                <th class="desktop-only-col">Rating</th>
                                <th style="display: none;">Created At</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic Rows will be appended here -->
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

        function getUserRole() {
            let payload = JSON.parse(atob(token.split('.')[1])); // Decode JWT
            return payload.role; // Extract user role
        }

        function fetchPerformance() {
            $.ajax({
                url: '<?= base_url("/api/performance/getAll") ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const performances = response.data;
                        let tableRows = '';
                        let userRole = getUserRole(); // Get role from JWT
                        var baseImagePath = "<?= base_url(env("ImagePath")) ?>";
                        performances.forEach((performance, index) => {
                            let imageUrl = performance.profile_image ?
                                `/upload/${performance.profile_image}` :
                                `${baseImagePath}upload/default-profile.jpg`;

                            // Desktop action buttons
                            let actionButtons = '';
                            if (userRole !== 'employee') {
                                actionButtons = `
                                <a href="/performance/profile/${performance.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                <a href="/performance/${performance.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <a href="#" class="text-danger fs-5 delete-performance" data-id="${performance.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                            `;
                            } else {
                                actionButtons = `
                                <a href="/performance/profile/${performance.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                            `;
                            }

                            // Mobile expanded details HTML
                            let mobileActionsHtml = '';
                            if (userRole !== 'employee') {
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/performance/profile/${performance.id}" class="btn btn-sm btn-primary" title="View"><i class="mdi mdi-eye"></i> View</a>
                                        <a href="/performance/${performance.id}" class="btn btn-sm btn-warning" title="Edit"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-performance" data-id="${performance.id}" title="Delete"><i class="mdi mdi-delete"></i> Delete</a>
                                    </div>
                                `;
                            } else {
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/performance/profile/${performance.id}" class="btn btn-sm btn-primary" title="View"><i class="mdi mdi-eye"></i> View</a>
                                    </div>
                                `;
                            }

                            tableRows += `
                            <tr data-id="${performance.id}" class="main-row">
                                <td class="py-1">
                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                        <a href="/performance/profile/${performance.id}" class="text-decoration-none">
                                            <img src="${imageUrl}"
                                                alt="Profile"
                                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        </a>
                                        <div style="flex: 1;">
                                            <a href="/performance/profile/${performance.id}" class="text-decoration-none text-dark">
                                                <span class="capitalize-text">${performance.employee_name}</span>
                                            </a>
                                            <div class="expanded-details" id="details-${performance.id}" onclick="event.stopPropagation();">
                                                <div class="detail-row">
                                                    <span class="detail-label">Designation:</span>
                                                    <span class="detail-value">${performance.designation_name}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Rating:</span>
                                                    <span class="detail-value">${performance.rating}/10</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Review Date:</span>
                                                    <span class="detail-value">${performance.review_date}</span>
                                                </div>
                                                ${mobileActionsHtml}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="desktop-only-col">${performance.review_date}</td>
                                <td class="desktop-only-col">${performance.designation_name}</td>
                                <td class="desktop-only-col">${performance.rating}</td>
                                <td style="display: none;">${performance.created_at}</td>
                                <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                    ${actionButtons}
                                </td>
                                <td class="mobile-expand-col text-center">
                                    <button type="button" class="expand-toggle" data-target="details-${performance.id}" aria-label="Expand details"></button>
                                </td>
                            </tr>
                        `;
                        });

                        $('#performance-table tbody').html(tableRows);
                        $('#performance-table').DataTable({
                            order: [
                                [4, 'desc']
                            ], // column index 4 = created_at
                            columnDefs: [
                                {
                                    targets: 4,
                                    visible: false,
                                    searchable: false
                                },
                                {
                                    targets: 6, // mobile expand column
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            }
                        });

                        // Apply mobile visibility after DataTable init
                        setTimeout(function() {
                            if (typeof applyMobileTableVisibility === 'function') {
                                applyMobileTableVisibility();
                            }
                        }, 100);

                    } else {
                        Swal.fire('Error', 'Failed to load performance records', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch performance records', 'error');
                }
            });
        }

        fetchPerformance();

        $(document).on('click', '.delete-performance', function(e) {
            e.preventDefault();
            const performanceId = $(this).data('id');

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
                        url: `/api/performance/${performanceId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The performance record has been deleted.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'btn hr-btnbg'
                                    }
                                }).then(() => {
                                    $(`tr[data-id="${performanceId}"]`).remove();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to delete the performance record.',
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
                                text: 'There was an error deleting the performance record.',
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
    });
</script>

<?= $this->endSection() ?>
