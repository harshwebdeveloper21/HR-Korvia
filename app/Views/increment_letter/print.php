<?php
$companyEmail = $company['company_email'] ?? '';
$companyPhone = $company['company_phone'] ?? '';
$footerBits = array_filter([$text['company_name'], esc($companyPhone), esc($companyEmail), $text['footer_website']]);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 36px 48px 50px 48px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.8pt; line-height: 1.55; color: #222; }
    .lh { text-align: center; }
    .lh-name { font-family: 'DejaVu Serif', serif; font-size: 17pt; font-weight: bold; color: #7f9c8e; letter-spacing: 1px; }
    .lh-sub { font-size: 7pt; color: #9aa6a0; margin-top: 1px; letter-spacing: .5px; }
    .lh-rule { border-top: 1px solid #c9a44c; margin: 7px 0 10px; }
    table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.meta td { padding: 0; }
    .muted { color: #666; font-style: italic; }
    .to p { margin: 0; }
    .subject { margin: 12px 0 6px; font-weight: bold; }
    .subject span { color: #1f4e3d; }
    .title { text-align: center; font-family: 'DejaVu Serif', serif; font-size: 13pt; font-weight: bold; color: #1f4e3d; letter-spacing: 1px; margin: 8px 0 12px; }
    .body { text-align: justify; margin: 0 0 10px; }
    .body p { margin: 0 0 6px; }
    .body ul, .body ol { margin: 0 0 6px 18px; padding: 0; }
    .sec { font-weight: bold; font-size: 9pt; color: #1f4e3d; border-bottom: 1px solid #c9a44c; padding-bottom: 3px; margin: 16px 0 4px; }
    table.rev { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.rev td { padding: 6px 4px; border-bottom: 1px solid #dddddd; vertical-align: top; }
    table.rev td.l { font-weight: bold; color: #1f4e3d; width: 22%; }
    table.rev td.v { width: 28%; }
    .hl { font-weight: bold; color: #1f4e3d; }
    table.sign { width: 100%; margin-top: 26px; border-collapse: collapse; }
    table.sign td { vertical-align: bottom; width: 50%; }
    .sig-img { height: 46px; }
    .stamp-img { height: 62px; margin-left: 8px; }
    .sign-line { border-top: 1px solid #333; width: 200px; padding-top: 3px; font-weight: bold; font-size: 8.4pt; }
    .sign-sub { font-size: 7.8pt; color: #555; }
    .footer { position: fixed; bottom: -32px; left: 0; right: 0; text-align: center; font-size: 6.4pt; color: #9aa3ad; border-top: 0.6px solid #e5e1d3; padding-top: 4px; }
</style>
</head>
<body>
<div class="footer"><?= implode('  |  ', $footerBits) ?></div>

<div class="lh">
    <div class="lh-name"><?= $text['company_name'] ?></div>
    <?php if ($text['company_tagline'] !== ''): ?><div class="lh-sub"><?= $text['company_tagline'] ?></div><?php endif; ?>
</div>
<div class="lh-rule"></div>

<table class="meta">
    <tr>
        <td><b>Ref:</b> <span class="muted"><?= esc($refNumber) ?></span></td>
        <td style="text-align:right;"><b>Date:</b> <span class="muted"><?= esc($letterDate) ?></span></td>
    </tr>
</table>

<div class="to">
    <p>To,</p>
    <p><b><?= esc($employeeName) ?></b></p>
    <?php if ($employeeLine !== ''): ?><p style="color:#666;"><?= esc($employeeLine) ?></p><?php endif; ?>
</div>

<div class="subject">Subject: <span><?= $subject ?></span></div>

<div class="title"><?= $title ?></div>

<div class="body"><?= $text['opening_paragraph'] ?></div>

<div class="sec"><?= $text['revision_heading'] ?></div>
<table class="rev">
    <tr>
        <td class="l"><?= $text['label_current_designation'] ?></td><td class="v"><?= esc($prevDesignation ?: '-') ?></td>
        <td class="l"><?= $text['label_new_designation'] ?></td><td class="v"><?= $isPromotion && $newDesignation !== $prevDesignation ? '<span class="hl">' . esc($newDesignation) . '</span>' : esc($newDesignation ?: '-') ?></td>
    </tr>
    <tr>
        <td class="l"><?= $text['label_current_department'] ?></td><td class="v"><?= esc($prevDepartment ?: '-') ?></td>
        <td class="l"><?= $text['label_new_department'] ?></td><td class="v"><?= esc($newDepartment) ?></td>
    </tr>
    <tr>
        <td class="l"><?= $text['label_current_ctc'] ?></td><td class="v"><?= esc($prevAnnualCtc) ?></td>
        <td class="l"><?= $text['label_new_ctc'] ?></td><td class="v"><?= $isIncrement ? '<span class="hl">' . esc($newAnnualCtc) . '</span>' : esc($newAnnualCtc) ?></td>
    </tr>
    <tr>
        <td class="l"><?= $text['label_effective_date'] ?></td><td class="v"><?= esc($effectiveDate ?: '-') ?></td>
        <td class="l"><?= $text['label_reporting_manager'] ?></td><td class="v"><?= esc($reportingManager ?: '-') ?></td>
    </tr>
</table>

<div class="body"><?= $isIncrement ? $text['terms_increment'] : $text['terms_promotion'] ?></div>

<div class="body"><?= $text['closing_paragraph'] ?></div>

<table class="sign">
    <tr>
        <td>
            <div style="margin-bottom:4px;">For <b><?= $text['signing_for'] ?></b></div>
            <?php if (!empty($signature['signature_src']) || !empty($signature['stamp_src'])): ?>
                <div>
                    <?php if (!empty($signature['signature_src'])): ?><img class="sig-img" src="<?= $signature['signature_src'] ?>" alt=""><?php endif; ?>
                    <?php if (!empty($signature['stamp_src'])): ?><img class="stamp-img" src="<?= $signature['stamp_src'] ?>" alt=""><?php endif; ?>
                </div>
            <?php else: ?>
                <div style="height:46px;"></div>
            <?php endif; ?>
            <div class="sign-line"><?= esc($signature['name'] ?? 'Authorised Signatory') ?: 'Authorised Signatory' ?></div>
            <div class="sign-sub"><?= esc($signature['designation'] ?? 'Human Resources') ?: 'Human Resources' ?></div>
        </td>
        <td style="text-align:right;">
            <div style="height:46px;"></div>
            <div class="sign-line" style="margin-left:auto; text-align:left;"><?= $text['employee_ack_label'] ?></div>
            <div class="sign-sub" style="text-align:left; width:200px; margin-left:auto;"><?= $text['employee_ack_sub'] ?></div>
        </td>
    </tr>
</table>
</body>
</html>
