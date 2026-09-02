<?= $this->extend("layout") ?>
<?= $this->section("content") ?>

<link rel="stylesheet" href="<?= base_url(env("ImagePath") . "assets/css/attendancereport.css?v=" . time()) ?>">

<div class="filter-section">
    <!-- Header Controls -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="card-title fw-bolder mb-1">Attendance Report</h4>
            <p class="text-muted mb-0 font-13" id="currentReportLabel">Monthly overview and employee daily punch matrix</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn hr-btnbg btnpdingam" onclick="exportTabularReportExcel();" style="white-space: nowrap;">
                <i class="mdi mdi-file-excel iconfontsize"></i> Export Tabular Report
            </button>
            <button type="button" class="btn hr-btnbg btnpdingam" onclick="fetchAttenReport();" style="white-space: nowrap;">
                <i class="mdi mdi-refresh me-1"></i> Generate Report
            </button>
        </div>
    </div>


    <!-- Filter Row -->
    <div id="filters-row" class="row g-3 mb-4">
        <!-- Department Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="attendance-filter-label">Department:</label>
            <select id="department_id" name="department_id" class="form-select font-13" onchange="loadEmployees(this.value);">
                <option value="">All Departments</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department["id"] ?>"><?= esc($department["department_name"]) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Employee Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="attendance-filter-label">Employee:</label>
            <select id="user_id" name="user_id" class="form-select font-13" onchange="fetchAttenReport();">
                <option value="">All Employees</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee["id"] ?>">
                        <?= esc(trim(($employee["firstname"] ?? '') . ' ' . ($employee["lastname"] ?? ''))) ?: 'Employee #' . $employee['id'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Year Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="attendance-filter-label">Year:</label>
            <select id="year" name="year" class="form-select font-13" onchange="fetchAttenReport();">
                <?php
                $curYear = date('Y');
                for ($i = $curYear; $i >= $curYear - 5; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $curYear ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Month Filter -->
        <div class="col-12 col-sm-6 col-md-3">
            <label class="attendance-filter-label">Month:</label>
            <select id="month" name="month" class="form-select font-13" onchange="fetchAttenReport();">
                <?php
                $curMonth = date('n');
                $monthNames = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                foreach ($monthNames as $num => $mName): ?>
                    <option value="<?= $num ?>" <?= $num == $curMonth ? 'selected' : '' ?>><?= $mName ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs attendance-nav-tabs" id="attendanceReportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tabular-tab" data-bs-toggle="tab" data-bs-target="#tabular-pane" type="button" role="tab" aria-controls="tabular-pane" aria-selected="true">
                <i class="mdi mdi-table-large fs-5"></i> Tabular Report
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-pane" type="button" role="tab" aria-controls="calendar-pane" aria-selected="false">
                <i class="mdi mdi-calendar-month fs-5"></i> Calendar View
            </button>
        </li>
    </ul>

    <!-- Tab Content Panes -->
    <div class="tab-content pt-2" id="attendanceReportTabContent">
        
        <!-- ══════════════════════════════════════════════════════
             TAB 1: TABULAR MATRIX REPORT
             ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade show active" id="tabular-pane" role="tabpanel" aria-labelledby="tabular-tab">
            <div class="matrix-table-wrapper">
                <table class="table table-bordered attendance-matrix-table" id="matrixTable">
                    <thead id="matrixHead">
                        <!-- Dynamic Month Headers populated via JS -->
                    </thead>
                    <tbody id="matrixBody">
                        <tr>
                            <td colspan="36" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2"></span> Loading attendance matrix...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Matrix Status Legend Box -->
            <div class="attendance-legend-card mt-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="legend-title"><i class="mdi mdi-information-outline me-1"></i> STATUS:</span>
                    <div class="legend-item"><span class="att-badge att-p">P</span> <span>=&gt; Present</span></div>
                    <div class="legend-item"><span class="att-badge att-a">A</span> <span>=&gt; Absent</span></div>
                    <div class="legend-item"><span class="att-badge att-l">L</span> <span>=&gt; Leave</span></div>
                    <div class="legend-item"><span class="att-badge att-hd">HD</span> <span>=&gt; Half Day</span></div>
                    <div class="legend-item"><span class="att-badge att-h">H</span> <span>=&gt; Holiday</span></div>
                    <div class="legend-item"><span class="att-badge att-wo">WO</span> <span>=&gt; Week Off</span></div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════
             TAB 2: CALENDAR VIEW
             ══════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="calendar-pane" role="tabpanel" aria-labelledby="calendar-tab">
            <div id="attendanceCalendar"></div>
        </div>

    </div>

    <!-- Attendance Trends Chart -->
    <div class="attendance-chart-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-chart-timeline-variant text-primary me-1"></i> Daily Attendance Trend
            </h6>
        </div>
        <div style="height: 260px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

</div>

<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let AttendanceChartInstance = null;
    let attendanceCalendarInstance = null;
    let currentMatrixData = [];
    let currentMonthDetails = {};
    let latestCalendarEvents = [];

    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfTokenValue = '<?= csrf_hash() ?>';

    function getCSRFData() {
        let data = {};
        data[csrfTokenName] = csrfTokenValue;
        return data;
    }

    function refreshCSRF(response) {
        if (response && response.csrfHash) {
            csrfTokenValue = response.csrfHash;
        }
    }


    function loadEmployees(departmentId) {
        const employeeSelect = document.getElementById('user_id');
        employeeSelect.innerHTML = '<option value="">All Employees</option>';

        $.ajax({
            url: "<?= site_url("report/fetchEmployeesByDepartment") ?>",
            type: "POST",
            dataType: "json",
            data: {
                ...getCSRFData(),
                department_id: departmentId
            },
            success: function (response) {
                refreshCSRF(response);
                employeeSelect.innerHTML = '<option value="">All Employees</option>';
                const list = response.employees || response;
                if (Array.isArray(list)) {
                    list.forEach(emp => {
                        const name = ((emp.firstname || '') + ' ' + (emp.lastname || '')).trim() || ('Employee #' + emp.id);
                        employeeSelect.innerHTML += `<option value="${emp.id}">${name}</option>`;
                    });
                }
                fetchAttenReport();
            },
            error: function (xhr) {
                console.error("Error loading employees:", xhr.status, xhr.responseText);
                fetchAttenReport();
            }
        });
    }

    function fetchAttenReport() {
        const departmentId = document.getElementById("department_id").value;
        const employeeId = document.getElementById('user_id').value;
        const year = document.getElementById('year').value;
        const month = document.getElementById('month').value;

        $('#matrixBody').html('<tr><td colspan="40" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading attendance data...</td></tr>');

        $.ajax({
            url: "<?= site_url("report/fetchAttendanceReport") ?>",
            type: "POST",
            dataType: "json",
            data: {
                ...getCSRFData(),
                department_id: departmentId,
                employee_id: employeeId,
                year: year,
                month: month
            },
            success: function (response) {
                refreshCSRF(response);

                if (response && response.status === 'success') {
                    currentMatrixData = response.matrixData || [];
                    currentMonthDetails = response.monthDetails || {};
                    latestCalendarEvents = response.calendarEvents || [];

                    // Update Title Label
                    if (currentMonthDetails.monthName) {
                        $('#currentReportLabel').text(`Monthly overview for ${currentMonthDetails.monthName}`);
                    }

                    // 1. Render Tabular Matrix
                    renderMatrixTable(currentMatrixData, currentMonthDetails);

                    // 2. Render Calendar (if calendar tab is active or update data)
                    if ($('#calendar-pane').is(':visible') || $('#calendar-tab').hasClass('active')) {
                        initOrUpdateCalendar();
                    } else if (attendanceCalendarInstance) {
                        const year = currentMonthDetails.year || new Date().getFullYear();
                        const month = String(currentMonthDetails.month || (new Date().getMonth() + 1)).padStart(2, '0');
                        attendanceCalendarInstance.removeAllEvents();
                        attendanceCalendarInstance.addEventSource(latestCalendarEvents);
                        attendanceCalendarInstance.gotoDate(`${year}-${month}-01`);
                    }

                    // 3. Update Trend Chart
                    if (response.chartData) {
                        updateChart(response.chartData);
                    }
                } else {
                    $('#matrixBody').html('<tr><td colspan="40" class="text-center py-4 text-danger">No attendance data available for the selected period.</td></tr>');
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.status, xhr.responseText);
                $('#matrixBody').html('<tr><td colspan="40" class="text-center py-4 text-danger">Error loading report. Please try again.</td></tr>');
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // Render Tab 1: Tabular Matrix
    // ══════════════════════════════════════════════════════
    function renderMatrixTable(matrixData, monthDetails) {
        const totalDays = monthDetails.totalDays || 31;
        const year = monthDetails.year || new Date().getFullYear();
        const month = monthDetails.month || (new Date().getMonth() + 1);

        // Build Header
        let headHtml = '<tr>';
        headHtml += '<th class="sticky-col text-start">Employee</th>';

        for (let d = 1; d <= totalDays; d++) {
            const curDate = new Date(year, month - 1, d);
            const isSun = curDate.getDay() === 0;
            const dayName = curDate.toLocaleDateString('en-US', { weekday: 'narrow' });
            headHtml += `
                <th class="day-header ${isSun ? 'is-sunday' : ''}" title="${curDate.toDateString()}">
                    <span class="day-num">${d}</span>
                    <span class="day-name">${dayName}</span>
                </th>
            `;
        }

        headHtml += `
            <th class="summary-col bg-light text-success" title="Total Present">P</th>
            <th class="summary-col bg-light text-danger" title="Total Absent">A</th>
            <th class="summary-col bg-light text-warning" title="Total Leave">L</th>
            <th class="summary-col bg-light text-info" title="Total Half Day">HD</th>
        </tr>`;

        $('#matrixHead').html(headHtml);

        // Build Body
        if (!matrixData || !matrixData.length) {
            $('#matrixBody').html(`<tr><td colspan="${totalDays + 5}" class="text-center py-4 text-muted">No available data found</td></tr>`);
            return;
        }

        let bodyHtml = '';
        matrixData.forEach(emp => {
            const fullName = ((emp.firstname || '') + ' ' + (emp.lastname || '')).trim() || ('Employee #' + emp.user_id);
            const dept = emp.department_name ? `<small class="text-muted d-block font-11">${emp.department_name}</small>` : '';

            bodyHtml += '<tr>';
            bodyHtml += `
                <td class="sticky-col">
                    <div class="fw-semibold text-dark font-13">${fullName}</div>
                    ${dept}
                </td>
            `;

            for (let d = 1; d <= totalDays; d++) {
                const dData = emp.days && emp.days[d] ? emp.days[d] : { code: '-', tooltip: '', statusType: 'empty' };
                const badgeClass = 'att-' + (dData.statusType === 'present' ? 'p' :
                                    dData.statusType === 'absent' ? 'a' :
                                    dData.statusType === 'leave' ? 'l' :
                                    dData.statusType === 'halfday' ? 'hd' :
                                    dData.statusType === 'holiday' ? 'h' :
                                    dData.statusType === 'weekoff' ? 'wo' : 'empty');

                bodyHtml += `
                    <td>
                        <span class="att-badge ${badgeClass}" data-bs-toggle="tooltip" data-bs-placement="top" title="${dData.tooltip || dData.code}">
                            ${dData.code}
                        </span>
                    </td>
                `;
            }

            // Summary Counts
            const sum = emp.summary || { present: 0, absent: 0, leave: 0, half_day: 0 };
            bodyHtml += `
                <td class="summary-col"><span class="summary-val text-success bg-success-subtle">${sum.present}</span></td>
                <td class="summary-col"><span class="summary-val text-danger bg-danger-subtle">${sum.absent}</span></td>
                <td class="summary-col"><span class="summary-val text-warning bg-warning-subtle">${sum.leave}</span></td>
                <td class="summary-col"><span class="summary-val text-info bg-info-subtle">${sum.half_day}</span></td>
            </tr>`;
        });

        $('#matrixBody').html(bodyHtml);

        // Re-initialize Bootstrap Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ══════════════════════════════════════════════════════
    // Render Tab 2: FullCalendar View (Robust Lazy/Active Init)
    // ══════════════════════════════════════════════════════
    function initOrUpdateCalendar() {
        const calendarEl = document.getElementById('attendanceCalendar');
        if (!calendarEl) return;

        const year = currentMonthDetails.year || new Date().getFullYear();
        const month = String(currentMonthDetails.month || (new Date().getMonth() + 1)).padStart(2, '0');
        const initialDate = `${year}-${month}-01`;

        if (attendanceCalendarInstance) {
            attendanceCalendarInstance.removeAllEvents();
            attendanceCalendarInstance.addEventSource(latestCalendarEvents || []);
            attendanceCalendarInstance.gotoDate(initialDate);
            setTimeout(() => {
                if (attendanceCalendarInstance) {
                    attendanceCalendarInstance.render();
                    attendanceCalendarInstance.updateSize();
                }
            }, 50);
            return;
        }

        if (typeof FullCalendar === 'undefined') {
            console.warn("FullCalendar library is not available");
            return;
        }

        attendanceCalendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: initialDate,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            height: 'auto',
            contentHeight: 650,
            aspectRatio: 1.5,
            expandRows: true,
            windowResizeDelay: 50,
            editable: false,
            dayMaxEvents: 3,
            events: latestCalendarEvents || [],
            eventClick: function(info) {
                if (info.event.title) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: info.event.title,
                            html: `<strong>Date:</strong> ${info.event.startStr}<br>${info.event.extendedProps.description || ''}`,
                            icon: 'info',
                            confirmButtonText: 'Close',
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    } else {
                        alert(info.event.title + "\nDate: " + info.event.startStr);
                    }
                }
            }
        });

        attendanceCalendarInstance.render();

        setTimeout(() => {
            if (attendanceCalendarInstance) {
                attendanceCalendarInstance.render();
                attendanceCalendarInstance.updateSize();
            }
        }, 80);
    }

    // Bind tab activation listeners for Calendar Tab
    document.addEventListener('DOMContentLoaded', function () {
        const calTabBtn = document.getElementById('calendar-tab');
        if (calTabBtn) {
            calTabBtn.addEventListener('shown.bs.tab', function () {
                setTimeout(() => {
                    initOrUpdateCalendar();
                }, 50);
            });
        }
    });

    $(document).on('shown.bs.tab', '#calendar-tab, button[data-bs-target="#calendar-pane"]', function () {
        setTimeout(() => {
            initOrUpdateCalendar();
        }, 50);
    });

    // ══════════════════════════════════════════════════════
    // Attendance Trend Line Chart
    // ══════════════════════════════════════════════════════
    function updateChart(data) {
        const canvas = document.getElementById('attendanceChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (AttendanceChartInstance instanceof Chart) {
            AttendanceChartInstance.destroy();
            AttendanceChartInstance = null;
        }

        if (!data || !data.length) return;

        const labels = data.map(e => e.date);
        const counts = data.map(e => e.attendance_count);

        AttendanceChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Present Employees',
                    data: counts,
                    fill: true,
                    backgroundColor: 'rgba(230, 97, 54, 0.12)',
                    borderColor: '#E66136',
                    borderWidth: 2,
                    pointBackgroundColor: '#E66136',
                    pointRadius: 3,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // Export Tabular Matrix Report to Excel
    // ══════════════════════════════════════════════════════
    function exportTabularReportExcel() {
        if (typeof XLSX === 'undefined') {
            alert('Export library is loading. Please try again.');
            return;
        }

        if (!currentMatrixData || currentMatrixData.length === 0) {
            alert('No attendance data available to export.');
            return;
        }

        const year = $('#year').val() || new Date().getFullYear();
        const month = $('#month').val() || (new Date().getMonth() + 1);
        const dateStr = new Date().toISOString().slice(0, 10);
        const totalDays = currentMonthDetails.totalDays || 31;

        // Build Excel Table Headers
        const headerRow = ['Employee Name', 'Department'];
        for (let d = 1; d <= totalDays; d++) {
            headerRow.push('Day ' + d);
        }
        headerRow.push('Total Present', 'Total Absent', 'Total Leave', 'Total Half Day');

        const rows = [headerRow];

        // Build Data Rows
        currentMatrixData.forEach(emp => {
            const name = ((emp.firstname || '') + ' ' + (emp.lastname || '')).trim() || ('Employee #' + emp.user_id);
            const dept = emp.department_name || 'General';
            const row = [name, dept];

            for (let d = 1; d <= totalDays; d++) {
                const dayObj = emp.days && emp.days[d] ? emp.days[d] : { code: '-' };
                row.push(dayObj.code || '-');
            }

            const sum = emp.summary || { present: 0, absent: 0, leave: 0, half_day: 0 };
            row.push(sum.present, sum.absent, sum.leave, sum.half_day);
            rows.push(row);
        });

        const ws = XLSX.utils.aoa_to_sheet(rows);

        // Set column widths for nice readability
        const colWidths = [{ wch: 24 }, { wch: 18 }];
        for (let d = 1; d <= totalDays; d++) {
            colWidths.push({ wch: 6 });
        }
        colWidths.push({ wch: 14 }, { wch: 14 }, { wch: 14 }, { wch: 16 });
        ws['!cols'] = colWidths;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Attendance_Matrix");
        XLSX.writeFile(wb, `Attendance_Tabular_Report_${year}_Month_${month}_${dateStr}.xlsx`);
    }

    $(document).ready(function () {
        fetchAttenReport();
    });
</script>

<?= $this->endSection() ?>