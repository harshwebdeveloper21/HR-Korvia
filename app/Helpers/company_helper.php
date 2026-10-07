<?php

use App\Models\CompanyLogoModel;

if (!function_exists('getCompanyLogo')) {
    function getCompanyLogo()
    {
        $companyModel = new CompanyLogoModel();
        $company = $companyModel->first(); // Fetch the first record

        return !empty($company['logo_img']) 
            ? base_url('upload/' . $company['logo_img']) 
            : base_url(env('ImagePath') . 'assets/images/fab_logo.png');
    }
}

if (!function_exists('getCompanyFavicon')) {
    function getCompanyFavicon()
    {
        $companyModel = new CompanyLogoModel();
        $company = $companyModel->first(); // Fetch the first record

        return !empty($company['favicon_icon']) 
            ? base_url('upload/' . $company['favicon_icon']) 
            : base_url(env('ImagePath') . 'assets/images/fab_fav_icon.png');
    }
}

if (!function_exists('getCompanyPdfLogo')) {
    function getCompanyPdfLogo()
    {
        $companyModel = new CompanyLogoModel();
        $company = $companyModel->first(); // Fetch the first record

        if (!empty($company['pdf_logo'])) {
            return base_url('upload/' . $company['pdf_logo']);
        }
        return getCompanyLogo();
    }
}

if (!function_exists('getCompanyName')) {
    function getCompanyName()
    {
        $companyModel = new CompanyLogoModel();
        $company = $companyModel->first(); // Fetch the first record

        return !empty($company['company_name']) 
            ? $company['company_name'] 
            : 'Fablead Developers Technolab';
    }
}

if (!defined('DEFAULT_PRIMARY_COLOR')) {
    define('DEFAULT_PRIMARY_COLOR', '#e66136');
}

if (!function_exists('normalizeHexColor')) {
    function normalizeHexColor($color)
    {
        $color = strtolower(trim((string) $color));
        if (preg_match('/^#?([0-9a-f]{3})$/', $color, $m)) {
            $c = $m[1];
            return '#' . $c[0] . $c[0] . $c[1] . $c[1] . $c[2] . $c[2];
        }
        if (preg_match('/^#?([0-9a-f]{6})$/', $color, $m)) {
            return '#' . $m[1];
        }
        return null;
    }
}

if (!function_exists('getPrimaryColor')) {
    function getPrimaryColor()
    {
        static $color = null;
        if ($color !== null) {
            return $color;
        }

        $color = DEFAULT_PRIMARY_COLOR;
        try {
            $company = (new CompanyLogoModel())->first();
            $saved = normalizeHexColor($company['primary_color'] ?? '');
            if ($saved) {
                $color = $saved;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Unable to read primary color: ' . $e->getMessage());
        }

        return $color;
    }
}

if (!function_exists('hexToRgb')) {
    function hexToRgb($hex)
    {
        $hex = ltrim(normalizeHexColor($hex) ?? DEFAULT_PRIMARY_COLOR, '#');
        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}

if (!function_exists('isLightColor')) {
    /**
     * YIQ perceived brightness; the default brand orange (#e66136) scores ~132 and stays "dark" (white text).
     */
    function isLightColor($hex)
    {
        [$r, $g, $b] = hexToRgb($hex);
        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000 >= 150;
    }
}

if (!function_exists('getContrastTextColor')) {
    function getContrastTextColor($hex)
    {
        return isLightColor($hex) ? '#000000' : '#ffffff';
    }
}

if (!function_exists('shadeHexColor')) {
    /**
     * Positive percent mixes toward white, negative toward black.
     */
    function shadeHexColor($hex, $percent)
    {
        [$r, $g, $b] = hexToRgb($hex);
        $p = max(-100, min(100, $percent)) / 100;
        $target = $p < 0 ? 0 : 255;
        $p = abs($p);
        $mix = static fn ($c) => (int) round($c + ($target - $c) * $p);
        return sprintf('#%02x%02x%02x', $mix($r), $mix($g), $mix($b));
    }
}

if (!function_exists('getPrimaryTheme')) {
    function getPrimaryTheme()
    {
        $primary = getPrimaryColor();
        $isLight = isLightColor($primary);

        return [
            'primary'      => $primary,
            'rgb'          => implode(', ', hexToRgb($primary)),
            'contrast'     => getContrastTextColor($primary),
            'is_light'     => $isLight,
            'is_default'   => $primary === DEFAULT_PRIMARY_COLOR,
            'dark'         => shadeHexColor($primary, -8),
            'light'        => shadeHexColor($primary, 15),
            'lighter'      => shadeHexColor($primary, 22),
            // Primary used as text on white surfaces; light primaries are darkened to stay readable.
            'text'         => $isLight ? shadeHexColor($primary, -55) : $primary,
        ];
    }
}

if (!function_exists('getDefaultProfileImage')) {
    function getDefaultProfileImage()
    {
        return base_url(env('ImagePath') . 'assets/images/default_avatar.png');
    }
}

if (!function_exists('getUserProfileImage')) {
    function getUserProfileImage($profileImage = null)
    {
        if (!empty($profileImage) && $profileImage !== '1789966027_54c5a38ccda20f7c2bac.jpg') {
            $rootUpload = ROOTPATH . 'upload/' . $profileImage;
            $fcUpload = defined('FCPATH') ? FCPATH . 'upload/' . $profileImage : '';
            if (file_exists($rootUpload) || (!empty($fcUpload) && file_exists($fcUpload))) {
                return base_url('upload/' . $profileImage);
            }
        }
        return getDefaultProfileImage();
    }
}

