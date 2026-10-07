<?php
$navy = '#1F3864';
$ratings = $assessment['ratings'];
$score = $assessment['score'];
$feedback = $assessment['feedback'];
$box = static fn (bool $on) => $on ? '&#9745;' : '&#9744;';
$text = static fn ($v) => nl2br(esc(trim((string) ($v ?? ''))));
$interviewDate = (!empty($assessment['interview_date']) && strpos($assessment['interview_date'], '0000') !== 0)
    ? date('d M Y', strtotime($assessment['interview_date'])) : '';
$host = parse_url(base_url(), PHP_URL_HOST);
$signature = $signature ?? null;
$signedAt = $assessment['updated_at'] ?? ($assessment['created_at'] ?? null);
$signedDate = date('d M Y', $signedAt ? strtotime($signedAt) : time());
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Candidate Assessment Form</title>
    <style>
        @page { margin: 12mm 14mm 16mm; }
        body { font-family: "DejaVu Sans", sans-serif; color: #222; font-size: 9.5px; margin: 0; }
        .company { text-align: center; font-size: 16px; font-weight: bold; color: <?= $navy ?>; letter-spacing: .5px; }
        .company-sub { text-align: center; font-size: 9px; color: #444; margin-top: 2px; }
        .logo { text-align: center; margin-bottom: 4px; }
        .logo img { max-height: 40px; max-width: 160px; }
        .rule { border-top: 1px solid #333; margin: 8px 0 6px; }
        h1 { text-align: center; font-size: 13px; color: <?= $navy ?>; margin: 4px 0 2px; letter-spacing: .5px; }
        .conf { text-align: center; font-style: italic; font-size: 8.5px; color: #555; margin-bottom: 4px; }
        h2 { font-size: 10px; color: <?= $navy ?>; margin: 12px 0 5px; padding-bottom: 3px; border-bottom: 1.5px solid <?= $navy ?>; letter-spacing: .3px; }
        table { width: 100%; border-collapse: collapse; }
        .details td { border: 1px solid #b7b7b7; padding: 5px 6px; height: 12px; }
        .details td.lbl { width: 18%; font-weight: bold; color: <?= $navy ?>; background: #f2f2f2; }
        .details td.val { width: 32%; }
        .hint { font-style: italic; font-size: 8.5px; color: #555; margin: 0 0 5px; }
        .eval th { background: <?= $navy ?>; color: #fff; border: 1px solid <?= $navy ?>; padding: 5px 4px; font-size: 8.5px; text-align: center; }
        .eval th.param { text-align: left; width: 34%; padding-left: 6px; }
        .eval td { border: 1px solid #b7b7b7; padding: 4px 6px; vertical-align: middle; }
        .eval tr.alt td { background: #f2f2f2; }
        .eval td.box { text-align: center; font-size: 12px; }
        .eval .t { font-weight: bold; font-size: 9.5px; }
        .eval .d { font-size: 8px; color: #555; }
        .overall { margin-top: 8px; }
        .overall td { border: 1px solid #b7b7b7; padding: 5px 6px; font-weight: bold; color: <?= $navy ?>; }
        .overall td.score { width: 26%; text-align: center; color: #222; }
        .free { min-height: 40px; padding: 4px 2px 8px; border-bottom: 1px solid #b7b7b7; font-size: 9.5px; }
        .recs td { border: 1px solid #b7b7b7; padding: 5px 6px; width: 33.33%; font-size: 9.5px; }
        .recs td span { font-size: 12px; }
        .auth td { border: 1px solid #b7b7b7; padding: 26px 10px 8px; width: 50%; vertical-align: bottom; }
        .auth .line { border-top: 1px solid #333; width: 70%; margin-bottom: 4px; }
        .auth .t { font-weight: bold; color: <?= $navy ?>; }
        .auth .s { font-size: 8.5px; color: #444; margin-top: 2px; }
        .auth table.sig-imgs { width: 100%; margin-bottom: 2px; }
        .auth table.sig-imgs td { border: 0; padding: 0; vertical-align: bottom; width: auto; }
        .auth table.sig-imgs td.sig img { max-height: 42px; max-width: 150px; }
        .auth table.sig-imgs td.stamp { text-align: right; }
        .auth table.sig-imgs td.stamp img { max-height: 60px; max-width: 70px; }
        .footer { position: fixed; bottom: -8mm; left: 0; right: 0; text-align: center; font-size: 8px; color: #888; border-top: 1px solid #ccc; padding-top: 4px; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <?php if (!empty($logo_src)): ?>
        <div class="logo"><img src="<?= $logo_src ?>" alt=""></div>
    <?php endif; ?>
    <div class="company"><?= esc(strtoupper((string) $company_name)) ?></div>
    <?php if (!empty($company_email)): ?>
        <div class="company-sub"><?= esc($company_email) ?></div>
    <?php endif; ?>
    <div class="rule"></div>
    <h1>CANDIDATE ASSESSMENT FORM</h1>
    <div class="conf">Confidential – For Internal Use Only</div>

    <h2>SECTION A &nbsp;|&nbsp; INTERVIEW DETAILS</h2>
    <table class="details">
        <tr>
            <td class="lbl">Candidate Name</td><td class="val"><?= $text($assessment['candidate_name']) ?></td>
            <td class="lbl">Position Applied For</td><td class="val"><?= $text($assessment['job_title']) ?></td>
        </tr>
        <tr>
            <td class="lbl">Date of Interview</td><td class="val"><?= esc($interviewDate) ?></td>
            <td class="lbl">Interviewer Name</td><td class="val"><?= $text($assessment['interviewer_name']) ?></td>
        </tr>
        <tr>
            <td class="lbl">Interview Round</td><td class="val"><?= $text($assessment['interview_round']) ?></td>
            <td class="lbl">Department</td><td class="val"><?= $text($assessment['department']) ?></td>
        </tr>
        <tr>
            <td class="lbl">Contact Number</td><td class="val"><?= $text($assessment['contact_number']) ?></td>
            <td class="lbl">Reporting Manager</td><td class="val"><?= $text($assessment['reporting_manager'] ?? '') ?></td>
        </tr>
    </table>

    <h2>SECTION B &nbsp;|&nbsp; COMPETENCY EVALUATION</h2>
    <p class="hint">Please rate the candidate on each parameter by ticking one box (1 = Poor, 5 = Excellent).</p>
    <table class="eval">
        <tr>
            <th class="param">Evaluation Parameter</th>
            <?php foreach ($ratingLabels as $num => $label): ?>
                <th><?= $num ?><br><?= esc($label) ?></th>
            <?php endforeach; ?>
        </tr>
        <?php $i = 0; foreach ($competencies as $key => [$title, $desc]): $current = $ratings[$key] ?? 0; ?>
            <tr class="<?= $i++ % 2 ? 'alt' : '' ?>">
                <td><div class="t"><?= esc($title) ?></div><div class="d"><?= esc($desc) ?></div></td>
                <?php foreach ($ratingLabels as $num => $label): ?>
                    <td class="box"><?= $box($current === $num) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
    <table class="overall">
        <tr>
            <td>Overall Rating (Total Score out of 30)</td>
            <td class="score"><?= !empty($ratings) ? array_sum($ratings) : '' ?> &nbsp;/ 30</td>
        </tr>
    </table>

    <h2>SECTION C &nbsp;|&nbsp; KEY STRENGTHS</h2>
    <div class="free"><?= $text($feedback['strengths'] ?? '') ?></div>

    <h2>SECTION D &nbsp;|&nbsp; AREAS OF CONCERN / DEVELOPMENT NEEDS</h2>
    <div class="free"><?= $text($feedback['weaknesses'] ?? '') ?></div>

    <h2>SECTION E &nbsp;|&nbsp; FINAL RECOMMENDATION</h2>
    <?php $selected = $assessment['recommendation'] ?? ''; $chunks = array_chunk($recommendations, 3); ?>
    <table class="recs">
        <?php foreach ($chunks as $chunk): ?>
            <tr>
                <?php foreach ($chunk as $rec): ?>
                    <td><span><?= $box($selected === $rec) ?></span> <?= esc($rec) ?></td>
                <?php endforeach; ?>
                <?php for ($pad = count($chunk); $pad < 3; $pad++): ?><td></td><?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>SECTION F &nbsp;|&nbsp; AUTHORISATION</h2>
    <table class="auth">
        <tr>
            <td>
                <div class="line"></div>
                <div class="t">Interviewer Signature &amp; Date</div>
                <div class="s">Name / Date: <?= esc($assessment['interviewer_name'] ?? '') ?></div>
            </td>
            <td>
                <?php if (!empty($signature['signature_src']) || !empty($signature['stamp_src'])): ?>
                    <table class="sig-imgs">
                        <tr>
                            <td class="sig"><?php if (!empty($signature['signature_src'])): ?><img src="<?= $signature['signature_src'] ?>" alt=""><?php endif; ?></td>
                            <td class="stamp"><?php if (!empty($signature['stamp_src'])): ?><img src="<?= $signature['stamp_src'] ?>" alt=""><?php endif; ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
                <div class="line"></div>
                <div class="t">HR / Owner Signature &amp; Date</div>
                <div class="s">Name / Date: <?= esc(trim(($signature['name'] ?? '') . (!empty($signature['designation']) ? ' (' . $signature['designation'] . ')' : ''))) ?><?= !empty($signature) ? ' / ' . esc($signedDate) : '' ?></div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <?= esc(strtoupper((string) $company_name)) ?><?= !empty($company_email) ? ' &nbsp;|&nbsp; ' . esc($company_email) : '' ?><?= $host ? ' &nbsp;|&nbsp; ' . esc($host) : '' ?>
    </div>
</body>
</html>
