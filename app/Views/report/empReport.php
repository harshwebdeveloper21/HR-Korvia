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

    .department-summary-container {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .department-cards .card {
        margin-bottom: 10px;
        border: none;
        border-radius: 6px;
    }

    .department-cards .card-body {
        padding: 15px;
        text-align: center;
    }

    .department-cards .card-title {
        font-size: 14px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .department-cards .card-text {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
    }

    /* Different background colors for department cards */
    .department-cards .card:nth-child(1) { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .department-cards .card:nth-child(2) { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .department-cards .card:nth-child(3) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .department-cards .card:nth-child(4) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
    .department-cards .card:nth-child(5) { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .department-cards .card:nth-child(6) { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: white; }
    .department-cards .card:nth-child(7) { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: white; }
    .department-cards .card:nth-child(8) { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: white; }

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
        }

        .department-summary-container {
            margin-top: 20px;
        }
    }
</style>
<!-- Filters and Search -->
<div class="filter-section">
    <!-- Header -->
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="header-controls">
            <h4 class="card-title fw-bolder mb-0">Employee Report</h4>
        </div>
        <div class="d-flex align-items-center">
            <button class="btn hr-btnbg btnpdingam" style="white-space: nowrap;" onclick="fetchSalaryReport()">Generate Report</button>
        </div>        
    </div>

    <button id="toggleFilters" class="btn btnpdingam hr-btnbg mx-0 w-100 d-md-none" onclick="toggleFilters()">Filters</button>

    <!-- Filter Form -->
    <div id="filters-row" class="row g-3">
        <!-- Department -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Department:</label>
            <select id="department_id" name="department_id" class="form-select" onchange="loadEmployees(this.value)">
                <option value="">All Departments</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department["id"] ?>"><?= $department["department_name"] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Employee -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Employee:</label>
            <select id="user_id" name="user_id" class="form-select">
                <option value="">All Employees</option>
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
                    <option value="<?= $i ?>"><?= $i ?></option>
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

        <!-- Joining From -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Joining From:</label>
            <input type="date" id="joining_from" class="form-control">
        </div>

        <!-- Joining To -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Joining To:</label>
            <input type="date" id="joining_to" class="form-control">
        </div>

    </div>
    
    <!-- Main Content Area -->
    <div class="row mt-4">
        <!-- Left Side: Chart -->
        <div class="col-lg-8">
            <div class="chart-container">
                <canvas id="employeeChart"></canvas>
            </div>
        </div>
        
        <!-- Right Side: Department Summary -->
        <div class="col-lg-4">
            <div class="department-summary-container">
                <h5 class="mb-3">Department Summary</h5>
                <div id="departmentSummary" class="department-cards">
                    <!-- Department cards will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container mt-4" id="table-section" style="display: none;">
        <h5 class="fw-semibold mb-3">Employee Records</h5>
        <div class="table-responsive">
            <table id="employeeTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Joining date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body">
                    <!-- Dynamic rows populated by fetchLeaveReport -->
                </tbody>
            </table>
        </div>
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



    // ── CSRF token store (refreshed after every AJAX response) ──
    let csrfTokenName  = '<?= csrf_token() ?>';
    let csrfTokenValue = '<?= csrf_hash() ?>';
    function getCSRFData() { let d = {}; d[csrfTokenName] = csrfTokenValue; return d; }
    function refreshCSRF(r) { if (r && r.csrfHash) csrfTokenValue = r.csrfHash; }

    // ── Load employees when department changes ──
    function loadEmployees(departmentId) {
        const sel = document.getElementById('user_id');
        sel.innerHTML = '<option value="">All Employees</option>';
        $.ajax({
            url: '<?= site_url("report/fetchEmployeesByDepartment") ?>',
            type: 'POST',
            dataType: 'json',
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
            data: { ...getCSRFData(), department_id: departmentId },
            success: function(response) {
                refreshCSRF(response);
                const list = response.employees || [];
                list.forEach(emp => {
                    sel.innerHTML += `<option value="${emp.id}">${emp.firstname} ${emp.lastname}</option>`;
                });
            },
            error: function(xhr) { console.error('loadEmployees error:', xhr.status, xhr.responseText); }
        });
    }

    function fetchSalaryReport() {

        let departmentId = document.getElementById("department_id").value;
        let employeeId = document.getElementById("user_id").value;
        let year = document.getElementById("year").value;
        let month = document.getElementById("month").value;
        let joiningFrom = document.getElementById("joining_from").value;
        let joiningTo = document.getElementById("joining_to").value;

        clearValidationMessages();

        const token = localStorage.getItem('token'); // Get token for authorization

        $.ajax({
            url: "<?= site_url("report/fetchEmployeeReport") ?>",
            type: "POST",
            headers: {
                'Authorization': `Bearer ${token}`,
            },
            data: {
                ...getCSRFData(),
                department_id: departmentId,
                employee_id: employeeId,
                joining_from: joiningFrom,
                joining_to: joiningTo,
                year: year,
                month: month
            },
            dataType: "json",
            success: function(response) {
                refreshCSRF(response);
                console.log("Employee report payload:", {
                    department_id: departmentId,
                    employee_id: employeeId,
                    joining_from: joiningFrom,
                    joining_to: joiningTo,
                    year: year,
                    month: month
                });
                console.log("Employee report response counts:", {
                    tableRows: Array.isArray(response?.tableData) ? response.tableData.length : 0,
                    chartLabels: Array.isArray(response?.chartData?.labels) ? response.chartData.labels.length : 0,
                    totalEmployees: response?.summary?.total_employees ?? 0
                });
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

                if (response.summary) {
                    renderSummary(response.summary);
                }
            },

            error: function(xhr, status, error) {
                console.error("Error fetching report:", error);
            }
        });
    }

    function populateTable(data) {
        console.log('populateTable called, received records:', Array.isArray(data) ? data.length : typeof data);

        // Ensure table section visible
        document.getElementById("table-section").style.display = "block";

        // Convert backend objects to DataTable rows (array of arrays)
        const rows = Array.isArray(data) ? data.map(item => {
            const status = (item.is_deleted == "1") ? 'Deleted' : 'Active';
            return [
                `${item.firstname} ${item.lastname}`,
                item.department_name || '-',
                item.designation_name || '-',
                item.joining_date ?? '-',
                status
            ];
        }) : [];

        // If DataTable instance exists, update its data using the API
        if (employeeDataTable) {
            employeeDataTable.clear();
            if (rows.length) employeeDataTable.rows.add(rows);
            employeeDataTable.draw();
            console.log('Updated existing DataTable, rows:', employeeDataTable.rows().count());
            return;
        }

        // If no instance yet, populate DOM table body (fallback) then initialize DataTable with data
        const tableBody = document.getElementById("employee-table-body");
        tableBody.innerHTML = "";
        if (!rows.length) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No records found</td></tr>`;
        } else {
            rows.forEach(r => {
                tableBody.innerHTML += `<tr><td class="capitalize-text">${r[0]}</td><td class="capitalize-text">${r[1]}</td><td class="capitalize-text">${r[2]}</td><td class="capitalize-text">${r[3]}</td><td class="capitalize-text">${r[4]}</td></tr>`;
            });
        }

        // Initialize DataTable once with current rows
        employeeDataTable = $('#employeeTable').DataTable({
            data: rows,
            columns: [
                { title: 'Employee Name' },
                { title: 'Department' },
                { title: 'Designation' },
                { title: 'Joining date' },
                { title: 'Status' }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 10,
            language: {
                search: "Search employees:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ employees",
                infoEmpty: "No employees found",
                zeroRecords: "No matching employees found"
            }
        });
        console.log('Initialized new DataTable, rows:', employeeDataTable.rows().count());
    }
    let employeeChart = null;
    let employeeDataTable = null;

    function updateChart(chartData) {
        // Destroy the existing chart if it exists
        if (employeeChart instanceof Chart) {
            employeeChart.destroy();
        }

        const ctx = document.getElementById('employeeChart').getContext('2d');

        // Create a new chart with dynamic data
        employeeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ── Department change: now handled by loadEmployees() above ──

    function renderSummary(summary) {
        let container = $('#departmentSummary');
        container.empty();

        // Add total employees card first
        container.append(`
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-title">Total Employees</div>
                    <div class="card-text">${summary.total_employees}</div>
                </div>
            </div>
        `);

        // Add active employees card
        if (summary.active_employees > 0) {
            container.append(`
                <div class="card mb-3" style="background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%); color: white;">
                    <div class="card-body">
                        <div class="card-title">Active Employees</div>
                        <div class="card-text">${summary.active_employees}</div>
                    </div>
                </div>
            `);
        }

        // Add deleted employees card
        if (summary.deleted_employees > 0) {
            container.append(`
                <div class="card mb-3" style="background: linear-gradient(135deg, #f44336 0%, #ef5350 100%); color: white;">
                    <div class="card-body">
                        <div class="card-title">Deleted Employees</div>
                        <div class="card-text">${summary.deleted_employees}</div>
                    </div>
                </div>
            `);
        }

        // Add department-wise cards
        summary.department_wise.forEach(dep => {
            let cardHtml = `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-title">${dep.total} - ${dep.department_name}</div>
                        <div class="card-text">
            `;

            if (dep.active > 0) {
                cardHtml += `<span style="color: #4caf50;">Active: ${dep.active}</span>`;
            }

            if (dep.active > 0 && dep.deleted > 0) {
                cardHtml += ` | `;
            }

            if (dep.deleted > 0) {
                cardHtml += `<span style="color: #f44336;">Deleted: ${dep.deleted}</span>`;
            }

            cardHtml += `
                        </div>
                    </div>
                </div>
            `;

            container.append(cardHtml);
        });
    }

    function toggleFilters() {
        const el = document.getElementById('filters-row');
        if (el) el.classList.toggle('d-none');
    }

    // Load data on page load
    $(document).ready(function() {
        fetchSalaryReport();
    });

</script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Initialize DataTable when table is shown
    function initializeDataTable() {
        try {
            if ($.fn.DataTable.isDataTable('#employeeTable')) {
                // if we have stored instance, use it to clear and destroy
                if (employeeDataTable && typeof employeeDataTable.clear === 'function') {
                    employeeDataTable.clear().destroy();
                } else {
                    $('#employeeTable').DataTable().clear().destroy();
                }
                employeeDataTable = null;
            }
        } catch (e) {
            console.warn('Error destroying existing DataTable instance', e);
        }

        // Do not recreate here; populateTable will initialize when needed.
        employeeDataTable = null;
    }
</script>

<?= $this->endSection() ?>
