<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
    }

    @media (max-width: 767px) {
        .attendenceall {
            padding: 6px !important;
            margin-bottom: 5px !important;
        }

        .iconfontsize {
            font-size: 11px !important;
        }

        .cart-sm-title {
            font-size: 12px !important;
            margin-bottom: 5px !important;
        }

        .dataTables_length {
            margin-left: 1rem !important;
            margin-bottom: .5rem !important;
            font-size: 12px !important;
            float: left !important;
        }

        .dataTables_filter {
            font-size: 12px !important;
            float: left !important;
        }

        .interviewsmbtn {
            font-size: 12px !important;
            padding: 8px !important;
            margin-top: 10px !important;
        }
    }

    /* Salary deduction info icon - match input-group addon style */
    .input-group-append .btn-deduction-info-single,
    .input-group .btn-deduction-info-single {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-left: 0;
        color: #E66136;
        padding: 0.375rem 0.65rem;
        border-radius: 0 0.25rem 0.25rem 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .input-group .btn-deduction-info-single:hover {
        background-color: #e9ecef;
        color: #d35400;
        border-color: #ced4da;
    }

    .input-group .btn-deduction-info-single i {
        font-size: 1.1rem;
    }

    /* Single border between input and button */
    #salary_deduction {
        border-right: 0;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Add Payroll</h4>
                    <a href="/payrollview" class="btn hr-btnbg attendenceall">
                        All Employee
                    </a>
                </div>

                <form class="form-sample" method="POST" id="payrollForm">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" id="enable_payroll" name="enable_payroll">
                    <input type="hidden" id="payroll_type" name="payroll_type">
                    <input type="hidden" id="working_hours_per_day" name="working_hours_per_day">
                    <input type="hidden" id="worked_hours" name="worked_hours">
                    <input type="hidden" id="overtime_pay" name="overtime_pay">
                    <input type="hidden" id="total_overtime_hours" name="total_overtime_hours">
                    <input type="hidden" id="half_day_hours" name="half_day_hours">
                    <input type="hidden" id="sunday_off" name="sunday_off">
                    <input type="hidden" id="sunday_pay_type" name="sunday_pay_type">
                    <input type="hidden" id="saturday_off_enabled" name="saturday_off_enabled">
                    <input type="hidden" id="saturday_off_type" name="saturday_off_type">
                    <input type="hidden" id="saturday_off_pattern" name="saturday_off_pattern">
                    <input type="hidden" id="saturday_pay_type" name="saturday_pay_type">
                    <input type="hidden" id="enable_tax" name="enable_tax">
                    <input type="hidden" id="tax_type" name="tax_type">
                    <!-- Employee Details Section -->
                    <h5 class="mb-3">Employee Details</h5>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Employee Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                </div>
                                <select class="form-select" name="user_id" id="user_id">
                                    <option value="" disabled selected>Select Employee</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?= $employee["id"] ?>"><?= esc($employee["username"], "html") ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Salary Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="number" class="form-control" name="salary_amount" id="salary_amount"
                                    placeholder="Salary Amount" value="0" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Month & Year</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-calendar-month fs-5"></i></span>
                                </div>
                                <input type="month" class="form-control" name="month_year" id="month_year" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Total Leaves Taken</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                </div>
                                <input type="number" step="0.5" min="0" class="form-control" id="total_leaves"
                                    name="total_leaves" value="0" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Half-Day Leaves Taken</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                </div>
                                <input type="number" step="0.5" min="0" class="form-control" id="total_halfday_leaves"
                                    name="total_halfday_leaves" value="0" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group" id="paid_leave_section" style="display: none;">
                            <label class="form-label">Did you take any paid leave?</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                </div>
                                <select class="form-select" id="include_paid_leave">
                                    <option value="" disabled selected>Select Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Paid Leave Section -->
                    <div id="paidLeaveSection" style="display:none;" class="mt-4">
                        <h5 class="mb-3">Paid Leave Details</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">Leave Type</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-tag-multiple fs-5"></i></span>
                                    </div>
                                    <select class="form-select" id="leave_type" name="leave_type">
                                        <option value="" disabled selected>Select Leave Type</option>
                                        <?php foreach ($leaveTypes as $type): ?>
                                            <option value="<?= $type[
                                                "id"
                                            ] ?>"><?= esc(
                                                 $type["leave_type"],
                                             ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">Total Paid Leaves</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                    </div>
                                    <input type="number" id="total_paid_leaves" name="total_paid_leaves"
                                        class="form-control" value="0" readonly>
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">Remaining Paid Leaves</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i
                                                class="mdi mdi-calendar-clock fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="remaining_paid_leaves"
                                        name="remaining_paid_leaves" readonly />
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="form-label">Used Paid Leaves</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="used_paid_leaves"
                                        name="used_paid_leaves" value="0" />
                                </div>
                                <div class="col-md-6 form-group mb-0 d-none" id="halfDayInfo">
                                    <label class="badge form-label fw-bold p-0 py-0 text-success">Use Paid Leave for
                                        Half-Day Absences. eg. 0.5 OR 1.5 OR 2.5 </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Breakdown Section -->
                    <h5 class="mb-3 mt-4">Salary Breakdown</h5>
                    <div class="row">
                        <!-- Payroll Type Specific Info -->
                        <div class="col-md-6 form-group" id="payrollTypeInfo" style="display:none;">
                            <label class="form-label" id="payrollTypeLabel"></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-information fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="payrollTypeValue" readonly />
                            </div>
                        </div>
                        <div class="col-md-6 form-group" id="workedHoursInfo" style="display:none;">
                            <label class="form-label">Worked Hours</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="worked_hours_display" readonly />
                            </div>
                        </div>
                        <div class="col-md-6 form-group" id="perHourRateInfo" style="display:none;">
                            <label class="form-label">Per Hour Rate</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="per_hour_rate_display" readonly />
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Working Days</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-calendar-week fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="working_days_display" readonly />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Unpaid Leaves</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i
                                            class="mdi mdi-calendar-remove-outline fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="unpaid_leaves_display" readonly />
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Tax Deduction</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="number" class="form-control" name="tax_deduction" id="tax_deduction"
                                    value="200" />
                                <input type="hidden" id="salary_above_tax" name="salary_above_tax">
                            </div>
                        </div>

                        <!-- Overtime Information (Not used) -->
                        <div class="col-md-6 form-group overtime-info" style="display: none !important;">
                            <!-- <label class="form-label">Total Overtime Hours</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-clock-plus fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="display_overtime_hours" value="0"
                                    readonly />
                            </div> -->
                        </div>

                        <div class="col-md-6 form-group overtime-info" style="display: none !important;">
                            <!-- <label class="form-label">Overtime Pay</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="text" class="form-control" id="display_overtime_pay" value="0" readonly />
                            </div> -->
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Salary Deduction</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="number" class="form-control" name="salary_deduction" id="salary_deduction"
                                    value="0" readonly />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-deduction-info-single"
                                        title="Why is this amount deducted?" id="btn_deduction_info"
                                        aria-label="View deduction details">
                                        <i class="mdi mdi-information-outline"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-md-6 form-group">
                            <label class="form-label">Bonuses</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="number" class="form-control" name="bonuses" id="bonuses" value="0" />
                            </div>
                        </div> -->

                        <div class="col-md-6 form-group">
                            <label class="form-label">Net Salary</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                </div>
                                <input type="number" class="form-control" name="net_salary" id="net_salary" value="0" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Payment Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                </div>
                                <input type="date" class="form-control" name="payment_date" id="payment_date" />
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="form-label">Payment Status</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                </div>
                                <select class="form-select" name="payment_status" id="payment_status">
                                    <option value="" disabled selected>Select Payment Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn hr-btnbg interviewsmbtn">Submit</button>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- Deduction breakdown modal (same as group salary-details) -->
<div class="modal fade" id="deductionBreakdownModalSingle" tabindex="-1"
    aria-labelledby="deductionBreakdownModalSingleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#E66136;color:white;">
                <h5 class="modal-title" id="deductionBreakdownModalSingleLabel">
                    <i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="deductionBreakdownBodySingle">
                <div class="text-center py-4" id="deductionBreakdownLoadingSingle">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0">Loading details...</p>
                </div>
                <div id="deductionBreakdownContentSingle" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const payroll_type = new Date();
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const currentMonth = `${year}-${month}`;

    document.getElementById('month_year').value = currentMonth;
    document.getElementById('month_year').setAttribute('max', currentMonth);

    $(document).ready(function () {
        function getDaysInCurrentMonth() {
            const monthYear = $('#month_year').val();
            if (!monthYear) return 30;

            const [year, month] = monthYear.split('-');
            return new Date(year, month, 0).getDate();
        }

        function togglePaidLeaveSection() {
            const leaves = parseFloat($('#total_leaves').val()) || 0;
            const halfDays = parseFloat($('#total_halfday_leaves').val()) || 0;
            
            if (leaves > 0 || halfDays > 0) {
                $('#paid_leave_section').slideDown();
            } else {
                $('#paid_leave_section').slideUp();
                $('#include_paid_leave').val('');
                $('#paidLeaveSection').slideUp();
            }
        }

        $('#salary_amount, #tax_deduction, #bonuses, #used_paid_leaves, #total_leaves, #total_halfday_leaves').on('input', function() {
            calculateNetSalary();
            if (this.id === 'total_leaves' || this.id === 'total_halfday_leaves') {
                togglePaidLeaveSection();
            }
        });

        $('#include_paid_leave').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#paidLeaveSection').slideDown();
            } else {
                $('#paidLeaveSection').slideUp();
                $('#total_paid_leaves, #used_paid_leaves, #remaining_paid_leaves').val('');
                calculateNetSalary();
            }
        });

        $('#leave_type').on('change', function () {
            const leaveId = $(this).val();
            const userId = $('#user_id').val();
            const monthYear = $('#month_year').val();

            if (!leaveId || !userId || !monthYear) return;

            $.ajax({
                url: '<?= base_url("api/payroll/get-leave-details") ?>',
                type: 'POST',
                data: {
                    leave_id: leaveId,
                    user_id: userId,
                    month_year: monthYear
                },
                success: function (response) {
                    const allowHalfDay = response.allow_half_day || false;
                    const usedPaidLeavesInput = $('#used_paid_leaves');

                    if (allowHalfDay) {
                        $("#halfDayInfo").removeClass("d-none");
                        usedPaidLeavesInput.attr('step', '0.5');
                        usedPaidLeavesInput.attr('min', '0');
                    } else {
                        $("#halfDayInfo").addClass("d-none");
                        usedPaidLeavesInput.attr('step', '1');
                        usedPaidLeavesInput.attr('min', '0');
                    }

                    usedPaidLeavesInput.attr('data-allow-half-day', allowHalfDay);

                    $('#total_paid_leaves').val(response.total_leaves);

                    const monthlyLeaves = parseFloat($('#total_leaves').val()) || 0;
                    const remainingAvailable = response.remaining_leaves;

                    // Suggest using as many paid leaves as possible to cover monthly leaves taken
                    const suggestedUsed = isEditMode ? (response.used_leaves || 0) : Math.min(monthlyLeaves, remainingAvailable);

                    $('#used_paid_leaves').val(suggestedUsed);

                    // Update remaining leaves display: Initial Remaining - what we are using now
                    const finalRemaining = remainingAvailable - suggestedUsed;
                    $('#remaining_paid_leaves').val(finalRemaining.toFixed(allowHalfDay ? 1 : 0));
                    $('#remaining_paid_leaves').attr('data-ogvalue', remainingAvailable);

                    calculateNetSalary();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch leave details',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                }
            });
        });

        $('#used_paid_leaves').on('input', function () {
            const allowHalfDay = $(this).attr('data-allow-half-day') === 'true';
            const ogValue = parseFloat($('#remaining_paid_leaves').attr('data-ogvalue')) || 0;
            let currentValue = parseFloat(this.value) || 0;

            // If half day is not allowed, ensure only integer values
            if (!allowHalfDay) {
                currentValue = Math.floor(currentValue);
                if (this.value !== currentValue.toString() && this.value !== "") {
                    this.value = currentValue;
                }
            }

            if (currentValue > ogValue) {
                // If user enters more than available, cap it
                currentValue = ogValue;
                this.value = ogValue;
            }

            const remaining = ogValue - currentValue;
            $('#remaining_paid_leaves').val(remaining.toFixed(allowHalfDay ? 1 : 0));
        });

        const token = localStorage.getItem('token');
        let isEditMode = false;
        let payrollId = null;
        let isLoadingEditData = false; // Flag to prevent recalculation during initial load

        $('#payrollForm').on('submit', function (e) {
            e.preventDefault();

            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            let url = isEditMode
                ? `<?= base_url("api/payroll/update/") ?>${payrollId}`
                : `<?= base_url("api/payroll/create") ?>`;

            let formData = new FormData(this);
            const csrfName = $('meta[name="csrf-token"]').attr('data-name');
            const csrfHash = $('meta[name="csrf-token"]').attr('content');
            formData.append(csrfName, csrfHash);

            $('#loader').show();

            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#loader').hide();

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        }).then(() => {
                            window.location.href = '/payrollview';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    }
                },
                error: function (xhr) {
                    $('#loader').hide();

                    let message = xhr.responseJSON?.message || 'An error occurred';

                    if (typeof message === 'object') {
                        $.each(message, function (key, value) {
                            let inputField = $(`[name="${key}"]`);
                            if (inputField.length) {
                                inputField.addClass('is-invalid');
                                inputField.after(`<div class="invalid-feedback">${value}</div>`);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            buttonsStyling: false,
                            customClass: { confirmButton: 'hr-btnbg' }
                        });
                    }
                }
            });
        });

        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            const tempId = pathParts[pathParts.length - 1];
            if (!isNaN(tempId) && !isNaN(parseFloat(tempId))) {
                Id = tempId;
            }
        }

        if (Id) {
            payrollId = Id;
            isEditMode = true;
            isLoadingEditData = true; // Set flag to prevent recalculation
            $('.card-title').text('Edit Payroll');

            // Fetch existing payroll data
            $.ajax({
                url: `<?= base_url("api/payroll/") ?>${Id}`,
                type: 'GET',
                headers: { 'Authorization': `Bearer ${token}` },
                success: function (responseData) {
                    if (responseData.status === 'success' && responseData.data.length > 0) {
                        const payroll = responseData.data[0];

                        // Store original payroll data to prevent overwriting
                        const originalPayroll = { ...payroll };

                        // Populate basic form fields
                        $('#id').val(payroll.id || '');
                        $('#user_id').val(payroll.user_id || '');
                        $('#salary_amount').val(payroll.salary_amount || '');
                        $('#month_year').val(payroll.month_year || '');
                        $('#total_leaves').val(payroll.total_leaves || 0);
                        $('#total_halfday_leaves').val(payroll.total_half_day || 0);
                        $('#tax_deduction').val(payroll.tax_deduction || 0);
                        $('#salary_deduction').val(payroll.salary_deduction || 0);
                        $('#bonuses').val(payroll.bonuses || 0);
                        $('#net_salary').val(payroll.net_salary || 0);
                        $('#payment_date').val(payroll.payment_date || '');
                        $('#payment_status').val(payroll.payment_status || '');

                        // Populate hidden fields if they exist
                        if (payroll.leave_type) {
                            $('#leave_type').val(payroll.leave_type);
                            // Fetch leave type details to set step attribute without overwriting values
                            if ($('#user_id').val() && $('#month_year').val()) {
                                $.ajax({
                                    url: '<?= base_url(
                                        "api/payroll/get-leave-details",
                                    ) ?>',
                                    type: 'POST',
                                    data: {
                                        leave_id: payroll.leave_type,
                                        user_id: $('#user_id').val(),
                                        month_year: $('#month_year').val()
                                    },
                                    success: function (response) {
                                        // Set step attribute based on allow_half_day
                                        const allowHalfDay = response.allow_half_day || false;
                                        const usedPaidLeavesInput = $('#used_paid_leaves');

                                        if (allowHalfDay) {
                                            usedPaidLeavesInput.attr('step', '0.5');
                                            usedPaidLeavesInput.attr('min', '0');
                                        } else {
                                            usedPaidLeavesInput.attr('step', '1');
                                            usedPaidLeavesInput.attr('min', '0');
                                        }

                                        // Store allow_half_day in a data attribute for validation
                                        usedPaidLeavesInput.attr('data-allow-half-day', allowHalfDay);

                                        // Don't overwrite existing values, just update step
                                    }
                                });
                            }
                        }
                        if (payroll.used_paid_leaves) {
                            $('#used_paid_leaves').val(payroll.used_paid_leaves);
                        }
                        if (payroll.total_paid_leaves) {
                            $('#total_paid_leaves').val(payroll.total_paid_leaves);
                        }
                        if (payroll.remaining_paid_leaves !== null && payroll.remaining_paid_leaves !== undefined) {
                            $('#remaining_paid_leaves').val(payroll.remaining_paid_leaves);
                            $('#remaining_paid_leaves').attr('data-ogvalue', payroll.remaining_paid_leaves);
                        }
                        if (payroll.worked_hours) {
                            $('#worked_hours').val(payroll.worked_hours);
                        }
                        if (payroll.total_overtime_hours) {
                            $('#total_overtime_hours').val(payroll.total_overtime_hours);
                        }
                        if (payroll.overtime_pay) {
                            $('#overtime_pay').val(payroll.overtime_pay);
                        }

                        // Handle paid leave section
                        if (payroll.leave_type && payroll.used_paid_leaves > 0) {
                            $('#include_paid_leave').val('yes');
                            $('#paidLeaveSection').show();
                            $('#paid_leave_section').show();
                        } else if (payroll.total_leaves > 0) {
                            $('#paid_leave_section').show();
                        }

                        // Handle overtime display for existing records
                        if (payroll.overtime_pay && parseFloat(payroll.overtime_pay) > 0) {
                            $('#display_overtime_hours').val((payroll.total_overtime_hours || 0) + ' hours');
                            $('#display_overtime_pay').val('₹' + parseFloat(payroll.overtime_pay).toFixed(2));
                            $('.overtime-info').show();
                        } else {
                            $('.overtime-info').hide();
                        }

                        // Load company rules and employee data (without triggering full recalculation)
                        const userId = payroll.user_id;
                        if (userId) {
                            fetch(`<?= base_url(
                                "api/get-salary",
                            ) ?>?user_id=${userId}`)
                                .then(response => response.json())
                                .then(data => {
                                    // Store company rules in hidden fields (but don't overwrite saved salary)
                                    $('#payroll_type').val(data.company_rules.payroll_type ?? 'monthly');
                                    $('#enable_payroll').val(data.company_rules.enable_payroll ?? 1);
                                    $('#working_hours_per_day').val(data.company_rules.working_hours_per_day ?? 8);
                                    $('#half_day_hours').val(data.company_rules.half_day_hours ?? 4);
                                    $('#sunday_off').val(data.company_rules.sunday_off ?? 1);
                                    $('#sunday_pay_type').val(data.company_rules.sunday_pay_type ?? 'unpaid');
                                    $('#saturday_off_enabled').val(data.company_rules.saturday_off_enabled ?? 0);
                                    $('#saturday_off_type').val(data.company_rules.saturday_off_type ?? 'all');
                                    $('#saturday_off_pattern').val(data.company_rules.saturday_off_pattern ?? '');
                                    $('#saturday_pay_type').val(data.company_rules.saturday_pay_type ?? 'unpaid');
                                    $('#enable_tax').val(data.company_rules.enable_tax ?? 1);
                                    $('#tax_type').val(data.company_rules.tax_type ?? 'percentage');
                                    $('#salary_above_tax').val(data.salary_above_tax ?? 12000);

                                    // Update working days display based on month_year
                                    const monthYear = $('#month_year').val();
                                    if (monthYear) {
                                        const [year, month] = monthYear.split('-');
                                        $.ajax({
                                            url: '<?= base_url(
                                                "api/payroll/get-working-days",
                                            ) ?>',
                                            method: 'POST',
                                            data: { year: year, month: month },
                                            dataType: 'json',
                                            success: function (res) {
                                                $('#working_days_display').val(res.working_days || 0);

                                                // Update unpaid leaves display
                                                const totalLeaves = parseFloat($('#total_leaves').val()) || 0;
                                                const usedPaidLeaves = parseFloat($('#used_paid_leaves').val()) || 0;
                                                const unpaidLeaves = Math.max(totalLeaves - usedPaidLeaves, 0);
                                                $('#unpaid_leaves_display').val(unpaidLeaves);

                                                // Update payroll type specific displays
                                                const payrollType = data.company_rules.payroll_type ?? 'monthly';
                                                if (payrollType === 'hourly') {
                                                    $('#payrollTypeInfo').show();
                                                    $('#payrollTypeLabel').text('Payroll Type');
                                                    $('#payrollTypeValue').val('Hourly');
                                                    $('#workedHoursInfo').show();
                                                    $('#worked_hours_display').val(originalPayroll.worked_hours || 0);
                                                    const perHourRate = parseFloat(originalPayroll.salary_amount) / (parseFloat(res.working_days || 30) * parseFloat(data.company_rules.working_hours_per_day || 8));
                                                    $('#perHourRateInfo').show();
                                                    $('#per_hour_rate_display').val(perHourRate.toFixed(2));
                                                } else if (payrollType === 'daily') {
                                                    $('#payrollTypeInfo').show();
                                                    $('#payrollTypeLabel').text('Payroll Type');
                                                    $('#payrollTypeValue').val('Daily');
                                                    $('#workedHoursInfo').hide();
                                                    $('#perHourRateInfo').hide();
                                                } else {
                                                    $('#payrollTypeInfo').show();
                                                    $('#payrollTypeLabel').text('Payroll Type');
                                                    $('#payrollTypeValue').val('Monthly');
                                                    $('#workedHoursInfo').hide();
                                                    $('#perHourRateInfo').hide();
                                                }
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching user data:', error);
                                });
                        }

                        // Mark initial load as complete
                        isLoadingEditData = false;
                    }
                },
                error: function () {
                    isLoadingEditData = false; // Reset flag on error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to load payroll data',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                }
            });
        }

        document.getElementById('user_id').addEventListener('change', function () {
            const userId = this.value;

            if (!userId) return;

            $('#loader').show();

            fetch(`<?= base_url("api/get-salary") ?>?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    // In edit mode, don't overwrite saved salary_amount
                    if (!isEditMode) {
                        $('#salary_amount').val(data.salary ?? '');
                    }

                    // Set tax deduction (can be updated in edit mode)
                    $('#tax_deduction').val(data.tax ?? 200).data('original-tax', data.tax ?? 200);
                    $('#salary_above_tax').val(data.salary_above_tax ?? 12000);

                    // Store company rules in hidden fields
                    $('#payroll_type').val(data.company_rules.payroll_type ?? 'monthly');
                    $('#enable_payroll').val(data.company_rules.enable_payroll ?? 1);
                    $('#working_hours_per_day').val(data.company_rules.working_hours_per_day ?? 8);
                    $('#half_day_hours').val(data.company_rules.half_day_hours ?? 4);
                    $('#sunday_off').val(data.company_rules.sunday_off ?? 1);
                    $('#sunday_pay_type').val(data.company_rules.sunday_pay_type ?? 'unpaid');
                    $('#saturday_off_enabled').val(data.company_rules.saturday_off_enabled ?? 0);
                    $('#saturday_off_type').val(data.company_rules.saturday_off_type ?? 'all');
                    $('#saturday_off_pattern').val(data.company_rules.saturday_off_pattern ?? '');
                    $('#saturday_pay_type').val(data.company_rules.saturday_pay_type ?? 'unpaid');
                    $('#enable_tax').val(data.company_rules.enable_tax ?? 1);
                    $('#tax_type').val(data.company_rules.tax_type ?? 'percentage');

                    // Trigger month change to load attendance data (only if not in edit mode to avoid overwriting)
                    if ($('#month_year').val() && !isEditMode) {
                        $('#month_year').trigger('change');
                    } else if ($('#month_year').val() && isEditMode) {
                        // In edit mode, just update working days without recalculating everything
                        const monthYear = $('#month_year').val();
                        const [year, month] = monthYear.split('-');
                        $.ajax({
                            url: '<?= base_url(
                                "api/payroll/get-working-days",
                            ) ?>',
                            method: 'POST',
                            data: { year: year, month: month },
                            dataType: 'json',
                            success: function (res) {
                                $('#working_days_display').val(res.working_days || 0);
                            }
                        });
                    }

                    $('#loader').hide();
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                    $('#loader').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load employee data',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                });
        });

        $('#user_id, #month_year').on('change', function () {
            // Skip if we're in the middle of loading edit data
            if (isLoadingEditData) return;

            const userId = $('#user_id').val();
            const monthYear = $('#month_year').val();

            if (!userId || !monthYear) return;

            const [year, month] = monthYear.split('-');
            const payrollType = $('#payroll_type').val();

            $('#loader').show();

            // Fetch leaves and half-days
            $.ajax({
                url: '<?= base_url("api/payroll/get-monthly-leaves") ?>',
                method: 'POST',
                data: { user_id: userId, year: year, month: month },
                dataType: 'json',
                success: function (res) {
                    const leaves = res.total_leaves || 0;
                    const halfDays = res.total_half_day_leaves || 0;

                    $('#total_leaves').val(leaves);
                    $('#total_halfday_leaves').val(halfDays);

                    togglePaidLeaveSection();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch leave data',
                        buttonsStyling: false,
                        customClass: { confirmButton: 'hr-btnbg' }
                    });
                }
            });

            // If hourly payroll, fetch worked hours
            if (payrollType === 'hourly') {
                $.ajax({
                    url: '<?= base_url("api/payroll/get-worked-hours") ?>',
                    method: 'POST',
                    data: { user_id: userId, year: year, month: month },
                    dataType: 'json',
                    success: function (res) {
                        $('#worked_hours').val(res.total_worked_hours || 0);
                        $('#total_overtime_hours').val(res.total_overtime_hours || 0);

                        // Show overtime info if overtime hours exist
                        if (res.total_overtime_hours && parseFloat(res.total_overtime_hours) > 0) {
                            $('#display_overtime_hours').val(res.total_overtime_hours + ' hours');
                            $('.overtime-info').show();
                        } else {
                            $('.overtime-info').hide();
                        }

                        calculateNetSalary();
                    },
                    error: function () {
                        console.error('Failed to fetch worked hours');
                    }
                });
            }

            // Fetch working days (same logic as group: holidays/Sundays excluded)
            $.ajax({
                url: '<?= base_url("api/payroll/get-working-days") ?>',
                method: 'POST',
                data: { year: year, month: month },
                dataType: 'json',
                success: function (res) {
                    $('#loader').hide();
                    $('#working_days_display').val(res.working_days || 0);
                    calculateNetSalary();
                },
                error: function () {
                    $('#loader').hide();
                    console.error('Failed to fetch working days');
                    calculateNetSalary();
                }
            });
        });

        function calculateNetSalary() {
            const userId = $('#user_id').val();
            const monthYear = $('#month_year').val();
            const baseSalary = parseFloat($('#salary_amount').val()) || 0;
            const bonuses = parseFloat($('#bonuses').val()) || 0;
            const payrollType = $('#payroll_type').val() || 'monthly';
            const usedPaidLeaves = parseFloat($('#used_paid_leaves').val()) || 0;
            if (!userId || !monthYear || baseSalary === 0) return;
            const [year, month] = monthYear.split('-');
            $.ajax({
                url: '<?= base_url("api/payroll/calculate-payroll") ?>',
                method: 'POST',
                data: {
                    user_id: userId,
                    year: year,
                    month: month,
                    payroll_type: payrollType,
                    salary_amount: baseSalary,
                    usedPaidLeaves: usedPaidLeaves,
                    total_leaves: parseFloat($('#total_leaves').val()) || 0,
                    total_halfday_leaves: parseFloat($('#total_halfday_leaves').val()) || 0
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status !== 'success') return;

                    const data = res.data;
                    // Tax from backend calculation
                    const calculatedTax = parseFloat(data.tax_deduction);
                    // If calculated tax is 0 and it's not a manual overwrite, use 200 as default
                    const finalTax = (calculatedTax > 0) ? calculatedTax : 200;
                    $('#tax_deduction').val(finalTax);

                    $('#working_days_display').val(data.working_days || 0);
                    $('#unpaid_leaves_display').val(data.unpaid_leaves || 0);

                    // Payroll type specific info (hourly/daily/monthly)
                    if (payrollType === 'hourly') {
                        $('#payrollTypeInfo').show();
                        $('#payrollTypeLabel').text('Payroll Type');
                        $('#payrollTypeValue').val('Hourly');
                        $('#workedHoursInfo').show();
                        $('#worked_hours_display').val(data.worked_hours || 0);
                        $('#perHourRateInfo').show();
                        $('#per_hour_rate_display').val(data.per_hour_salary || 0);
                        $('#presentDaysInfo').hide();
                    } else if (payrollType === 'daily') {
                        $('#payrollTypeInfo').show();
                        $('#payrollTypeLabel').text('Payroll Type');
                        $('#payrollTypeValue').val('Daily');
                        $('#presentDaysInfo').show();
                        $('#present_days_display').val(data.present_days || 0);
                        $('#workedHoursInfo').hide();
                        $('#perHourRateInfo').hide();
                    } else {
                        $('#payrollTypeInfo').show();
                        $('#payrollTypeLabel').text('Payroll Type');
                        $('#payrollTypeValue').val('Monthly');
                        $('#workedHoursInfo').hide();
                        $('#perHourRateInfo').hide();
                        $('#presentDaysInfo').hide();
                    }

                    // Overtime from backend calc (Disabled as per user request)
                    let overtimePay = 0;
                    $('#overtime_pay').val(0);
                    $('#total_overtime_hours').val(0);
                    $('.overtime-info').hide();

                    // SECOND CALL: use the same breakdown API that powers the popup
                    // so salary_deduction exactly equals the popup's total_deduction.
                    $.ajax({
                        url: '<?= base_url("api/payroll/get-deduction-breakdown") ?>',
                        method: 'POST',
                        data: {
                            user_id: userId,
                            month: monthYear,
                            salary_amount: parseFloat($('#salary_amount').val()) || 0,
                            total_leaves: parseFloat($('#total_leaves').val()) || 0,
                            total_halfday_leaves: parseFloat($('#total_halfday_leaves').val()) || 0
                        },
                        dataType: 'json',
                        success: function (breakdownRes) {
                            if (breakdownRes.status !== 'success' || !breakdownRes.data || !breakdownRes.data.summary) {
                                return;
                            }
                            const sum = breakdownRes.data.summary;
                            const rawDeduction = parseFloat(sum.total_deduction || 0);

                            // Adjust deduction for paid leaves entered on this form:
                            // each paid leave unit (1 = full day, 0.5 = half-day) covers one per-day salary.
                            const usedPaidLeavesForm = parseFloat($('#used_paid_leaves').val()) || 0;
                            const perDayFromSummary = parseFloat(sum.per_day_salary || 0) ||
                                (data.working_days > 0 ? (baseSalary / data.working_days) : 0);
                            const paidLeaveCredit = usedPaidLeavesForm * perDayFromSummary;

                            const salaryDeduction = Math.max(rawDeduction - paidLeaveCredit, 0);
                            $('#salary_deduction').val(salaryDeduction.toFixed(2));

                            // Net salary: base + overtime - adjusted_deduction - tax + bonuses
                            const tax = parseFloat($('#tax_deduction').val()) || 200;
                            const netSalary = baseSalary + overtimePay - salaryDeduction - tax + bonuses;
                            $('#net_salary').val(netSalary.toFixed(2));
                        }
                    });
                },
                error: function () {
                    // Fallback to simple calculation
                    simpleCalculateNetSalary();
                }
            });
        }

        /**
         * Fallback simple calculation (use working days from API when available, same as group view)
         */
        function simpleCalculateNetSalary() {
            const salaryAmount = parseFloat($('#salary_amount').val()) || 0;
            const bonuses = parseFloat($('#bonuses').val()) || 0;
            const usedPaidLeaves = parseFloat($('#used_paid_leaves').val()) || 0;
            const allowedPaidLeaves = parseFloat($('#total_leaves').val()) || 0;
            const halfDays = parseFloat($('#total_halfday_leaves').val()) || 0;
            const payrollType = $('#payroll_type').val() || 'monthly';
            const workingDaysDisplay = parseInt($('#working_days_display').val(), 10);
            const daysInMonth = (workingDaysDisplay > 0) ? workingDaysDisplay : getDaysInCurrentMonth();
            const perDaySalary = salaryAmount / daysInMonth;
            const perHourSalary = perDaySalary / (parseFloat($('#working_hours_per_day').val()) || 8);
            // Tax calculation
            const salaryAboveTax = parseFloat($('#salary_above_tax').val()) || 12000;
            const taxValue = parseFloat($('#tax_deduction').data('original-tax')) || 200;
            const taxDeduction = salaryAmount > salaryAboveTax ? taxValue : 200;
            $('#tax_deduction').val(taxDeduction);
            // Leave deduction - account for paid leave covering both full days and half days
            // Calculate how paid leave covers full days and half days
            const fullDaysCoveredByPaidLeave = Math.min(Math.floor(usedPaidLeaves), allowedPaidLeaves);
            const halfDaysCoveredByPaidLeave = (usedPaidLeaves - fullDaysCoveredByPaidLeave) * 2; // 0.5 = 1 half day

            // Unpaid full days
            const unpaidFullDays = Math.max(allowedPaidLeaves - fullDaysCoveredByPaidLeave, 0);
            // Unpaid half days (half days not covered by paid leave)
            const unpaidHalfDays = Math.max(halfDays - halfDaysCoveredByPaidLeave, 0);

            const halfDayDeduction = unpaidHalfDays * (perDaySalary / 2);
            const leaveDeduction = unpaidFullDays * perDaySalary;
            const totalDeduction = leaveDeduction + halfDayDeduction;
            $('#salary_deduction').val(totalDeduction.toFixed(2));
            $('#unpaid_leaves_display').val(unpaidFullDays);
            $('#working_days_display').val(daysInMonth);
            let netSalary = salaryAmount - taxDeduction - totalDeduction + bonuses;
            // Overtime is disabled
            const overtimePay = 0;
            const totalOvertimeHours = 0;
            $('.overtime-info').hide();
            // Payroll type specific info
            if (payrollType === 'hourly') {
                $('#payrollTypeInfo').show();
                $('#payrollTypeLabel').text('Payroll Type');
                $('#payrollTypeValue').val('Hourly');
                $('#workedHoursInfo').show();
                $('#worked_hours_display').val($('#worked_hours').val() || 0);
                $('#perHourRateInfo').show();
                $('#per_hour_rate_display').val(perHourSalary.toFixed(2));
                $('#presentDaysInfo').hide();
            } else if (payrollType === 'daily') {
                $('#payrollTypeInfo').show();
                $('#payrollTypeLabel').text('Payroll Type');
                $('#payrollTypeValue').val('Daily');
                $('#presentDaysInfo').show();
                $('#present_days_display').val($('#total_present_days').val() || 0);
                $('#workedHoursInfo').hide();
                $('#perHourRateInfo').hide();
            } else {
                $('#payrollTypeInfo').show();
                $('#payrollTypeLabel').text('Payroll Type');
                $('#payrollTypeValue').val('Monthly');
                $('#workedHoursInfo').hide();
                $('#perHourRateInfo').hide();
                $('#presentDaysInfo').hide();
            }
            $('#net_salary').val(Math.max(netSalary, 0).toFixed(2));
        }

        // Deduction info modal (user-wise, same as group view) – works on mobile
        $(document).on('click', '.btn-deduction-info-single, #btn_deduction_info', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const userId = $('#user_id').val();
            const month = $('#month_year').val();
            const name = $('#user_id option:selected').text() || 'Employee';
            if (!userId || !month) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select employee and month',
                    text: 'Please select an employee and month/year first.',
                    buttonsStyling: false,
                    customClass: { confirmButton: 'hr-btnbg' }
                });
                return;
            }
            const modal = document.getElementById('deductionBreakdownModalSingle');
            const loading = document.getElementById('deductionBreakdownLoadingSingle');
            const content = document.getElementById('deductionBreakdownContentSingle');
            document.getElementById('deductionBreakdownModalSingleLabel').innerHTML = '<i class="mdi mdi-information-outline me-1"></i> Why was this amount deducted? – ' + name;
            loading.style.display = 'block';
            content.style.display = 'none';
            content.innerHTML = '';
            if (modal && modal.parentNode !== document.body) {
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
                data: {
                    user_id: userId,
                    month: month
                },
                dataType: 'json',
                success: function (res) {
                    loading.style.display = 'none';
                    if (res.status !== 'success' || !res.data) {
                        content.innerHTML = '<p class="text-danger">Could not load deduction details.</p>';
                        content.style.display = 'block';
                        return;
                    }
                    const d = res.data;
                    let html = '<p class="text-muted small mb-3">' + d.employee_name + ' – ' + d.month_label + '</p>';
                    if (d.leaves && (d.leaves.count > 0 || (d.leaves.dates && d.leaves.dates.length))) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-danger">Leaves (' + (d.leaves.count || 0) + ' day(s))</strong>';
                        if (d.leaves.dates && d.leaves.dates.length) {
                            html += '<ul class="list-unstyled small mb-1" style="max-height:120px;overflow-y:auto;">';
                            d.leaves.dates.forEach(function (l) { html += '<li>' + (l.label || l.date) + (l.reason ? ' – ' + l.reason : '') + '</li>'; });
                            html += '</ul>';
                        }
                        html += '<span class="text-danger">Deduction: ₹' + (d.leaves.deduction_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.absent && d.absent.dates && d.absent.dates.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-warning">Absent (' + d.absent.dates.length + ' day(s))</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.absent.dates.forEach(function (a) { html += '<li>' + (a.label || a.date) + '</li>'; });
                        html += '</ul></div>';
                    }
                    if (d.half_day && (d.half_day.count > 0 || (d.half_day.dates && d.half_day.dates.length))) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong>Half-day (' + (d.half_day.count || 0) + ')</strong>';
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
                        html += '<div class="breakdown-section" style="border-left:3px solid #adb5bd;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-muted">Late Arrival (info only – not deducted)</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.late.list.forEach(function (l) { html += '<li class="text-muted">' + (l.label || l.date) + ' – ' + (l.late_text || l.late_minutes + ' min') + '</li>'; });
                        html += '</ul><span class="text-muted small">(No salary deduction for late arrivals)</span></div>';
                    }
                    if (d.overtime && d.overtime.list && d.overtime.list.length) {
                        html += '<div class="breakdown-section" style="border-left:3px solid #E66136;padding-left:0.75rem;margin-bottom:1rem;"><strong class="text-success">Overtime</strong><ul class="list-unstyled small" style="max-height:120px;overflow-y:auto;">';
                        d.overtime.list.forEach(function (o) { html += '<li>' + (o.label || o.date) + ' – ' + (o.overtime_text || o.overtime_hours + 'h') + '</li>'; });
                        html += '</ul><span class="text-success">Added to salary: ₹' + (d.overtime.pay_amount || 0).toFixed(2) + '</span></div>';
                    }
                    if (d.summary) {
                        html += '<hr><div class="fw-bold"><span>Total deduction (leaves + half-day):</span> <span class="text-danger">₹' + (d.summary.total_deduction || 0).toFixed(2) + '</span></div>';
                        if (d.summary.overtime_added > 0) html += '<div class="fw-bold"><span>Overtime added:</span> <span class="text-success">₹' + d.summary.overtime_added.toFixed(2) + '</span></div>';
                    }
                    if (!d.leaves?.count && !d.absent?.dates?.length && !d.half_day?.count && !d.overtime?.list?.length) {
                        html += '<p class="text-muted">No leaves, absent, or overtime in this month.</p>';
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
        });
    });
</script>
<?= $this->endSection() ?>