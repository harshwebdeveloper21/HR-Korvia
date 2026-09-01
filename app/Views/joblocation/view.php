<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {


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

        #location-table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #location-table_length::first-text,
        #location-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #location-table_length label {
            font-size: 0;
            /* hide text */
        }

        #location-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #location-table_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }

        #location-table_filter label {
            font-size: 0;
        }

        #location-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        #location-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        /* div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 29px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        } */


    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Location</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportJobLocation" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/joblocation" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Location
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="location-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Location</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="location-table-body">
                            <!-- Table rows will be dynamically inserted here -->
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

        // Function to fetch departments from the API
        function fetchLocation() {
            $.ajax({
                url: '/api/joblocation', // URL to get all departments
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.locations) {
                        const locations = response.locations;
                        let tableRows = '';
                        locations.forEach((location, index) => {
                            const formattedName = location.job_location.charAt(0).toUpperCase() + location.job_location.slice(1).toLowerCase();
                            tableRows += `
                        <tr data-id="${location.location_id}">
                            <td>${index + 1}</td>
                            <td>
                                <div style="flex: 1;">
                                    <span>${formattedName}</span>
                                    <div class="expanded-details" id="location-details-${location.location_id}" onclick="event.stopPropagation();">
                                        <div class="detail-actions">
                                            <a href="/joblocation?id=${location.location_id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                            <a href="#" class="btn btn-sm btn-danger delete-location" data-id="${location.location_id}"><i class="mdi mdi-delete"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                <a href="/joblocation?id=${location.location_id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <a href="#" class="text-danger fs-5 delete-location" data-id="${location.location_id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                            </td>
                            <td class="mobile-expand-col text-center">
                                <button type="button" class="expand-toggle" data-target="location-details-${location.location_id}" aria-label="Expand details"></button>
                            </td>
                        </tr>
                    `;
                        });
                        $('#location-table-body').html(tableRows);
                        if ($.fn.DataTable.isDataTable('#location-table')) {
                            $('#location-table').DataTable().clear().destroy();
                        }
                        $('#location-table').DataTable({
                            columnDefs: [
                                {
                                    targets: 3,
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
                        Swal.fire('Error', 'Failed to load job location', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch job location', 'error');
                }
            });
        }


        // Call the function to populate departments on page load
        fetchLocation();

        // Handle the delete button click
        $(document).on('click', '.delete-location', function(e) {
            e.preventDefault();

            const locationId = $(this).data('id');

            if (!locationId) {
                Swal.fire('Error', 'Invalid location ID', 'error');
                return;
            }

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
                        url: `/api/joblocation/${locationId}`, // Ensure this is the correct API route
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(responseData) {
                            console.log(responseData); // Debugging: See actual response

                            if (responseData && responseData.message) {
                                Swal.fire('Deleted!', responseData.message, 'success')
                                    .then(() => {
                                        $(`tr[data-id="${locationId}"]`).remove();
                                    });
                            } else {
                                Swal.fire('Error!', 'Failed to delete the location.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText); // Debugging: See actual error message

                            let errorMessage = 'There was an error deleting the location.';
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
        $('#btnExportJobLocation').on('click', function () {
            const $btn = $(this);
            const search = $('#location-table_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/joblocation/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Job_Locations_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Job locations exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export job locations', 'error');
            });
        });
    });
</script>

<?= $this->endSection() ?>
