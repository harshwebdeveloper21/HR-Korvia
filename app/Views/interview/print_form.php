<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Interview Information Form</title>
    <style>
        @page { margin: 14mm 14mm 16mm; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111; font-size: 11px; margin: 0; }
        .head { width: 100%; border-bottom: 2px solid #111; margin-bottom: 12px; }
        .head td { border: none; padding: 0 0 8px; vertical-align: middle; }
        .head img { max-height: 46px; max-width: 170px; }
        .head .company { text-align: right; font-size: 13px; font-weight: bold; }
        h1 { text-align: center; font-size: 16px; letter-spacing: 1px; margin: 0 0 12px; }
        h2 { font-size: 11px; letter-spacing: .5px; margin: 12px 0 0; padding: 6px 8px; background: #e6e6e6; border: 1px solid #999; border-bottom: none; }
        table.form { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.form th, table.form td { border: 1px solid #999; padding: 7px 8px; vertical-align: top; text-align: left; word-wrap: break-word; }
        table.form th { width: 22%; background: #f5f5f5; font-weight: bold; }
        table.form td { width: 28%; height: 14px; }
        table.form tr { page-break-inside: avoid; }
        .sign { width: 100%; margin-top: 55px; page-break-inside: avoid; }
        .sign td { width: 50%; padding: 0 30px; text-align: center; font-weight: bold; border: none; }
        .sign span { display: block; border-top: 1px solid #111; padding-top: 6px; }
    </style>
</head>
<body>
    <table class="head">
        <tr>
            <td><?php if (!empty($logo_src)): ?><img src="<?= $logo_src ?>" alt=""><?php endif; ?></td>
            <td class="company"><?= esc($company_name) ?></td>
        </tr>
    </table>

    <h1>INTERVIEW INFORMATION FORM</h1>

    <?php foreach ($sections as $title => $fields): ?>
        <h2><?= esc($title) ?></h2>
        <table class="form">
            <?php
            $rows = [];
            $pair = [];
            foreach ($fields as $field) {
                if (!empty($field[2])) {
                    if ($pair) { $rows[] = $pair; $pair = []; }
                    $rows[] = [$field];
                    continue;
                }
                $pair[] = $field;
                if (count($pair) === 2) { $rows[] = $pair; $pair = []; }
            }
            if ($pair) { $rows[] = $pair; }
            ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php if (count($row) === 1): ?>
                        <th><?= esc($row[0][0]) ?></th>
                        <td colspan="3"><?= nl2br(esc((string) ($row[0][1] ?? ''))) ?></td>
                    <?php else: ?>
                        <th><?= esc($row[0][0]) ?></th>
                        <td><?= nl2br(esc((string) ($row[0][1] ?? ''))) ?></td>
                        <th><?= esc($row[1][0]) ?></th>
                        <td><?= nl2br(esc((string) ($row[1][1] ?? ''))) ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>

    <table class="sign">
        <tr>
            <td><span>Candidate Signature</span></td>
            <td><span>Interviewer Signature</span></td>
        </tr>
    </table>
</body>
</html>
