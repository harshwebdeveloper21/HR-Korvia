<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
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

        #order-listing_length label {
            margin-top: 1px;
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #order-listing_length label::first-text,
        #order-listing_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #order-listing_length label {
            font-size: 0;
            /* hide text */
        }

        #order-listing_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #order-listing_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #order-listing_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        #order-listing_filter label {
            font-size: 0;
        }

        #order-listing_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 2.05rem !important;
        }

        .custom-select {
            height: 32px !important;
            width: 57px !important;
        }
    }
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage City</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportCity" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/city" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add City
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="order-listing">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>City </th>
                                <th class="desktop-only-col">Country</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 20px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php foreach ($cities as $city): ?>
                                <?php
                                $country = null;
                                foreach ($countries as $cntry) {
                                    if ($cntry["id"] == $city["country_id"]) {
                                        $country = ucfirst(
                                            strtolower($cntry["country_name"]),
                                        );
                                        // Capitalize first letter
                                        break;
                                    }
                                }
                                $cityName = ucfirst(
                                    strtolower($city["city_name"]),
                                );

                                // Capitalize first letter
                                ?>
                                <tr data-id="<?= $city[
                                    "id"
                                ] ?>"> <!-- Add data-id here -->
                                    <td><?= $counter++ ?></td>
                                    <td>
                                        <?= $cityName ?>
                                        <div class="expanded-details" id="city-details-<?= $city["id"] ?>" onclick="event.stopPropagation();">
                                            <div class="detail-row">
                                                <span class="detail-label">Country Name:</span>
                                                <span class="detail-value"><?= $country ? $country : "N/A" ?></span>
                                            </div>
                                            <div class="detail-actions">
                                                <a href="/city?id=<?= $city[
                                                    "id"
                                                ] ?>" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                <a href="#" class="btn btn-sm btn-danger delete-city" data-id="<?= $city[
                                                    "id"
                                                ] ?>"><i class="mdi mdi-delete"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="desktop-only-col"><?= $country ? $country : "N/A" ?></td>
                                    <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                        <a href="/city?id=<?= $city[
                                            "id"
                                        ] ?>" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                        <a href="#" class="text-danger fs-5 delete-city" data-id="<?= $city[
                                            "id"
                                        ] ?>" title="Delete"><i class="mdi mdi-delete"></i></a>
                                    </td>
                                    <td class="mobile-expand-col text-center">
                                        <button type="button" class="expand-toggle" data-target="city-details-<?= $city["id"] ?>" aria-label="Expand details"></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // DataTable is already initialized by global datatable.js
        // Just ensure it exists and apply mobile visibility
        
        // Apply mobile visibility
        if (typeof applyMobileTableVisibility === 'function') {
            applyMobileTableVisibility();
        }
        
        // Handle the delete button click
        $(document).on('click', '.delete-city', function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            const cityId = $(this).data('id');
            const token = localStorage.getItem('token'); // JWT token

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
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
                        url: `/api/city/${cityId}`, // URL to delete the city
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(responseData) {
                            if (responseData.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The city record has been deleted.',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'hr-btnbg'
                                    }
                                }).then(() => {
                                    $(`tr[data-id="${cityId}"]`).remove(); // Remove the deleted row
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: responseData.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    buttonsStyling: false,
                                    customClass: {
                                        confirmButton: 'hr-btnbg'
                                    }
                                });
                            }
                        },

                        error: function(xhr) {
                            let errorMessage = 'There was an error deleting the city. Please try again.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg'
                                }
                            });
                        }
                    });
                }
            });
        });

        // 📥 Export to Excel functionality
        $('#btnExportCity').on('click', function () {
            const $btn = $(this);
            const search = $('#order-listing_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/city/export') ?>?${queryParams.toString()}`, {
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
                a.download = `Cities_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'Cities exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export cities', 'error');
            });
        });
    });
</script>
<?= $this->endSection() ?>
