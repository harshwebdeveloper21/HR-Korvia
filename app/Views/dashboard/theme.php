<?php
helper('company');
$theme = getPrimaryTheme();
?>
<style id="hr-theme-vars">
    :root {
        --hr-primary: <?= esc($theme['primary']) ?>;
        --hr-primary-rgb: <?= esc($theme['rgb']) ?>;
        --hr-primary-dark: <?= esc($theme['dark']) ?>;
        --hr-primary-light: <?= esc($theme['light']) ?>;
        --hr-primary-text: <?= esc($theme['text']) ?>;
        --hr-on-primary: <?= esc($theme['contrast']) ?>;
    }
<?php if (!$theme['is_default']): ?>
    ::selection { background: var(--hr-primary); color: var(--hr-on-primary); }
    .hr-btnbg, .btn.hr-btnbg, .hr-btnbg i, .btn.hr-btnbg i,
    .navbar.default-layout .welcome-text,
    .navbar.default-layout .welcome-text *,
    .navbar.default-layout .navbar-toggler,
    .navbar.default-layout .navbar-toggler *,
    .notification-permission-toast,
    .notification-permission-toast strong,
    .notification-permission-toast small,
    .notification-permission-toast i {
        color: var(--hr-on-primary) !important;
    }
    .hr-btnbg:hover, .hr-btnbg:focus, .hr-btnbg:active,
    .btn.hr-btnbg:hover, .btn.hr-btnbg:focus, .btn.hr-btnbg:active,
    .hr-btnbg:hover i, .hr-btnbg:focus i, .hr-btnbg:active i,
    .btn.hr-btnbg:hover i, .btn.hr-btnbg:focus i, .btn.hr-btnbg:active i {
        color: var(--hr-primary-text) !important;
    }
<?php endif; ?>
</style>
<script id="hr-table-edges">
(function () {
    'use strict';

    var CLASSES = ['hr-edge-tl', 'hr-edge-tr', 'hr-edge-bl', 'hr-edge-br'];

    function visibleCells(row) {
        if (!row) return [];
        return Array.prototype.filter.call(row.children, function (cell) {
            return getComputedStyle(cell).display !== 'none';
        });
    }

    function lastVisibleRow(tbody) {
        if (!tbody) return null;
        for (var i = tbody.rows.length - 1; i >= 0; i--) {
            var row = tbody.rows[i];
            if (getComputedStyle(row).display !== 'none' && visibleCells(row).length) return row;
        }
        return null;
    }

    function mark(cells, first, last) {
        if (!cells.length) return;
        cells[0].classList.add(first);
        cells[cells.length - 1].classList.add(last);
    }

    function markTable(table) {
        CLASSES.forEach(function (c) {
            table.querySelectorAll('.' + c).forEach(function (el) { el.classList.remove(c); });
        });
        var headRow = table.tHead && table.tHead.rows[0];
        mark(visibleCells(headRow), 'hr-edge-tl', 'hr-edge-tr');
        mark(visibleCells(lastVisibleRow(table.tBodies[0])), 'hr-edge-bl', 'hr-edge-br');
    }

    function markAll() {
        document.querySelectorAll('table').forEach(markTable);
    }

    var timer = null;
    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(markAll, 60);
    }

    document.addEventListener('DOMContentLoaded', function () {
        markAll();
        if (window.jQuery) {
            jQuery(document).on('draw.dt init.dt column-visibility.dt responsive-display.dt', schedule);
        }
        new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var t = mutations[i].target;
                if (t.nodeType === 1 && (t.closest('table') || t.querySelector && t.querySelector('table'))) {
                    schedule();
                    return;
                }
            }
        }).observe(document.body, { childList: true, subtree: true });
    });
    window.addEventListener('load', markAll);
    window.addEventListener('resize', schedule);
})();
</script>
<?php if (!$theme['is_default']): ?>
<script id="hr-theme-engine">
(function () {
    'use strict';

    var T = <?= json_encode([
        'primary'  => hexToRgb($theme['primary']),
        'dark'     => hexToRgb($theme['dark']),
        'light'    => hexToRgb($theme['light']),
        'lighter'  => hexToRgb($theme['lighter']),
        'text'     => hexToRgb($theme['text']),
        'contrast' => $theme['contrast'],
        'isLight'  => $theme['is_light'],
    ]) ?>;

    // Brand shades hardcoded across the app (as browsers serialise them) -> theme slot.
    var SOURCES = [
        { rgb: '230, 97, 54',  slot: 'primary' }, // #e66136
        { rgb: '240, 89, 41',  slot: 'dark' },    // #f05929
        { rgb: '231, 92, 37',  slot: 'dark' },    // #e75c25
        { rgb: '255, 123, 74', slot: 'light' },   // #ff7b4a
        { rgb: '240, 132, 90', slot: 'lighter' }  // #f0845a
    ];
    var HEX_SOURCES = { '#e66136': 'primary', '#f05929': 'dark', '#e75c25': 'dark', '#ff7b4a': 'light', '#f0845a': 'lighter' };

    var SOURCE_RE = new RegExp('rgba?\\((' + SOURCES.map(function (s) {
        return s.rgb.replace(/ /g, '\\s*');
    }).join('|') + ')(\\s*,\\s*[\\d.]+)?\\)', 'gi');
    var QUICK_RE = /(230,\s*97,\s*54|240,\s*89,\s*41|231,\s*92,\s*37|255,\s*123,\s*74|240,\s*132,\s*90)/;

    var primaryRgb = T.primary.join(', ');
    var primaryRe = new RegExp('rgba?\\(' + primaryRgb.replace(/ /g, '\\s*') + '(\\s*,\\s*1)?\\)');

    function slotFor(rgbText) {
        var norm = rgbText.replace(/\s+/g, '').toLowerCase();
        for (var i = 0; i < SOURCES.length; i++) {
            if (SOURCES[i].rgb.replace(/ /g, '') === norm) return SOURCES[i].slot;
        }
        return 'primary';
    }

    function remapValue(value, asText) {
        return value.replace(SOURCE_RE, function (_, rgb, alpha) {
            var target = (asText ? T.text : T[slotFor(rgb)]).join(', ');
            return alpha ? 'rgba(' + target + alpha + ')' : 'rgb(' + target + ')';
        });
    }

    function isWhite(v) {
        v = (v || '').replace(/\s+/g, '').toLowerCase();
        return v === 'white' || v === '#fff' || v === '#ffffff' || v === 'rgb(255,255,255)' || v === 'rgba(255,255,255,1)';
    }

    function isBlack(v) {
        v = (v || '').replace(/\s+/g, '').toLowerCase();
        return v === 'black' || v === '#000' || v === '#000000' || v === 'rgb(0,0,0)';
    }

    function remapStyle(style, isInline) {
        if (!style || !style.length || !QUICK_RE.test(style.cssText)) return;

        var bgChanged = false;
        var bgPriority = '';
        var props = [];
        for (var i = 0; i < style.length; i++) props.push(style[i]);

        props.forEach(function (prop) {
            var val = style.getPropertyValue(prop);
            if (!val || !QUICK_RE.test(val)) return;
            var priority = style.getPropertyPriority(prop);
            style.setProperty(prop, remapValue(val, prop === 'color'), priority);
            if (prop.indexOf('background') === 0) {
                bgChanged = true;
                bgPriority = bgPriority || priority;
            }
        });

        if (bgChanged) {
            var color = style.getPropertyValue('color');
            if (isWhite(color) || isBlack(color) || (!color && T.isLight && !isInline)) {
                style.setProperty('color', T.contrast, style.getPropertyPriority('color') || bgPriority);
            } else if (!color && T.isLight && isInline) {
                style.setProperty('color', T.contrast);
            }
        }
    }

    function walkRules(rules) {
        if (!rules) return;
        for (var i = 0; i < rules.length; i++) {
            var rule = rules[i];
            if (rule.style) remapStyle(rule.style, false);
            if (rule.cssRules) walkRules(rule.cssRules);
            if (rule.styleSheet) processSheet(rule.styleSheet);
        }
    }

    var doneSheets = typeof WeakSet !== 'undefined' ? new WeakSet() : null;

    function processSheet(sheet) {
        if (!sheet || (doneSheets && doneSheets.has(sheet))) return;
        var rules;
        try {
            rules = sheet.cssRules;
        } catch (e) {
            return; // cross-origin stylesheet (CDN) – never contains brand colors
        }
        if (!rules) return;
        if (doneSheets) doneSheets.add(sheet);
        walkRules(rules);
    }

    function processAllSheets() {
        for (var i = 0; i < document.styleSheets.length; i++) processSheet(document.styleSheets[i]);
    }

    function isIgnored(el) {
        return !!(el.closest && el.closest('[data-theme-ignore]'));
    }

    function processElement(el) {
        if (el.nodeType !== 1 || isIgnored(el)) return;
        if (el.hasAttribute('style')) remapStyle(el.style, true);
        ['fill', 'stroke', 'color', 'bgcolor'].forEach(function (attr) {
            var v = el.getAttribute(attr);
            if (v && HEX_SOURCES[v.toLowerCase()]) {
                el.setAttribute(attr, 'rgb(' + T[HEX_SOURCES[v.toLowerCase()]].join(', ') + ')');
            }
        });
    }

    function processTree(root) {
        if (!root || root.nodeType !== 1) return;
        processElement(root);
        var nodes = root.querySelectorAll('[style],[fill],[stroke],[color],[bgcolor]');
        for (var i = 0; i < nodes.length; i++) processElement(nodes[i]);
    }

    // Light primaries need dark text on primary surfaces, including children that hardcode white text.
    function isPrimarySurface(cs) {
        return primaryRe.test(cs.backgroundColor) || (cs.backgroundImage !== 'none' && cs.backgroundImage.indexOf(primaryRgb) !== -1);
    }

    function hasOwnBackground(cs) {
        return cs.backgroundImage !== 'none' || !/rgba\(0,\s*0,\s*0,\s*0\)|transparent/.test(cs.backgroundColor);
    }

    function darkenWhiteText(node) {
        for (var i = 0; i < node.children.length; i++) {
            var child = node.children[i];
            var cs = getComputedStyle(child);
            if (hasOwnBackground(cs)) continue;
            if (/rgba?\(255,\s*255,\s*255/.test(cs.color)) child.style.setProperty('color', T.contrast, 'important');
            darkenWhiteText(child);
        }
    }

    function fixContrast(root) {
        if (!T.isLight || !root || root.nodeType !== 1) return;
        var nodes = [root].concat(Array.prototype.slice.call(root.querySelectorAll('*')));
        nodes.forEach(function (el) {
            if (el.closest && el.closest('#sidebar, [data-theme-ignore]')) return;
            var cs = getComputedStyle(el);
            if (!isPrimarySurface(cs)) return;
            if (/rgba?\(255,\s*255,\s*255/.test(cs.color)) el.style.setProperty('color', T.contrast, 'important');
            darkenWhiteText(el);
        });
    }

    processAllSheets();

    var pending = [];
    var timer = null;
    function flush() {
        timer = null;
        processAllSheets();
        var batch = pending;
        pending = [];
        batch.forEach(function (node) {
            if (node.isConnected) {
                processTree(node);
                fixContrast(node);
            }
        });
    }
    function queue(node) {
        pending.push(node);
        if (!timer) timer = setTimeout(flush, 30);
    }

    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            if (m.type === 'attributes') {
                processElement(m.target);
                return;
            }
            m.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) return;
                if (node.tagName === 'LINK') {
                    node.addEventListener('load', processAllSheets);
                } else {
                    queue(node);
                }
            });
        });
    });
    observer.observe(document.documentElement, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'fill', 'stroke']
    });

    function full() {
        processAllSheets();
        if (document.body) {
            processTree(document.body);
            fixContrast(document.body);
        }
    }
    document.addEventListener('DOMContentLoaded', full);
    window.addEventListener('load', full);
})();
</script>
<?php endif; ?>
