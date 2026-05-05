<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    @media (min-width: 767px) {
        div.dataTables_wrapper div.dataTables_filter label input {
            width: 287px !important;
        }
    }

    .capitalize-text {
        text-transform: capitalize;
    }

    .managepayrol {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 3px;
    }

    @media (max-width: 767px) {
        .cart-sm-title {
            font-size: 12px !important;
        }

        .managepayrol {
            display: block !important;
            justify-content: unset !important;
            align-items: unset !important;
            margin-bottom: 0px !important;
        }

        .attendenceall {
            /* font-size: 12px !important; */
            padding: 6px !important;
            margin-top: 8px !important;
            margin-bottom: 5px !important;
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
            max-width: 50%;
        }

        .dataTables_filter label:before {
            content: "" !important;
            display: none !important;
        }

        #payroll-table_length label {
            margin-top: 1px;
            display: flex;
            align-items: center;
        }

        /* Hide the text inside the label */
        #payroll-table_length label::first-text,
        #payroll-table_length label::before {
            display: none !important;
        }

        /* Or a simpler and reliable trick */
        #payroll-table_length label {
            font-size: 0;
            /* hide text */
        }

        #payroll-table_length label input {
            font-size: 10px;
            /* reset font size for input */
        }

        #payroll-table_filter label {
            font-size: 0;
        }

        #payroll-table_filter input {
            font-size: 14px;
        }

        #payroll-table_length label {
            font-size: 0;
            /* hide all text inside the label */
        }

        #payroll-table_length label select {
            font-size: 14px;
            /* restore font size for the dropdown */
        }

        .attendencepaddbottom {
            margin-bottom: 5px !important;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: 212px !important;
            height: 2.05rem !important;
        }

        .custom-select {
            height: 26px !important;
            width: 57px !important;
        }

        #payrollMonthFilter {
            width: 100% !important;
        }
    }
</style>

<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1" aria-labelledby="addSalaryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryLabel">Add Employee Salaries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Month Select -->
                <div class="mb-3">
                    <label for="salaryMonth" class="form-label">Select Month</label>
                    <input type="month" id="salaryMonth" class="form-control" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn hr-btnbg" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn hr-btnbg" id="openSalaryPage">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Yearly Salary Slip Modal -->
<div class="modal fade" id="yearlySlipModal" tabindex="-1" aria-labelledby="yearlySlipLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yearlySlipLabel">Download Yearly Salary Slips</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="yearlySlipForm">
                    <div class="mb-3">
                        <label for="yearlyEmployee" class="form-label">Select Employee</label>
                        <select id="yearlyEmployee" class="form-select select2" required style="width: 100%;">
                            <option value="">Select Employee</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="yearlyYear" class="form-label">Select Year</label>
                        <select id="yearlyYear" class="form-select" required>
                            <?php
                            $currentYear = date("Y");
                            for ($i = 0; $i < 5; $i++) {
                                $year = $currentYear - $i;
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startMonth" class="form-label">Start Month</label>
                            <select id="startMonth" class="form-select" required>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endMonth" class="form-label">End Month</label>
                            <select id="endMonth" class="form-select" required>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12" selected>December</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn hr-btnbg" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn hr-btnbg" id="downloadYearlySlips">Download</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="managepayrol mb-3">
                    <h4 class="card-title">Manage Payrolls</h4>
                    <?php $role = session()->get("role"); ?>
                    <?php if ($role !== "employee"): ?>
                        <div class="d-md-flex gap-2 attendencepaddbottom flex-wrap align-items-center">
                            <?php
                            $now = new DateTime();
                            $now->modify("-1 month");
                            $lastMonth = $now->format("Y-m");
                            $currentMonth = date("Y-m");
                            ?>
                            <input type="month" id="payrollMonthFilter" name="payrollMonth" class="form-control w-auto"
                                value="<?= esc($_GET["month"] ?? $lastMonth) ?>" max="<?= $currentMonth ?>" />
                            <button type="button" id="downloadMultipleBtn" class="btn hr-btnbg attendenceall"
                                style="white-space:nowrap;">
                                <i class="mdi mdi-download iconfontsize"></i> Download Selected
                            </button>
                            <button class="btn hr-btnbg attendenceall" id="groupsalary">
                                <i class="mdi mdi-plus iconfontsize"></i> Group Salary
                            </button>
                            <button class="btn hr-btnbg attendenceall" id="yearlySlipBtn">
                                <i class="mdi mdi-download iconfontsize"></i> Yearly Slip
                            </button>
                            <button type="button" id="btnSalarySheetView" class="btn hr-btnbg attendenceall" onclick="generateSalarySheetPDF()">
                                <i class="mdi mdi-file-pdf iconfontsize"></i> Salary Sheet
                            </button>

                            <a href="/payroll" class="btn hr-btnbg attendenceall">
                                <i class="mdi mdi-plus iconfontsize"></i> Add Payroll
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped w-100" id="payroll-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th>Employee Name</th>
                                <th class="desktop-only-col">Base Salary</th>
                                <th class="desktop-only-col">Month & Year</th>
                                <th class="desktop-only-col">Net Salary</th>
                                <th class="desktop-only-col">Payment Date</th>
                                <th style="display: none;">Created At</th>
                                <th class="desktop-only-col">Action</th>
                                <th class="mobile-expand-col" style="width: 50px;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="payroll-table tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deduction breakdown modal (same as Add Payroll / Profile) -->
<div class="modal fade" id="listDeductionBreakdownModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listDeductionBreakdownModalLabel"><i
                        class="mdi mdi-information-outline me-1"></i> Why was this amount deducted?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="listDeductionBreakdownLoading" style="display:block;">Loading...</div>
                <div id="listDeductionBreakdownContent" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    $(document).ready(function () {
        // Get last month as default
        const now = new Date();
        now.setMonth(now.getMonth() - 1);
        const lastMonth = now.toISOString().slice(0, 7); // "YYYY-MM" format for last month

        // Get month from URL or use last month as default
        const urlParams = new URLSearchParams(window.location.search);
        const selectedMonth = urlParams.get('month') || lastMonth;

        // Set the month filter value
        const monthFilter = document.getElementById('payrollMonthFilter');
        if (monthFilter) {
            monthFilter.value = selectedMonth;
        }

        $('#groupsalary').on('click', function () {
            // Use last month as default for Group Salary
            const currentNow = new Date();
            currentNow.setMonth(currentNow.getMonth() - 1);
            const month = currentNow.toISOString().slice(0, 7); // "YYYY-MM" format for last month
            window.location.href = `/payroll/salary-details?month=${month}`;
        });

        // Month filter change event
        if (monthFilter) {
            monthFilter.addEventListener('change', function () {
                const month = this.value;
                if (month) {
                    window.location.href = `/payrollview?month=${month}`;
                }
            });
        }

        const token = localStorage.getItem('token'); // JWT token

        // Get the user role from the decoded JWT
        function getUserRole() {
            let payload = JSON.parse(atob(token.split('.')[1])); // Decode JWT
            return payload.role; // Extract user role
        }

        function fetchPayroll() {
            // Build URL with month filter
            let apiUrl = '<?= base_url("/api/payroll/getAll") ?>';
            if (selectedMonth) {
                apiUrl += '?month=' + selectedMonth;
            }

            $.ajax({
                url: apiUrl,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
                success: function (response) {
                    if (response.status === 'success' && response.data) {
                        const payrolls = response.data;
                        window._payrollData = payrolls; // cache for Salary Sheet PDF
                        let tableRows = '';
                        let userRole = getUserRole(); // Get role from JWT
                        var baseImagePath = "<?= base_url(env("ImagePath")) ?>";
                        payrolls.forEach((payroll) => {
                            let imageUrl = payroll.profile_image ?
                                `/upload/${payroll.profile_image}` :
                                `${baseImagePath}upload/default-profile.jpg`;
                            let actionButtons = '';
                            let mobileActionsHtml = '';

                            const employeeId = payroll.employee_id || payroll.user_id;
                            const monthYear = payroll.month_year || '';
                            const empName = (payroll.username || '').replace(/"/g, '&quot;');
                            const infoIcon = `<a href="javascript:void(0)" role="button" class="text-info fs-5 payroll-deduction-info" title="Why was this amount deducted?" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="mdi mdi-information-outline"></i></a>`;
                            if (userRole !== 'employee') {
                                actionButtons = `
                                    <a href="/payroll/profile/${payroll.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    ${infoIcon}
                                    <a href="/payroll/${payroll.id}" class="text-warning fs-5" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    <a href="#" class="text-danger fs-5 delete-payroll" data-id="${payroll.id}" title="Delete"><i class="mdi mdi-delete"></i></a>
                                    <a href="/payroll/download-slip/${payroll.id}" class="text-success fs-5" title="Download" target="_blank"><i class="mdi mdi-download"></i></a>
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/payroll/profile/${payroll.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                        <a href="javascript:void(0)" role="button" class="btn btn-sm btn-info payroll-deduction-info" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="mdi mdi-information-outline"></i> Deduction</a>
                                        <a href="/payroll/${payroll.id}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-payroll" data-id="${payroll.id}"><i class="mdi mdi-delete"></i> Delete</a>
                                        <a href="/payroll/download-slip/${payroll.id}" class="btn btn-sm btn-success" target="_blank"><i class="mdi mdi-download"></i> Download</a>
                                    </div>
                                `;
                            } else {
                                actionButtons = `
                                    <a href="/payroll/profile/${payroll.id}" class="text-primary fs-5" title="View"><i class="mdi mdi-eye"></i></a>
                                    ${infoIcon}
                                `;
                                mobileActionsHtml = `
                                    <div class="detail-actions">
                                        <a href="/payroll/profile/${payroll.id}" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i> View</a>
                                        <a href="javascript:void(0)" role="button" class="btn btn-sm btn-info payroll-deduction-info" data-user-id="${employeeId}" data-month-year="${monthYear}" data-name="${empName}"><i class="mdi mdi-information-outline"></i> Deduction</a>
                                    </div>
                                `;
                            }

                            tableRows += `
                                <tr data-id="${payroll.id}" data-employee-id="${payroll.employee_id || payroll.id}">
                                    <td>
                                        <input type="checkbox" class="payroll-checkbox form-check-input"
                                               data-id="${payroll.id}"
                                               data-employee-id="${payroll.employee_id || payroll.id}">
                                    </td>
                                    <td class="py-1">
                                        <div style="align-items: flex-start; gap: 10px;">
                                            <a href="/payroll/profile/${payroll.id}" class="text-decoration-none">
                                                <img src="${imageUrl}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            </a>
                                            <a href="/payroll/profile/${payroll.id}" class="text-decoration-none text-dark">
                                                <span class="capitalize-text">${payroll.username}</span>
                                            </a>                                            
                                        </div>
                                        <div style="flex: 1;" class="align-self-center">
                                                
                                                <div class="expanded-details" id="payroll-details-${payroll.id}" onclick="event.stopPropagation();">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Base Salary:</span>
                                                        <span class="detail-value">${payroll.salary_amount}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Month & Year:</span>
                                                        <span class="detail-value">${payroll.month_year}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Net Salary:</span>
                                                        <span class="detail-value">${payroll.net_salary}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Payment Date:</span>
                                                        <span class="detail-value">${payroll.payment_date}</span>
                                                    </div>
                                                    ${mobileActionsHtml}
                                                </div>
                                            </div>
                                    </td>
                                    <td class="desktop-only-col capitalize-text">${payroll.salary_amount}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.month_year}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.net_salary}</td>
                                    <td class="desktop-only-col capitalize-text">${payroll.payment_date}</td>
                                    <td class="capitalize-text" style="display: none;">${payroll.created_at}</td>
                                    <td class="desktop-only-col" style="display: flex; align-items: center; gap: 8px;">
                                        ${actionButtons}
                                    </td>
                                    <td class="mobile-expand-col text-center">
                                        <button type="button" class="expand-toggle" data-target="payroll-details-${payroll.id}" aria-label="Expand details"></button>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#payroll-table tbody').html(tableRows); // Update the table body
                        // $('#payroll-table').DataTable(); // Initialize DataTable for the updated rows
                        $('#payroll-table').DataTable({
                            order: [
                                [6, 'desc']
                            ],
                            columnDefs: [{
                                targets: 0, // checkbox column
                                orderable: false,
                                searchable: false
                            },
                            {
                                targets: 6,
                                visible: false
                            },
                            {
                                targets: 8, // mobile expand column
                                orderable: false,
                                searchable: false
                            }
                            ],
                            language: {
                                search: "",
                                searchPlaceholder: "Search"
                            }
                        });

                        // Initialize checkbox functionality after DataTable
                        initCheckboxFunctionality();
                        // Apply mobile visibility
                        if (typeof applyMobileTableVisibility === 'function') {
                            applyMobileTableVisibility();
                        }


                    } else {
                        Swal.fire('Error', 'Failed to load payroll records', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch payroll records', 'error');
                }
            });
        }

        // Checkbox management for multiple downloads
        function initCheckboxFunctionality() {
            const checkAllCheckbox = document.getElementById('checkAll');
            const downloadMultipleBtn = document.getElementById('downloadMultipleBtn');

            function updateDownloadButtonVisibility() {
                const checkedCount = document.querySelectorAll('.payroll-checkbox:checked').length;
                if (downloadMultipleBtn) {
                    downloadMultipleBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                }
            }

            // Check all functionality
            if (checkAllCheckbox) {
                checkAllCheckbox.addEventListener('change', function () {
                    document.querySelectorAll('.payroll-checkbox').forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateDownloadButtonVisibility();
                });
            }

            // Individual checkbox logic
            $(document).on('change', '.payroll-checkbox', function () {
                const allCheckboxes = document.querySelectorAll('.payroll-checkbox');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);

                if (checkAllCheckbox) {
                    checkAllCheckbox.checked = allChecked;
                    checkAllCheckbox.indeterminate = someChecked && !allChecked;
                }
                updateDownloadButtonVisibility();
            });

            // Download multiple slips functionality
            if (downloadMultipleBtn) {
                downloadMultipleBtn.addEventListener('click', function () {
                    const selectedCheckboxes = document.querySelectorAll('.payroll-checkbox:checked');
                    const selectedPayrollIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.id);

                    if (selectedPayrollIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No payrolls selected',
                            text: 'Please select at least one payroll record'
                        });
                        return;
                    }

                    // Show loading state
                    Swal.fire({
                        title: 'Preparing download...',
                        html: 'Generating salary slips for ' + selectedPayrollIds.length + ' record(s)',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send request to download multiple slips
                    fetch("<?= base_url("/api/payroll/downloadMultipleByIds", ) ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify({
                            payroll_ids: selectedPayrollIds,
                            month: selectedMonth
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to download salary slips');
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            Swal.close();

                            // Create download link
                            const url = window.URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = `salary-slips-${selectedMonth}.pdf`;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(url);

                            Swal.fire({
                                icon: 'success',
                                title: 'Downloaded successfully',
                                text: selectedPayrollIds.length + ' salary slip(s) combined in one PDF',
                                toast: true,
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Download failed',
                                text: error.message
                            });
                        });
                });
            }
        }

        // Call the fetchPayroll function on page load
        fetchPayroll();

        // Deduction breakdown popup – open modal (shared for click and touchend)
        function openListDeductionModal($el) {
            const userId = $el.data('user-id');
            const month = $el.data('month-year');
            const name = $el.data('name') || 'Employee';
            if (!userId || !month) return;
            const modal = document.getElementById('listDeductionBreakdownModal');
            const loading = document.getElementById('listDeductionBreakdownLoading');
            const content = document.getElementById('listDeductionBreakdownContent');
            if (!modal) return;
            document.getElementById('listDeductionBreakdownModalLabel').innerHTML = '<i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted? – ' + name;
            loading.style.display = 'block';
            content.style.display = 'none';
            content.innerHTML = '';
            if (modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            $.ajax({
                url: '<?= base_url("api/payroll/get-deduction-breakdown") ?>',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                data: { user_id: userId, month: month },
                dataType: 'json',
                success: function (res) {
                    loading.style.display = 'none';
                    if (res.status !== 'success' || !res.data) {
                        content.innerHTML = '<p class="text-muted">No breakdown available.</p>';
                        content.style.display = 'block';
                        return;
                    }
                    const d = res.data;
                    let html = '';
                    if (d.leaves && d.leaves.count > 0) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Leaves</strong>';
                        if (d.leaves.dates && d.leaves.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.leaves.dates.forEach(function (l) { html += '<li>' + (l.label || l.date) + '</li>'; });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.leaves.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.absent && d.absent.dates && d.absent.dates.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Absent</strong><ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                        d.absent.dates.forEach(function (a) { html += '<li>' + (a.label || a.date) + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.absent.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.half_day && d.half_day.count > 0) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Half-day</strong>';
                        if (d.half_day.dates && d.half_day.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.half_day.dates.forEach(function (h) {
                                var baseLabel = (h.label || h.date);
                                var worked = h.worked_text ? (' – Worked: ' + h.worked_text) : '';
                                var missing = h.missing_text ? (' – Deduct: ' + h.missing_text) : '';
                                html += '<li>' + baseLabel + worked + missing + '</li>';
                            });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.half_day.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.late && d.late.list && d.late.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Late arrival</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.late.list.forEach(function (l) { html += '<li>' + (l.label || l.date) + ' – ' + (l.late_text || l.late_minutes + ' min') + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.late.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.overtime && d.overtime.list && d.overtime.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-success">Overtime</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.overtime.list.forEach(function (o) { html += '<li>' + (o.label || o.date) + ' – ' + (o.overtime_text || o.overtime_hours + 'h') + '</li>'; });
                        html += '</ul><span class="text-success">Added to salary: ₹' + (d.overtime.pay_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.summary) {
                        html += '<hr><div class="fw-bold"><span>Total deduction (leaves + half-day + late):</span> <span class="text-danger">₹' + (d.summary.total_deduction || 0).toFixed(2) + '</span></div>';
                        if (d.summary.overtime_added > 0) html += '<div class="fw-bold"><span>Overtime added:</span> <span class="text-success">₹' + d.summary.overtime_added.toFixed(2) + '</span></div>';
                    }
                    if (!d.leaves?.count && !d.absent?.dates?.length && !d.half_day?.count && !d.late?.list?.length && !d.overtime?.list?.length) {
                        html += '<p class="text-muted">No leaves, absent, late, or overtime in this month.</p>';
                    }
                    content.innerHTML = html;
                    content.style.display = 'block';
                },
                error: function () {
                    loading.style.display = 'none';
                    content.innerHTML = '<p class="text-danger">Failed to load details.</p>';
                    content.style.display = 'block';
                }
            });
        }

        // Deduction: handle both touchend (mobile) and click (desktop), avoid double-open
        var listDeductionLastTouch = 0;
        $(document).on('touchend', '.payroll-deduction-info', function (e) {
            e.preventDefault();
            e.stopPropagation();
            listDeductionLastTouch = Date.now();
            var $el = $(e.target).closest('.payroll-deduction-info');
            if ($el.length) openListDeductionModal($el);
        });
        $(document).on('click', '.payroll-deduction-info', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (Date.now() - listDeductionLastTouch < 400) return; // already opened by touchend
            openListDeductionModal($(this));
        });

        // Handle delete action
        $(document).on('click', '.delete-payroll', function (e) {
            e.preventDefault();
            const payrollId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'hr-btnbg',
                    cancelButton: 'hr-btnbg',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/payroll/${payrollId}`,
                        type: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', 'The payroll record has been deleted.', 'success').then(() => {
                                    $(`tr[data-id="${payrollId}"]`).remove();
                                });
                            } else {
                                Swal.fire('Error!', 'Failed to delete the payroll record.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'There was an error deleting the payroll record.', 'error');
                        }
                    });
                }
            });
        });

        // Yearly Slip Modal Logic
        const yearlySlipModal = new bootstrap.Modal(document.getElementById('yearlySlipModal'));

        function updateAvailableMonths() {
            const year = $('#yearlyYear').val();
            const startMonthSelect = $('#startMonth');
            const endMonthSelect = $('#endMonth');

            if (year === '2026') {
                const allowedMonths = ['01', '02', '03'];

                [startMonthSelect, endMonthSelect].forEach(select => {
                    select.find('option').each(function () {
                        const val = $(this).val();
                        if (allowedMonths.includes(val)) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });

                    // Reset if current value is now disabled
                    if (!allowedMonths.includes(select.val())) {
                        select.val(select.attr('id') === 'endMonth' ? '03' : '01');
                    }
                });
            } else {
                [startMonthSelect, endMonthSelect].forEach(select => {
                    select.find('option').prop('disabled', false);
                });
            }
        }

        $('#yearlyYear').on('change', updateAvailableMonths);

        $('#yearlySlipBtn').on('click', function () {
            yearlySlipModal.show();
            fetchEmployeesForYearly();
            updateAvailableMonths(); // Initialize on modal open
        });

        function fetchEmployeesForYearly() {
            $.ajax({
                url: '<?= base_url("/api/payroll/getEmployees") ?>',
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (response) {
                    if (response.status === 'success') {
                        let options = '<option value="">Select Employee</option>';
                        response.data.forEach(emp => {
                            options += `<option value="${emp.user_id}">${emp.firstname} ${emp.lastname}</option>`;
                        });
                        $('#yearlyEmployee').html(options);
                    }
                }
            });
        }

        $('#downloadYearlySlips').on('click', function () {
            const userId = $('#yearlyEmployee').val();
            const year = $('#yearlyYear').val();
            const startMonth = $('#startMonth').val();
            const endMonth = $('#endMonth').val();

            if (!userId || !year || !startMonth || !endMonth) {
                Swal.fire('Error', 'Please select all required fields', 'error');
                return;
            }

            if (parseInt(startMonth) > parseInt(endMonth)) {
                Swal.fire('Error', 'Start month cannot be after end month', 'error');
                return;
            }

            Swal.fire({
                title: 'Preparing your slips...',
                html: 'This may take a moment.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('<?= base_url("/api/payroll/downloadYearly") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    user_id: userId,
                    year: year,
                    start_month: startMonth,
                    end_month: endMonth
                })
            })
                .then(response => {
                    if (!response.ok) throw new Error('No payroll records found for this year');
                    return response.blob();
                })
                .then(blob => {
                    Swal.close();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `salary_slips_${year}_${userId}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    yearlySlipModal.hide();
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire('Error', error.message, 'error');
                });
        });
    });
</script>

<script>
    // ─── Salary Sheet PDF (client-side, jsPDF + AutoTable) ──────────────────
    function generateSalarySheetPDF() {
        if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
            alert('PDF library not loaded yet. Please wait a moment and try again.');
            return;
        }
        const payrolls = window._payrollData || [];
        if (!payrolls.length) {
            Swal.fire({ icon: 'warning', title: 'No Data', text: 'No payroll records loaded. Please wait for the table to finish loading.' });
            return;
        }

        const token = localStorage.getItem('token');

        // Fetch company info first, then generate PDF
        fetch('<?= base_url("/api/getCompanyLogo") ?>', {
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(r => r.json())
        .then(companyData => {
            _buildSalarySheetPDF(payrolls, companyData);
        })
        .catch(() => {
            _buildSalarySheetPDF(payrolls, {});
        });
    }

    function _buildSalarySheetPDF(payrolls, companyData) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

        const companyName    = companyData.company_name    || 'Fablead Developers Technolab';
        const companyAddress = companyData.company_address || '';
        const companyLogo    = companyData.logo_img        || '';
        const rawMonth    = document.getElementById('payrollMonthFilter')?.value || '';
        const [yr, mo]    = (rawMonth || '2026-01').split('-');
        const monthLabel  = new Date(yr, parseInt(mo) - 1, 1).toLocaleString('en-IN', { month: 'long' }) + ' ' + yr;
        const fileName    = 'Salary_Sheet_' + monthLabel.replace(' ', '_') + '.pdf';

        const tableBody = [];
        let totalLeave = 0, totalDeduction = 0, totalSalary = 0, totalTax = 0, totalNetPay = 0, totalSalaryAmt = 0;

        payrolls.forEach(p => {
            const name      = (p.username || '').trim();
            const salary    = Math.round(parseFloat(p.salary_amount) || 0);
            const leaves    = parseFloat(p.total_leaves)     || 0;
            const [yr, mo]    = (rawMonth || '2026-01').split('-');
            const daysInMonth = new Date(parseInt(yr), parseInt(mo), 0).getDate();
            const perDay      = Math.round(salary / daysInMonth);

            // Fetch actual values from the database and round them
            const deduction = Math.round(parseFloat(p.salary_deduction) || 0);
            const taxAmt    = Math.round(parseFloat(p.tax_deduction)    || 0);
            
            const taxText = taxAmt > 0 ? 'Rs. ' + taxAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'No Tax';

            // Fetch Net Pay directly from the database and round it
            const netPay = Math.round(parseFloat(p.net_salary) || 0);

            totalLeave     += leaves;
            totalDeduction += deduction;
            totalSalary    += salary;
            totalTax       += taxAmt;
            totalNetPay    += netPay;

            tableBody.push([
                name,
                leaves % 1 === 0 ? leaves.toString() : leaves.toFixed(1),
                'Rs. ' + perDay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                'Rs. ' + deduction.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                'Rs. ' + salary.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                taxText,
                'Rs. ' + netPay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            ]);
        });

        tableBody.push([
            'TOTAL',
            totalLeave % 1 === 0 ? totalLeave.toString() : totalLeave.toFixed(1),
            '',
            'Rs. ' + totalDeduction.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
            'Rs. ' + totalSalary.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
            'Rs. ' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
            'Rs. ' + totalNetPay.toLocaleString('en-IN', { minimumFractionDigits: 2 })
        ]);

        const orange      = [230, 97, 54];
        const darkBg      = [40, 40, 50];
        const lightGr     = [248, 248, 248];
        const white       = [255, 255, 255];
        const black       = [0, 0, 0];
        const pageW       = doc.internal.pageSize.getWidth();
        const totalRowIdx = tableBody.length - 1;

        // ── Header: centered layout like salary slip ────────────────────────────
        const headerH    = companyAddress ? 62 : 46;
        const logoSize   = 44;

        doc.setDrawColor(180, 180, 180);
        doc.setLineWidth(0.5);
        doc.rect(30, 10, pageW - 60, headerH, 'S');

        // Logo (if available) – centered left side
        let logoEndX = 40;
        if (companyLogo) {
            const logoX = pageW / 2 - logoSize - 5;
            const logoY = 14;
            const img = new Image();
            img.src = companyLogo;
            try { doc.addImage(img, logoX, logoY, logoSize, logoSize); logoEndX = logoX + logoSize + 8; } catch(e) {}
        }

        // Company Name – bold, centered
        doc.setTextColor(...black);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(companyName, pageW / 2, 35, { align: 'center' });

        // Company Address – normal, centered, smaller
        if (companyAddress) {
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(80, 80, 80);
            doc.text(companyAddress, pageW / 2, 50, { align: 'center' });
        }

        // Month / Sheet title row – dark bar below header
        const titleBarY = headerH + 10 + 10;
        doc.setFillColor(...darkBg);
        doc.rect(30, titleBarY - 14, pageW - 60, 20, 'F');
        doc.setTextColor(...white);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.text('Monthly Salary Sheet - ' + monthLabel, pageW / 2, titleBarY, { align: 'center' });

        const tableStartY = titleBarY + 12;

        doc.autoTable({
            startY: tableStartY,
            margin: { left: 40, right: 40 },
            head: [['NAME', 'LEAVE\n(Days)', 'PER DAY\nSALARY (Rs)', 'DEDUCTION\n(Rs)', 'SALARY\n(Rs)', 'TAX\n(Rs)', 'NET PAY\n(Rs)']],
            body: tableBody,
            headStyles: { fillColor: orange, textColor: white, fontStyle: 'bold', fontSize: 7, halign: 'center', valign: 'middle', cellPadding: 3 },
            columnStyles: {
                0: { halign: 'left',   cellWidth: 'auto' },
                1: { halign: 'center', cellWidth: 40 },
                2: { halign: 'right' },
                3: { halign: 'right' },
                4: { halign: 'right' },
                5: { halign: 'center' },
                6: { halign: 'right' }
            },
            styles: { fontSize: 7.5, cellPadding: { top: 4, bottom: 4, left: 4, right: 4 }, overflow: 'linebreak', lineColor: [220, 220, 220], lineWidth: 0.3 },
            alternateRowStyles: { fillColor: lightGr },
            bodyStyles: { textColor: [30, 30, 30], valign: 'middle' },
            didParseCell: function (data) {
                if (data.section === 'body' && data.row.index === totalRowIdx) {
                    data.cell.styles.fillColor = darkBg;
                    data.cell.styles.textColor = white;
                    data.cell.styles.fontStyle = 'bold';
                    data.cell.styles.fontSize  = 8;
                    return;
                }
                // Red highlight on Leave (1) and Tax (5) columns
                if (data.section === 'body' && (data.column.index === 1 || data.column.index === 5)) {
                    data.cell.styles.textColor = [200, 0, 0];
                }
                // Red color for Deduction values (3)
                if (data.section === 'body' && data.column.index === 3) {
                    data.cell.styles.textColor = [200, 0, 0];
                    data.cell.styles.fontStyle = 'bold';
                }
                // Green color for Net Pay (6)
                if (data.section === 'body' && data.column.index === 6) {
                    data.cell.styles.textColor = [0, 150, 70];
                    data.cell.styles.fontStyle = 'bold';
                }
            },
            foot: [['Generated by Fablead HR Portal - ' + monthLabel, '', '', '', '', '', '']],
            footStyles: { fillColor: [240, 240, 240], textColor: [100, 100, 100], fontSize: 6, halign: 'left', fontStyle: 'italic' },
        });

        doc.save(fileName);
    }
</script>

<?= $this->endSection() ?>