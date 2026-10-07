/*
 * Turns the "Action" column of list tables into one primary button plus a "more" (⋯) menu.
 * The original links/buttons are moved into the menu, so their onclick and delegated handlers keep working.
 * Opt out per table with data-no-action-menu, or per cell with data-no-action-menu on the <td>.
 */
(function () {
    'use strict';

    var ICON_LABELS = [
        [/mdi-eye/, 'View'],
        [/mdi-printer/, 'Print'],
        [/mdi-(download|file-download|file-pdf)/, 'Download'],
        [/mdi-(pencil|square-edit|lead-pencil|file-edit)/, 'Edit'],
        [/mdi-(delete|trash)/, 'Delete'],
        [/mdi-clipboard-check/, 'Assessment'],
        [/mdi-clipboard-plus/, 'Add Assessment'],
        [/mdi-history/, 'History'],
        [/mdi-(check-circle|check)/, 'Approve'],
        [/mdi-(close-circle|close|cancel)/, 'Reject'],
        [/mdi-(email|send)/, 'Send'],
        [/mdi-content-copy/, 'Duplicate'],
        [/mdi-(account-convert|account-switch)/, 'Convert'],
        [/mdi-key/, 'Reset Password'],
        [/mdi-(lock|block)/, 'Block'],
        [/mdi-(upload|cloud-upload)/, 'Upload'],
        [/mdi-qrcode/, 'QR Code'],
        [/fa-eye/, 'View'],
        [/fa-(pen|edit|pencil)/, 'Edit'],
        [/fa-(trash|trash-alt)/, 'Delete'],
        [/fa-print/, 'Print'],
        [/fa-download/, 'Download']
    ];
    var PRIMARY_ORDER = ['View', 'Print', 'Edit'];
    var openMenu = null;

    function iconClasses(el) {
        var icon = el.querySelector('i, svg, span[class*="mdi"]');
        return icon ? (icon.getAttribute('class') || '') : '';
    }

    function labelFor(el) {
        var text = (el.getAttribute('title') || el.getAttribute('aria-label') || el.getAttribute('data-bs-original-title') || '').trim();
        if (!text) {
            text = (el.textContent || '').replace(/\s+/g, ' ').trim();
        }
        if (!text) {
            var classes = iconClasses(el);
            for (var i = 0; i < ICON_LABELS.length; i++) {
                if (ICON_LABELS[i][0].test(classes)) {
                    text = ICON_LABELS[i][1];
                    break;
                }
            }
        }
        return text || 'Action';
    }

    function isDanger(el, label) {
        return /delete|remove/i.test(label) || /mdi-(delete|trash)|fa-trash/.test(iconClasses(el)) || el.classList.contains('text-danger') || el.classList.contains('btn-danger');
    }

    function actionColumnIndexes(table) {
        var headRow = table.tHead && table.tHead.rows[table.tHead.rows.length - 1];
        if (!headRow) return [];
        var indexes = [];
        var col = 0;
        Array.prototype.forEach.call(headRow.cells, function (th) {
            if (/^\s*actions?\s*$/i.test(th.textContent || '')) {
                indexes.push(col);
            }
            col += th.colSpan || 1;
        });
        return indexes;
    }

    function topLevelActions(cell) {
        var candidates = Array.prototype.slice.call(cell.querySelectorAll('a, button, form'));
        return candidates.filter(function (el) {
            if (el.closest('.hr-act-wrap')) return false;
            if (el.matches('.expand-toggle, .dropdown-toggle, [data-bs-toggle="dropdown"]')) return false;
            if (el.closest('.dropdown-menu')) return false;
            if (el.tagName !== 'FORM' && el.closest('form') && cell.contains(el.closest('form'))) return false;
            var parent = el.parentElement;
            while (parent && parent !== cell) {
                if (candidates.indexOf(parent) !== -1) return false;
                parent = parent.parentElement;
            }
            return !el.classList.contains('d-none') && el.style.display !== 'none' && !el.hidden;
        });
    }

    function dropdownActions(cell) {
        var result = { items: [], wrappers: [] };
        Array.prototype.forEach.call(cell.querySelectorAll('.dropdown-menu'), function (menu) {
            if (menu.closest('.hr-act-wrap')) return;
            Array.prototype.forEach.call(menu.querySelectorAll('a, button'), function (el) {
                if (el.parentElement.closest('a, button') && menu.contains(el.parentElement.closest('a, button'))) return;
                if (el.classList.contains('d-none') || el.style.display === 'none' || el.hidden) return;
                result.items.push(el);
            });
            var wrapper = menu.closest('.dropdown, .dropup, .dropstart, .dropend, .btn-group') || menu.parentElement;
            if (wrapper && wrapper !== cell && cell.contains(wrapper)) {
                result.wrappers.push(wrapper);
            } else {
                result.wrappers.push(menu);
                Array.prototype.forEach.call(cell.querySelectorAll('[data-bs-toggle="dropdown"], .dropdown-toggle'), function (t) {
                    result.wrappers.push(t);
                });
            }
        });
        return result;
    }

    function pickPrimary(items) {
        for (var p = 0; p < PRIMARY_ORDER.length; p++) {
            for (var i = 0; i < items.length; i++) {
                if (!items[i].fromDropdown && items[i].label === PRIMARY_ORDER[p]) return i;
            }
        }
        for (var j = 0; j < items.length; j++) {
            if (!items[j].fromDropdown) return j;
        }
        return -1;
    }

    function enhanceCell(cell) {
        if (cell.dataset.hrActDone || cell.hasAttribute('data-no-action-menu')) return;
        if (cell.querySelector('select, input:not([type="hidden"])')) return;

        var elements = topLevelActions(cell);
        var dropdown = dropdownActions(cell);
        if (elements.length + dropdown.items.length < 2) return;

        var items = elements.map(function (el) {
            var target = el.tagName === 'FORM' ? (el.querySelector('button, [type="submit"]') || el) : el;
            return { el: el, target: target, label: labelFor(target) };
        }).concat(dropdown.items.map(function (el) {
            return { el: el, target: el, label: labelFor(el), fromDropdown: true };
        }));

        var primaryIndex = pickPrimary(items);
        var primary = primaryIndex === -1 ? null : items.splice(primaryIndex, 1)[0];

        items.forEach(function (item) { item.danger = isDanger(item.target, item.label); });
        items = items.filter(function (i) { return !i.danger; }).concat(items.filter(function (i) { return i.danger; }));

        var wrap = document.createElement('div');
        wrap.className = 'hr-act-wrap';

        if (primary) {
            primary.target.classList.add('hr-act-primary');
            primary.target.setAttribute('title', primary.label);
            wrap.appendChild(primary.el);
        }

        var more = document.createElement('button');
        more.type = 'button';
        more.className = 'hr-act-more';
        more.setAttribute('aria-label', 'More actions');
        more.setAttribute('aria-haspopup', 'true');
        more.innerHTML = '<i class="mdi mdi-dots-horizontal"></i>';
        wrap.appendChild(more);

        var menu = document.createElement('div');
        menu.className = 'hr-act-menu';
        menu.setAttribute('role', 'menu');

        items.forEach(function (item) {
            var target = item.target;
            target.classList.add('hr-act-item');
            target.classList.remove('fs-5', 'fs-4', 'btn-sm', 'me-1', 'me-2', 'ms-1', 'ms-2', 'py-2');
            if (item.danger) target.classList.add('hr-act-danger');
            if (!(target.textContent || '').trim()) {
                var span = document.createElement('span');
                span.className = 'hr-act-label';
                span.textContent = item.label;
                target.appendChild(span);
            }
            target.setAttribute('role', 'menuitem');
            item.el.classList.add('hr-act-menu-entry');
            menu.appendChild(item.el);
        });
        wrap.appendChild(menu);

        dropdown.wrappers.forEach(function (w) { if (w.parentNode) w.remove(); });

        // Remove empty layout wrappers that held the moved buttons
        var leftovers = Array.prototype.slice.call(cell.children);
        cell.appendChild(wrap);
        leftovers.forEach(function (child) {
            if (child !== wrap && !(child.textContent || '').trim() && !child.querySelector('a, button, form, input, select')) {
                child.remove();
            }
        });

        cell.dataset.hrActDone = '1';
        cell.classList.add('hr-act-cell');
    }

    function enhanceTable(table) {
        if (table.hasAttribute('data-no-action-menu')) return;
        var indexes = actionColumnIndexes(table);
        if (!indexes.length) return;
        Array.prototype.forEach.call(table.tBodies, function (tbody) {
            Array.prototype.forEach.call(tbody.rows, function (row) {
                var col = 0;
                Array.prototype.forEach.call(row.cells, function (cell) {
                    if (indexes.indexOf(col) !== -1) enhanceCell(cell);
                    col += cell.colSpan || 1;
                });
            });
        });
    }

    function enhanceAll() {
        Array.prototype.forEach.call(document.querySelectorAll('table'), enhanceTable);
    }

    function closeMenu() {
        if (!openMenu) return;
        openMenu.menu.classList.remove('show');
        openMenu.button.setAttribute('aria-expanded', 'false');
        openMenu = null;
    }

    function positionMenu(button, menu) {
        var rect = button.getBoundingClientRect();
        menu.style.visibility = 'hidden';
        menu.classList.add('show');
        var width = menu.offsetWidth;
        var height = menu.offsetHeight;
        var left = Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8));
        var top = rect.bottom + 4;
        if (top + height > window.innerHeight - 8) {
            top = Math.max(8, rect.top - height - 4);
        }
        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.visibility = '';
    }

    document.addEventListener('click', function (e) {
        var button = e.target.closest('.hr-act-more');
        if (button) {
            e.preventDefault();
            e.stopPropagation();
            var menu = button.parentElement.querySelector('.hr-act-menu');
            var wasOpen = openMenu && openMenu.menu === menu;
            closeMenu();
            if (!wasOpen) {
                positionMenu(button, menu);
                button.setAttribute('aria-expanded', 'true');
                openMenu = { menu: menu, button: button };
            }
            return;
        }
        if (openMenu && e.target.closest('.hr-act-menu')) {
            setTimeout(closeMenu, 0);
            return;
        }
        closeMenu();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
    window.addEventListener('resize', closeMenu);
    window.addEventListener('scroll', closeMenu, true);

    var scheduled = null;
    function schedule() {
        if (scheduled) return;
        scheduled = setTimeout(function () {
            scheduled = null;
            enhanceAll();
        }, 60);
    }

    function start() {
        enhanceAll();
        new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var m = mutations[i];
                if (m.target.closest && (m.target.closest('.hr-act-wrap'))) continue;
                if (m.addedNodes.length) {
                    schedule();
                    return;
                }
            }
        }).observe(document.body, { childList: true, subtree: true });
        if (window.jQuery) {
            window.jQuery(document).on('draw.dt init.dt', schedule);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
