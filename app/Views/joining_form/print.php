<?php
$f = $fields;
$v = static fn($key) => esc($f[$key] ?? '');
$box = static fn(bool $done) => $done
    ? '<span class="cb cb-on">&#10004;</span>'
    : '<span class="cb"></span>';
$pairs = static function (array $items): array {
    return array_chunk($items, 2);
};
$companyEmail = $company['company_email'] ?? '';
$companyPhone = $company['company_phone'] ?? '';
$footerBits = array_filter(['KORVIA RETAIL PRIVATE LIMITED', $companyPhone, $companyEmail, 'www.korviasmart.com']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 30px 36px 46px 36px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.4pt; color: #222; }
    .lh { text-align: center; }
    .lh-name { font-family: 'DejaVu Serif', serif; font-size: 17pt; font-weight: bold; color: #1f4e3d; letter-spacing: 1px; }
    .lh-sub { font-size: 7pt; color: #8a948f; margin-top: 1px; }
    .lh-rule { border-top: 1.2px solid #c9a44c; margin: 6px 0 8px; }
    .title { text-align: center; font-family: 'DejaVu Serif', serif; font-size: 12pt; font-weight: bold; color: #1f4e3d; letter-spacing: .5px; }
    .subtitle { text-align: center; font-size: 6.8pt; color: #6b7280; letter-spacing: .5px; margin: 1px 0 10px; }
    .sec { font-weight: bold; font-size: 8.4pt; color: #1f4e3d; letter-spacing: .4px; border-bottom: 1.2px solid #c9a44c; padding-bottom: 2px; margin: 12px 0 5px; }
    table.grid { width: 100%; border-collapse: collapse; }
    table.grid td { border: 0.8px solid #c9c2ad; padding: 6px 6px; vertical-align: middle; height: 13px; }
    table.grid td.l { background: #f4f1e6; font-weight: bold; color: #333; width: 21%; font-size: 7.8pt; }
    table.grid td.v { width: 29%; }
    table.grid td.l-wide { width: 21%; }
    table.grid td.addr { height: 34px; vertical-align: top; }
    table.grid td.gap { border: none; width: 0; padding: 0; }
    table.chk { width: 100%; border-collapse: collapse; }
    table.chk td { border: 0.8px solid #c9c2ad; padding: 6px 7px; width: 50%; font-size: 8pt; vertical-align: middle; }
    table.chk td.done { background: #f1f7f3; }
    .cb { display: inline-block; width: 9px; height: 9px; border: 0.9px solid #555; margin-right: 6px; vertical-align: -1px; text-align: center; font-size: 7pt; line-height: 9px; }
    .cb-on { border-color: #1f4e3d; background: #1f4e3d; color: #fff; }
    .meta { font-size: 6.8pt; color: #6b7280; }
    table.assets { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.assets th { background: #1f4e3d; color: #fff; font-size: 7.4pt; text-align: left; padding: 5px 6px; }
    table.assets td { border: 0.8px solid #c9c2ad; padding: 5px 6px; font-size: 7.6pt; }
    .badge { font-size: 6.8pt; padding: 1px 5px; border-radius: 6px; color: #fff; background: #1f4e3d; }
    .badge-ret { background: #9ca3af; }
    .sign { width: 100%; margin-top: 44px; border-collapse: collapse; }
    .sign td { width: 50%; padding: 0 14px 0 0; vertical-align: bottom; }
    .sign .line { border-top: 1px solid #333; padding-top: 4px; font-weight: bold; font-size: 7.8pt; }
    .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 6.2pt; color: #9aa3ad; border-top: 0.6px solid #e5e1d3; padding-top: 4px; }
    .page-break { page-break-before: always; }
    .note { font-size: 6.8pt; color: #6b7280; margin-top: 4px; }
</style>
</head>
<body>
<div class="footer"><?= esc(implode('  |  ', $footerBits)) ?></div>

<div class="lh">
    <div class="lh-name">KORVIA RETAIL PRIVATE LIMITED</div>
    <div class="lh-sub">KORVIA SMART &bull; Surat, Gujarat</div>
</div>
<div class="lh-rule"></div>

<div class="title">NEW JOINEE FORM</div>
<div class="subtitle">PERSONAL DETAILS &amp; DOCUMENT CHECKLIST</div>

<div class="sec">EMPLOYMENT DETAILS</div>
<table class="grid">
    <tr><td class="l">Employee Name</td><td class="v"><?= $v('employee_name') ?></td><td class="l">Employee ID</td><td class="v"><?= $v('employee_id') ?></td></tr>
    <tr><td class="l">Designation</td><td class="v"><?= $v('designation') ?></td><td class="l">Department</td><td class="v"><?= $v('department') ?></td></tr>
    <tr><td class="l">Reporting Manager</td><td class="v"><?= $v('reporting_manager') ?></td><td class="l">Work Location</td><td class="v"><?= $v('work_location') ?></td></tr>
    <tr><td class="l">Date of Joining</td><td class="v"><?= $v('joining_date') ?></td><td class="l">Employment Type</td><td class="v"><?= $v('employment_type') ?></td></tr>
</table>

<div class="sec">PERSONAL DETAILS</div>
<table class="grid">
    <tr><td class="l">Father's / Spouse's Name</td><td class="v"><?= $v('father_spouse_name') ?></td><td class="l">Date of Birth</td><td class="v"><?= $v('date_of_birth') ?></td></tr>
    <tr><td class="l">Gender</td><td class="v"><?= $v('gender') ?></td><td class="l">Blood Group</td><td class="v"><?= $v('blood_group') ?></td></tr>
    <tr><td class="l">Mobile Number</td><td class="v"><?= $v('mobile') ?></td><td class="l">Personal Email</td><td class="v"><?= $v('personal_email') ?></td></tr>
    <tr><td class="l">Aadhaar Number</td><td class="v"><?= $v('aadhaar_number') ?></td><td class="l">PAN Number</td><td class="v"><?= $v('pan_number') ?></td></tr>
    <tr><td class="l">Highest Qualification</td><td class="v"><?= $v('qualification') ?></td><td class="l">Previous Employer (if any)</td><td class="v"><?= $v('previous_employer') ?></td></tr>
</table>

<table class="grid" style="margin-top:8px;">
    <tr><td class="l">Present Address</td><td class="addr"><?= $v('present_address') ?></td></tr>
    <tr><td class="l">Permanent Address</td><td class="addr"><?= $v('permanent_address') ?></td></tr>
</table>

<div class="sec">EMERGENCY CONTACT</div>
<table class="grid">
    <tr><td class="l">Contact Name</td><td class="v"><?= $v('emergency_name') ?></td><td class="l">Relationship</td><td class="v"><?= $v('emergency_relationship') ?></td></tr>
    <tr><td class="l">Contact Number</td><td class="v"><?= $v('emergency_number') ?></td><td class="l">Alternate Number</td><td class="v"><?= $v('emergency_alt_number') ?></td></tr>
</table>

<div class="sec">BANK DETAILS (FOR SALARY PROCESSING)</div>
<table class="grid">
    <tr><td class="l">Bank Name</td><td class="v"><?= $v('bank_name') ?></td><td class="l">Branch</td><td class="v"><?= $v('bank_branch') ?></td></tr>
    <tr><td class="l">Account Number</td><td class="v"><?= $v('account_number') ?></td><td class="l">IFSC Code</td><td class="v"><?= $v('ifsc') ?></td></tr>
</table>

<div class="page-break"></div>

<div class="sec">DOCUMENT CHECKLIST &mdash; FOR HR USE</div>
<table class="chk">
    <?php foreach ($pairs($documentChecklist) as $row): ?>
        <tr>
            <?php foreach ($row as $item): ?>
                <td class="<?= $item['done'] ? 'done' : '' ?>"><?= $box($item['done']) ?><?= esc($item['label']) ?></td>
            <?php endforeach; ?>
            <?php if (count($row) < 2): ?><td></td><?php endif; ?>
        </tr>
    <?php endforeach; ?>
    <?php foreach ($pairs($extraDocuments) as $row): ?>
        <tr>
            <?php foreach ($row as $label): ?>
                <td class="done"><?= $box(true) ?><?= esc($label) ?> <span class="meta">(uploaded)</span></td>
            <?php endforeach; ?>
            <?php if (count($row) < 2): ?><td></td><?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>
<div class="note">Items are auto-ticked from documents uploaded in the employee's Documents section, or ticked by HR in Edit Joining Form.</div>

<div class="sec">ONBOARDING &amp; ASSET ISSUANCE &mdash; FOR HR USE</div>
<table class="chk">
    <?php foreach ($pairs($onboardingChecklist) as $row): ?>
        <tr>
            <?php foreach ($row as $item): ?>
                <td class="<?= $item['done'] ? 'done' : '' ?>"><?= $box($item['done']) ?><?= esc($item['label']) ?></td>
            <?php endforeach; ?>
            <?php if (count($row) < 2): ?><td></td><?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>

<?php if ($assets): ?>
    <table class="assets">
        <thead>
            <tr><th style="width:4%;">#</th><th>Asset</th><th>Type</th><th>Serial / Model</th><th>Issued On</th><th>Condition</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($assets as $i => $a):
                $returned = strtolower((string) $a['status']) === 'returned' || !empty($a['return_date']);
                $serial = trim(implode(' / ', array_filter([$a['serial_number'] ?? '', $a['model_number'] ?? ''])));
            ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $box(!$returned) ?><?= esc(trim($a['gadget_name'])) ?></td>
                    <td><?= esc($a['gadget_type'] ?? '') ?></td>
                    <td><?= esc($serial) ?></td>
                    <td><?= !empty($a['issuance_date']) ? date('d/m/Y', strtotime($a['issuance_date'])) : '' ?></td>
                    <td><?= esc($a['gadget_condition'] ?? '') ?></td>
                    <td><span class="badge <?= $returned ? 'badge-ret' : '' ?>"><?= $returned ? 'Returned' . (!empty($a['return_date']) ? ' ' . date('d/m/Y', strtotime($a['return_date'])) : '') : esc($a['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="note">Assets are listed automatically from Gadget Issuance.</div>
<?php endif; ?>

<table class="sign">
    <tr>
        <td><div class="line">Employee Signature &amp; Date</div></td>
        <td><div class="line">HR Signature &amp; Date</div></td>
    </tr>
</table>
</body>
</html>
