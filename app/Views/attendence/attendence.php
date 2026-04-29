<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .form-control {
        height: 2.3rem;
    }
    .employee-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .employee-row:hover {
        background-color: #f8f9fa;
    }

    .employee-row.selected {
        background-color: #e3f2fd;
    }

    .history-row {
        display: none;
        background-color: #f8f9fa;
    }

    .history-row.show {
        display: table-row;
    }

    .history-content {
        padding: 20px;
    }

    .history-table {
        width: 100%;
        margin-top: 10px;
    }

    .history-table th {
        background-color: #e9ecef;
        padding: 8px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
    }

    .history-table td {
        padding: 8px;
        border-bottom: 1px solid #dee2e6;
        font-size: 13px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-present {
        background-color: #d4edda;
        color: #155724;
    }

    .status-absent {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-half-day {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-leave {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .loading-spinner {
        text-align: center;
        padding: 20px;
    }

    /* Mobile Expand Styles */
    .desktop-only-col {
        display: table-cell;
    }

    .mobile-expand-col {
        display: none;
    }

    .expanded-details {
        display: none;
        margin-top: 12px;
        padding: 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #007bff;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
        font-size: 13px;
    }

    .detail-value {
        color: #212529;
        font-size: 13px;
        text-align: right;
    }

    .detail-actions {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid #dee2e6;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .detail-actions .btn {
        flex: 1;
        min-width: 80px;
    }

    .expand-toggle i {
        transition: transform 0.3s ease;
    }

    .expand-toggle.active i {
        transform: rotate(180deg);
    }

    @media (max-width: 767px) {
        .desktop-only-col {
            display: none !important;
        }

        .mobile-expand-col {
            display: table-cell !important;
        }

        .expanded-details.show {
            display: block;
        }

        .attendenceall {
            font-size: 8px !important;
            padding: 12px !important;
        }

        .btnpdingam {
            margin: 0px !important;
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .history-table {
            font-size: 11px;
        }

        .history-table th,
        .history-table td {
            padding: 6px 4px;
        }

        /* Prevent row click on mobile */
        .employee-row {
            cursor: default;
        }
    }
</style>

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="d-md-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title">Employee Attendance Summary</h4>
                <div class="d-md-flex gap-2">
                    <select id="month-selector" class="form-select">
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
                    <select id="year-selector" class="form-select">
                        <!-- Will be populated by JS -->
                    </select>
                    <a href="/view" class="btn hr-btnbg btnpdingam text-nowrap">
                        All Attendance
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="employee-attendance-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th class="desktop-only-col">Total Days</th>
                            <th class="desktop-only-col">Present Days</th>
                            <th class="desktop-only-col">Work Hours</th>
                            <th class="desktop-only-col">Overtime</th>
                            <th class="desktop-only-col">Late Hours</th>
                            <th class="desktop-only-col">Leaves</th>
                            <th class="desktop-only-col">Absent</th>
                            <th class="desktop-only-col">Action</th>
                            <th class="mobile-expand-col" style="width: 50px;">Details</th>
                        </tr>
                    </thead>
                    <tbody id="employee-attendance-body">
                        <tr>
                            <td colspan="10" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        };

        const defaultImagePath = "<?= base_url(env("ImagePath") . "upload/default-profile.jpg") ?>";

        // Initialize month and year selectors
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        document.getElementById('month-selector').value = currentMonth;

        // Populate year selector (last 5 years)
        const yearSelector = document.getElementById('year-selector');
        for (let i = 0; i < 5; i++) {
            const year = currentYear - i;
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === currentYear) option.selected = true;
            yearSelector.appendChild(option);
        }

        // Load attendance data
        const loadAttendanceData = () => {
            const month = document.getElementById('month-selector').value;
            const year = document.getElementById('year-selector').value;

            fetch(`/api/attendance/getAttendance/${month}/${year}`, { headers })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderEmployeeTable(data.data, month, year);
                    }
                })
                .catch(error => {
                    console.error('Error loading attendance:', error);
                    document.getElementById('employee-attendance-body').innerHTML =
                        '<tr><td colspan="10" class="text-center text-danger">Error loading data</td></tr>';
                });
        };

        const renderEmployeeTable = (attendanceData, month, year) => {
            const tbody = document.getElementById('employee-attendance-body');
            tbody.innerHTML = '';

            const users = attendanceData.users;
            const holidays = attendanceData.holidays || [];
            const saturdayOffDates = attendanceData.saturdayOffDates || [];
            console.log(saturdayOffDates);
            // Calculate total working days (excluding future dates for current month/year)
            const totalDaysInMonth = new Date(year, month, 0).getDate();
            let workingDays = 0;
            const todayStr = new Date().toISOString().split('T')[0];
            const now = new Date();
            const currentMonthNow = now.getMonth() + 1;
            const currentYearNow = now.getFullYear();
            const isCurrentMonthYear = parseInt(month) === currentMonthNow && parseInt(year) === currentYearNow;

            for (let day = 1; day <= totalDaysInMonth; day++) {
                const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                // For the current month/year, do not count future dates as working days
                if (isCurrentMonthYear && date > todayStr) {
                    continue;
                }
                const dayOfWeek = new Date(date).getDay();
                const isHoliday = holidays.some(h => h.holiday_date === date);
                const isSaturdayOff = saturdayOffDates.includes(date);
                if(attendanceData.isIncludedHoliday == "1"){
                    workingDays++;
                }else if (dayOfWeek !== 0 && !isHoliday && !isSaturdayOff) {
                    workingDays++;
                }
            }

            users.forEach(user => {
                const attendance = user.attendance || [];

                // For the current month/year, ignore future dates in stats so they are not counted as absent
                const effectiveAttendance = isCurrentMonthYear
                    ? attendance.filter(a => {
                        const d = (a.date || '').substring(0, 10);
                        return d && d <= todayStr;
                    })
                    : attendance;

                // Calculate stats based on effective attendance only
                // Group records by date to handle multiple check-ins on the same day
                const recordsByDate = {};
                effectiveAttendance.forEach(a => {
                    const date = a.date.substring(0, 10);
                    if (!recordsByDate[date]) {
                        recordsByDate[date] = [];
                    }
                    recordsByDate[date].push(a);
                });

                // Determine final status for each date based on API response
                let presentDays = 0;
                let halfDays = 0;
                let absentDays = 0;
                let leaveDays = 0;

                Object.values(recordsByDate).forEach(dateRecords => {
                    let hasLeave = false;
                    let hasHoliday = false;
                    let hasWeekOff = false;
                    let hasPresent = false;
                    let hasHalfDay = false;

                    dateRecords.forEach(record => {
                        const status = record.status ? record.status.toLowerCase() : '';
                        if (status === 'leave') {
                            hasLeave = true;
                        } else if (status === 'holiday') {
                            hasHoliday = true;
                        } else if (status === 'week off' || status === 'week_off') {
                            hasWeekOff = true;
                        } else if (status === 'present') {
                            hasPresent = true;
                        } else if (status === 'half-day') {
                            hasHalfDay = true;
                        }
                    });

                    // Determine status based on API status
                    if (hasLeave) {
                        leaveDays++;
                    } else if (hasHoliday || hasWeekOff) {
                        presentDays++;
                    } else if (hasPresent) {
                        presentDays++;
                    } else if (hasHalfDay) {
                        halfDays++;
                    } else {
                        absentDays++;
                    }
                });

                // Update absent days calculation
                absentDays = workingDays - presentDays - halfDays - leaveDays;
                if (absentDays < 0) absentDays = 0;

                // Calculate total work hours
                let totalSeconds = 0;
                let totalOvertimeSeconds = 0;
                let totalLateMinutes = 0;

                attendance.forEach(record => {
                    if (record.work_hours && record.work_hours !== '00:00:00') {
                        totalSeconds += timeToSeconds(record.work_hours);
                    } else if (record.check_in_time && record.check_out_time) {
                        // Fallback just in case work_hours is not set
                        const workHours = calculateWorkHours(record);
                        totalSeconds += workHours;
                    }

                    if (record.overtime) {
                        totalOvertimeSeconds += timeToSeconds(record.overtime);
                    }

                    if (record.is_late && record.late_minutes) {
                        totalLateMinutes += parseInt(record.late_minutes);
                    }
                });

                const totalWorkHours = formatSeconds(totalSeconds);
                const totalOvertime = formatSeconds(totalOvertimeSeconds);
                const totalLateTime = formatMinutesToHours(totalLateMinutes);

                const profileImage = user.profile_image
                    ? `/upload/${user.profile_image}`
                    : defaultImagePath;

                // Employee row - Following the exact pattern from the second file
                const row = document.createElement('tr');
                row.className = 'employee-row';
                row.dataset.userId = user.user_id;
                row.innerHTML = `
                    <td class="py-1">
                        <div style="display: flex; align-items: flex-start; gap: 10px;" class="align-items-center">
                            <img src="${profileImage}" alt="Profile"
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <div style="flex: 1;">
                                <span>${user.employee_name}</span>                                
                            </div>
                        </div>
                        <div class="expanded-details" id="employee-details-${user.user_id}" onclick="event.stopPropagation();">
                            <div class="detail-row">
                                <span class="detail-label">Total Days:</span>
                                <span class="detail-value">${workingDays}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Present Days:</span>
                                <span class="detail-value">${presentDays + (halfDays * 0.5)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Work Hours:</span>
                                <span class="detail-value">${totalWorkHours}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Overtime:</span>
                                <span class="detail-value">${totalOvertime || '0h 0m'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Late Hours:</span>
                                <span class="detail-value">${totalLateTime || '0h 0m'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Leaves:</span>
                                <span class="detail-value">${leaveDays}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Absent:</span>
                                <span class="detail-value">${absentDays > 0 ? absentDays : 0}</span>
                            </div>
                            <div class="detail-actions">
                                <button class="btn btn-sm btn-info view-history-btn-mobile" data-user-id="${user.user_id}">
                                    <i class="mdi mdi-history"></i> View History
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="desktop-only-col">${workingDays}</td>
                    <td class="desktop-only-col">${presentDays + (halfDays * 0.5)}</td>
                    <td class="desktop-only-col">${totalWorkHours}</td>
                    <td class="desktop-only-col">${totalOvertime || '0h 0m'}</td>
                    <td class="desktop-only-col">${totalLateTime || '0h 0m'}</td>
                    <td class="desktop-only-col">${leaveDays}</td>
                    <td class="desktop-only-col">${absentDays > 0 ? absentDays : 0}</td>
                    <td class="desktop-only-col">
                        <button class="btn btn-sm btn-info view-history-btn" data-user-id="${user.user_id}">
                            View History
                        </button>
                    </td>
                    <td class="mobile-expand-col text-center">
                        <button type="button" class="expand-toggle" data-target="employee-details-${user.user_id}" aria-label="Expand details"></button>
                    </td>
                `;

                // History row (hidden by default)
                const historyRow = document.createElement('tr');
                historyRow.className = 'history-row';
                historyRow.id = `history-${user.user_id}`;
                historyRow.innerHTML = `
                    <td colspan="10">
                        <div class="history-content">
                            <h6>Check-in History for ${user.employee_name}</h6>
                            <div id="history-data-${user.user_id}">
                                <div class="loading-spinner">Loading history...</div>
                            </div>
                        </div>
                    </td>
                `;

                tbody.appendChild(row);
                tbody.appendChild(historyRow);
            });

            // Add click event listeners for desktop view history buttons
            document.querySelectorAll('.view-history-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const userId = btn.dataset.userId;
                    toggleHistory(userId, month, year);
                });
            });

            // Add click event listeners for mobile view history buttons
            document.querySelectorAll('.view-history-btn-mobile').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const userId = btn.dataset.userId;
                    toggleHistory(userId, month, year);
                });
            });

            // Desktop row click for history (only on desktop)
            document.querySelectorAll('.employee-row').forEach(row => {
                row.addEventListener('click', (e) => {
                    // Only trigger on desktop and not when buttons are clicked
                    if (!e.target.closest('.expand-toggle') && 
                        !e.target.closest('.view-history-btn-mobile') && 
                        window.innerWidth >= 768) {
                        const userId = row.dataset.userId;
                        toggleHistory(userId, month, year);
                    }
                });
            });
        };

        const toggleHistory = (userId, month, year) => {
            const historyRow = document.getElementById(`history-${userId}`);
            const employeeRow = document.querySelector(`tr[data-user-id="${userId}"]`);

            // Close other open histories
            document.querySelectorAll('.history-row').forEach(row => {
                if (row.id !== `history-${userId}`) {
                    row.classList.remove('show');
                }
            });
            document.querySelectorAll('.employee-row').forEach(row => {
                if (row.dataset.userId !== userId) {
                    row.classList.remove('selected');
                }
            });

            // Toggle current history
            if (historyRow.classList.contains('show')) {
                historyRow.classList.remove('show');
                employeeRow.classList.remove('selected');
            } else {
                historyRow.classList.add('show');
                employeeRow.classList.add('selected');
                loadEmployeeHistory(userId, month, year);
            }
        };

        const loadEmployeeHistory = (userId, month, year) => {
            const historyContainer = document.getElementById(`history-data-${userId}`);

            fetch(`/api/attendance/getAttendance/${month}/${year}`, { headers })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const user = data.data.users.find(u => u.user_id == userId);
                        if (user) {
                            renderHistory(historyContainer, user.attendance);
                        }
                    }
                })
                .catch(error => {
                    historyContainer.innerHTML = '<p class="text-danger">Error loading history</p>';
                });
        };

        const renderHistory = (container, attendance) => {
            if (!attendance || attendance.length === 0) {
                container.innerHTML = '<p class="text-muted">No attendance records found</p>';
                return;
            }

            // Exclude future dates so they are not shown as "absent"
            const today = new Date();
            const validAttendance = attendance.filter(r => new Date(r.date) <= today);

            if (validAttendance.length === 0) {
                container.innerHTML = '<p class="text-muted">No attendance records found</p>';
                return;
            }

            validAttendance.sort((a, b) => new Date(b.date) - new Date(a.date));

            let html = '<table class="history-table"><thead><tr>';
            html += '<th>Date</th><th>Check In</th><th>Check Out</th><th>Work Hours</th><th>Overtime</th><th>Late</th><th>Status</th>';
            html += '</tr></thead><tbody>';

            validAttendance.forEach(record => {
                const statusClass = `status-${record.status}`;
                const lateInfo = record.is_late ? formatMinutesToHours(record.late_minutes) : '-';
                const overtime = record.overtime ? formatTimeString(record.overtime) : '-';
                const workHours = calculateWorkHoursDisplay(record);

                html += `<tr>
                    <td>${formatDate(record.date)}</td>
                    <td>${record.check_in_time || '-'}</td>
                    <td>${record.check_out_time || '-'}</td>
                    <td>${workHours}</td>
                    <td>${overtime}</td>
                    <td>${lateInfo}</td>
                    <td><span class="status-badge ${statusClass}">${record.status}</span></td>
                </tr>`;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        };

        // Helper functions
        const calculateWorkHours = (record) => {
            if (!record.check_in_time || !record.check_out_time) return 0;
            const checkIn = new Date(`2000-01-01 ${record.check_in_time}`);
            const checkOut = new Date(`2000-01-01 ${record.check_out_time}`);
            return (checkOut - checkIn) / 1000;
        };

        const formatSeconds = (seconds) => {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            return `${hours}h ${minutes}m`;
        };

        const formatMinutesToHours = (minutes) => {
            if (!minutes || minutes === 0) return '0h 0m';
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${hours}h ${mins}m`;
        };

        const timeToSeconds = (timeString) => {
            if (!timeString) return 0;
            const parts = timeString.split(':');
            const hours = parseInt(parts[0]) || 0;
            const minutes = parseInt(parts[1]) || 0;
            const seconds = parseInt(parts[2]) || 0;
            return (hours * 3600) + (minutes * 60) + seconds;
        };

        const formatTimeString = (timeString) => {
            if (!timeString || timeString === '00:00:00') return '0h 0m';
            const parts = timeString.split(':');
            const hours = parseInt(parts[0]) || 0;
            const minutes = parseInt(parts[1]) || 0;
            return `${hours}h ${minutes}m`;
        };

        const calculateWorkHoursDisplay = (record) => {
            if (!record.check_in_time || !record.check_out_time) return '-';
            const checkIn = new Date(`2000-01-01 ${record.check_in_time}`);
            const checkOut = new Date(`2000-01-01 ${record.check_out_time}`);
            const seconds = (checkOut - checkIn) / 1000;
            return formatSeconds(seconds);
        };

        const formatDate = (dateStr) => {
            const date = new Date(dateStr);
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        };

        // Event listeners for month/year change
        document.getElementById('month-selector').addEventListener('change', loadAttendanceData);
        document.getElementById('year-selector').addEventListener('change', loadAttendanceData);

        // Initial load
        loadAttendanceData();
    });
</script>

<?= $this->endSection() ?>