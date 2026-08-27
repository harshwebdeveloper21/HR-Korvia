<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (max-width: 767px) {
        /* .attendenceall {
            font-size: 12px !important;
            padding: 5.3px !important;
        } */

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

        #country-table_length label {
            display: flex;
            align-items: center;
            margin-top: 1px;
        }

        /* Hide the text inside the label */
        #country-table_length label::first-text,
        #country-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #country-table_length label {
            font-size: 0;
            /* hide text */
        }

        #country-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #country-table_length label {
            font-size: 0px;
            /* hide all text inside the label */
        }
        #country-table_filter label {
            font-size: 0;
        }

        #country-table_filter input {
            font-size: 14px;
            /* Keep input font size normal */
        }
        #country-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }
           div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 32px !important
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }

         /* .form-control {
            height: 0px !important;
        }  */
    }
    @media (min-width: 768px) {
        .btn.hr-btnbg.attendenceall {
            width: 193px !important;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage State</h4>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnExportState" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
                        </button>
                        <a href="/state" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add State
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="country-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>State Name</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="country-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token'); // JWT token from login

        // Fetch country data when the page loads
        $.ajax({
            url: '/api/state',
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(responseData) {
                if (responseData.status === 'success') {
                    const countries = responseData.data; // Get the country data
                    const tableBody = $('#country-table-body');
                    tableBody.empty(); // Clear the table body first

                    countries.forEach((country, index) => {
                        const formattedName = country.state_name.charAt(0).toUpperCase() + country.state_name.slice(1).toLowerCase();
                        const row = $('<tr>').attr('data-id', country.id);
                        row.html(`
                            <td>${index + 1}</td>
                            <td>
                                <div style="flex: 1;">
                                    <span>${formattedName}</span>
                                    <div class="expanded-details" id="state-details-${country.id}" onclick="event.stopPropagation();">
                                        <div class="detail-actions">
                                            <a href="/state?id=${country.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                            <a href="#" class="btn btn-sm btn-danger" data-id="${country.id}" onclick="deleteCountry(event)"><i class="mdi mdi-delete"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                <a href="/state?id=${country.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                <a href="#" class="text-danger fs-5" title="Delete" data-id="${country.id}" onclick="deleteCountry(event)"><i class="mdi mdi-delete"></i></a>
                            </td>
                            <td class="mobile-expand-col text-center">
                                <button type="button" class="expand-toggle" data-target="state-details-${country.id}" aria-label="Expand details"></button>
                            </td>
                        `);
                        tableBody.append(row);
                    });

                    // Initialize DataTable after populating the table rows
                    $('#country-table').DataTable({
                        "aLengthMenu": [
                            [5, 10, 15, -1],
                            [5, 10, 15, "All"]
                        ],
                        "iDisplayLength": 10,
                        "columnDefs": [
                            {
                                targets: 3,
                                orderable: false,
                                searchable: false
                            }
                        ],
                        "language": {
                            searchPlaceholder: "Search",
                            search: ""
                        }
                    });
                    // Apply mobile visibility
                    if (typeof applyMobileTableVisibility === 'function') {
                        applyMobileTableVisibility();
                    }
                } else {
                    console.error('Failed to fetch countries:', responseData.message);
                }
            },
            error: function(error) {
                console.error('Error fetching countries:', error);
            }
        });
    });

    function deleteCountry(event) {
        event.preventDefault();
        const countryId = $(event.target).closest('a').data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to revert this!',
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
                    url: `/api/state/${countryId}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'application/json',
                    },
                    success: function(responseData) {
                        if (responseData.status === 'success') {
                            $(`tr[data-id="${countryId}"]`).remove();

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'The country has been deleted.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg'
                                }
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
                        let errorMessage = 'There was an error deleting the country. Please try again.';
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
        // 📥 Export to Excel functionality
        $('#btnExportState').on('click', function () {
            const $btn = $(this);
            const search = $('#country-table_filter input').val() || '';
            const token = localStorage.getItem('token');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Exporting...');

            const queryParams = new URLSearchParams({ search: search });

            fetch(`<?= base_url('api/state/export') ?>?${queryParams.toString()}`, {
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
                a.download = `States_${dateStr}.xlsx`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Exported!',
                    text: 'States exported to Excel successfully.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                $btn.prop('disabled', false).html('<i class="mdi mdi-file-excel iconfontsize"></i> Export Excel');
                Swal.fire('Export Error', error.message || 'Failed to export states', 'error');
            });
        });
    });
</script>

<?= $this->endSection() ?>
