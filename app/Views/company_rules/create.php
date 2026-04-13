<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    @media (min-width: 375px) and (max-width: 667px) {
        .sm-margin {
            margin-top: 8px !important;
        }
    }

    .section-header {
        background: #f8f9fa;
        color: #495057;
        padding: 10px 15px;
        border-radius: 5px;
        margin: 15px 0 10px 0;
        font-weight: 600;
        border-left: 4px solid #ea6161;
    }

    .form-check-input:checked {
        background-color: #ea6161;
        border-color: #ea6161;
    }

    .form-label-bold {
        font-weight: 600;
        color: #2d3748;
    }

    .form-switch {
        padding-left: 2.5em !important;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Company Rules</h4>
                <!-- <p class="text-muted">Configure your HR policies, payroll settings, and attendance rules</p> -->

                <form id="companyRulesForm">
                    <input type="hidden" name="id" id="id">

                    <!-- PAYROLL CONFIGURATION -->
                    <div class="section-header">
                        <i class="mdi mdi-cash-multiple"></i> Payroll Configuration
                    </div>

                    <div class="row">
                        <!-- <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Enable Payroll</label>
                                <div class="col-sm-8">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_payroll" name="enable_payroll" checked>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Payroll Type</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-briefcase fs-5"></i></span>
                                        </div>
                                        <select class="form-control" name="payroll_type" id="payroll_type">
                                            <option value="monthly">Monthly</option>
                                            <!-- <option value="daily">Per Day</option> -->
                                            <option value="hourly">Hourly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Working Hours/Day</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="working_hours_per_day"
                                            id="working_hours_per_day" step="0.5" placeholder="e.g. 8" />
                                    </div>
                                    <div id="display_working_hours" class="mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Half Day Hours</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="half_day_hours"
                                            id="half_day_hours" step="0.5" placeholder="e.g. 5 (5 hrs = half day)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OVERTIME CONFIGURATION -->
                    <div class="section-header">
                        <i class="mdi mdi-clock-plus"></i> Overtime Configuration
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-5 col-form-label">Enable Overtime</label>
                                <div class="col-sm-8 col-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_overtime"
                                            name="enable_overtime">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Overtime Rate Type</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-cash-plus fs-5"></i></span>
                                        </div>
                                        <select class="form-control" name="overtime_rate_type" id="overtime_rate_type">
                                            <option value="multiplier">Multiplier (e.g. 1.5x)</option>
                                            <option value="fixed">Fixed Rate per Hour</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Overtime Multiplier</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-multiplication fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="overtime_multiplier"
                                            id="overtime_multiplier" step="any" placeholder="e.g. 1.5" value="1.5" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Min OT Count(In Minutes)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-clock-outline fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="min_overtime_count_in_minutes"
                                            id="min_overtime_count_in_minutes" step="any" placeholder="e.g. 30"
                                            value="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ATTENDANCE & WORKING DAYS -->
                    <div class="section-header">
                        <i class="mdi mdi-calendar-clock"></i> Attendance & Working Days
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Start Time</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-clock-start fs-5"></i></span>
                                        </div>
                                        <input type="time" class="form-control" name="start_time" id="start_time" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Lunch Break</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-food fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="lunch_break" id="lunch_break"
                                            placeholder="HH:MM:SS (e.g. 01:00:00)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">End Time</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-clock-end fs-5"></i></span>
                                        </div>
                                        <input type="time" class="form-control" name="end_time" id="end_time" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Grace Period (Min)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-timer fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="grace_period" id="grace_period"
                                            placeholder="e.g. 15" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUNDAY CONFIGURATION -->
                    <!-- <div class="section-header">
                        <i class="mdi mdi-calendar-weekend"></i> Sunday Configuration
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Sunday Weekly Off</label>
                                <div class="col-sm-8">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sunday_off" name="sunday_off" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- SATURDAY CONFIGURATION -->
                    <div class="section-header">
                        <i class="mdi mdi-calendar-week"></i> Saturday Configuration
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-5 col-form-label">Saturday Weekly Off</label>
                                <div class="col-sm-8 col-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="saturday_off_enabled"
                                            name="saturday_off_enabled" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Saturday Off Type</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-calendar-week fs-5"></i></span>
                                        </div>
                                        <select class="form-control" name="saturday_off_type" id="saturday_off_type">
                                            <option value="all">Every Saturday Off</option>
                                            <option value="alternate-even">Alternate Saturday Off (Even)</option>
                                            <option value="alternate-odd">Alternate Saturday Off (Odd)</option>
                                            <option value="custom">Custom Saturday Off</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Saturday Off Pattern</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-calendar-text fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="saturday_off_pattern"
                                            id="saturday_off_pattern" placeholder="e.g. 1,3,5 or 2,4" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Saturday Half Day Pattern</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-calendar-text fs-5"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="saturday_half_day_pattern"
                                            id="saturday_half_day_pattern" placeholder="e.g. 1,3,5 or 2,4" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label">Saturday Half Day Enable</label>
                                <div class="col-sm-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="saturday_half_day_enabled" name="saturday_half_day_enabled" checked>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div> -->

                    <!-- TAX & DEDUCTIONS -->
                    <div class="section-header">
                        <i class="mdi mdi-calculator"></i> Tax & Deductions
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-5 col-form-label">Enable Tax</label>
                                <div class="col-sm-8 col-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_tax"
                                            name="enable_tax" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Tax Type</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i
                                                    class="mdi mdi-calculator fs-5"></i></span>
                                        </div>
                                        <select class="form-control" name="tax_type" id="tax_type">
                                            <option value="fixed">Fixed Amount</option>
                                            <option value="percentage">Percentage</option>
                                            <option value="slab">Slab Based</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Tax Value</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-cash fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="tax" id="tax"
                                            placeholder="e.g. 200 or 10" step="0.01" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Taxable Salary Threshold</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="mdi mdi-cash fs-5"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name="salary_above_tax"
                                            id="salary_above_tax" placeholder="e.g. 12000" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WORKING DAYS CALCULATOR -->
                    <div class="section-header">
                        <i class="mdi mdi-calendar-check"></i> Working Days Calculator
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-5 col-form-label">Include Holidays</label>
                                <div class="col-sm-8 col-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            id="include_holidays_in_working_days" checked>
                                        <label class="form-check-label" for="include_holidays_in_working_days">
                                            Include holidays as working days
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-5 col-form-label">Sandwich Leave</label>
                                <div class="col-sm-8 col-7">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sandwich_leave">
                                        <label class="form-check-label" for="sandwich_leave">
                                            Enable Sandwich Leave Rule
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-end sm-margin">
                        <a href="/view-rules" class="btn hr-btnbg">Back</a>
                        <button type="submit" class="btn hr-btnbg" id="submitBtn">Save</button>
                    </div>

                    <div id="responseMessage" class="mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Form submission
        $('#saturday_off_type').on('change', function (e) {
            const selectedType = $(this).val();
            if (selectedType === "alternate-even") {
                $('#saturday_off_pattern').val('2,4');
            } else if (selectedType === "alternate-odd") {
                $('#saturday_off_pattern').val('1,3,5');
            } else if (selectedType === "custom") {
                $('#saturday_off_pattern').val('1');
            } else {
                $('#saturday_off_pattern').val('0,0');
            }
        })
        $('#working_hours_per_day').on('change', function () {
            const selectedHours = $(this).val();
            if (!selectedHours) {
                $('#display_working_hours').text('');
                return;
            }

            const hoursDecimal = parseFloat(selectedHours);
            const hours = Math.floor(hoursDecimal);
            const minutes = Math.round((hoursDecimal - hours) * 60);

            const workingHoursDisplay = minutes > 0
                ? `${hours}h ${minutes}m`
                : `${hours}h`;

            $('#display_working_hours').text(
                `Working time per day: ${workingHoursDisplay}`
            );
        });


        $('#companyRulesForm').on('submit', function (e) {
            e.preventDefault();
            if ($('#saturday_off_type').val() === "alternate-even") {
                $('#saturday_off_pattern').val('2,4');
            } else if ($('#saturday_off_type').val() === "alternate-odd") {
                $('#saturday_off_pattern').val('1,3,5');
            } else if ($('#saturday_off_type').val() === "custom") {
                // $('#saturday_off_pattern').val('1');
            } else {
                $('#saturday_off_pattern').val('0,0');
            }
            const formData = {
                id: $('#id').val() || '',
                // Payroll
                // enable_payroll: $('#enable_payroll').is(':checked'),
                payroll_type: $('#payroll_type').val(),
                working_hours_per_day: $('#working_hours_per_day').val(),

                half_day_hours: $('#half_day_hours').val(),
                // Overtime
                enable_overtime: $('#enable_overtime').is(':checked'),
                overtime_rate_type: $('#overtime_rate_type').val(),
                overtime_multiplier: $('#overtime_multiplier').val(),
                min_overtime_count_in_minutes: $('#min_overtime_count_in_minutes').val(),
                // Attendance
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                lunch_break: $('#lunch_break').val(),
                grace_period: $('#grace_period').val(),
                // Sunday
                // sunday_off: $('#sunday_off').is(':checked'),
                sunday_off: true,
                sunday_pay_type: "unpaid",
                // Saturday
                saturday_off_enabled: $('#saturday_off_enabled').is(':checked'),
                saturday_off_type: $('#saturday_off_type').val(),
                saturday_off_pattern: $('#saturday_off_pattern').val(),
                // saturday_pay_type: $('#saturday_pay_type').val(),
                saturday_pay_type: "regular",

                saturday_half_day_enabled: $('#saturday_half_day_enabled').is(':checked'),
                saturday_half_day_pattern: $('#saturday_half_day_pattern').val(),

                include_holidays_in_working_days: $('#include_holidays_in_working_days').is(':checked'),
                sandwich_leave: $('#sandwich_leave').is(':checked'),
                // Tax
                enable_tax: $('#enable_tax').is(':checked'),
                tax_type: $('#tax_type').val(),
                tax: $('#tax').val(),
                salary_above_tax: $('#salary_above_tax').val()
            };

            const token = localStorage.getItem('token');

            $.ajax({
                url: '/api/company-rules/store',
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(formData),
                success: function (response) {
                    if (response.status === 'success') {
                        $('#responseMessage').html(`<div class="alert alert-success">${response.message}</div>`);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        $('#responseMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                },
                error: function (xhr) {
                    $('#responseMessage').html(`<div class="alert alert-danger">Error: ${xhr.responseJSON?.message || 'Something went wrong'}</div>`);
                }
            });
        });

        // Load existing data
        fetch("<?= base_url('api/rules_get') ?>")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const rules = data.data;
                    // Populate all fields
                    Object.keys(rules).forEach(key => {
                        const element = document.getElementById(key);


                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = rules[key] == 1 || rules[key] === true;
                            } else {
                                if (key === "working_hours_per_day") {
                                    var hoursDecimal = parseFloat(rules[key]);
                                    const hours = Math.floor(hoursDecimal);
                                    const minutes = Math.round((hoursDecimal - hours) * 60);

                                    const workingHoursDisplay = minutes > 0
                                        ? `${hours}h ${minutes}m`
                                        : `${hours}h`;

                                    $('#display_working_hours').text(
                                        `Working time per day: ${workingHoursDisplay}`
                                    );
                                }

                                element.value = rules[key] || '';
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>

<?= $this->endSection(); ?>