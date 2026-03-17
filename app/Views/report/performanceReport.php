<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<!-- <link rel="stylesheet" href="assets/css/performancereport.css"> -->
  <link rel="stylesheet" href="<?= base_url(
      env("ImagePath") . "assets/css/performancereport.css",
  ) ?>">
<style>
    .capitalize-text {
        text-transform: capitalize;
    }
    .reporstmagin{
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
            margin-left: 2rem !important;
            /* margin: 7px !important; */
        }
    }
     @media (max-width: 767px) {
             .attendenceall {
            font-size: 12px !important;
            padding: 5px !important;
            margin-top: 5px !important;
            /* margin-bottom: 5px !important; */
        }
    }
</style>
<div class="filter-section">
    <!-- Header -->

 <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="header-controls">
        <h4 class="card-title fw-bolder mb-0">Employee Performance Report</h4>
    </div>
    <button class="btn hr-btnbg btnpdingam" style="white-space: nowrap;" onclick="fetchPerformanceReport()">Generate Report</button>
</div>
    <!-- Filters -->
    <div class="row g-3">
        <!-- Department -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="department_id" class="form-label">Department:</label>
            <select id="department_id" name="department_id" class="form-select">
                <option value="" disabled selected>Select Department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department["id"] ?>"><?= $department[
    "department_name"
] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Employee -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="user_id" class="form-label">Employee:</label>
            <select id="user_id" name="user_id" class="form-select">
                <option value="" disabled selected>Select Employee</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee["id"] ?>"><?= esc(
    $employee["username"],
) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Start Date -->
        <div class="col-12 col-sm-6 col-md-3">
            <label for="start_date" class="form-label">Start Date:</label>
            <input type="date" id="start_date" class="form-control">
        </div>

        <!-- Button -->

    </div>

    <!-- Chart -->
    <div class="chart-container mt-5">
        <canvas id="performanceChart"></canvas>
    </div>

    <!-- Table -->
    <div class="table-container mt-4" id="table-section" style="display: none;">
        <h5 class="fw-semibold mb-3">Employee Performance Data</h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody id="performance-table-body">
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
        inputElement.parentNode.appendChild(errorElement);
    }

    function clearValidationMessages() {
        document.querySelectorAll(".text-danger").forEach(message => message.remove());
    }

    let performanceChartInstance = null;

    function fetchPerformanceReport() {
        const departmentId = document.getElementById("department_id").value;
        const employeeId = document.getElementById("user_id").value;
        const startDate = document.getElementById("start_date").value;
        clearValidationMessages();

        let isValid = true;
        if (!departmentId) {
            displayValidationMessage("department_id", "Please select a department.");
            isValid = false;
        }
        if (!employeeId) {
    displayValidationMessage("user_id", "Please select an employee.");
    isValid = false;
}

        if (!isValid) return;

        $.ajax({
            url: "<?= site_url("report/fetchPerformanceReport") ?>",
            type: "POST",
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>', // Add CSRF token here
                department_id: departmentId, employee_id: employeeId, start_date: startDate },
            success: function(response) {
                if (response.tableData && response.tableData.length) {
                    populateTable(response.tableData);
                    document.getElementById("table-section").style.display = "block";
                    updateChart(response.tableData);
                } else {
                    populateTable([]);
                    updateChart([]);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            },
        });
    }

    function populateTable(data) {
        const tableBody = document.getElementById("performance-table-body");

        tableBody.innerHTML = "";

        if (!data.length) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No data available</td></tr>';
            document.getElementById("table-section").style.display = "block";
            return;
        }

        data.forEach((row) => {
            tableBody.innerHTML += `
                <tr>
                    <td class="capitalize-text">${row.firstname}</td>
                    <td class="capitalize-text">${row.department_name}</td>
                    <td class="capitalize-text">${row.rating}</td>
                </tr>`;
        });
        document.getElementById("table-section").style.display = "block";
    }

    function updateChart(data) {
        const ctx = document.getElementById("performanceChart").getContext("2d");

        // If no data is available, destroy the chart and clear canvas
        if (!data || !data.length) {
            if (performanceChartInstance instanceof Chart) {
                performanceChartInstance.destroy();
            }
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            return;
        }

        const labels = data.map(item => item.firstname);
        const ratings = data.map(item => item.rating);

        if (performanceChartInstance instanceof Chart) {
            performanceChartInstance.destroy();
        }

        performanceChartInstance = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Performance Rating",
                    data: ratings,
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: "top" },
                    title: { display: true, text: "Employee Performance Ratings" },
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: "Ratings" } },
                    x: { title: { display: true, text: "Employees" } },
                },
            },
        });
    }
</script>

<?= $this->endSection() ?>
