<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            font-size: 14px;
        }

        .slip-container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border: 2px solid #000;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .no-border td {
            border: none;
        }

        .header-title {
            background: #E66136;
            color: #fff;
            font-weight: bold;
        }

        .section-title {
            background: #F3C7B8;
            font-weight: bold;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .net-salary {
            font-weight: bold;
            border-top: 2px solid #000;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="slip-container">

    <!-- HEADER -->
    <table class="no-border">
        <tr>
            <td width="20%">
                <?php if (!empty($companyLogoBase64)): ?>
                    <img src="<?= $companyLogoBase64 ?>" style="max-width:90px;">
                <?php endif; ?>
            </td>
            <td width="80%">
                <h2 style="margin:0;"><?= esc($company['company_name']) ?></h2>
                <p style="margin:3px 0;">
                    <?= esc($company['company_address']) ?>
                </p>
            </td>
        </tr>
    </table>

    <!-- TITLE -->
    <table style="margin-top:10px;">
        <tr class="header-title">
            <td>Salary Slip</td>
            <td class="right">Month: <?= esc($payroll['month_year']) ?></td>
        </tr>
    </table>

    <!-- EMPLOYEE DETAILS -->
    <table style="margin-top:10px;">
        <tr>
            <td width="50%">Employee Name: <b><?= esc($user['firstname'].' '.$user['lastname']) ?></b></td>
            <td width="50%">Employee Code: EMP#<?= esc($user['employee_id']) ?></td>
        </tr>
        <tr>
            <td>Designation: <?= esc($designation['designation_name']) ?></td>
            <td>Department: <?= esc($department['department_name']) ?></td>
        </tr>
        <tr>
            <td>Date of Joining: <?= esc($user['joining_date']) ?></td>
            <td>
                Working Days: <?= esc($calculatedData['working_days']) ?>
                | Present: <?= esc($calculatedData['present_days']) ?>
                | Absent: <?= esc($calculatedData['absent_days']) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Leave Details:
                Total: <?= esc($calculatedData['total_leaves']) ?> |
                Paid Used: <?= esc($calculatedData['used_paid_leaves']) ?> |
                Unpaid: <?= esc($calculatedData['unpaid_leaves']) ?> |
                Half Day: <?= esc($calculatedData['half_days']) ?>
            </td>
        </tr>
    </table>

    <!-- EARNINGS & DEDUCTIONS -->
    <table style="margin-top:15px;">
        <tr>
            <td width="50%" style="padding:0;">
                <table>
                    <tr class="section-title">
                        <td colspan="2">Earnings</td>
                    </tr>
                    <tr>
                        <td>Basic Salary</td>
                        <td class="right">₹<?= number_format($payroll['salary_amount'],2) ?></td>
                    </tr>

                    <?php if (!empty($payroll['overtime_pay'])): ?>
                    <tr>
                        <td>Overtime</td>
                        <td class="right">₹<?= number_format($payroll['overtime_pay'],2) ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!empty($payroll['bonuses'])): ?>
                    <tr>
                        <td>Bonus</td>
                        <td class="right">₹<?= number_format($payroll['bonuses'],2) ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr class="bold">
                        <td>Total Earnings</td>
                        <td class="right">₹<?= number_format($calculatedData['total_earnings'],2) ?></td>
                    </tr>
                </table>
            </td>

            <td width="50%" style="padding:0;">
                <table>
                    <tr class="section-title">
                        <td colspan="2">Deductions</td>
                    </tr>

                    <?php if (!empty($calculatedData['salary_deduction'])): ?>
                    <tr>
                        <td>Leave Deduction (LOP)</td>
                        <td class="right">-₹<?= number_format($calculatedData['salary_deduction'],2) ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!empty($payroll['tax_deduction'])): ?>
                    <tr>
                        <td>Tax Deduction</td>
                        <td class="right">-₹<?= number_format($payroll['tax_deduction'],2) ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr class="bold">
                        <td>Total Deductions</td>
                        <td class="right">-₹<?= number_format($calculatedData['total_deductions'],2) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- NET SALARY -->
    <table style="margin-top:15px;">
        <tr>
            <td class="net-salary">
                Gross Salary: ₹<?= number_format($calculatedData['total_earnings'],2) ?>
                |
                Total Deductions: ₹<?= number_format($calculatedData['total_deductions'],2) ?>
                |
                Net Salary: ₹<?= number_format($payroll['net_salary'],2) ?>
            </td>
        </tr>
    </table>

    <!-- PAYMENT INFO -->
    <table style="margin-top:10px;">
        <tr>
            <td>Payment Status</td>
            <td><?= esc($payroll['payment_status']) ?></td>
            <td>Payment Date</td>
            <td><?= esc($payroll['payment_date'] ?? 'Not Processed') ?></td>
        </tr>
    </table>

    <!-- SIGNATURE -->
    <table class="no-border" style="margin-top:40px;">
        <tr>
            <td width="50%">
                ___________________________<br>
                Authorized By
            </td>
        </tr>
    </table>

</div>

</body>
</html>
