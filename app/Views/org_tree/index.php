<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>
<?php
$canOpenProfile = in_array($meRole, ['admin', 'hr', 'branch_admin'], true);

$person = function (array $p, string $variant = '') use ($canOpenProfile) {
    $tag   = ($canOpenProfile && $p['info_id']) ? 'a' : 'div';
    $href  = $tag === 'a' ? ' href="/employee/profile/' . (int) $p['info_id'] . '"' : '';
    $sub   = $p['designation'] !== '' ? $p['designation'] : $p['role_label'];
    $html  = '<' . $tag . $href . ' class="ot-person ' . $variant . ($p['is_me'] ? ' is-me' : '') . '" data-search="' . esc(strtolower($p['name'] . ' ' . $p['employee_code'] . ' ' . $sub)) . '">';
    $html .= '<img src="' . esc($p['avatar']) . '" alt="" onerror="this.onerror=null;this.src=\'' . getDefaultProfileImage() . '\';">';
    $html .= '<span class="ot-person-text"><span class="ot-name">' . esc($p['name']) . ($p['is_me'] ? ' <em class="ot-you">You</em>' : '') . '</span>';
    $html .= '<span class="ot-sub">' . esc($sub) . ($p['employee_code'] ? ' · ' . esc($p['employee_code']) : '') . '</span></span>';
    $html .= '</' . $tag . '>';
    return $html;
};

$card = function (string $level, string $icon, string $title, string $caption, array $people, string $empty = '') use ($person) {
    $html  = '<div class="ot-card ot-' . $level . '">';
    $html .= '<div class="ot-card-head"><i class="mdi ' . $icon . '"></i><span class="ot-card-title">' . esc($title) . '</span>';
    if ($caption !== '') {
        $html .= '<span class="ot-card-cap">' . esc($caption) . '</span>';
    }
    $html .= '</div><div class="ot-card-body">';
    if ($people) {
        foreach ($people as $p) {
            $html .= $person($p);
        }
    } else {
        $html .= '<div class="ot-empty">' . esc($empty) . '</div>';
    }
    $html .= '</div><button type="button" class="ot-toggle" aria-label="Collapse"><i class="mdi mdi-minus"></i></button></div>';
    return $html;
};

$renderBranches = function () use ($branchNodes, $card) {
    $html = '';
    foreach ($branchNodes as $b) {
        $html .= '<li>' . $card('branch', 'mdi-office-building', $b['title'], 'Branch Manager', $b['managers'], 'No branch manager assigned');
        if ($b['departments']) {
            $html .= '<ul>';
            foreach ($b['departments'] as $d) {
                $html .= '<li>' . $card('department', 'mdi-briefcase-outline', $d['title'], 'Department Manager', $d['managers'], 'No department manager');
                if ($d['employees']) {
                    $html .= '<ul><li>' . $card('employee', 'mdi-account-group-outline', 'Employees', count($d['employees']) . ' member' . (count($d['employees']) > 1 ? 's' : ''), $d['employees']) . '</li></ul>';
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
    }
    return $html;
};
?>
<style>
    .ot-page { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(16,24,40,.06); padding:18px 20px; }
    .ot-top { display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .ot-top h4 { margin:0; font-size:18px; font-weight:700; color:#1f2937; }
    .ot-top p { margin:2px 0 0; font-size:12.5px; color:#6b7280; }
    .ot-tools { display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
    .ot-tools .ot-search { position:relative; }
    .ot-tools .ot-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; }
    .ot-tools .ot-search input { height:34px; border:1px solid #e5e7eb; border-radius:8px; padding:0 10px 0 30px; font-size:13px; width:220px; outline:none; }
    .ot-tools .ot-search input:focus { border-color:var(--hr-primary,#e8602c); }
    .ot-btn { height:34px; min-width:34px; border:1px solid #e5e7eb; background:#fff; border-radius:8px; font-size:13px; color:#374151; padding:0 10px; display:inline-flex; align-items:center; gap:4px; }
    .ot-btn:hover { border-color:var(--hr-primary,#e8602c); color:var(--hr-primary-text,var(--hr-primary,#e8602c)); }
    .ot-stats { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
    .ot-stat { background:rgba(var(--hr-primary-rgb,232,96,44),.08); color:var(--hr-primary-text,var(--hr-primary,#e8602c)); border-radius:20px; padding:4px 12px; font-size:12px; font-weight:600; }
    .ot-legend { display:flex; gap:12px; flex-wrap:wrap; font-size:12px; color:#6b7280; margin-left:auto; align-items:center; }
    .ot-legend span::before { content:''; display:inline-block; width:10px; height:10px; border-radius:3px; margin-right:5px; vertical-align:-1px; background:var(--c); }

    .ot-canvas { overflow:auto; border:1px dashed #e5e7eb; border-radius:10px; background:
        radial-gradient(circle, #eef0f3 1px, transparent 1px) 0 0/18px 18px; padding:24px 12px 32px; min-height:420px; max-height:calc(100vh - 260px); cursor:grab; }
    .ot-canvas.dragging { cursor:grabbing; user-select:none; }
    .org-tree { display:inline-block; min-width:100%; text-align:center; }

    .org-tree ul { position:relative; display:flex; justify-content:center; padding:22px 0 0; margin:0; }
    .org-tree li { list-style:none; position:relative; padding:22px 8px 0; display:flex; flex-direction:column; align-items:center; }
    .org-tree li::before, .org-tree li::after { content:''; position:absolute; top:0; right:50%; width:50%; height:22px; border-top:2px solid #cbd5e1; }
    .org-tree li::after { right:auto; left:50%; border-left:2px solid #cbd5e1; }
    .org-tree li:only-child::before, .org-tree li:only-child::after { display:none; }
    .org-tree li:only-child { padding-top:0; }
    .org-tree li:first-child::before, .org-tree li:last-child::after { border:0 none; }
    .org-tree li:last-child::before { border-right:2px solid #cbd5e1; border-radius:0 8px 0 0; }
    .org-tree li:first-child::after { border-radius:8px 0 0 0; }
    .org-tree ul ul::before { content:''; position:absolute; top:0; left:50%; height:22px; border-left:2px solid #cbd5e1; }
    .org-tree > ul { padding-top:0; }
    .org-tree li.ot-collapsed > ul { display:none; }

    .ot-card { --lvl:#64748b; position:relative; background:#fff; border:1px solid #e5e7eb; border-top:3px solid var(--lvl); border-radius:10px; min-width:210px; max-width:260px; box-shadow:0 2px 6px rgba(16,24,40,.06); text-align:left; }
    .ot-card-head { display:flex; align-items:center; gap:6px; padding:8px 10px 6px; border-bottom:1px solid #f1f5f9; flex-wrap:wrap; }
    .ot-card-head i { color:var(--lvl); font-size:16px; }
    .ot-card-title { font-weight:700; font-size:13px; color:#111827; }
    .ot-card-cap { margin-left:auto; font-size:10.5px; font-weight:600; color:var(--lvl); background:color-mix(in srgb, var(--lvl) 12%, transparent); padding:1px 7px; border-radius:10px; white-space:nowrap; }
    .ot-card-body { padding:6px; display:flex; flex-direction:column; gap:4px; }
    .ot-employee .ot-card-body { max-height:260px; overflow:auto; }
    .ot-empty { font-size:12px; color:#9ca3af; padding:4px 6px; font-style:italic; }

    .ot-admin { --lvl:var(--hr-primary,#e8602c); }
    .ot-hr { --lvl:#0e9fb3; }
    .ot-branch { --lvl:#1e40af; }
    .ot-department { --lvl:#d97706; }
    .ot-employee { --lvl:#64748b; }

    .ot-person { display:flex; align-items:center; gap:8px; padding:5px 6px; border-radius:8px; color:inherit; text-decoration:none; }
    a.ot-person:hover { background:#f8fafc; color:inherit; }
    .ot-person img { width:30px; height:30px; border-radius:50%; object-fit:cover; flex:0 0 30px; background:#f1f5f9; }
    .ot-person-text { display:flex; flex-direction:column; min-width:0; }
    .ot-name { font-size:12.5px; font-weight:600; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .ot-sub { font-size:11px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .ot-person.is-me { background:rgba(var(--hr-primary-rgb,232,96,44),.10); box-shadow:inset 3px 0 0 var(--hr-primary,#e8602c); }
    .ot-you { font-style:normal; font-size:10px; font-weight:700; color:var(--hr-on-primary,#fff); background:var(--hr-primary,#e8602c); border-radius:8px; padding:0 6px; margin-left:4px; }
    .ot-person.ot-hit { outline:2px solid var(--hr-primary,#e8602c); background:rgba(var(--hr-primary-rgb,232,96,44),.08); }

    .ot-toggle { position:absolute; left:50%; bottom:-11px; transform:translateX(-50%); width:22px; height:22px; border-radius:50%; border:1px solid #cbd5e1; background:#fff; color:#475569; font-size:13px; line-height:1; display:none; align-items:center; justify-content:center; padding:0; z-index:2; }
    li.ot-has-children > .ot-card > .ot-toggle { display:inline-flex; }
    .ot-toggle:hover { border-color:var(--hr-primary,#e8602c); color:var(--hr-primary,#e8602c); }

    @media (max-width: 767px) {
        .ot-page { padding:14px 12px; }
        .ot-tools .ot-search input { width:100%; }
        .ot-tools, .ot-tools .ot-search { width:100%; }
        .ot-legend { margin-left:0; }
    }
</style>

<div class="ot-page">
    <div class="ot-top">
        <div>
            <h4><i class="mdi mdi-sitemap-outline me-1"></i> Organization Tree</h4>
            <p><?= $isFullView ? 'Complete hierarchy: Super Admin → HR → Branch Manager → Department Manager → Employees' : 'Your reporting hierarchy' ?></p>
        </div>
        <div class="ot-tools">
            <div class="ot-search"><i class="mdi mdi-magnify"></i><input type="text" id="otSearch" placeholder="Search name or emp ID..." autocomplete="off"></div>
            <button type="button" class="ot-btn" id="otExpand" title="Expand all"><i class="mdi mdi-arrow-expand-all"></i></button>
            <button type="button" class="ot-btn" id="otCollapse" title="Collapse to branches"><i class="mdi mdi-arrow-collapse-all"></i></button>
            <button type="button" class="ot-btn" id="otZoomOut" title="Zoom out"><i class="mdi mdi-magnify-minus-outline"></i></button>
            <button type="button" class="ot-btn" id="otZoomReset" title="Reset zoom"><span id="otZoomLabel">100%</span></button>
            <button type="button" class="ot-btn" id="otZoomIn" title="Zoom in"><i class="mdi mdi-magnify-plus-outline"></i></button>
        </div>
    </div>

    <div class="ot-stats">
        <span class="ot-stat"><?= (int) $totals['branches'] ?> Branch<?= $totals['branches'] == 1 ? '' : 'es' ?></span>
        <span class="ot-stat"><?= (int) $totals['departments'] ?> Department<?= $totals['departments'] == 1 ? '' : 's' ?></span>
        <span class="ot-stat"><?= (int) $totals['people'] ?> Staff</span>
        <div class="ot-legend">
            <span style="--c:var(--hr-primary,#e8602c)">Super Admin</span>
            <span style="--c:#0e9fb3">HR</span>
            <span style="--c:#1e40af">Branch Manager</span>
            <span style="--c:#d97706">Department Manager</span>
            <span style="--c:#64748b">Employee</span>
        </div>
    </div>

    <div class="ot-canvas" id="otCanvas">
        <div class="org-tree" id="orgTree">
            <ul>
                <li>
                    <?= $card('admin', 'mdi-shield-crown-outline', 'Super Admin', 'Top level', $admins, 'Not assigned') ?>
                    <?php if ($hrs || $branchNodes): ?>
                        <ul>
                            <?php if ($hrs): ?>
                                <li>
                                    <?= $card('hr', 'mdi-account-tie-outline', 'Human Resources', 'HR', $hrs) ?>
                                    <?php if ($branchNodes): ?><ul><?= $renderBranches() ?></ul><?php endif; ?>
                                </li>
                            <?php else: ?>
                                <?= $renderBranches() ?>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
(function () {
    const tree = document.getElementById('orgTree');
    const canvas = document.getElementById('otCanvas');

    tree.querySelectorAll('li').forEach(function (li) {
        if (li.querySelector(':scope > ul')) li.classList.add('ot-has-children');
    });

    function setCollapsed(li, collapsed) {
        li.classList.toggle('ot-collapsed', collapsed);
        const icon = li.querySelector(':scope > .ot-card > .ot-toggle i');
        if (icon) icon.className = 'mdi ' + (collapsed ? 'mdi-plus' : 'mdi-minus');
    }

    tree.addEventListener('click', function (e) {
        const btn = e.target.closest('.ot-toggle');
        if (!btn) return;
        const li = btn.closest('li');
        setCollapsed(li, !li.classList.contains('ot-collapsed'));
    });

    document.getElementById('otExpand').addEventListener('click', function () {
        tree.querySelectorAll('li.ot-has-children').forEach(li => setCollapsed(li, false));
    });
    document.getElementById('otCollapse').addEventListener('click', function () {
        tree.querySelectorAll('li.ot-has-children').forEach(function (li) {
            setCollapsed(li, !!li.querySelector(':scope > .ot-card.ot-branch'));
        });
    });

    var zoom = 1;
    const label = document.getElementById('otZoomLabel');
    function applyZoom() {
        tree.style.zoom = zoom;
        label.textContent = Math.round(zoom * 100) + '%';
    }
    document.getElementById('otZoomIn').addEventListener('click', () => { zoom = Math.min(1.5, +(zoom + 0.1).toFixed(2)); applyZoom(); });
    document.getElementById('otZoomOut').addEventListener('click', () => { zoom = Math.max(0.4, +(zoom - 0.1).toFixed(2)); applyZoom(); });
    document.getElementById('otZoomReset').addEventListener('click', () => { zoom = 1; applyZoom(); });

    document.getElementById('otSearch').addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        let first = null;
        tree.querySelectorAll('.ot-person').forEach(function (el) {
            const hit = q !== '' && el.dataset.search.indexOf(q) !== -1;
            el.classList.toggle('ot-hit', hit);
            if (hit) {
                let li = el.closest('li');
                while (li) { setCollapsed(li, false); li = li.parentElement.closest('li'); }
                if (!first) first = el;
            }
        });
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
    });

    let drag = null;
    canvas.addEventListener('mousedown', function (e) {
        if (e.target.closest('a, button, input')) return;
        drag = { x: e.clientX, y: e.clientY, left: canvas.scrollLeft, top: canvas.scrollTop };
        canvas.classList.add('dragging');
    });
    window.addEventListener('mousemove', function (e) {
        if (!drag) return;
        canvas.scrollLeft = drag.left - (e.clientX - drag.x);
        canvas.scrollTop = drag.top - (e.clientY - drag.y);
    });
    window.addEventListener('mouseup', function () {
        drag = null;
        canvas.classList.remove('dragging');
    });

    const fit = (canvas.clientWidth - 24) / tree.scrollWidth;
    if (fit < 1) {
        zoom = Math.max(0.6, Math.floor(fit * 20) / 20);
        applyZoom();
    }
    canvas.scrollLeft = (canvas.scrollWidth - canvas.clientWidth) / 2;

    const me = tree.querySelector('.ot-person.is-me');
    if (me) setTimeout(() => me.scrollIntoView({ block: 'nearest', inline: 'center' }), 100);
})();
</script>
<?= $this->endSection(); ?>
