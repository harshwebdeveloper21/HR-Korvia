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
            <h4 class="card-title fw-bolder mb-0">Payroll Report</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn hr-btnbg btnpdingam export-page-btn" data-table="#payrollTable" data-filename="Payroll_Salary_Report" style="white-space: nowrap;">
                <i class="mdi mdi-file-excel iconfontsize"></i> Export
            </button>
            <button class="btn hr-btnbg btnpdingam" style="white-space: nowrap;" onclick="fetchPayrollReport()">Generate Report</button>
        </div>
    </div>

    <!-- Filters -->
    <div id="filters-row" class="row g-3">
        <!-- Department -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="department_id" class="form-label">Department:</label>
            <select id="department_id" name="department_id" class="form-select" onchange="loadEmployees(this.value)">
                <option value="">All Departments</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department["id"] ?>"><?= $department["department_name"] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Employee -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="user_id" class="form-label">Employee:</label>
            <select id="user_id" name="user_id" class="form-select" onchange="fetchPayrollReport()">
                <option value="">All Employees</option>
            </select>
        </div>

        <!-- Year -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label">Year:</label>
            <select id="year" name="year" class="form-select" onchange="fetchPayrollReport()">
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
            <select id="month" name="month" class="form-select" onchange="fetchPayrollReport()">
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

        <!-- From Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="start_date" class="form-label">From Date:</label>
            <input type="date" id="start_date" class="form-control" placeholder="Select Start Date" onchange="fetchPayrollReport()">
        </div>

        <!-- To Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="end_date" class="form-label">To Date:</label>
            <input type="date" id="end_date" class="form-control" placeholder="Select End Date" onchange="fetchPayrollReport()">
        </div>
    </div>

    <!-- Chart -->
    <div class="row mt-md-5 mt-3">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="chart-container">
                <canvas id="salaryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Salary Table -->
    <div class="table-container mt-5" id="table-section" style="display: none;">
        <h5 class="fw-semibold mb-3">Salary Report</h5>
        <div class="table-responsive">
            <table id="payrollTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Payment Date</th>
                        <th>Base Salary</th>
                        <th>Bonuses</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody id="salary-table-body">
                    <!-- Dynamic rows go here -->
                </tbody>
            </table>
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
            data: { ...getCSRFData(), department_id: departmentId },
            success: function(response) {
                refreshCSRF(response);
                const list = response.employees || [];
                list.forEach(emp => {
                    const name = (emp.firstname || '') + ' ' + (emp.lastname || '');
                    sel.innerHTML += `<option value="${emp.id}">${name.trim()}</option>`;
                });
                fetchPayrollReport();
            },
            error: function (xhr) { 
                console.error('loadEmployees error:', xhr.status, xhr.responseText); 
                fetchPayrollReport();
            }
        });
    }

    let salaryChartInstance = null;

    function fetchPayrollReport() {
        const departmentId = document.getElementById("department_id").value;
        const employeeId = document.getElementById('user_id').value;
        const year = document.getElementById('year').value;
        const month = document.getElementById('month').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        clearValidationMessages();

        $.ajax({
            url: "<?= site_url("report/fetchPayrollReport") ?>",
            type: "POST",
            dataType: "json",
            data: {
                ...getCSRFData(),
                department_id: departmentId,
                employee_id: employeeId,
                year: year,
                month: month,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                refreshCSRF(response);
                const tableData = (response && Array.isArray(response.tableData)) ? response.tableData : [];
                populateTable(tableData);
                document.getElementById("table-section").style.display = "block";
                
                if (tableData.length > 0) {
                    updateChart(tableData);
                } else {
                    if (salaryChartInstance instanceof Chart) {
                        salaryChartInstance.destroy();
                        salaryChartInstance = null;
                    }
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // Alias so any other code calling fetchSalaryReport works seamlessly
    window.fetchSalaryReport = fetchPayrollReport;

    function populateTable(data) {
        // Destroy existing DataTable first
        if ($.fn.DataTable.isDataTable('#payrollTable')) {
            $('#payrollTable').DataTable().clear().destroy();
        }

        const tableBody = document.getElementById('salary-table-body');
        tableBody.innerHTML = "";

        if (!data.length) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No data available</td></tr>';
            return;
        }

        let rowsHtml = '';
        data.forEach(row => {
            const fullName = ((row.firstname || '') + ' ' + (row.lastname || '')).trim() || 'N/A';
            const baseSalary = parseFloat(row.salary_amount) || 0;
            const bonuses = parseFloat(row.bonuses) || 0;
            const totalDeductions = (parseFloat(row.tax_deduction) || 0) + (parseFloat(row.salary_deduction) || 0);
            const netSalary = parseFloat(row.net_salary) || (baseSalary + bonuses - totalDeductions);

            rowsHtml += `
                <tr>
                    <td class="capitalize-text">${fullName}</td>
                    <td class="capitalize-text">${row.department_name || 'N/A'}</td>
                    <td class="capitalize-text">${row.payment_date || row.month_year || 'N/A'}</td>
                    <td>₹${baseSalary.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td>₹${bonuses.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td>₹${totalDeductions.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="fw-bold text-success">₹${netSalary.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                </tr>
            `;
        });
        tableBody.innerHTML = rowsHtml;

        // Initialize DataTable after populating the table
        initializePayrollDataTable();
    }

    function updateChart(data) {
        const ctx = document.getElementById('salaryChart').getContext('2d');
        if (salaryChartInstance instanceof Chart) {
            salaryChartInstance.destroy();
        }

        // Aggregate total base, bonuses, deductions across all filtered records
        let totalBase = 0;
        let totalBonuses = 0;
        let totalDeductions = 0;

        data.forEach(item => {
            totalBase += parseFloat(item.salary_amount) || 0;
            totalBonuses += parseFloat(item.bonuses) || 0;
            totalDeductions += (parseFloat(item.tax_deduction) || 0) + (parseFloat(item.salary_deduction) || 0);
        });

        const empSelect = document.getElementById('user_id');
        const selectedEmpName = (empSelect && empSelect.value) ? empSelect.options[empSelect.selectedIndex]?.text : '';
        const chartTitle = (selectedEmpName && selectedEmpName !== 'All Employees')
            ? `Salary Breakdown for ${selectedEmpName}`
            : (data.length === 1
                ? `Salary Breakdown for ${((data[0].firstname || '') + ' ' + (data[0].lastname || '')).trim()}`
                : `Total Salary Breakdown (${data.length} Records)`);

        salaryChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Base Salary', 'Bonuses', 'Deductions'],
                datasets: [{
                    label: 'Salary Breakdown',
                    data: [totalBase, totalBonuses, totalDeductions],
                    backgroundColor: ['#4caf50', '#ff9800', '#f44336']
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
                        text: chartTitle
                    }
                }
            }
        });
    }
</script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    function initializePayrollDataTable() {
        if ($.fn.DataTable.isDataTable('#payrollTable')) {
            $('#payrollTable').DataTable().destroy();
        }
        $('#payrollTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": false,
            "pageLength": 10,
            "language": {
                "search": "Search payroll:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records found",
                "zeroRecords": "No matching records found"
            }
        });
    }


    $(document).ready(function() {
        fetchPayrollReport();
    });
</script>
<?= $this->endSection() ?>