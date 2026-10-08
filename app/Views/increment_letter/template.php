<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<style>
    .lt-wrap { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(16,24,40,.06); }
    .lt-head { display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #eef0f3; }
    .lt-head h4 { margin:0; font-size:18px; font-weight:700; color:#1f2937; }
    .lt-head p { margin:2px 0 0; font-size:12.5px; color:#6b7280; }
    .lt-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .lt-actions .btn, .lt-actions .form-select { height:36px; font-size:13px; border-radius:8px; }
    .lt-actions .btn { display:inline-flex; align-items:center; gap:5px; padding:0 14px; }
    .lt-actions .btn-light { background:#fff; border:1px solid #e5e7eb; }
    .lt-actions .form-select { width:auto; padding-top:0; padding-bottom:0; }
    .lt-body { padding:18px 20px; }
    .lt-layout { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:20px; align-items:start; }
    .lt-sec { margin-bottom:20px; }
    .lt-sec-title { font-size:12.5px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:var(--hr-primary-text,var(--hr-primary,#1f4e3d)); border-bottom:2px solid rgba(var(--hr-primary-rgb,31,78,61),.18); padding-bottom:5px; margin-bottom:12px; }
    .lt-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px 16px; }
    .lt-field label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:4px; }
    .lt-field .form-control { font-size:13px; height:36px; border-radius:8px; }
    .lt-field.is-rich { grid-column:1 / -1; }
    .lt-side { position:sticky; top:80px; border:1px solid #eef0f3; border-radius:10px; padding:14px; background:#fafbfc; }
    .lt-side h6 { font-size:13px; font-weight:700; margin:0 0 4px; color:#1f2937; }
    .lt-side p { font-size:11.5px; color:#6b7280; margin:0 0 10px; }
    .lt-chip { display:flex; justify-content:space-between; gap:6px; width:100%; text-align:left; border:1px solid #e5e7eb; background:#fff; border-radius:7px; padding:5px 8px; margin-bottom:5px; font-size:11.5px; cursor:pointer; }
    .lt-chip:hover { border-color:var(--hr-primary,#1f4e3d); }
    .lt-chip code { color:var(--hr-primary-text,var(--hr-primary,#1f4e3d)); font-size:11px; }
    .lt-chip span { color:#6b7280; text-align:right; }
    .lt-foot { display:flex; justify-content:space-between; gap:8px; padding:14px 20px; border-top:1px solid #eef0f3; }
    @media (max-width: 991px) { .lt-layout { grid-template-columns:1fr; } .lt-side { position:static; } }
    @media (max-width: 575px) { .lt-grid { grid-template-columns:1fr; } }
</style>

<form method="post" action="/increment-letter-template/save" class="lt-wrap" id="templateForm">
    <?= csrf_field() ?>
    <div class="lt-head">
        <div>
            <h4><i class="mdi mdi-file-document-edit-outline me-1"></i> Increment / Promotion Letter Template</h4>
            <p>Edit the wording used on every increment and promotion letter. Employee details are filled in automatically from the placeholders.</p>
        </div>
        <div class="lt-actions">
            <select class="form-select" id="previewType" aria-label="Preview type">
                <option value="increment">Preview: Increment</option>
                <option value="promotion">Preview: Promotion</option>
                <option value="both">Preview: Increment + Promotion</option>
            </select>
            <button type="button" class="btn btn-light" id="previewBtn"><i class="mdi mdi-eye-outline"></i> Preview PDF</button>
            <button type="submit" class="btn hr-btnbg" <?= $tableReady ? '' : 'disabled' ?>><i class="mdi mdi-content-save-outline"></i> Save</button>
        </div>
    </div>

    <div class="lt-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (!$tableReady): ?>
            <div class="alert alert-warning py-2">Saving is not available yet. Run <code>db/server_update_letter_template_settings.sql</code> on the database first. Preview still works.</div>
        <?php endif; ?>

        <div class="lt-layout">
            <div>
                <?php foreach ($groups as $groupTitle => $fields): ?>
                    <div class="lt-sec">
                        <div class="lt-sec-title"><?= esc($groupTitle) ?></div>
                        <div class="lt-grid">
                            <?php foreach ($fields as $key => [$label]):
                                $isRich = in_array($key, $richFields, true);
                            ?>
                                <div class="lt-field<?= $isRich ? ' is-rich' : '' ?>">
                                    <label for="tpl_<?= $key ?>"><?= esc($label) ?></label>
                                    <?php if ($isRich): ?>
                                        <textarea class="form-control lt-rich" id="tpl_<?= $key ?>" name="tpl[<?= $key ?>]" rows="4"><?= esc($values[$key]) ?></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control lt-input" id="tpl_<?= $key ?>" name="tpl[<?= $key ?>]" value="<?= esc($values[$key]) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p class="text-muted small mb-0">Leaving a field empty uses the default wording. The signature and stamp come from the default Digital Signature; phone and email in the footer come from Company settings.</p>
            </div>

            <aside class="lt-side">
                <h6>Placeholders</h6>
                <p>Click the field you want to edit, then click a placeholder to insert it at the cursor.</p>
                <?php foreach ($placeholders as $key => $desc): ?>
                    <button type="button" class="lt-chip" data-placeholder="{<?= esc($key) ?>}">
                        <code>{<?= esc($key) ?>}</code><span><?= esc($desc) ?></span>
                    </button>
                <?php endforeach; ?>
            </aside>
        </div>
    </div>

    <div class="lt-foot">
        <button type="submit" name="reset" value="1" class="btn btn-outline-danger btn-sm" id="resetBtn" <?= $tableReady ? '' : 'disabled' ?>>
            <i class="mdi mdi-restore"></i> Reset to Default
        </button>
        <button type="submit" class="btn hr-btnbg btn-sm" <?= $tableReady ? '' : 'disabled' ?>><i class="mdi mdi-content-save-outline"></i> Save Template</button>
    </div>
</form>

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
(function () {
    const form = document.getElementById('templateForm');
    let lastTarget = null;

    document.querySelectorAll('.lt-rich').forEach(function (el) {
        const editor = CKEDITOR.replace(el.id, {
            versionCheck: false,
            height: 110,
            removePlugins: 'elementspath',
            resize_enabled: false,
            toolbar: [
                ['Bold', 'Italic', 'Underline'],
                ['NumberedList', 'BulletedList'],
                ['RemoveFormat', 'Undo', 'Redo']
            ]
        });
        editor.on('focus', function () { lastTarget = editor; });
    });

    document.querySelectorAll('.lt-input').forEach(function (input) {
        input.addEventListener('focus', function () { lastTarget = input; });
    });

    document.querySelectorAll('.lt-chip').forEach(function (chip) {
        chip.addEventListener('mousedown', function (e) { e.preventDefault(); });
        chip.addEventListener('click', function () {
            const text = chip.dataset.placeholder;
            if (!lastTarget) {
                alert('Click inside a field first, then choose a placeholder.');
                return;
            }
            if (lastTarget instanceof HTMLInputElement) {
                const start = lastTarget.selectionStart ?? lastTarget.value.length;
                const end = lastTarget.selectionEnd ?? start;
                lastTarget.value = lastTarget.value.slice(0, start) + text + lastTarget.value.slice(end);
                lastTarget.focus();
                lastTarget.setSelectionRange(start + text.length, start + text.length);
            } else {
                lastTarget.focus();
                lastTarget.insertText(text);
            }
        });
    });

    function syncEditors() {
        for (const name in CKEDITOR.instances) {
            CKEDITOR.instances[name].updateElement();
        }
    }

    form.addEventListener('submit', function (e) {
        syncEditors();
        if (e.submitter && e.submitter.id === 'resetBtn'
            && !confirm('Reset the whole template back to the default wording? Your saved changes will be removed.')) {
            e.preventDefault();
        }
    });

    document.getElementById('previewBtn').addEventListener('click', async function () {
        const btn = this;
        syncEditors();
        const data = new FormData(form);
        data.append('preview_type', document.getElementById('previewType').value);

        const win = window.open('', '_blank');
        btn.disabled = true;
        try {
            const res = await fetch('/increment-letter-template/preview', {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const newHash = res.headers.get('X-CSRF-HASH');
            if (newHash) {
                form.querySelector('input[name="<?= csrf_token() ?>"]').value = newHash;
            }
            if (!res.ok || !(res.headers.get('Content-Type') || '').includes('application/pdf')) {
                throw new Error('Preview failed (' + res.status + '). Please reload the page and try again.');
            }
            const url = URL.createObjectURL(await res.blob());
            if (win) {
                win.location.href = url;
            } else {
                window.open(url, '_blank');
            }
        } catch (err) {
            if (win) win.close();
            alert(err.message);
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
<?= $this->endSection(); ?>
