<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
     .capitalize-text {
        text-transform: capitalize;
    }
    @media (max-width: 767px) {
        .interviewsmbtn {
            font-size: 10px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }

        .sm-margins-size {
            font-size: 8.2px !important;
        }

        .tab-content {
            overflow: hidden;
        }

        .tab-pane {
            overflow-x: auto;

        }

        .fontsize-sm-payr {
            font-size: 10px !important;
        }

        .emp-photo-profile {
            width: 50px !important;
            height: 50px !important;
        }

    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="<?= base_url(
    env("ImagePath") . "assets/css/payroll.css",
) ?>">
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="payroll-card">
                <div class="payroll-header">
                    <i class="fas fa-money-check me-2" style="color: #E66136;"></i> Payroll Details
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs sm-margins-size justify-content-center px-3" id="payrollTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="employee-tab" data-bs-toggle="tab" data-bs-target="#employee" type="button" role="tab">
                            <i class="mdi mdi-account me-1"></i> Employee Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab">
                            <i class="mdi mdi-cash-multiple me-1"></i> Salary
                        </button>
                    </li>
                </ul>


                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Employee Info Tab -->
                    <div class="tab-pane fade show active" id="employee" role="tabpanel">
                        <div class="section-header"><i class="fas fa-user-circle me-2" style="color: #E66136;"></i> Employee Information</div>
                        <div class="d-flex align-items-center">
                            <img id="employeePhoto" class="employee-photo shadow emp-photo-profile" width="100" height="100" style="border-radius: 50%; object-fit: cover;">
                            <div class="ms-4">
                                <p class="text-muted fontsize-sm-payr capitalize-text"><i class="fas fa-user me-2" style="color: #E66136;"></i> <span id="employeeName"></span></p>
                                <p class="text-muted fontsize-sm-payr capitalize-text"><i class="fas fa-id-badge me-2" style="color: #E66136;"></i> EMP# <span id="employee_id"></span></p>
                                <p class="text-muted fontsize-sm-payr capitalize-text"><i class="fas fa-envelope me-2" style="color: #E66136;"></i> <span id="employeeEmail"></span></p>
                                <p class="text-muted fontsize-sm-payr capitalize-text   "><i class="fas fa-briefcase me-2" style="color: #E66136;"></i> <span id="employeeDesignation"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Breakdown Tab -->
                    <div class="tab-pane fade" id="salary" role="tabpanel">
                        <div class="section-header"><i class="fas fa-file-invoice-dollar me-2" style="color: #E66136;"></i> Salary</div>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><i class="mdi mdi-cash me-1" style="color: #E66136;"></i> Base Salary</td>
                                    <td><span id="salary_amount" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-month me-1" style="color: #E66136;"></i> Month & Year</td>
                                    <td><span id="month_year" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-remove me-1" style="color: #E66136;"></i> Total Applied Leaves</td>
                                    <td><span id="total_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-check me-1" style="color: #E66136;"></i> Total Paid Leaves</td>
                                    <td><span id="total_paid_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-check me-1" style="color: #E66136;"></i> Total Half-day Leaves</td>
                                    <td><span id="total_hald_day_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-format-list-bulleted me-1" style="color: #E66136;"></i> Leave Type</td>
                                    <td><span id="leave_type" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-clock me-1" style="color: #E66136;"></i> Remaining Paid Leaves</td>
                                    <td><span id="remaining_paid_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-multiple-check me-1" style="color: #E66136;"></i> Used Paid Leaves</td>
                                    <td><span id="used_paid_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-remove-outline me-1" style="color: #E66136;"></i> Unpaid Leaves</td>
                                    <td><span id="unpaid_leaves" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-week me-1" style="color: #E66136;"></i> Working Days</td>
                                    <td><span id="working_days" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-clock-outline me-1" style="color: #E66136;"></i> Worked Hours</td>
                                    <td><span id="worked_hours" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-clock-plus-outline me-1" style="color: #E66136;"></i> Overtime Hours</td>
                                    <td><span id="overtime_hours" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-cash-plus me-1" style="color: #E66136;"></i> Overtime Pay</td>
                                    <td><span id="overtime_pay" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-gift-outline me-1" style="color: #E66136;"></i> Bonuses</td>
                                    <td><span id="bonuses" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-percent me-1" style="color: #E66136;"></i> Tax Deduction</td>
                                    <td><span id="tax_deduction" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-percent me-1" style="color: #E66136;"></i> Salary Deduction</td>
                                    <td>
                                        <span id="salary_deduction" class="capitalize-text"></span>
                                        <button type="button" class="btn btn-sm btn-link text-primary ms-1 p-0 align-middle" id="profileDeductionInfoBtn" title="Why is this amount deducted?" style="display:none;">
                                            <i class="mdi mdi-information-outline" style="font-size:1.2rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-check-circle-outline me-1" style="color: #E66136;"></i> Payment Status</td>
                                    <td><span id="payment_status" class="capitalize-text"></span></td>
                                </tr>
                                <tr>
                                    <td><i class="mdi mdi-calendar-outline me-1" style="color: #E66136;"></i> Payment Date</td>
                                    <td><span id="payment_date" class="capitalize-text"></span></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Deduction breakdown modal (same calculation as Manage Salary & Add Payroll) -->
                        <div class="modal fade" id="profileDeductionBreakdownModal" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="profileDeductionBreakdownModalLabel"><i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="profileDeductionBreakdownLoading" style="display:block;">Loading...</div>
                                        <div id="profileDeductionBreakdownContent" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="net-salary-card text-center">
                            <i class="fas fa-wallet me-2"></i> Net Salary: <span id="net_salary"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-end p-3">
                    <a href="<?= site_url(
                        "/payrollview",
                    ) ?>" class="btn hr-btnbg interviewsmbtn">
                        <i class="fas fa-arrow-left me-1 iconfontsize"></i> Back
                    </a>
                </div>
                <div id="error-message" class="text-danger text-center mb-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        const userId = window.location.pathname.split('/').pop();
        console.log("Payroll Record ID:", userId);

        $.ajax({
            url: `<?= site_url("payroll/details") ?>/${userId}`,
            type: "GET",
            headers: {
                'Authorization': `Bearer ${token}`
            },
            success: function(response) {
                if (response.status === 'success') {
                    const d = response.data;
                    const salary_above_tax = response.salary_above_tax;
                    const tax = response.tax;
                    // console.log(d);


                    // Employee Details
                    $("#employeeName").text(d.firstname + ' ' + d.lastname);
                    $("#employee_id").text(d.employee_id ?? 'N/A');
                    $("#employeeEmail").text(d.email ?? 'N/A');
                    $("#employeeDesignation").text(d.designation ?? 'N/A');

                    const profileImg = d.profile_image ?
                        "<?= base_url("upload/") ?>" + d.profile_image :
                        "<?= base_url(
                            env("ImagePath") . "upload/default-profile.jpg",
                        ) ?>";
                    $("#employeePhoto").attr("src", profileImg);

                    // Salary Details
                    $("#salary_amount").text(`₹${parseFloat(d.salary_amount || 0).toFixed(2)}`);
                    $("#month_year").text(d.month_year ?? 'N/A');
                    $("#total_leaves").text(d.total_leaves ?? '0');
                    $("#total_paid_leaves").text(d.total_paid_leaves ?? '0');
                    $("#total_hald_day_leaves").text(d.total_half_day ?? '0');
                    $("#leave_type").text(d.leave_type ?? 'N/A');
                    $("#remaining_paid_leaves").text(d.remaining_paid_leaves ?? '0');
                    $("#used_paid_leaves").text(d.used_paid_leaves ?? '0');
                    $("#unpaid_leaves").text('N/A'); // Not stored
                    $("#working_days").text('N/A'); // Not stored
                    $("#worked_hours").text(d.worked_hours ? d.worked_hours + ' hours' : 'N/A');
                    $("#overtime_hours").text(d.total_overtime_hours ? d.total_overtime_hours + ' hours' : 'N/A');
                    $("#overtime_pay").text(d.overtime_pay ? `+₹${parseFloat(d.overtime_pay).toFixed(2)}` : 'N/A');
                    $("#bonuses").text(d.bonuses ? `+₹${parseFloat(d.bonuses).toFixed(2)}` : 'N/A');

                    let taxDeduction = d.tax_deduction || 0;
                    $("#tax_deduction").text(taxDeduction ? `-₹${parseFloat(taxDeduction).toFixed(2)}` : 'N/A');
                    $("#salary_deduction").text(d.salary_deduction ? `-₹${parseFloat(d.salary_deduction).toFixed(2)}` : 'N/A');
                    // $("#tax_deduction").text(d.tax_deduction ? `-₹${parseFloat(d.tax_deduction).toFixed(2)}` : 'N/A');
                    $("#payment_status").text(d.payment_status ?? 'Pending');
                    $("#payment_date").text(d.payment_date ?? 'N/A');
                    $("#net_salary").text(`₹${parseFloat(d.net_salary || 0).toFixed(2)}`);

                    // Show deduction breakdown button and store ids for same calculation as group/single
                    if (d.user_id && d.month_year) {
                        $('#profileDeductionInfoBtn').data('user-id', d.user_id).data('month-year', d.month_year).show();
                    }

                    // Bank Details
                    $("#acc_in_name").text(d.acc_in_name ?? 'N/A');
                    $("#bank_name").text(d.bank_name ?? 'N/A');
                    $("#ifsc_code").text(d.ifsc_code ?? 'N/A');
                    $("#acc_number").text(d.acc_number ?? 'N/A');
                    $("#branch_name").text(d.branch_name ?? 'N/A');
                    $("#branch_code").text(d.branch_code ?? 'N/A');
                } else {
                    $("#error-message").text("Error fetching payroll details.").show();
                }
            },
            error: function() {
                $("#error-message").text("An error occurred while fetching data.").show();
            }
        });

        // Deduction breakdown – touchend (mobile) and click (desktop)
        var profileDeductionLastTouch = 0;
        function openProfileDeductionModal() {
            const userId = $('#profileDeductionInfoBtn').data('user-id');
            const month = $('#profileDeductionInfoBtn').data('month-year');
            if (!userId || !month) return;
            const modal = document.getElementById('profileDeductionBreakdownModal');
            const loading = document.getElementById('profileDeductionBreakdownLoading');
            const content = document.getElementById('profileDeductionBreakdownContent');
            if (!modal) return;
            document.getElementById('profileDeductionBreakdownModalLabel').innerHTML = '<i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted?';
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
                success: function(res) {
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
                            d.leaves.dates.forEach(function(l) { html += '<li>' + (l.label || l.date) + '</li>'; });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.leaves.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.absent && d.absent.dates && d.absent.dates.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Absent</strong><ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                        d.absent.dates.forEach(function(a) { html += '<li>' + (a.label || a.date) + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.absent.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.half_day && d.half_day.count > 0) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Half-day</strong>';
                        if (d.half_day.dates && d.half_day.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.half_day.dates.forEach(function(h) { html += '<li>' + (h.label || h.date) + '</li>'; });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.half_day.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.late && d.late.list && d.late.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Late arrival</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.late.list.forEach(function(l) { html += '<li>' + (l.label || l.date) + ' – ' + (l.late_text || l.late_minutes + ' min') + '</li>'; });
                        html += '</ul><span class="text-danger">Deduction: ₹' + (d.late.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.overtime && d.overtime.list && d.overtime.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-success">Overtime</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.overtime.list.forEach(function(o) { html += '<li>' + (o.label || o.date) + ' – ' + (o.overtime_text || o.overtime_hours + 'h') + '</li>'; });
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
                error: function() {
                    loading.style.display = 'none';
                    content.innerHTML = '<p class="text-danger">Failed to load details.</p>';
                    content.style.display = 'block';
                }
            });
        }
        $(document).on('touchend', '#profileDeductionInfoBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            profileDeductionLastTouch = Date.now();
            openProfileDeductionModal();
        });
        $(document).on('click', '#profileDeductionInfoBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (Date.now() - profileDeductionLastTouch < 400) return;
            openProfileDeductionModal();
        });
    });
</script>

<?= $this->endSection() ?>
