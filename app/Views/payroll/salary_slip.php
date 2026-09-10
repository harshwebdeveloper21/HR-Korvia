<?php
$monthYearFormatted = $payroll['month_year'] ?? '';
if (!empty($payroll['month_year'])) {
    $rawDate = trim($payroll['month_year']);
    if (preg_match('/^(\d{4})-(\d{1,2})$/', $rawDate, $matches)) {
        $dt = DateTime::createFromFormat('!Y-m', $matches[1] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT));
        if ($dt) {
            $monthYearFormatted = $dt->format('F-Y');
        }
    } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $rawDate)) {
        $dt = DateTime::createFromFormat('!Y-m-d', $rawDate);
        if ($dt) {
            $monthYearFormatted = $dt->format('F-Y');
        }
    } else {
        $timestamp = strtotime($rawDate);
        if ($timestamp !== false) {
            $monthYearFormatted = date('F-Y', $timestamp);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        body, table, th, td, div, p, span, h1, h2, h3 {
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #fff;
            padding: 20px;
            font-size: 12px;
        }

        .slip-container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            padding: 15px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: bottom;
            border: none;
            padding: 0;
        }

        .header-line {
            border-bottom: 4px solid #002B5B;
            margin-bottom: 15px;
            margin-top: 5px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .title-row {
            background-color: #F17F29;
            color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .section-header {
            background-color: #E2E2E2;
            font-weight: bold;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .orange-bg {
            background-color: #F17F29;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="slip-container">

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="30%">
                <?php if (!empty($companyLogoBase64)): ?>
                    <img src="<?= $companyLogoBase64 ?>" style="max-height:120px; max-width: 250px;">
                <?php else: ?>
                    <h2 style="margin:0; color:#F17F29;"><?= esc($company['company_name']) ?></h2>
                <?php endif; ?>
            </td>
            <td width="70%" class="right" style="font-weight: bold; font-size: 11px; padding-bottom: 5px;">
                <?= esc($company['company_address']) ?>
            </td>
        </tr>
    </table>
    <div class="header-line"></div>

    <!-- MAIN TABLE -->
    <table class="main-table">
        <tr class="title-row">
            <td colspan="4">Pay slip for <?= esc($monthYearFormatted) ?></td>
        </tr>
        <tr>
            <td class="bold" width="22%">Emp. Name</td>
            <td width="28%"><?= esc($user['firstname'].' '.$user['lastname']) ?></td>
            <td class="bold" width="22%">Department</td>
            <td width="28%"><?= esc($department['department_name']) ?></td>
        </tr>
        <tr>
            <td class="bold">Designation</td>
            <td><?= esc($designation['designation_name']) ?></td>
            <td class="bold">Leave/Absence</td>
            <td><?= esc($calculatedData['unpaid_leaves']) ?> Days</td>
        </tr>
        <tr>
            <td class="bold">Salary Month</td>
            <td><?= esc($monthYearFormatted) ?></td>
            <td></td>
            <td></td>
        </tr>

        <!-- Earnings and Deductions Header -->
        <tr class="section-header">
            <td colspan="2">Income (INR)</td>
            <td colspan="2">Deduction (INR)</td>
        </tr>

        <!-- Earnings and Deductions Rows -->
        <?php
        $incomes = [
            ['label' => 'Basic', 'amount' => $payroll['salary_amount'] ?? 0],
            ['label' => 'HRA', 'amount' => $payroll['hra'] ?? 0],
            ['label' => 'Other Allowance', 'amount' => $payroll['other_allowances'] ?? 0],
        ];
        if (!empty($payroll['overtime_pay']) && $payroll['overtime_pay'] > 0) {
            $incomes[] = ['label' => 'Overtime', 'amount' => $payroll['overtime_pay']];
        }
        if (!empty($payroll['bonuses']) && $payroll['bonuses'] > 0) {
            $incomes[] = ['label' => 'Bonus', 'amount' => $payroll['bonuses']];
        }

        $deductions = [
            ['label' => 'Leave Amount', 'amount' => $calculatedData['salary_deduction'] ?? 0],
            ['label' => 'Profession Tax', 'amount' => $payroll['professional_tax'] ?? 0],
            ['label' => 'Provident Fund', 'amount' => $payroll['pf_employee'] ?? 0],
            ['label' => 'Other Deductions', 'amount' => $payroll['other_deductions'] ?? $payroll['loan_deduction'] ?? 0],
        ];
        if (!empty($payroll['tax_deduction']) && $payroll['tax_deduction'] > 0) {
            $deductions[] = ['label' => 'Tax Deduction', 'amount' => $payroll['tax_deduction']];
        }

        // Add some empty rows to make it look like the template
        $maxRows = max(count($incomes), count($deductions));
        $maxRows = max($maxRows, 4);

        for ($i = 0; $i < $maxRows; $i++) {
            echo "<tr>";
            
            if (isset($incomes[$i])) {
                echo "<td class='bold'>" . esc($incomes[$i]['label']) . "</td>";
                echo "<td class='right'>" . number_format($incomes[$i]['amount'], 2) . "</td>";
            } else {
                echo "<td></td><td></td>";
            }

            if (isset($deductions[$i])) {
                echo "<td class='bold'>" . esc($deductions[$i]['label']) . "</td>";
                echo "<td class='right'>" . number_format($deductions[$i]['amount'], 2) . "</td>";
            } else {
                echo "<td></td><td></td>";
            }

            echo "</tr>";
        }
        ?>

        <!-- Gross Earnings -->
        <tr>
            <td class="orange-bg">Gross Earnings</td>
            <td class="right bold"><?= number_format($calculatedData['total_earnings'], 2) ?></td>
            <td></td>
            <td></td>
        </tr>

        <!-- Empty Row Spacer -->
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <!-- Totals -->
        <tr>
            <td colspan="3" class="orange-bg right">Total</td>
            <td class="right bold"><?= number_format($payroll['net_salary'], 2) ?></td>
        </tr>
        <tr>
            <td colspan="3" class="orange-bg right">Net Pay</td>
            <td class="right bold"><?= number_format($payroll['net_salary'], 2) ?></td>
        </tr>
    </table>

</div>

</body>
</html>
