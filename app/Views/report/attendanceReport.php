<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<!-- <link rel="stylesheet" href="assets/css/attendancereport.css"> -->
<link rel="stylesheet" href="<?= base_url(
    env("ImagePath") . "assets/css/attendancereport.css",
) ?>">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .capitalize-text {
        text-transform: capitalize;
    }

    .reporstmagin {
        margin-top: 47px !important;
    }

    @media (max-width: 767px) {
        .attendenceall {
            font-size: 12px !important;
            padding: 5px !important;
            margin-top: 5px !important;
            /* margin-bottom: 5px !important; */
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .btnpdingam {
            padding: 5.3px !important;
            font-size: 12px !important;
            /* margin: 7px !important; */
        }
    }
</style>
<div class="filter-section">
    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="header-controls">
            <h4 class="card-title fw-bolder mb-0">Attendance Report</h4>
        </div>
        <div class="d-flex align-items-center">
            <button class="btn hr-btnbg btnpdingam" style="white-space: nowrap;" onclick="fetchAttenReport()">Generate
                Report</button>
        </div>
    </div>

    <button id="toggleFilters" class="btn btnpdingam hr-btnbg mx-0 w-100 d-md-none"
        onclick="toggleFilters()">Filters</button>

    <!-- Filter Row -->
    <div id="filters-row" class="row g-3">
        <!-- Department -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Department:</label>
            <select id="department_id" name="department_id" class="form-select" onchange="loadEmployees(this.value);">
                <option value="">All Departments</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department["id"] ?>"><?= $department["department_name"] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Employee -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Employee:</label>
            <select id="user_id" name="user_id" class="form-select" onchange="fetchAttenReport();">
                <option value="">All Employees</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee["id"] ?>"><?= esc(
                          $employee["username"] ?? ''
                      ) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Year -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Year:</label>
            <select id="year" name="year" class="form-select" onchange="fetchAttenReport();">
                <option value="">All Years</option>
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 5; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Month -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Month:</label>
            <select id="month" name="month" class="form-select" onchange="fetchAttenReport();">
                <option value="">All Months</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        </div>

        <!-- Start Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">From Date:</label>
            <input type="date" id="start_date" class="form-control" onchange="fetchAttenReport();">
        </div>

        <!-- End Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">To Date:</label>
            <input type="date" id="end_date" class="form-control" onchange="fetchAttenReport();">
        </div>
    </div>

    <!-- Chart and Table Container -->
    <div class="row mt-md-5 mt-3">
        <div class="col-12">
            <div class="chart-container mb-4">
                <canvas id="attendanceChart"></canvas>
            </div>

            <!-- Attendance Table -->
            <div class="table-container" id="table-section" style="display: none;">
                <h5 class="fw-semibold mb-3">Attendance Report</h5>
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-body">
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function displayValidationMessage(inputId, message) {
        const inputElement = document.getElementById(inputId);
        const errorElement = document.createElement("div");
        errorElement.className = "text-danger mt-1";
        errorElement.innerText = message;

        // Insert the error message after the input field
        inputElement.parentNode.appendChild(errorElement);
    }

    // Function to clear all validation messages
    function clearValidationMessages() {
        const errorMessages = document.querySelectorAll(".text-danger");
        errorMessages.forEach(message => message.remove());
    }

    let AttendanceInstance = null;

    // Helper: get fresh CSRF token from cookie
    function getCSRFToken() {
        let cookieName = '<?= csrf_token() ?>';
        let csrfCookieName = 'csrf_cookie_name';
        let match = document.cookie.match(new RegExp('(^| )' + csrfCookieName + '=([^;]+)'));
        return match ? match[2] : '<?= csrf_hash() ?>';
    }

    function getCSRFData() {
        let data = {};
        data['<?= csrf_token() ?>'] = getCSRFToken();
        return data;
    }

    function loadEmployees(departmentId) {
        // Reset employee dropdown immediately
        const employeeSelect = document.getElementById('user_id');
        employeeSelect.innerHTML = '<option value="">All Employees</option>';

        $.ajax({
            url: "<?= site_url("report/fetchAttendanceEmployeesByDepartment") ?>",
            type: "POST",
            dataType: "json",
            data: {
                ...getCSRFData(),
                department_id: departmentId
            },
            success: function (response) {
                employeeSelect.innerHTML = '<option value="">All Employees</option>';
                if (Array.isArray(response)) {
                    response.forEach(emp => {
                        employeeSelect.innerHTML += `<option value="${emp.id}">${emp.firstname} ${emp.lastname}</option>`;
                    });
                }
                // Fetch report AFTER employees are loaded
                fetchAttenReport();
            },
            error: function (xhr) {
                console.error("Error loading employees:", xhr.responseText);
                // Still fetch report even if employee load fails
                fetchAttenReport();
            }
        });
    }

    function fetchAttenReport() {
        const departmentId = document.getElementById("department_id").value;
        const employeeId = document.getElementById('user_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const year = document.getElementById('year').value;
        const month = document.getElementById('month').value;

        console.log("Fetching report with:", { departmentId, employeeId, startDate, endDate, year, month });
        clearValidationMessages();

        $.ajax({
            url: "<?= site_url("report/fetchAttendanceReport") ?>",
            type: "POST",
            dataType: "json",
            data: {
                ...getCSRFData(),
                department_id: departmentId,
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate,
                year: year,
                month: month
            },
            success: function (response) {
                console.log("AJAX Response:", response);
                console.log("Sent employee_id:", employeeId);

                if (response && response.tableData && Array.isArray(response.tableData)) {
                    populateTable(response.tableData);
                    document.getElementById("table-section").style.display = "block";
                } else {
                    document.getElementById("table-section").style.display = "none";
                    console.error("Invalid data for the table:", response);
                }

                if (response.chartData) {
                    updateChart(response.chartData);
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.status, xhr.responseText);
            }
        });
    }

    function populateTable(data) {
        // IMPORTANT: Destroy DataTable FIRST before touching the DOM
        // Otherwise destroy() puts cached old rows back into the tbody
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().clear().destroy();
        }

        const tableBody = document.getElementById('attendance-body');
        tableBody.innerHTML = "";

        if (!data.length) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
            return;
        }

        data.forEach(row => {
            tableBody.innerHTML += `
                <tr>
                    <td class="capitalize-text">${row.firstname} ${row.lastname || ''}</td>
                    <td class="capitalize-text">${row.department_name || 'N/A'}</td>
                    <td class="capitalize-text">${row.date}</td>
                    <td class="capitalize-text">${row.status || 'N/A'}</td>
                </tr>
            `;
        });

        // Now initialize fresh DataTable with the new rows only
        $('#attendanceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "Search attendance:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "zeroRecords": "No matching records found"
            }
        });
    }

    function updateChart(data) {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        if (AttendanceInstance instanceof Chart) {
            AttendanceInstance.destroy();
        }

        const labels = data.map(entry => entry.date);
        const attendanceCounts = data.map(entry => entry.attendance_count); // Ensure this field exists

        AttendanceInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Attendance Count',
                    data: attendanceCounts,
                    fill: true,
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderColor: '#4caf50',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Attendance Status Distribution'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Load data on page load (moved below after DataTables scripts are loaded)
</script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Initialize DataTable when table is shown
    function initializeAttendanceDataTable() {
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
            $('#attendanceTable').DataTable().destroy();
        }
        $('#attendanceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "Search attendance:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "zeroRecords": "No matching records found"
            }
        });
    }
</script>
<script>
    function toggleFilters() {
        const el = document.getElementById('filters-row');
        if (el) el.classList.toggle('d-none');
    }

    // Table starts empty - data loads only when user selects filters or clicks Generate Report
    $(document).ready(function () {
        // Do not auto-fetch on page load
    });
</script>

<?= $this->endSection() ?>