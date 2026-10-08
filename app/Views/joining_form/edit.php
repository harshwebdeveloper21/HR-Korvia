<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<?php
$textareas = ['present_address', 'permanent_address'];
$hints = [
    'aadhaar_number' => '1234 5678 9012',
    'pan_number'     => 'ABCDE1234F',
    'ifsc'           => 'SBIN0001234',
    'joining_date'   => 'dd/mm/yyyy',
    'date_of_birth'  => 'dd/mm/yyyy',
    'blood_group'    => 'e.g. B+',
];
$old = static fn(string $key, string $fallback) => old($key) !== null ? old($key) : $fallback;
$renderChecklist = static function (array $items): string {
    $html = '';
    foreach ($items as $item) {
        $id = 'chk_' . $item['key'];
        $html .= '<label class="jf-check' . ($item['done'] ? ' is-done' : '') . '" for="' . $id . '">';
        if ($item['auto']) {
            $html .= '<input type="checkbox" id="' . $id . '" checked disabled>';
        } else {
            $html .= '<input type="checkbox" id="' . $id . '" name="checks[]" value="' . esc($item['key']) . '"' . ($item['done'] ? ' checked' : '') . '>';
        }
        $html .= '<span>' . esc($item['label']) . '</span>';
        if ($item['auto']) {
            $html .= '<em class="jf-auto">Auto</em>';
        }
        $html .= '</label>';
    }
    return $html;
};
?>
<style>
    .jf-wrap { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(16,24,40,.06); }
    .jf-head { display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #eef0f3; }
    .jf-head h4 { margin:0; font-size:18px; font-weight:700; color:#1f2937; }
    .jf-head p { margin:2px 0 0; font-size:12.5px; color:#6b7280; }
    .jf-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .jf-actions .btn { height:36px; display:inline-flex; align-items:center; gap:5px; padding:0 14px; font-size:13px; border-radius:8px; }
    .jf-actions .btn-light { background:#fff; border:1px solid #e5e7eb; }
    .jf-body { padding:18px 20px; }
    .jf-sec { margin-bottom:18px; }
    .jf-sec-title { font-size:12.5px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:var(--hr-primary-text,var(--hr-primary,#1f4e3d)); border-bottom:2px solid rgba(var(--hr-primary-rgb,31,78,61),.18); padding-bottom:5px; margin-bottom:12px; }
    .jf-grid { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:12px 16px; }
    .jf-field label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:4px; }
    .jf-field .form-control { font-size:13px; height:36px; border-radius:8px; }
    .jf-field textarea.form-control { height:auto; min-height:64px; }
    .jf-field.span-2 { grid-column:span 2; }
    .jf-field .jf-src { font-size:10.5px; color:#9ca3af; margin-top:2px; }
    .jf-checks { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:8px 16px; }
    .jf-check { display:flex; align-items:center; gap:8px; border:1px solid #e5e7eb; border-radius:8px; padding:8px 10px; font-size:13px; margin:0; cursor:pointer; }
    .jf-check.is-done { background:rgba(var(--hr-primary-rgb,31,78,61),.06); border-color:rgba(var(--hr-primary-rgb,31,78,61),.25); }
    .jf-check input { width:16px; height:16px; accent-color:var(--hr-primary,#1f4e3d); }
    .jf-check input:disabled + span { color:#374151; }
    .jf-auto { margin-left:auto; font-style:normal; font-size:10.5px; font-weight:700; color:var(--hr-on-primary,#fff); background:var(--hr-primary,#1f4e3d); padding:1px 7px; border-radius:10px; }
    .jf-note { font-size:12px; color:#6b7280; margin-top:6px; }
    .jf-assets { width:100%; font-size:12.5px; margin-top:10px; }
    .jf-assets th { background:var(--hr-primary,#1f4e3d); color:var(--hr-on-primary,#fff); font-weight:600; padding:7px 8px; }
    .jf-assets td { padding:7px 8px; border-bottom:1px solid #eef0f3; }
    .jf-foot { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid #eef0f3; }
    @media (max-width: 991px) { .jf-grid { grid-template-columns:repeat(2, minmax(0,1fr)); } }
    @media (max-width: 575px) { .jf-grid, .jf-checks { grid-template-columns:1fr; } .jf-field.span-2 { grid-column:span 1; } }
</style>

<form method="post" action="/employee/joining-form/<?= (int) $userId ?>/save" class="jf-wrap" id="joiningForm">
    <?= csrf_field() ?>
    <div class="jf-head">
        <div>
            <h4><i class="mdi mdi-file-account-outline me-1"></i> Edit Joining Form</h4>
            <p><?= esc($fields['employee_name']) ?><?= $fields['employee_id'] ? ' · ' . esc($fields['employee_id']) : '' ?><?= $savedAt ? ' · Last saved ' . date('d M Y, h:i A', strtotime($savedAt)) : '' ?></p>
        </div>
        <div class="jf-actions">
            <a href="/empview" class="btn btn-light"><i class="mdi mdi-arrow-left"></i> Back</a>
            <a href="/employee/joining-form/<?= (int) $userId ?>" target="_blank" rel="noopener" class="btn btn-light"><i class="mdi mdi-printer"></i> Print</a>
            <button type="submit" class="btn hr-btnbg"><i class="mdi mdi-content-save-outline"></i> Save</button>
        </div>
    </div>

    <div class="jf-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (!$tableReady): ?>
            <div class="alert alert-warning py-2">Saving is not available yet. Run <code>db/server_update_joining_form.sql</code> on the database first.</div>
        <?php endif; ?>

        <?php foreach ($sections as $title => $group): ?>
            <div class="jf-sec">
                <div class="jf-sec-title"><?= esc($title) ?></div>
                <div class="jf-grid">
                    <?php foreach ($group as $key => $label):
                        $value = $old($key, $fields[$key] ?? '');
                        $autoVal = (string) ($autoFields[$key] ?? '');
                    ?>
                        <div class="jf-field<?= in_array($key, $textareas, true) ? ' span-2' : '' ?>">
                            <label for="f_<?= $key ?>"><?= esc($label) ?></label>
                            <?php if (in_array($key, $textareas, true)): ?>
                                <textarea class="form-control" id="f_<?= $key ?>" name="<?= $key ?>" rows="2"><?= esc($value) ?></textarea>
                            <?php else: ?>
                                <input type="text" class="form-control" id="f_<?= $key ?>" name="<?= $key ?>" value="<?= esc($value) ?>" placeholder="<?= esc($hints[$key] ?? '') ?>" autocomplete="off">
                            <?php endif; ?>
                            <?php if ($autoVal !== '' && $autoVal !== $value): ?>
                                <div class="jf-src">From profile: <?= esc($autoVal) ?></div>
                            <?php elseif ($autoVal !== ''): ?>
                                <div class="jf-src">Auto-filled from employee profile</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($title === 'Permanent Address' || array_key_exists('permanent_address', $group)): ?>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="sameAddress">
                        <label class="form-check-label small" for="sameAddress">Permanent address same as present address</label>
                    </div>
                <?php endif; ?>
                <?php if (array_key_exists('bank_name', $group)): ?>
                    <div class="jf-note">Bank details are also saved to the employee's bank account details for payroll.</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="jf-sec">
            <div class="jf-sec-title">Document Checklist &mdash; for HR use</div>
            <div class="jf-checks"><?= $renderChecklist($documentChecklist) ?></div>
            <?php if ($extraDocuments): ?>
                <div class="jf-note">Other uploaded documents: <?= esc(implode(', ', $extraDocuments)) ?></div>
            <?php endif; ?>
            <div class="jf-note"><em class="jf-auto">Auto</em> items are ticked from uploaded documents. <a href="/employee-documents/<?= (int) $userId ?>">Upload documents</a></div>
        </div>

        <div class="jf-sec mb-0">
            <div class="jf-sec-title">Onboarding &amp; Asset Issuance &mdash; for HR use</div>
            <div class="jf-checks"><?= $renderChecklist($onboardingChecklist) ?></div>
            <?php if ($assets): ?>
                <table class="jf-assets">
                    <thead><tr><th>Asset</th><th>Type</th><th>Serial / Model</th><th>Issued On</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($assets as $a): ?>
                            <tr>
                                <td><?= esc(trim($a['gadget_name'])) ?></td>
                                <td><?= esc($a['gadget_type'] ?? '') ?></td>
                                <td><?= esc(trim(implode(' / ', array_filter([$a['serial_number'] ?? '', $a['model_number'] ?? ''])))) ?></td>
                                <td><?= !empty($a['issuance_date']) ? date('d/m/Y', strtotime($a['issuance_date'])) : '' ?></td>
                                <td><?= esc(!empty($a['return_date']) ? 'Returned' : $a['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <div class="jf-note">Assets are pulled from Gadget Issuance. <a href="/gadget-issuance">Issue a new asset</a></div>
        </div>
    </div>

    <div class="jf-foot">
        <a href="/empview" class="btn btn-light border">Cancel</a>
        <button type="submit" class="btn btn-light border" name="then_print" value="1" formtarget="_self"><i class="mdi mdi-printer"></i> Save &amp; Print</button>
        <button type="submit" class="btn hr-btnbg"><i class="mdi mdi-content-save-outline"></i> Save</button>
    </div>
</form>

<script>
(function () {
    const same = document.getElementById('sameAddress');
    const present = document.getElementById('f_present_address');
    const permanent = document.getElementById('f_permanent_address');
    if (same && present && permanent) {
        same.checked = permanent.value.trim() !== '' && permanent.value.trim() === present.value.trim();
        same.addEventListener('change', function () {
            if (this.checked) permanent.value = present.value;
            permanent.readOnly = this.checked;
        });
        present.addEventListener('input', function () {
            if (same.checked) permanent.value = present.value;
        });
        permanent.readOnly = same.checked;
    }
})();
</script>
<?= $this->endSection(); ?>
