<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;      
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #666;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .payroll-details {
            margin-top: 20px;
        }

        .payroll-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .payroll-details th, .payroll-details td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .payroll-details th {
            background-color: #f1f1f1;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="<?= $company_logo ?>" alt="Company Logo">
            <h2><?= $company_name ?></h2>
        </div>

        <div class="payroll-details">
            <h3>Payroll Details for <?= $employee_name ?></h3>
            <table>
                <tr>
                    <th>Salary Amount</th>
                    <td><?= $salary_amount ?></td>
                </tr>
                <tr>
                    <th>Tax Deduction</th>
                    <td><?= $tax_deduction ?></td>
                </tr>
                <tr>
                    <th>Bonuses</th>
                    <td><?= $bonuses ?></td>
                </tr>
                <tr>
                    <th>Net Salary</th>
                    <td><?= $net_salary ?></td>
                </tr>
                <tr>
                    <th>Payment Date</th>
                    <td><?= $payment_date ?></td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td><?= $payment_status ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>&copy; <?= date("Y") ?> <?= $company_name ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;      
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #666;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .payroll-details {
            margin-top: 20px;
        }

        .payroll-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .payroll-details th, .payroll-details td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .payroll-details th {
            background-color: #f1f1f1;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="<?= $company_logo ?>" alt="Company Logo">
            <h2><?= $company_name ?></h2>
        </div>

        <div class="payroll-details">
            <h3>Payroll Details for <?= $employee_name ?></h3>
            <p><strong>Payroll Period:</strong> <?= $month_year ?></p>

            <h4>Earnings</h4>
            <table>
                <tr>
                    <th>Basic Salary</th>
                    <td>₹<?= number_format($salary_amount, 2) ?></td>
                </tr>
                <?php if ($overtime_pay > 0): ?>
                <tr>
                    <th>Overtime Pay</th>
                    <td>₹<?= number_format($overtime_pay, 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($bonuses > 0): ?>
                <tr>
                    <th>Bonuses</th>
                    <td>₹<?= number_format($bonuses, 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr style="background-color: #e8f5e8;">
                    <th><strong>Gross Earnings</strong></th>
                    <td><strong>₹<?= number_format($salary_amount + $overtime_pay + $bonuses, 2) ?></strong></td>
                </tr>
            </table>

            <h4>Deductions</h4>
            <table>
                <?php if ($total_leaves > 0): ?>
                <tr>
                    <th>Leave Deductions</th>
                    <td><?= $total_leaves ?> days</td>
                </tr>
                <?php endif; ?>
                <?php if ($total_half_day > 0): ?>
                <tr>
                    <th>Half-day Deductions</th>
                    <td><?= $total_half_day ?> days</td>
                </tr>
                <?php endif; ?>
                <?php if ($salary_deduction > 0): ?>
                <tr>
                    <th>Salary Deduction</th>
                    <td>₹<?= number_format($salary_deduction, 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($tax_deduction > 0): ?>
                <tr>
                    <th>Tax Deduction</th>
                    <td>₹<?= number_format($tax_deduction, 2) ?></td>
                </tr>
                <?php endif; ?>
                <tr style="background-color: #ffe8e8;">
                    <th><strong>Total Deductions</strong></th>
                    <td><strong>₹<?= number_format($salary_deduction + $tax_deduction, 2) ?></strong></td>
                </tr>
            </table>

            <h4>Work Summary</h4>
            <table>
                <tr>
                    <th>Worked Hours</th>
                    <td><?= $worked_hours ?> hours</td>
                </tr>
                <?php if ($total_overtime_hours > 0): ?>
                <tr>
                    <th>Overtime Hours</th>
                    <td><?= $total_overtime_hours ?> hours</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Total Leaves</th>
                    <td><?= $total_leaves ?> days</td>
                </tr>
                <tr>
                    <th>Half-days</th>
                    <td><?= $total_half_day ?> days</td>
                </tr>
            </table>

            <h4>Payment Information</h4>
            <table>
                <tr style="background-color: #e8f0ff;">
                    <th><strong>Net Salary</strong></th>
                    <td><strong>₹<?= number_format($net_salary, 2) ?></strong></td>
                </tr>
                <tr>
                    <th>Payment Date</th>
                    <td><?= $payment_date ?></td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td><span style="color: <?= $payment_status == 'Paid' ? 'green' : 'orange' ?>; font-weight: bold;"><?= $payment_status ?></span></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>&copy; <?= date("Y") ?> <?= $company_name ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
