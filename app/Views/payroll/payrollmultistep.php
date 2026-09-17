<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
    }
</style>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Payroll</h4>

                <form class="form-sample" method="POST" id="payrollForm">
                    <input type="hidden" name="id" id="id" value="">

                    <!-- Step 1: Employee Details -->
                    <div class="step active" id="step1">
                        <h4>Step 1: Employee Details</h4>
                        <div class="row">
                            <!-- Employee Name -->
                            <div class="col-md-6">
                                <label class="form-label">Employee Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                    </div>
                                    <select class="form-select" name="user_id" id="user_id">
                                        <option value="" disabled selected>Select Employee</option>
                                        <?php foreach ($employees as $employee): ?>
                                            <option value="<?= $employee['id']; ?>"><?= esc($employee['username']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Salary Amount -->
                            <div class="col-md-6">

                                <label class="form-label">Salary Amount</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="salary_amount" id="salary_amount" placeholder="Salary Amount" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Month & Year</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-calendar-month fs-5"></i></span>
                                    </div>
                                    <input type="month" class="form-control" name="month_year" id="month_year" />
                                </div>
                            </div>
                            <!-- Total Leaves Taken -->
                            <div class="col-md-6">
                                <label class="form-label">Total Leaves Taken</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="total_leaves" name="total_leaves" readonly />
                                </div>

                            </div>
                            <div class="col-md-6 mt-3">
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
                            <div class="row">
                                <!-- Leave Type -->
                                <div class="col-md-6">
                                    <label class="form-label">Leave Type</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-tag-multiple fs-5"></i></span>
                                        </div>
                                        <select class="form-select" id="leave_type" name="leave_type">
                                            <option value="" disabled selected>Select Leave Type</option>
                                            <?php foreach ($leaveTypes as $type): ?>
                                                <option value="<?= $type['id']; ?>"><?= esc($type['leave_type']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Paid Leave Info -->
                                <div class="col-md-6">
                                    <label class="form-label">Total Paid Leaves</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                        </div>
                                        <input type="number" id="total_paid_leaves" name="total_paid_leaves" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Remaining Paid Leaves</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-clock fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="remaining_paid_leaves" name="remaining_paid_leaves" readonly />
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Used Paid Leaves</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="used_paid_leaves" name="used_paid_leaves" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn hr-btnbg next">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Leave Info -->
                    <div class="step" id="step2">
                        <h4>Step 2: Account Details</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Name In Account</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter Name In Account"
                                        name="acc_in_name" id="acc_in_name" />
                                </div>
                            </div>
                            <!-- Month & Year -->
                            <div class="col-md-6">
                                <label class="form-label">Account Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter account number"
                                        name="acc_number" id="acc_number" />
                                </div>
                            </div>

                            <!-- Total Leaves Taken -->
                            <div class="col-md-6">
                                <label class="form-label">Bank Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter bank name"
                                        name="bank_name" id="bank_name" />
                                </div>
                            </div>

                            <!-- Paid Leave Option -->
                            <div class="col-md-6 mt-3">
                                <label class="form-label">IFSC Code</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-barcode fs-5"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter IFSC Code"
                                        name="ifsc_code" id="ifsc_code" />
                                </div>
                            </div>
                        </div>

                        <!-- Paid Leave Section -->


                        <div class="text-end mt-3">
                            <button type="button" class="btn hr-btnbg prev">Previous</button>
                            <button type="button" class="btn hr-btnbg next">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Salary Info -->
                    <div class="step" id="step3">
                        <h4>Step 3: Salary Breakdown</h4>
                        <div class="row">
                            <!-- Tax Deduction -->
                            <div class="col-md-6">
                                <label class="form-label">Tax Deduction</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="tax_deduction" id="tax_deduction" />
                                </div>
                            </div>

                            <!-- Bonuses -->
                            <div class="col-md-6">
                                <label class="form-label">Bonuses</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="bonuses" id="bonuses" />
                                </div>
                            </div>

                            <!-- Net Salary -->
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Net Salary</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                    </div>
                                    <input type="number" class="form-control" name="net_salary" id="net_salary" />
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 mt-3">
                                <label class="form-label">Payment Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="payment_date" id="payment_date" />
                                </div>
                            </div>

                            <!-- Payment Status -->
                            <div class="col-md-6 mt-3">
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

                        <div class="text-end mt-3">
                            <button type="button" class="btn hr-btnbg prev">Previous</button>
                            <button type="submit" class="btn hr-btnbg">Submit</button>
                        </div>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- payrolle simple page -->
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Payroll</h4>
                <form class="form-sample" method="POST" id="payrollForm" novalidate>
                    <input type="hidden" name="id" id="id" value=""> <!-- Hidden input for id -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Employee Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-account fs-5"></i></span>
                                        </div>
                                        <select class="form-select" name="user_id" id="user_id">
                                            <option value="" disabled selected>Select Employee Name</option>
                                            <?php foreach ($employees as $employee) : ?>
                                                <option value="<?= $employee['id']; ?>"><?= esc($employee['username']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Salary Amount</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="salary_amount" id="salary_amount" placeholder="Salary Amount" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Month & Year</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-month fs-5"></i></span>
                                        </div>
                                        <input type="month" class="form-control" name="month_year" id="month_year" placeholder="Select Month" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Total Leaves Taken</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="total_leaves" name="total_leaves" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">

                                <label class="col-sm-3 col-form-label">Account Number</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter account number"
                                            name="acc_number" id="acc_number" />
                                    </div>
                                    <div class="text-danger" id="acc_number-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Bank Name</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-numeric fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Enter bank name"
                                            name="bank_name" id="bank_name" />
                                    </div>
                                    <div class="text-danger" id="bank_name-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="paid_leave_section" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Did you take any paid leave?</label>
                                <div class="col-sm-9">
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
                        </div>
                    </div>

                    <div class="row" id="paidLeaveSection" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Leave Type</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-tag-multiple fs-5"></i></span>
                                        </div>
                                        <select class="form-select" id="leave_type" name="leave_type">
                                            <option value="" disabled selected>Select Leave Type</option>
                                            <?php foreach ($leaveTypes as $type): ?>
                                                <option value="<?= $type['id']; ?>"><?= esc($type['leave_type']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Total Paid Leaves</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-airplane fs-5"></i></span>
                                        </div>
                                        <input type="number" id="total_paid_leaves" name="total_paid_leaves" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Remaining Paid Leaves</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar-clock fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="remaining_paid_leaves" name="remaining_paid_leaves" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Used Paid Leaves</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-check-circle fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="used_paid_leaves" name="used_paid_leaves" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tax Deduction</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="tax_deduction" id="tax_deduction" placeholder="Enter Tax Deduction" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Bonuses</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="bonuses" id="bonuses" placeholder="Enter Bonuses" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Net Salary</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-currency-inr fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="net_salary" id="net_salary" placeholder="Enter Net Salary" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Payment Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-calendar fs-5"></i></span>
                                        </div>
                                        <input type="date" class="form-control" name="payment_date" id="payment_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Payment Status</label>
                                <div class="col-sm-9">
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
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="<?= base_url('/payrollview'); ?>" class="btn hr-btnbg">
                            Back
                        </a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Submit</button>
                    </div>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>
<!-- end simple apge -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        let currentStep = 0;
        const steps = $(".step");

        function showStep(index) {
            steps.removeClass("active");
            steps.eq(index).addClass("active");
        }

        function displayError(element, message) {
            element.removeClass("is-valid").addClass("is-invalid");
            if (element.next(".invalid-feedback").length === 0) {
                element.after('<div class="invalid-feedback">' + message + '</div>');
            } else {
                element.next(".invalid-feedback").text(message);
            }
        }

        function clearError(element) {
            element.removeClass("is-invalid").addClass("is-valid");
            element.next(".invalid-feedback").remove();
        }

        function validateStep(stepIndex) {
            let isValid = true;
            const includePaidLeave = $('#include_paid_leave').val();

            const inputs = steps.eq(stepIndex).find("input, select");
            inputs.each(function() {
                const input = $(this);
                const value = input.val();
                const id = input.attr("id");

                // Skip hidden fields
                if (input.attr("type") === "hidden") return;

                // If the field is inside paidLeaveSection but paid leave is not selected
                if (input.closest("#paidLeaveSection").length && includePaidLeave !== "yes") {
                    return;
                }

                if (!value || value === "") {
                    isValid = false;
                    let message = "This field is required";

                    if (id === "user_id") message = "Please select an employee";
                    else if (id === "salary_amount") message = "Please enter salary amount";
                    else if (id === "month_year") message = "Please select month and year";
                    else if (id === "include_paid_leave") message = "Please select if paid leave is taken";
                    else if (id === "leave_type") message = "Please select leave type";
                    else if (id === "used_paid_leaves") message = "Please enter used paid leaves";
                    else if (id === "acc_in_name") message = "Enter name in account";
                    else if (id === "acc_number") message = "Enter account number";
                    else if (id === "bank_name") message = "Enter bank name";
                    else if (id === "ifsc_code") message = "Enter IFSC code";
                    else if (id === "tax_deduction") message = "Enter tax deduction amount";
                    else if (id === "bonuses") message = "Enter bonuses";
                    else if (id === "net_salary") message = "Enter net salary";
                    else if (id === "payment_date") message = "Select payment date";
                    else if (id === "payment_status") message = "Select payment status";

                    displayError(input, message);
                } else {
                    clearError(input);
                }
            });

            return isValid;
        }

        $(".next").click(function() {
            if (validateStep(currentStep)) {
                if (currentStep < $(".step").length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });

        $(".prev").click(function() {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });

        $('#include_paid_leave').change(function() {
            const val = $(this).val();
            $('#paidLeaveSection').toggle(val === 'yes');
        });

        $("input, select").on("input change", function() {
            const input = $(this);
            if (input.val()) {
                clearError(input);
            }
        });

        $("#payrollForm").on("submit", function(e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
            }
        });

        showStep(currentStep);
    });
</script>



<script>
    $(document).ready(function() {
        function getDaysInCurrentMonth() {
            const now = new Date();
            return new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
        }

        function calculateNetSalary() {
            let salaryAmount = parseFloat($('#salary_amount').val()) || 0;
            let taxDeduction = parseFloat($('#tax_deduction').val()) || 0;
            let bonuses = parseFloat($('#bonuses').val()) || 0;

            let usedPaidLeaves = parseFloat($('#used_paid_leaves').val()) || 0;
            let allowedPaidLeaves = parseFloat($('#total_leaves').val()) || 0;
            let remaining_paid_leaves = parseFloat($('#remaining_paid_leaves').val()) || 0;
            let totalDaysInMonth = getDaysInCurrentMonth();
            let perDaySalary = salaryAmount / totalDaysInMonth;

            let extraLeaves = Math.max(allowedPaidLeaves - usedPaidLeaves, 0);
            let leaveDeduction = extraLeaves * perDaySalary;

            let netSalary = salaryAmount - taxDeduction - leaveDeduction + bonuses;

            console.log("usedPaidLeaves:", usedPaidLeaves);
            console.log("allowedPaidLeaves:", allowedPaidLeaves);
            console.log("extraLeaves:", extraLeaves);
            console.log("perDaySalary:", perDaySalary);
            console.log("remaining_paid_leaves:", remaining_paid_leaves);
            console.log("leaveDeduction:", leaveDeduction);
            console.log("netSalary:", netSalary);

            // $('#net_salary').val(netSalary.toFixed(2));
            $('#net_salary').val(Math.round(netSalary));

            // $('#remaining_paid_leaves').val(Math.max(allowedPaidLeaves - usedPaidLeaves, 0));
        }


        // Trigger calculation on input changes
        $('#salary_amount, #tax_deduction, #bonuses, #used_paid_leaves').on('input', calculateNetSalary);

        $('#include_paid_leave').on('change', function() {
            if ($(this).val() === 'yes') {
                $('#paidLeaveSection').slideDown();
            } else {
                $('#paidLeaveSection').slideUp();
                $('#total_paid_leaves, #used_paid_leaves, #remaining_paid_leaves').val('0');
                calculateNetSalary(); // <- recalculate after clearing paid leave fields
            }
        });

        $('#leave_type').on('change', function() {
            let leaveId = $(this).val();
            let userId = $('#user_id').val();
            console.log(userId);

            let monthYear = $('#month_year').val();

            if (leaveId && userId && monthYear) {
                $.ajax({
                    url: '<?= base_url("api/payroll/get-leave-details") ?>',
                    type: 'POST',
                    data: {
                        leave_id: leaveId,
                        user_id: userId,
                        month_year: monthYear
                    },
                    success: function(response) {
                        $('#total_paid_leaves').val(response.total_leaves);
                        $('#used_paid_leaves').val(response.used_leaves);
                        $('#remaining_paid_leaves').val(response.remaining_leaves);
                        $('#remaining_paid_leaves').attr('data-ogvalue', response.remaining_leaves);
                        calculateNetSalary();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch leave details.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });

        $('#total_paid_leaves, #used_paid_leaves').on('input', function() {
            let total = parseFloat($('#total_paid_leaves').val()) || 0;
            let used = parseFloat($('#used_paid_leaves').val()) || 0;
            $('#remaining_paid_leaves').val(Math.max(total - used, 0));
            console.log(total);
            calculateNetSalary();
            if ($(this).attr('id') == 'used_paid_leaves' || $(this).attr('id') == 'total_paid_leaves') {
                let totalLeaves = parseFloat($('#total_paid_leaves').val()) || 0;
                let usedLeaves = parseFloat($('#used_paid_leaves').val()) || 0;
                let remainingLeaves = Math.max(totalLeaves - usedLeaves, 0);
                $('#remaining_paid_leaves').val(remainingLeaves);
                // console.log("value",remainingLeaves);

            }
        });

        $('#salary_amount, #tax_deduction, #bonuses, #used_paid_leaves').on('input', calculateNetSalary);
    });
</script>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        let isEditMode = false; // Flag for edit mode
        let payrollId = null; // To store payroll ID for updating

        // Handle form submission
        $('#payrollForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous validation messages
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            let url = `<?= base_url('api/payroll/create') ?>`; // Default URL for insert
            let method = 'POST'; // Default method for insert
            let formData = new FormData();

            // Gather form data
            $('#payrollForm').find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name !== undefined) {
                    const value = $(this).val();
                    formData.append(name, value !== null ? value : '');
                }
            });

            if (isEditMode) {
                url = `<?= base_url('api/payroll/update/') ?>${payrollId}`; // Update URL
                method = 'POST'; // Method for update
            }

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content-type
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000, // Auto-close after 2 seconds
                            showConfirmButton: false,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        }).then(() => {
                            window.location.href = '/payrollview'; // Redirect after success
                        });

                        $('#payrollForm')[0].reset(); // Reset the form
                        if (isEditMode) {
                            $('#submitBtn').text('Submit'); // Reset button text
                            isEditMode = false;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },

                error: function(xhr) {
                    let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.';

                    // Check if message is an object (validation errors), otherwise, show the message directly
                    if (typeof message === 'object') {
                        if (typeof displayValidationErrors === 'function') displayValidationErrors(message);
                    } else {
                        // Display general error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message, // Shows "Performance record already exists for this month."
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                }
            });
        });

        const params = new URLSearchParams(window.location.search);
        let Id = params.get('id');

        // Try extracting from URL path if not found in query params
        if (!Id) {
            const pathParts = window.location.pathname.split('/');
            var temp_id = pathParts[pathParts.length - 1];
            if (!isNaN(temp_id) && !isNaN(parseFloat(temp_id))) {
                Id = temp_id;
            }
        }

        if (Id) {
            payrollId = Id;
            fetchPayrollData(Id);
        }

        // Fetch payroll data for editing
        function fetchPayrollData(Id) {
            console.log("Fetching data for payroll ID: " + Id); // Debugging
            $.ajax({
                url: `<?= base_url('api/payroll/') ?>${Id}`,
                type: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                success: function(responseData) {
                    console.log(responseData); // Debugging: Check API response in console

                    if (responseData.status === 'success' && responseData.data.length > 0) {
                        const payroll = responseData.data[0];

                        // Populate form fields
                        $('#user_id').val(payroll.user_id);
                        $('#salary_amount').val(payroll.salary_amount);
                        $('#tax_deduction').val(payroll.tax_deduction);
                        $('#bonuses').val(payroll.bonuses);
                        $('#net_salary').val(payroll.net_salary);
                        $('#payment_date').val(payroll.payment_date);
                        $('#payment_status').val(payroll.payment_status);
                        $('#month_year').val(payroll.month_year);
                        $('#total_leaves').val(payroll.total_leaves);
                        $('#leave_type').val(payroll.leave_type);
                        $('#total_paid_leaves').val(payroll.total_paid_leaves);
                        $('#remaining_paid_leaves').val(payroll.remaining_paid_leaves);
                        $('#used_paid_leaves').val(payroll.used_paid_leaves);
                        $('#acc_in_name').val(payroll.acc_in_name);
                        $('#acc_number').val(payroll.acc_number);
                        $('#bank_name').val(payroll.bank_name);
                        $('#ifsc_code').val(payroll.ifsc_code);
                        $('#id').val(payroll.id); // Set hidden input field for ID
                        $('#submitBtn').text('Update'); // Change button text
                        $('.card-title').text('Edit Payroll');
                        isEditMode = true;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Found!',
                            text: 'Payroll record not found.',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching payroll:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error fetching payroll data.',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'hr-btnbg',

                        }
                    });
                }
            });
        }
    });
</script>
<script>
    document.getElementById('user_id').addEventListener('change', function() {
        const userId = this.value;
        const monthYear = document.getElementById('month_year').value || '';

        if (userId) {
            fetch(`<?= base_url('api/get-salary') ?>?user_id=${userId}&month_year=${monthYear}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('salary_amount').value = data.salary;
                })
                .catch(error => {
                    console.error('Error fetching salary:', error);
                });
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#include_paid_leave').on('change', function() {
            if ($(this).val() === 'yes') {
                $('#paidLeaveSection').show();
            } else {
                $('#paidLeaveSection').hide();
            }
        });


        $('#user_id, #month_year').on('change', function(e) {
            const userId = $('#user_id').val();
            const monthYear = $('#month_year').val(); // format YYYY-MM

            if (userId && monthYear) {
                if (e.target.id === 'month_year' && !isEditMode) {
                    fetch(`<?= base_url('api/get-salary') ?>?user_id=${userId}&month_year=${monthYear}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('salary_amount').value = data.salary;
                        });
                }

                const [year, month] = monthYear.split('-');

                $.ajax({
                    url: '<?= base_url("api/payroll/get-monthly-leaves"); ?>',
                    method: 'POST',
                    data: {
                        user_id: userId,
                        year: year,
                        month: month
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.total_leaves && res.total_leaves > 0) {
                            $('#total_leaves').val(res.total_leaves);
                        } else {
                            $('#total_leaves').val(0);
                            Swal.fire({
                                icon: 'info',
                                title: 'No Approved Leaves',
                                text: 'Leaves for the selected month are not approved yet.',
                                confirmButtonText: 'OK',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'hr-btnbg',

                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while fetching leaves.',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'hr-btnbg',

                            }
                        });
                    }
                });
            }
        });
});
</script>



<?= $this->endSection(); ?>