<?= $this->extend("layout") ?>
<?= $this->section("content") ?>
<style>
    .theme-swatches {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .theme-swatch {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #d6d9e0;
        cursor: pointer;
        padding: 0;
        transition: transform 0.15s ease;
    }
    .theme-swatch:hover { transform: scale(1.1); }
    .theme-swatch.selected { box-shadow: 0 0 0 2px #1c2333; }
    .theme-color-input {
        width: 56px;
        height: 42px;
        padding: 2px;
        border: 1px solid #d6d9e0;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
    }
    .theme-hex-input {
        max-width: 140px;
        text-transform: lowercase;
        font-family: monospace;
    }
    .theme-preview {
        border: 1px solid #e5e7ee;
        border-radius: 12px;
        overflow: hidden;
        background: #f5f6fa;
    }
    .theme-preview-header {
        padding: 14px 18px;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .theme-preview-body {
        padding: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .theme-preview-btn {
        border: 2px solid transparent;
        border-radius: 4px;
        padding: 6px 14px;
        font-size: 14px;
        font-weight: 500;
    }
    .theme-preview-outline {
        background: #fff;
        border-radius: 4px;
        padding: 6px 14px;
        font-size: 14px;
        font-weight: 500;
        border: 2px solid;
    }
    .theme-preview-badge {
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .theme-preview-link { font-weight: 600; }
    .theme-contrast-note {
        font-size: 13px;
        color: #6b7280;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-palette-outline me-2"></i>Theme Color
                </h4>
                <p class="text-muted mb-4">Set the primary color for the whole portal. It applies to every branch, and to all buttons, the header, and highlights. Text on the color switches to black or white automatically so it stays readable.</p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Primary Color</label>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <input type="color" id="primaryColorPicker" class="theme-color-input" value="<?= esc($theme['primary']) ?>" aria-label="Pick primary color">
                            <input type="text" id="primaryColorHex" class="form-control theme-hex-input" value="<?= esc($theme['primary']) ?>" maxlength="7" aria-label="Primary color HEX">
                        </div>

                        <label class="form-label fw-semibold">Quick Picks</label>
                        <div class="theme-swatches mb-3" id="themeSwatches" data-theme-ignore>
                            <?php foreach ([$defaultColor, '#1f3bb3', '#0d6efd', '#7b20c7', '#d63384', '#dc3545', '#198754', '#34b1aa', '#ffc107', '#f8f9fa', '#1c2333', '#000000'] as $swatch): ?>
                                <button type="button" class="theme-swatch" data-color="<?= esc($swatch) ?>" style="background: <?= esc($swatch) ?>;" title="<?= esc($swatch) ?><?= $swatch === $defaultColor ? ' (default)' : '' ?>"></button>
                            <?php endforeach; ?>
                        </div>

                        <p class="theme-contrast-note mb-4" id="contrastNote"></p>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn hr-btnbg" id="saveThemeBtn">
                                <i class="mdi mdi-content-save"></i>Save Color
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetThemeBtn">
                                <i class="mdi mdi-restore"></i>Reset to Default
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Live Preview</label>
                        <div class="theme-preview" data-theme-ignore>
                            <div class="theme-preview-header" id="previewHeader">
                                <span>Good Afternoon, <strong>Korvia</strong></span>
                                <i class="mdi mdi-bell-outline"></i>
                            </div>
                            <div class="theme-preview-body">
                                <span class="theme-preview-btn" id="previewBtn"><i class="mdi mdi-plus"></i> Add Employee</span>
                                <span class="theme-preview-outline" id="previewOutline">View All</span>
                                <span class="theme-preview-badge" id="previewBadge">Active</span>
                                <a href="javascript:void(0)" class="theme-preview-link" id="previewLink">Sample link</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    var DEFAULT_COLOR = <?= json_encode($defaultColor) ?>;
    var savedColor = <?= json_encode($theme['primary']) ?>;

    function normalize(hex) {
        hex = String(hex || '').trim().toLowerCase();
        if (hex.charAt(0) !== '#') hex = '#' + hex;
        if (/^#[0-9a-f]{3}$/.test(hex)) {
            hex = '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
        }
        return /^#[0-9a-f]{6}$/.test(hex) ? hex : null;
    }

    function rgb(hex) {
        return [parseInt(hex.substr(1, 2), 16), parseInt(hex.substr(3, 2), 16), parseInt(hex.substr(5, 2), 16)];
    }

    // Must match isLightColor() in company_helper.php.
    function isLight(hex) {
        var c = rgb(hex);
        return (c[0] * 299 + c[1] * 587 + c[2] * 114) / 1000 >= 150;
    }

    function shade(hex, percent) {
        var c = rgb(hex), p = Math.abs(percent) / 100, t = percent < 0 ? 0 : 255;
        return '#' + c.map(function (v) {
            return ('0' + Math.round(v + (t - v) * p).toString(16)).slice(-2);
        }).join('');
    }

    function preview(hex) {
        var light = isLight(hex);
        var onColor = light ? '#000000' : '#ffffff';
        var textColor = light ? shade(hex, -55) : hex;

        $('#previewHeader').css({ background: 'linear-gradient(135deg, ' + hex + ', ' + shade(hex, 15) + ')', color: onColor });
        $('#previewBtn').css({ background: hex, borderColor: shade(hex, -8), color: onColor });
        $('#previewOutline').css({ borderColor: hex, color: textColor });
        $('#previewBadge').css({ background: 'rgba(' + rgb(hex).join(',') + ',0.15)', color: textColor });
        $('#previewLink').css({ color: textColor });

        $('#contrastNote').html('<i class="mdi mdi-format-color-text me-1"></i>This is a <strong>' + (light ? 'light' : 'dark') +
            '</strong> color, so text on it will be <strong>' + (light ? 'black' : 'white') + '</strong>.');

        $('.theme-swatch').removeClass('selected').filter('[data-color="' + hex + '"]').addClass('selected');
    }

    function setColor(hex) {
        $('#primaryColorPicker').val(hex);
        $('#primaryColorHex').val(hex).removeClass('is-invalid');
        preview(hex);
    }

    $('#primaryColorPicker').on('input change', function () {
        setColor(normalize(this.value));
    });

    $('#primaryColorHex').on('input', function () {
        var hex = normalize(this.value);
        $(this).toggleClass('is-invalid', !hex);
        if (hex) {
            $('#primaryColorPicker').val(hex);
            preview(hex);
        }
    });

    $('#themeSwatches').on('click', '.theme-swatch', function () {
        setColor($(this).data('color'));
    });

    function save(payload, $btn) {
        var original = $btn.html();
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>Saving...');
        var token = localStorage.getItem('token');

        $.ajax({
            url: '/api/theme-settings/update',
            method: 'POST',
            contentType: 'application/json',
            headers: token ? { 'Authorization': 'Bearer ' + token } : {},
            data: JSON.stringify(payload),
            success: function (res) {
                Swal.fire({
                    title: 'Saved',
                    text: res.message || 'Theme color updated.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'hr-btnbg' }
                }).then(function () {
                    window.location.reload();
                });
            },
            error: function (xhr) {
                Swal.fire({
                    title: 'Error',
                    text: (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update theme color',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function () {
                $btn.prop('disabled', false).html(original);
            }
        });
    }

    $('#saveThemeBtn').on('click', function () {
        var hex = normalize($('#primaryColorHex').val());
        if (!hex) {
            $('#primaryColorHex').addClass('is-invalid').focus();
            return;
        }
        save({ primary_color: hex }, $(this));
    });

    $('#resetThemeBtn').on('click', function () {
        setColor(DEFAULT_COLOR);
        save({ reset: true }, $(this));
    });

    setColor(normalize(savedColor) || DEFAULT_COLOR);
});
</script>
<?= $this->endSection() ?>
