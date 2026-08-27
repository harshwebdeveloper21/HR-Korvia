<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<link rel="stylesheet" href="<?= base_url(env("ImagePath") . "assets/css/performancereport.css") ?>">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    .capitalize-text { text-transform: capitalize; }
    .reporstmagin    { margin-top: 47px !important; }

    /* Filter bar */
    .filter-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px 24px 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        margin-bottom: 22px;
    }

    /* PDF button pulse */
    #downloadPdfBtn {
        transition: transform .15s, box-shadow .15s;
    }
    #downloadPdfBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(230,97,54,.35);
    }
    #downloadPdfBtn:disabled { opacity: .65; cursor: not-allowed; }

    /* Rating stars display */
    .rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: linear-gradient(135deg,#f093fb,#f5576c);
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-weight: 600;
        font-size: 13px;
    }

    /* Table header dark */
    #performanceTable thead { background: #1a1a2e; color: #fff; }
    #performanceTable thead th { color: #fff !important; }



    @media (max-width: 767px) {
        .btnpdingam { padding: 5.3px !important; font-size: 12px !important; }
        .attendenceall { font-size: 12px !important; padding: 5px !important; margin-top: 5px !important; }
    }
</style>

<!-- ===== Header ===== -->
<div class="filter-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="card-title fw-bolder mb-0">Employee Performance Report</h4>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <button type="button" class="btn hr-btnbg btnpdingam export-page-btn" data-table="#performanceTable" data-filename="Performance_Report" style="white-space:nowrap;">
                <i class="mdi mdi-file-excel iconfontsize"></i> Export Excel
            </button>
            <button class="btn hr-btnbg btnpdingam" style="white-space:nowrap;" onclick="fetchPerformanceReport()">
                <i class="mdi mdi-chart-bar me-1"></i>Generate Report
            </button>
            <button class="btn hr-btnbg btnpdingam" id="downloadPdfBtn" style="white-space:nowrap; display:none;" onclick="downloadPDF()">
                <i class="mdi mdi-file-pdf-box me-1"></i>Download PDF
            </button>
        </div>
    </div>

    <!-- ===== Filters ===== -->
    <div class="row g-3">
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
            <select id="user_id" name="user_id" class="form-select" onchange="fetchPerformanceReport()">
                <option value="">All Employees</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee["id"] ?>"><?= esc(trim(($employee["firstname"] ?? '') . ' ' . ($employee["lastname"] ?? ''))) ?: 'Employee #' . $employee['id'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Month -->
        <div class="col-12 col-sm-6 col-md-2">
            <label for="filter_month" class="form-label">Month:</label>
            <select id="filter_month" class="form-select" onchange="fetchPerformanceReport()">
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

        <!-- Year -->
        <div class="col-12 col-sm-6 col-md-2">
            <label for="filter_year" class="form-label">Year:</label>
            <select id="filter_year" class="form-select" onchange="fetchPerformanceReport()">
                <option value="">All Years</option>
                <?php $cy = date('Y'); for ($i = $cy; $i >= $cy - 10; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $cy ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Start Date -->
        <div class="col-12 col-sm-6 col-md-2">
            <label for="start_date" class="form-label">From Date:</label>
            <input type="date" id="start_date" class="form-control" onchange="fetchPerformanceReport()">
        </div>

        <!-- End Date -->
        <div class="col-12 col-sm-6 col-md-2">
            <label for="end_date" class="form-label">To Date:</label>
            <input type="date" id="end_date" class="form-control" onchange="fetchPerformanceReport()">
        </div>
    </div>
</div>



<!-- ===== Chart ===== -->
<div class="chart-container mt-2 mb-4">
    <canvas id="performanceChart"></canvas>
</div>

<!-- ===== Table ===== -->
<div class="table-container mt-2" id="table-section" style="display:none;">
    <h5 class="fw-semibold mb-3">Employee Performance Data</h5>
    <div class="table-responsive">
        <table id="performanceTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Review Date</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody id="performance-table-body">
                <!-- Dynamic rows -->
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Scripts ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jsPDF + AutoTable for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<!-- html2canvas to capture chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
/* ============================================================
   Helpers
   ============================================================ */
function displayValidationMessage(inputId, message) {
    const el = document.getElementById(inputId);
    const div = document.createElement("div");
    div.className = "text-danger mt-1 validation-msg";
    div.innerText = message;
    el.parentNode.appendChild(div);
}
function clearValidationMessages() {
    document.querySelectorAll(".validation-msg").forEach(m => m.remove());
}

/* ============================================================
   CSRF token store (refreshed after every AJAX response)
   ============================================================ */
let csrfTokenName  = '<?= csrf_token() ?>';
let csrfTokenValue = '<?= csrf_hash() ?>';
function getCSRFData() { let d = {}; d[csrfTokenName] = csrfTokenValue; return d; }
function refreshCSRF(r) { if (r && r.csrfHash) csrfTokenValue = r.csrfHash; }

/* ============================================================
   Load employees when department changes
   ============================================================ */
function loadEmployees(departmentId) {
    const employeeSelect = document.getElementById('user_id');
    employeeSelect.innerHTML = '<option value="">All Employees</option>';
    
    $.ajax({
        url: '<?= site_url("report/fetchEmployeesByDepartment") ?>',
        type: 'POST',
        dataType: 'json',
        data: { ...getCSRFData(), department_id: departmentId },
        success: function(response) {
            refreshCSRF(response);
            employeeSelect.innerHTML = '<option value="">All Employees</option>';
            const list = response.employees || [];
            list.forEach(emp => {
                const name = (emp.firstname || '') + ' ' + (emp.lastname || '');
                employeeSelect.innerHTML += `<option value="${emp.id}">${name.trim()}</option>`;
            });
            fetchPerformanceReport();
        },
        error: function (xhr) {
            console.error("Error loading employees:", xhr.status, xhr.responseText);
            fetchPerformanceReport();
        }
    });
}

/* ============================================================
   Chart
   ============================================================ */
let performanceChartInstance = null;

function updateChart(data) {
    const ctx = document.getElementById("performanceChart").getContext("2d");
    if (!data || !data.length) {
        if (performanceChartInstance instanceof Chart) {
            performanceChartInstance.destroy();
            performanceChartInstance = null;
        }
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        return;
    }

    const labels  = data.map(i => ((i.firstname || '') + ' ' + (i.lastname || '')).trim() || 'N/A');
    const ratings = data.map(i => parseFloat(i.rating) || 0);

    // Gradient colours per bar
    const colours = [
        'rgba(102,126,234,.85)',
        'rgba(240,147,251,.85)',
        'rgba(67,233,123,.85)',
        'rgba(254,173,30,.85)',
        'rgba(240,87,108,.85)',
        'rgba(54,162,235,.85)',
        'rgba(255,159,64,.85)',
    ];
    const bg = ratings.map((_, i) => colours[i % colours.length]);

    if (performanceChartInstance instanceof Chart) performanceChartInstance.destroy();

    performanceChartInstance = new Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label: "Performance Rating",
                data: ratings,
                backgroundColor: bg,
                borderWidth: 0,
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: "top" },
                title: { display: true, text: `Employee Performance Ratings (${data.length} Records)`, font: { size: 15 } },
            },
            scales: {
                y: { beginAtZero: true, max: 5.5, title: { display: true, text: "Ratings (out of 5)" } },
                x: { title: { display: true, text: "Employees" } },
            },
        },
    });
}

/* ============================================================
   Table
   ============================================================ */
function populateTable(data) {
    if ($.fn.DataTable.isDataTable('#performanceTable')) {
        $('#performanceTable').DataTable().clear().destroy();
    }

    const tbody = document.getElementById("performance-table-body");
    tbody.innerHTML = "";

    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No data found for selected filters.</td></tr>';
        document.getElementById("table-section").style.display = "block";
        return;
    }

    let rowsHtml = '';
    data.forEach((row, idx) => {
        const fullName = ((row.firstname || '') + ' ' + (row.lastname || '')).trim() || 'N/A';
        const reviewDate = row.review_date
            ? new Date(row.review_date).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'})
            : 'N/A';
        const ratingVal = parseFloat(row.rating) || 0;
        const stars = '★'.repeat(Math.min(5, Math.max(0, Math.round(ratingVal)))) + '☆'.repeat(Math.max(0, 5 - Math.round(ratingVal)));
        rowsHtml += `
            <tr>
                <td>${idx + 1}</td>
                <td class="capitalize-text fw-semibold">${fullName}</td>
                <td class="capitalize-text">${row.department_name || 'N/A'}</td>
                <td>${reviewDate}</td>
                <td>
                    <span class="rating-badge">
                        ${ratingVal.toFixed(1)}
                        <span style="letter-spacing:1px;font-size:11px;">${stars}</span>
                    </span>
                </td>
            </tr>`;
    });
    tbody.innerHTML = rowsHtml;
    document.getElementById("table-section").style.display = "block";

    $('#performanceTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": false,
        "pageLength": 10,
        "language": {
            "search": "Search performance:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "No records found",
            "zeroRecords": "No matching records found"
        }
    });
}

/* ============================================================
   Fetch report
   ============================================================ */
let lastReportData = [];

function fetchPerformanceReport() {
    const departmentId = document.getElementById("department_id").value;
    const employeeId   = document.getElementById("user_id").value;
    const startDate    = document.getElementById("start_date").value;
    const endDate      = document.getElementById("end_date") ? document.getElementById("end_date").value : '';
    const month        = document.getElementById("filter_month").value;
    const year         = document.getElementById("filter_year").value;
    clearValidationMessages();

    $.ajax({
        url: "<?= site_url("report/fetchPerformanceReport") ?>",
        type: "POST",
        dataType: "json",
        data: {
            ...getCSRFData(),
            department_id: departmentId,
            employee_id:   employeeId,
            start_date:    startDate,
            end_date:      endDate,
            month:         month,
            year:          year,
        },
        success: function(response) {
            refreshCSRF(response);
            const data = (response.tableData && response.tableData.length) ? response.tableData : [];
            lastReportData = data;
            populateTable(data);
            updateChart(data);

            // Show/hide PDF button
            document.getElementById("downloadPdfBtn").style.display = data.length ? "inline-flex" : "none";
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch performance data.', confirmButtonColor: '#d33' });
        },
    });
}

/* ============================================================
   Filter helpers for PDF title
   ============================================================ */
function getMonthName(val) {
    const names = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    return names[parseInt(val)] || '';
}

/* ============================================================
   PDF Download
   ============================================================ */
async function downloadPDF() {
    if (!lastReportData.length) {
        Swal.fire({ icon: 'warning', title: 'No Data', text: 'Generate a report first before downloading.', confirmButtonColor: '#3085d6' });
        return;
    }

    const btn = document.getElementById("downloadPdfBtn");
    btn.disabled = true;
    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Preparing PDF…';

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        const pageW   = doc.internal.pageSize.getWidth();
        const pageH   = doc.internal.pageSize.getHeight();
        const margin  = 14;
        let   cursorY = margin;

        /* ---- Header band ---- */
        doc.setFillColor(26, 26, 46);           // dark navy
        doc.rect(0, 0, pageW, 32, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.setTextColor(255, 255, 255);
        doc.text('Employee Performance Report', margin, 14);

        // Sub-title line: filters used
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        const dept  = document.getElementById("department_id").options[document.getElementById("department_id").selectedIndex]?.text || '';
        const emp   = document.getElementById("user_id").options[document.getElementById("user_id").selectedIndex]?.text || '';
        const month = getMonthName(document.getElementById("filter_month").value);
        const year  = document.getElementById("filter_year").value;
        const sDate = document.getElementById("start_date").value;
        let subtitle = `Department: ${dept}  |  Employee: ${emp}`;
        if (month) subtitle += `  |  Month: ${month}`;
        if (year)  subtitle += `  |  Year: ${year}`;
        if (sDate) subtitle += `  |  From: ${sDate}`;
        doc.text(subtitle, margin, 22);

        // Generated at
        doc.setFontSize(7.5);
        const now = new Date().toLocaleString('en-IN');
        doc.text(`Generated: ${now}`, margin, 28);

        cursorY = 38;

        /* ---- Stats row ---- */
        const ratings = lastReportData.map(r => parseFloat(r.rating) || 0);
        const avg = (ratings.reduce((a, b) => a + b, 0) / ratings.length).toFixed(1);
        const stats = [
            { label: 'Total Records', value: lastReportData.length, color: [102,126,234] },
            { label: 'Avg Rating',    value: avg,                   color: [67,233,123] },
            { label: 'Highest',       value: Math.max(...ratings),  color: [254,173,30] },
            { label: 'Lowest',        value: Math.min(...ratings),  color: [240,87,108] },
        ];
        const cardW = (pageW - margin * 2 - 9) / 4;
        stats.forEach((s, i) => {
            const x = margin + i * (cardW + 3);
            doc.setFillColor(...s.color);
            doc.roundedRect(x, cursorY, cardW, 18, 3, 3, 'F');
            doc.setTextColor(255,255,255);
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.text(s.label, x + cardW/2, cursorY + 6, { align: 'center' });
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text(String(s.value), x + cardW/2, cursorY + 14, { align: 'center' });
        });
        cursorY += 24;

        /* ---- Chart image ---- */
        const chartCanvas = document.getElementById("performanceChart");
        if (chartCanvas && chartCanvas.width > 0) {
            const chartImg = await html2canvas(chartCanvas, { backgroundColor: '#ffffff', scale: 2 });
            const imgData  = chartImg.toDataURL('image/png');
            const chartH   = 70;
            const chartW   = pageW - margin * 2;
            doc.addImage(imgData, 'PNG', margin, cursorY, chartW, chartH);
            cursorY += chartH + 6;
        }

        /* ---- Table ---- */
        const tHeaders = [['#', 'Employee Name', 'Department', 'Review Date', 'Rating']];
        const tBody = lastReportData.map((row, idx) => {
            const reviewDate = row.review_date
                ? new Date(row.review_date).toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'})
                : 'N/A';
            return [idx + 1, row.firstname || 'N/A', row.department_name || 'N/A', reviewDate, row.rating + ' / 5'];
        });

        doc.autoTable({
            startY: cursorY,
            head: tHeaders,
            body: tBody,
            theme: 'grid',
            styles: { fontSize: 9, cellPadding: 4 },
            headStyles: { fillColor: [26, 26, 46], textColor: 255, fontStyle: 'bold', halign: 'center' },
            columnStyles: {
                0: { halign: 'center', cellWidth: 10 },
                4: { halign: 'center' },
            },
            alternateRowStyles: { fillColor: [245, 245, 250] },
            didDrawPage: (hookData) => {
                // Footer on each page
                const pgCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.setTextColor(150);
                doc.setFont('helvetica', 'normal');
                doc.text(
                    `Page ${hookData.pageNumber} of ${pgCount}  |  HR Portal - Employee Performance Report`,
                    pageW / 2, pageH - 6, { align: 'center' }
                );
            },
        });

        /* ---- Save ---- */
        const fileName = `Performance_Report_${emp.replace(/\s+/g,'_')}_${year || 'All'}_${month || 'All'}.pdf`;
        doc.save(fileName);

        Swal.fire({ icon: 'success', title: 'PDF Downloaded!', text: `Saved as: ${fileName}`, confirmButtonColor: '#3085d6', timer: 3000, showConfirmButton: false });
    } catch (err) {
        console.error('PDF error:', err);
        Swal.fire({ icon: 'error', title: 'PDF Failed', text: 'Could not generate PDF. Please try again.', confirmButtonColor: '#d33' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-file-pdf-box me-1"></i>Download PDF';
    }
}

$(document).ready(function() {
    fetchPerformanceReport();
});
</script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<?= $this->endSection() ?>
