<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<!-- <link rel="stylesheet" href="assets/css/empreport.css"> -->
<link rel="stylesheet" href="<?= base_url(
                                    env("ImagePath") . "assets/css/empreport.css",
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
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .btnpdingam {
            padding: 5.3px !important;
            font-size: 12px !important;
        }
    }

    @media (min-width: 768px) {
        .col-md-3 {
            flex: 0 0 auto !important;
            width: 21% !important;
        }
    }
</style>
<div class="filter-section">
    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="header-controls">
            <h4 class="card-title fw-bolder mb-0">Leave Report</h4>
        </div>
        <div class="d-flex align-items-center">

            <button class="btn hr-btnbg btnpdingam" style="white-space: nowrap;" onclick="fetchLeaveReport()">Generate Report</button>
        </div>
    </div>
    <button id="toggleFilters" class="btn btnpdingam hr-btnbg mx-0 w-100 d-md-none" onclick="toggleFilters()">Filters</button>
    <!-- Filter Form -->
    <div id="filters-row" class="row g-3">
        <!-- Employee/HR -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="user_id" class="form-label">Employee/HR:</label>
            <select class="form-select" id="user_id">
                <option value="">All Employees</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee["id"] ?>"><?= esc($employee["username"],) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Leave Type -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="leave_type" class="form-label">Leave Type:</label>
            <select class="form-select" id="leave_type">
                <option value="">All Types</option>
                <?php foreach ($leaveTypes as $type): ?>
                    <option value="<?= $type["id"] ?>"><?= esc($type["leave_type"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" id="status">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <!-- Year -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Year:</label>
            <select id="year" name="year" class="form-select">
                <option value="">All Years</option>
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 10; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Month -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Month:</label>
            <select id="month" name="month" class="form-select">
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
            <label for="start_date" class="form-label">From Date:</label>
            <input type="date" id="start_date" class="form-control">
        </div>

        <!-- End Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="end_date" class="form-label">To Date:</label>
            <input type="date" id="end_date" class="form-control">
        </div>
    </div>

    <!-- Chart -->
    <div class="mt-md-5 mt-3">
        <canvas id="leaveChart"></canvas>
    </div>

    <!-- Table -->
    <div class="table-responsive mt-4" id="table-section" style="display: none;">
        <table id="leaveTable" class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="leave-table-body">
                <!-- Dynamic rows will be injected here -->
            </tbody>
        </table>
    </div>
</div>


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
    let leaveChartInstance = null;

    function fetchLeaveReport() {
        const employeeId = document.getElementById('user_id').value;
        const leaveType = document.getElementById('leave_type').value;
        const status = document.getElementById('status').value;
        const year = document.getElementById('year').value;
        const month = document.getElementById('month').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        clearValidationMessages();

        $.ajax({
            url: "<?= site_url("report/fetchLeaveReport") ?>",
            type: "POST",
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>', // Add CSRF token here
                employee_id: employeeId,
                leave_type: leaveType,
                status: status,
                year: year,
                month: month,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                // Debugging: Log the entire response to inspect its structure
                console.log("AJAX Response:", response);

                // Ensure the response contains the necessary data
                if (response && response.tableData && Array.isArray(response.tableData)) {

                    populateTable(response.tableData);
                    document.getElementById("table-section").style.display = "block"; // Show Table
                } else {
                    document.getElementById("table-section").style.display = "none"; // Hide Table
                    console.error("Invalid data for the table:", response);
                }

                if (response.chartData) {
                    updateChart(response.chartData);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    function populateTable(data) {
        const tableBody = document.getElementById('leave-table-body');
        tableBody.innerHTML = "";

        if (!data.length) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
            return;
        }

        data.forEach(row => {
            tableBody.innerHTML += `
            <tr>
                <td class="capitalize-text">${row.username}</td>
                <td class="capitalize-text">${row.leave_type}</td>
                <td class="capitalize-text">${row.start_date}</td>
                <td class="capitalize-text">${row.end_date}</td>
                <td class="capitalize-text">${row.status || 'N/A'}</td>
            </tr>
        `;
        });

        // Initialize DataTable after populating
        initializeLeaveDataTable();
    }

    function updateChart(chartData) {
        const ctx = document.getElementById('leaveChart').getContext('2d');
        if (leaveChartInstance instanceof Chart) {
            leaveChartInstance.destroy();
        }
        leaveChartInstance = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Leave Statistics'
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
    function initializeLeaveDataTable() {
        if ($.fn.DataTable.isDataTable('#leaveTable')) {
            $('#leaveTable').DataTable().destroy();
        }
        $('#leaveTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "Search leaves:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ leaves",
                "infoEmpty": "No leaves found",
                "zeroRecords": "No matching leaves found"
            }
        });
    }
</script>
<script>
    function toggleFilters() {
        const el = document.getElementById('filters-row');
        if (el) el.classList.toggle('d-none');
    }    

    // Ensure DataTables scripts are loaded before initial fetch
    $(document).ready(function() {
        fetchLeaveReport();
    });
</script>

<?= $this->endSection() ?>