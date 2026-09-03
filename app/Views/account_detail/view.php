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
            max-width: 24%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
        }

        #account-table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #account-table_length label::first-text,
        #account-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #account-table_length label {
            font-size: 0;
            /* hide text */
        }

        #account-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #account-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #account-table_filter label {
            font-size: 0;
        }

        #account-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        /* .form-control {
            height: 0px !important;
        } */

        .attendencepaddbottom {
            margin-bottom: 5px !important
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
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


                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Manage Account Detail</h4>
                    <a href="<?= base_url(
                        "/accountdetail",
                    ) ?>" class="btn hr-btnbg attendenceall" style="white-space: nowrap;">
                        <i class="mdi mdi-plus iconfontsize"></i> Add Account Detail
                    </a>
                </div>


                <div class="table-responsive">
                    <table class="table table-striped" id="account-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Account Number</th>
                                <th class="desktop-only-col">Bank Name</th>
                                <th class="desktop-only-col">Branch Name</th>
                                <th class="desktop-only-col">Branch Code</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="account-table-body">
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

        // Fetch and display account data
        function fetchAccounts() {
            $.ajax({
                url: '/api/account-detail/view',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.data) {
                        const accounts = response.data;
                        let tableRows = '';
                        accounts.forEach((account, index) => {
                            tableRows += `
                                <tr data-id="${account.id}">
                                    <td>${index + 1}</td>
                                    <td class="capitalize-text">
                                        <div style="flex: 1;">
                                            <span>${account.username}</span>
                                            <div class="expanded-details" id="account-details-${account.id}">
                                                <div class="detail-row">
                                                    <span class="detail-label">Account Number:</span>
                                                    <span class="detail-value">${account.acc_number}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Bank Name:</span>
                                                    <span class="detail-value">${account.bank_name}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Branch Name:</span>
                                                    <span class="detail-value">${account.branch_name}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Branch Code:</span>
                                                    <span class="detail-value">${account.branch_code}</span>
                                                </div>
                                                <div class="detail-actions">
                                                    <a href="edit/detail/${account.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteAccount(${account.id})"><i class="mdi mdi-delete"></i> Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="desktop-only-col capitalize-text">${account.acc_number}</td>
                                    <td class="desktop-only-col capitalize-text">${account.bank_name}</td>
                                    <td class="desktop-only-col capitalize-text">${account.branch_name}</td>
                                    <td class="desktop-only-col capitalize-text">${account.branch_code}</td>
                                    <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                        <a href="edit/detail/${account.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                        <a href="javascript:void(0);" class="text-danger fs-5" title="Delete" onclick="deleteAccount(${account.id})"><i class="mdi mdi-delete"></i></a>
                                    </td>
                                    <td class="mobile-expand-col text-center">
                                        <button type="button" class="expand-toggle" data-target="account-details-${account.id}" aria-label="Expand details"></button>
                                    </td>
                                </tr>
                            `;
                        });

                        if ($.fn.DataTable.isDataTable('#account-table')) {
                            $('#account-table').DataTable().clear().destroy();
                        }

                        $('#account-table-body').html(tableRows);

                        const dt = $('#account-table').DataTable({
                            columnDefs: [
                                {
                                    targets: 7,
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            }
                        });

                        dt.on('draw', function() {
                            if (typeof applyMobileTableVisibility === 'function') {
                                applyMobileTableVisibility();
                            }
                        });

                        // Apply mobile visibility
                        if (typeof applyMobileTableVisibility === 'function') {
                            applyMobileTableVisibility();
                        }

                    } else {
                        Swal.fire('Error', 'Failed to load Account details', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to fetch Account details', 'error');
                }
            });
        }

        fetchAccounts(); // Load data initially

        // Global delete handler
        window.deleteAccount = function(accountId) {
            if (!accountId) return;

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
                        url: `/api/account-detail/delete/${accountId}`,
                        type: 'POST',
                        data: { _method: 'DELETE' },
                        headers: {
                            'Authorization': `Bearer ${token}`,
                        },
                        success: function(responseData) {
                            if (responseData.status === 'success') {
                                Swal.fire('Deleted!', 'The account detail has been deleted.', 'success')
                                    .then(() => {
                                        fetchAccounts();
                                    });
                            } else {
                                Swal.fire('Error!', responseData.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'There was an error deleting the account.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        };

        $(document).on('click', '.delete-department', function(e) {
            e.preventDefault();
            deleteAccount($(this).data('id'));
        });
    });
</script>


<?= $this->endSection() ?>
