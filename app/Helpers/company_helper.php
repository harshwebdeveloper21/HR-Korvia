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
