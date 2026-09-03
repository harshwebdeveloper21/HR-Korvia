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

                    <div class="d-md-flex gap-2 align-items-center">
                        <!-- Year Filter -->
                        <select class="form-select" id="holidayYearFilter" style="min-width: 130px; width: auto;">
                            <!-- Populated dynamically by JS -->
                        </select>

                        <!-- Auto-Generate Dropdown -->
                        <div class="dropdown">
                            <button class="btn hr-btnbg attendenceall dropdown-toggle text-nowrap" type="button" id="btnAutoGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-auto-fix iconfontsize"></i> Auto-Generate
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3" aria-labelledby="btnAutoGenerate">
                                <li>
                                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="triggerAutoGenerate('single')">
                                        <i class="mdi mdi-calendar-check me-2 text-success fs-16"></i> Generate for Selected Year (<span class="lbl-selected-year"><?= date('Y') ?></span>)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="triggerAutoGenerate('next_5_years')">
                                        <i class="mdi mdi-calendar-range me-2 text-warning fs-16"></i> Auto-Generate Next 5 Years (<?= date('Y') ?> - <?= date('Y') + 5 ?>)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="triggerAutoGenerate('all')">
                                        <i class="mdi mdi-calendar-star me-2 text-primary fs-16"></i> Populate Full Range (2024 - 2030)
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Export Button -->
                        <button type="button" id="btnExportHoliday" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-file-excel iconfontsize"></i> Export
                        </button>

                        <!-- Add Holiday Button -->
                        <a href="/add" class="btn hr-btnbg attendenceall text-nowrap">
                            <i class="mdi mdi-plus iconfontsize"></i> Add Holiday
                        </a>
                    </div>
                </div>

                <!-- Empty State Banner (Shown if selected year has 0 holidays) -->
                <div id="emptyYearBanner" style="display:none;" class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 border-0 shadow-sm p-3 mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-alert-circle-outline fs-24 me-3 text-warning"></i>
                        <div>
                            <strong class="text-dark">No holidays found for year <span class="lbl-banner-year"></span>.</strong>
                            <div class="text-muted fs-13">You can auto-generate festival holiday dates with 1 click.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm text-white px-3 fw-bold" style="background-color: #E66136; border-radius: 8px;" onclick="triggerAutoGenerate('single')">
                        <i class="mdi mdi-auto-fix me-1"></i> Auto-Generate <span class="lbl-banner-year"></span> Holidays
                    </button>
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
    let selectedYear = '<?= date('Y') ?>';

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

    function fetchHolidays(yearToFetch) {
        const token = localStorage.getItem('token');
        const reqYear = yearToFetch || selectedYear;

        $.ajax({
            url: `<?= base_url('api/get_holidays') ?>?year=${reqYear}`,
            type: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Populate Year dropdown if not yet populated
                    if ($('#holidayYearFilter option').length <= 1 && Array.isArray(response.years)) {
                        let yearOpts = '';
                        response.years.forEach(yr => {
                            yearOpts += `<option value="${yr}" ${yr == reqYear ? 'selected' : ''}>${yr}</option>`;
                        });
                        yearOpts += `<option value="all" ${reqYear === 'all' ? 'selected' : ''}>All Years</option>`;
                        $('#holidayYearFilter').html(yearOpts);
                    }

                    $('.lbl-selected-year').text(reqYear === 'all' ? 'All' : reqYear);
                    $('.lbl-banner-year').text(reqYear);

                    const holidays = Array.isArray(response.data) ? response.data : [];

                    // Show empty state alert if 0 holidays for specific year
                    if (holidays.length === 0 && reqYear !== 'all') {
                        $('#emptyYearBanner').fadeIn();
                    } else {
                        $('#emptyYearBanner').hide();
                    }

                    let tableRows = '';
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
                                <div class="expanded-details" id="holiday-details-${holiday.id}">
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
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteHoliday(${holiday.id})"><i class="mdi mdi-delete"></i> Delete</button>
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
                                <a href="javascript:void(0);" class="text-danger fs-5" title="Delete" onclick="deleteHoliday(${holiday.id})">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </div>
                        </td>
                        <td class="mobile-expand-col text-center">
                            <button type="button" class="expand-toggle" data-target="holiday-details-${holiday.id}" aria-label="Expand details"></button>
                        </td>
                    </tr>`;
                    });

                    // Destroy old DataTable if initialized
                    if ($.fn.DataTable.isDataTable('#task-table')) {
                        $('#task-table').DataTable().clear().destroy();
                    }

                    // Populate table body
                    $('#task-table tbody').html(tableRows);

                    // Reinitialize DataTable
                    const dt = $('#task-table').DataTable({
                        order: [[1, 'asc']],
                        columnDefs: [
                            {
                                targets: 4,
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            search: '',
                            searchPlaceholder: 'Search'
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
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch holidays for the selected year.', 'error');
            }
        });
    }

    function triggerAutoGenerate(mode) {
        const token = localStorage.getItem('token');
        let descText = '';
        if (mode === 'next_5_years') {
            descText = `This will automatically populate festival holidays (Makar Sankranti, Holi/Dhuleti, Raksha Bandhan, Janmashtami, Diwali, Bhai Duj, etc.) for the next 5 years (<?= date('Y') ?> to <?= date('Y')+5 ?>). Existing dates will NOT be overwritten.`;
        } else if (mode === 'all') {
            descText = `This will generate festival holidays for all supported years (2024 to 2030). Existing records will NOT be overwritten.`;
        } else {
            descText = `This will auto-generate festival holidays for year ${selectedYear}. Existing records will NOT be overwritten.`;
        }

        Swal.fire({
            title: 'Auto-Generate Holidays?',
            text: descText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="mdi mdi-auto-fix me-1"></i> Yes, Generate',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn text-white px-4 py-2',
                cancelButton: 'btn btn-secondary ms-2 px-3 py-2'
            },
            buttonsStyling: false,
            didOpen: () => {
                const btn = Swal.getConfirmButton();
                if (btn) btn.style.backgroundColor = '#E66136';
            }
        }).then((res) => {
            if (res.isConfirmed) {
                Swal.fire({
                    title: 'Generating Holidays...',
                    text: 'Please wait while holiday dates are being calculated and configured.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('api/holidays/auto-generate') ?>',
                    type: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    data: JSON.stringify({
                        year: selectedYear,
                        mode: mode
                    }),
                    success: function(resp) {
                        if (resp.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Holidays Generated!',
                                text: resp.message,
                                confirmButtonColor: '#E66136'
                            }).then(() => {
                                fetchHolidays(selectedYear);
                            });
                        } else {
                            Swal.fire('Error', resp.message || 'Failed to auto-generate holidays.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'An error occurred while generating holidays.', 'error');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        const token = localStorage.getItem('token');

        // Initial fetch
        fetchHolidays(selectedYear);

        // Year Filter change event
        $('#holidayYearFilter').on('change', function() {
            selectedYear = $(this).val();
            fetchHolidays(selectedYear);
        });

        // Export Button
        $('#btnExportHoliday').on('click', function() {
            const yr = $('#holidayYearFilter').val() || selectedYear;
            window.location.href = `<?= base_url('api/holidays/export') ?>?year=${yr}`;
        });

        // Delete holiday
        window.deleteHoliday = function(id) {
            if (!id) return;
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
                            fetchHolidays(selectedYear);
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete holiday.', 'error');
                        }
                    });
                }
            });
        };

        $(document).on('click', '.delete-holiday', function(e) {
            e.preventDefault();
            deleteHoliday($(this).data('id'));
        });
    });
</script>

<?= $this->endSection(); ?>