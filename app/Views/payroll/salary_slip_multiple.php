<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pay Slip</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .slip-container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border: 2px solid #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            /* border-right: 2px solid #000; */
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 26px;
        }

        .header p {
            margin: 3px 0;
            font-size: 14px;
        }

        .title-row {
            display: flex;
            justify-content: space-between;
            background: #E66136;
            color: #fff;
            font-weight: bold;
            padding: 10px;
            margin-top: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 5px 10px;
        }

        .pay-section {
            display: flex;
            margin-top: 15px;
            gap: 20px;
        }

        .pay-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .pay-section th,
        .pay-section td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .pay-section th {
            background: rgb(243, 199, 184);
        }

        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .footer-table th,
        .footer-table td {
            border: 1px solid #000;
            padding: 6px 10px;
        }

        .signatures {
            width: 100%;
            display: flex;
            /* justify-content: space-between; */
            margin-top: 30px;
            text-align: left !important;
        }

        .signature-box {
            width: 45%;
            border: 1px solid #000;
            padding: 30px 10px;
            /* text-align: center; */
            font-weight: bold;
        }

        .net-salary {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="slip-container">
        <div class="header">

            <?php if (!empty($companyLogoBase64)): ?>
                <img src="<?= $companyLogoBase64 ?>" alt="Company Logo">
            <?php endif; ?>


            <h1><?= esc($company["company_name"]) ?></h1>
            <p><?= esc($company["company_address"]) ?></p>
        </div>

        <div class="title-row">
            <div>Salary Slip</div>
            <div>Month: <?= esc($payroll["month_year"]) ?></div>
        </div>

        <table class="info-table">
            <tr>
                <td>Employee Name: <?= esc(
                                        $user["firstname"] . " " . $user["lastname"],
                                    ) ?></td>
                <td>Employee Code: EMP#<?= esc($user["employee_id"]) ?></td>
            </tr>
            <tr>
                <td>Designation: <?= esc(
                                        $designation["designation_name"],
                                    ) ?></td>
                <td>Department: <?= esc($department["department_name"]) ?></td>
            </tr>
            <tr>
                <td>Date of Joining: <?= esc($user["joining_date"]) ?></td>
                <td>Working Days: <?= esc(
                                        $calculatedData["working_days"],
                                    ) ?> | Present: <?= esc(
                                    $calculatedData["present_days"],
                                ) ?> | Absent: <?= esc($calculatedData["absent_days"]) ?></td>
            </tr>
            <tr>
                <td colspan="2">Leave Details: Total Leaves: <?= esc(
                                                                    $calculatedData["total_leaves"],
                                                                ) ?> | Paid Leaves Used: <?= esc(
                                                $calculatedData["used_paid_leaves"],
                                            ) ?> | Unpaid Leaves: <?= esc(
                            $calculatedData["unpaid_leaves"],
                        ) ?> | Half Days: <?= esc($calculatedData["half_days"]) ?></td>
            </tr>
        </table>

        <div class="pay-section">
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Earnings</th>
                    </tr>
                    <tr>
                        <th>Particulars</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td>₹<?= esc(
                                    number_format($payroll["salary_amount"] ?? 0, 2),
                                ) ?></td>
                    </tr>
                    <!-- <tr><td>House Rent Allowance (HRA)</td><td>₹<?= esc(
                                                                            number_format($payroll["hra"] ?? 0, 2),
                                                                        ) ?></td></tr>
                    <tr><td>Conveyance Allowance</td><td>₹<?= esc(
                                                                number_format($payroll["conveyance"] ?? 0, 2),
                                                            ) ?></td></tr>
                    <tr><td>Medical Allowance</td><td>₹<?= esc(
                                                            number_format($payroll["medical"] ?? 0, 2),
                                                        ) ?></td></tr> -->
                    <?php if (!empty($payroll["overtime_pay"])): ?>
                        <tr>
                            <td>Overtime Pay</td>
                            <td>₹<?= esc(
                                        number_format($payroll["overtime_pay"] ?? 0, 2),
                                    ) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($payroll["bonuses"])): ?>
                        <tr>
                            <td>Bonuses</td>
                            <td>₹<?= esc(
                                        number_format($payroll["bonuses"] ?? 0, 2),
                                    ) ?></td>
                        </tr>
                    <?php endif; ?>
                    <!-- <tr><td>Other Allowances</td><td>₹<?= esc(
                                                                number_format($payroll["other_allowances"] ?? 0, 2),
                                                            ) ?></td></tr> -->
                    <tr style="font-weight: bold;">
                        <td>Total Earnings</td>
                        <td>₹<?= esc(
                                    number_format($calculatedData["total_earnings"], 2),
                                ) ?></td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th colspan="2">Deductions</th>
                    </tr>
                    <tr>
                        <th>Particulars</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr><td>Provident Fund (Employee)</td><td>-₹<?= esc(
                                                                            number_format($payroll["pf_employee"] ?? 0, 2),
                                                                        ) ?></td></tr>
                    <tr><td>ESI (Employee)</td><td>-₹<?= esc(
                                                            number_format($payroll["esi_employee"] ?? 0, 2),
                                                        ) ?></td></tr>
                    <tr><td>Professional Tax</td><td>-₹<?= esc(
                                                            number_format($payroll["professional_tax"] ?? 0, 2),
                                                        ) ?></td></tr> -->
                    <?php if (!empty($calculatedData["salary_deduction"])): ?>
                        <tr>
                            <td>Leave Deduction (LOP)</td>
                            <td>-₹<?= esc(
                                        number_format($calculatedData["salary_deduction"], 2),
                                    ) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($payroll["tax_deduction"])): ?>
                        <tr>
                            <td>Tax Deduction</td>
                            <td>-₹<?= esc(
                                        number_format($payroll["tax_deduction"] ?? 0, 2),
                                    ) ?></td>
                        </tr>
                    <?php endif; ?>
                    <!-- <tr><td>Loan/Advance</td><td>-₹<?= esc(
                                                            number_format($payroll["loan_deduction"] ?? 0, 2),
                                                        ) ?></td></tr> -->
                    <tr style="font-weight: bold;">
                        <td>Total Deductions</td>
                        <td>-₹<?= esc(
                                    number_format($calculatedData["total_deductions"], 2),
                                ) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="net-salary">
            Gross Salary: ₹<?= esc(
                                number_format($calculatedData["total_earnings"], 2),
                            ) ?> |
            Total Deductions: ₹<?= esc(
                                    number_format($calculatedData["total_deductions"], 2),
                                ) ?> |
            Net Salary: ₹<?= esc(number_format($payroll["net_salary"], 2)) ?>
        </div>

        <table class="footer-table">
            <tr>
                <th>Payment Status</th>
                <td><?= esc($payroll["payment_status"]) ?></td>
                <th>Payment Date</th>
                <td><?= esc($payroll["payment_date"] ?? "Not Processed") ?></td>
            </tr>
        </table>

        <div class="signatures">
            <hr style="width: 150px; margin-left: 1px;">
            <div style="margin-left: 20px;" class="">Authorized By</div>
        </div>
    </div>
</body>

</html>