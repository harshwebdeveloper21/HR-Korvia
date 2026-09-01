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
        margin-top: 1.1rem;
    }

    .filtermenu {
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .departmrgin {
        margin-right: 10px !important;
    }

    @media (max-width: 767px) {
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
            font-size: 10px !important;
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

        #statusFilter {
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
            margin-top: .5rem !important;
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
            float: left !important;
            margin-left: -5rem !important;
        }

        .col-sm-12.col-md-6 {
            flex: 0 0 50%;
            max-width: 43%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none;
        }

        #task-table_length label {
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #task-table_length label::first-text,
        #task-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #task-table_length label {
            font-size: 0;
            /* hide text */
        }

        #task-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #task-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #task-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        #task-table_filter label {
            font-size: 0;
        }

        #task-table_filter input {
            font-size: 14px;
            margin-left: 30px;
            /* Keep input font size normal */
        }

        .form-control {
            height: 0px !important;
        }

        .attendencepaddbottom {
            margin-bottom: 5px !important
        }

        /* .sm-form-size{
                height: 30px !important;
            } */
        .sm-font-size-filter {
            font-size: 12px !important;
            margin-right: 5px !important;
        }

        .fonsize-titile-sm {
            font-size: 13px !important;
        }

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
</style>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Manage Holiday</h4>
                    <a href="/add" class="btn hr-btnbg attendenceall text-nowrap">
                        <i class="mdi mdi-plus iconfontsize"></i> Add Holiday
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="task-table">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th class="desktop-only-col">Holiday Date</th>
                                <th class="desktop-only-col">Description</th>
                                <th class="desktop-only-col action-column" style="width: 130px;">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');

        function formatHolidayDate(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                const year = parts[0];
                const monthIndex = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[2], 10);
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const monthName = monthNames[monthIndex] || parts[1];
                return `${day} ${monthName} ${year}`;
            }
            return dateStr;
        }

        function fetchHolidays() {
            $.ajax({
                url: '<?= base_url('api/get_holidays') ?>',
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function(response) {
                    if (response.status === 'success' && Array.isArray(response.data)) {
                        let tableRows = '';
                        const holidays = response.data;

                        // Sort ascending by holiday_date
                        holidays.sort((a, b) => new Date(a.holiday_date) - new Date(b.holiday_date));

                        holidays.forEach(holiday => {
                            const formattedName = holiday.title.charAt(0).toUpperCase() + holiday.title.slice(1).toLowerCase();
                            const displayDate = formatHolidayDate(holiday.holiday_date);
                            tableRows += `
                            <tr>
                                <td class="capitalize-text">
                                    <div style="flex: 1;">
                                        <span>${formattedName}</span>
                                        <div class="expanded-details" id="holiday-details-${holiday.id}" onclick="event.stopPropagation();">
                                            <div class="detail-row">
                                                <span class="detail-label">Holiday Date:</span>
                                                <span class="detail-value">${displayDate}</span>
                                            </div>
                                            <div class="detail-row">
                                                <span class="detail-label">Description:</span>
                                                <span class="detail-value">${holiday.description || ''}</span>
                                            </div>
                                            <div class="detail-actions">
                                                <a href="/edit-holiday/${holiday.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                                <a href="#" class="btn btn-sm btn-danger delete-holiday" data-id="${holiday.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="desktop-only-col" data-order="${holiday.holiday_date}">${displayDate}</td>
                                <td class="desktop-only-col capitalize-text">${holiday.description || ''}</td>
                                <td class="desktop-only-col">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <a href="/edit-holiday/${holiday.id}" class="text-warning fs-5 edit-holiday" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="#" class="text-danger fs-5 delete-holiday" data-id="${holiday.id}" title="Delete">
                                            <i class="mdi mdi-delete"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="mobile-expand-col text-center">
                                    <button type="button" class="expand-toggle" data-target="holiday-details-${holiday.id}" aria-label="Expand details"></button>
                                </td>
                            </tr>`;
                        });

                        // Populate table body
                        $('#task-table tbody').html(tableRows);

                        // Reinitialize DataTable
                        if ($.fn.DataTable.isDataTable('#task-table')) {
                            $('#task-table').DataTable().clear().destroy();
                        }

                        $('#task-table').DataTable({
                            order: [[1, 'asc']], // Order by Holiday Date ascending
                            columnDefs: [
                                {
                                    targets: 4, // mobile expand column
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            language: {
                                search: '',
                                searchPlaceholder: 'Search'
                            }
                        });
                        // Apply mobile visibility
                        if (typeof applyMobileTableVisibility === 'function') {
                            applyMobileTableVisibility();
                        }
                    } else {
                        Swal.fire('Error', 'No holiday data found.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch holidays.', 'error');
                }
            });
        }

        // Call function on page load
        fetchHolidays();

        $(document).on('click', '.delete-holiday', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This holiday will be deleted permanently!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('api/holioday/delete') ?>/${id}`,
                        type: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            // fetchHolidays();
                            location.reload();
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete holiday.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>

<?= $this->endSection(); ?>