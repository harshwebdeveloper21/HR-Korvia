<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<?php
$score = $assessment['score'];
$ratings = $assessment['ratings'];
$rows = [];
foreach ($competencies as $key => [$title, $desc]) {
    $rows[] = ['title' => $title, 'desc' => $desc, 'rating' => $ratings[$key] ?? 0];
}
if (empty($ratings) && !empty($assessment['ratings_list'])) {
    $rows = [];
    foreach ($assessment['ratings_list'] as $item) {
        $rows[] = ['title' => $item['title'] ?? 'Criteria', 'desc' => $item['desc'] ?? '', 'rating' => (int) ($item['rating'] ?? 0)];
    }
}
$show = static function ($value) {
    $value = trim((string) ($value ?? ''));
    return $value === '' ? '<span class="asv-empty">-</span>' : nl2br(esc($value));
};
$interviewDate = (!empty($assessment['interview_date']) && strpos($assessment['interview_date'], '0000') !== 0)
    ? date('d M Y', strtotime($assessment['interview_date'])) : '';
?>
<style>
    .asv-wrap { width: 100%; max-width: 1400px; margin: 0 auto; }
    .asv-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
    .asv-header h3 { font-size: 22px; font-weight: 600; color: #111827; margin-bottom: 4px; }
    .asv-sub { font-size: 13px; color: #6b7280; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .asv-rec { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: rgba(var(--hr-primary-rgb, 230, 97, 54), .12); color: var(--hr-primary-text, var(--hr-primary, #e66136)); }
    .asv-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .asv-score { text-align: center; min-width: 110px; padding: 6px 16px; border-radius: 8px; border: 1px solid rgba(var(--hr-primary-rgb, 230, 97, 54), .35); background: rgba(var(--hr-primary-rgb, 230, 97, 54), .06); }
    .asv-score .num { font-size: 22px; font-weight: 700; color: var(--hr-primary-text, var(--hr-primary, #e66136)); line-height: 1.1; }
    .asv-score .lbl { font-size: 11px; color: #6b7280; font-weight: 600; }
    .asv-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px 20px 10px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .asv-section-title {
        display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
        color: var(--hr-primary-text, var(--hr-primary, #e66136));
        background: rgba(var(--hr-primary-rgb, 230, 97, 54), .08);
        border-left: 3px solid var(--hr-primary, #e66136);
        border-radius: 4px; padding: 7px 12px; margin: 6px 0 12px;
    }
    .asv-section-title .tag { opacity: .75; font-weight: 600; }
    .asv-item { margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px dashed #e5e7eb; }
    .asv-label { font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 3px; }
    .asv-value { font-size: 14px; color: #111827; word-break: break-word; }
    .asv-empty { color: #9ca3af; }
    .asv-table { width: 100%; border: 1px solid #e5e7eb; }
    .asv-table th, .asv-table td { padding: 8px 10px; border-bottom: 1px solid #eef0f3; vertical-align: middle; }
    .asv-table thead th { font-size: 12px; text-align: center; }
    .asv-table thead th:first-child { text-align: left; }
    .asv-table td.rate { text-align: center; width: 11%; }
    .asv-dot { display: inline-block; width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cbd2dc; }
    .asv-dot.on { background: var(--hr-primary, #e66136); border-color: var(--hr-primary, #e66136); }
    .asv-total { display: flex; justify-content: space-between; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; margin: 12px 0 16px; font-weight: 600; }
    .asv-total .val { color: var(--hr-primary-text, var(--hr-primary, #e66136)); }
    .asv-text { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; min-height: 60px; font-size: 14px; white-space: normal; }
    .asv-recs { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; margin-bottom: 10px; }
    .asv-recs div { border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 9px 12px; font-size: 13.5px; color: #6b7280; display: flex; gap: 8px; align-items: center; }
    .asv-recs div i { font-size: 18px; }
    .asv-recs div.on { border-color: var(--hr-primary, #e66136); color: var(--hr-primary-text, var(--hr-primary, #e66136)); background: rgba(var(--hr-primary-rgb, 230, 97, 54), .06); font-weight: 600; }
    @media (max-width: 767px) {
        .asv-table thead { display: none; }
        .asv-table td.rate { width: auto; }
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="asv-wrap">
            <div class="asv-header">
                <div>
                    <h3>Candidate Assessment</h3>
                    <div class="asv-sub">
                        <span><?= esc($assessment['candidate_name'] ?: 'Candidate') ?><?= !empty($assessment['job_title']) ? ' — ' . esc($assessment['job_title']) : '' ?></span>
                        <?php if (!empty($assessment['recommendation'])): ?>
                            <span class="asv-rec"><?= esc($assessment['recommendation']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="asv-actions">
                    <div class="asv-score">
                        <div class="num"><?= $score['max'] ? $score['total'] . ' / ' . $score['max'] : '-' ?></div>
                        <div class="lbl">OVERALL RATING</div>
                    </div>
                    <a href="/assessment/edit/<?= $assessment['id'] ?>" class="btn hr-btnbg"><i class="mdi mdi-pencil me-1"></i> Edit</a>
                    <a href="/assessment/pdf/<?= $assessment['id'] ?>" target="_blank" rel="noopener" class="btn btn-light"><i class="mdi mdi-printer me-1"></i> Print</a>
                    <a href="/assessment/pdf/<?= $assessment['id'] ?>?download=1" class="btn btn-light"><i class="mdi mdi-download me-1"></i> Download PDF</a>
                    <a href="/assessment" class="btn btn-light"><i class="mdi mdi-arrow-left me-1"></i> Back</a>
                </div>
            </div>

            <div class="asv-card">
                <div class="asv-section-title"><span class="tag">Section A</span> | Interview Details</div>
                <div class="row">
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Candidate Name</div><div class="asv-value"><?= $show($assessment['candidate_name']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Position Applied For</div><div class="asv-value"><?= $show($assessment['job_title']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Date of Interview</div><div class="asv-value"><?= $show($interviewDate) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Interviewer Name</div><div class="asv-value"><?= $show($assessment['interviewer_name']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Interview Round</div><div class="asv-value"><?= $show($assessment['interview_round']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Department</div><div class="asv-value"><?= $show($assessment['department']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Contact Number</div><div class="asv-value"><?= $show($assessment['contact_number']) ?></div></div>
                    <div class="col-md-6 col-lg-3 asv-item"><div class="asv-label">Reporting Manager</div><div class="asv-value"><?= $show($assessment['reporting_manager'] ?? '') ?></div></div>
                </div>

                <div class="asv-section-title"><span class="tag">Section B</span> | Competency Evaluation</div>
                <div class="table-responsive-sm">
                    <table class="asv-table">
                        <thead>
                            <tr>
                                <th>Evaluation Parameter</th>
                                <?php foreach ($ratingLabels as $num => $label): ?>
                                    <th><?= $num ?><br><span style="font-weight:500;"><?= esc($label) ?></span></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= esc($row['title']) ?></div>
                                        <?php if ($row['desc']): ?><div class="text-muted" style="font-size:12px;"><?= esc($row['desc']) ?></div><?php endif; ?>
                                    </td>
                                    <?php foreach ($ratingLabels as $num => $label): ?>
                                        <td class="rate"><span class="asv-dot <?= $row['rating'] === $num ? 'on' : '' ?>" title="<?= esc($label) ?>"></span></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="asv-total">
                    <span>Overall Rating (Total Score out of <?= $score['max'] ?: 30 ?>)</span>
                    <span class="val"><?= $score['max'] ? $score['total'] . ' / ' . $score['max'] : '-' ?></span>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <div class="asv-section-title"><span class="tag">Section C</span> | Key Strengths</div>
                        <div class="asv-text"><?= $show($assessment['feedback']['strengths'] ?? '') ?></div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="asv-section-title"><span class="tag">Section D</span> | Areas of Concern / Development Needs</div>
                        <div class="asv-text"><?= $show($assessment['feedback']['weaknesses'] ?? '') ?></div>
                    </div>
                </div>

                <div class="asv-section-title"><span class="tag">Section E</span> | Final Recommendation</div>
                <div class="asv-recs">
                    <?php foreach (\App\Controllers\InterviewAssessments::RECOMMENDATIONS as $rec): $on = ($assessment['recommendation'] ?? '') === $rec; ?>
                        <div class="<?= $on ? 'on' : '' ?>"><i class="mdi <?= $on ? 'mdi-checkbox-marked' : 'mdi-checkbox-blank-outline' ?>"></i> <?= esc($rec) ?></div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($assessment['recommendation']) && !in_array($assessment['recommendation'], \App\Controllers\InterviewAssessments::RECOMMENDATIONS, true)): ?>
                    <p class="text-muted" style="font-size:13px;">Saved recommendation: <strong><?= esc($assessment['recommendation']) ?></strong></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
