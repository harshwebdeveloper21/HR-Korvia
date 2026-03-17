<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CompanyLogoModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;

class CompanyLogoController extends ResourceController
{

    private $companyLogoModel;
    private $authService;

    public function __construct()
    {
        $this->companyLogoModel = new CompanyLogoModel();
        $this->authService = new AuthService(service('request'));
    }

 
    public function getCompanyLogo()
    {
        $company = $this->companyLogoModel->first(); // Fetch latest logo

        // Set default logo path
        $defaultLogo = base_url(env('ImagePath').'upload/fab_logo.jpg');

        if ($company && !empty($company['logo_img'])) {
            return $this->respond([
                'status' => 'success',
                'logo_img' =>base_url('upload/' . $company['logo_img'])
            ]);
        } else {
            return $this->respond([
                'status' => 'success',
                'logo_img' => $defaultLogo // Return default logo instead of error
            ]);
        }
    }

    public function updateLogo()
    {
        $companyModel = new CompanyLogoModel();
        $company = $companyModel->first();

        $logo = $this->request->getFile('logo_img');

        // Check if a file is uploaded
        if (!$logo->isValid()) {
            return $this->fail(['logo_img' => 'You must upload an image.']);
        }

        // Define validation rules
        $validationRules = [
            'logo_img' => 'uploaded[logo_img]|is_image[logo_img]|mime_in[logo_img,image/jpg,image/jpeg,image/png,image/webp]|max_size[logo_img,2048]'
        ];

        // Validate the uploaded file
        if (!$this->validate($validationRules)) {
            return $this->fail(['logo_img' => 'Invalid file format. Only JPG, JPEG, PNG, and WEBP images are allowed.']);
        }

        // Move and rename the uploaded file
        $newLogoName = $logo->getRandomName();
        $logo->move(FCPATH . 'upload/', $newLogoName);

        // Delete old logo if it exists
        if ($company && !empty($company['logo_img']) && file_exists(FCPATH . 'upload/' . $company['logo_img'])) {
            unlink(FCPATH . 'upload/' . $company['logo_img']);
        }

        // Update or insert the logo in the database
        if ($company) {
            $companyModel->update($company['id'], ['logo_img' => $newLogoName]);
        } else {
            $companyModel->insert(['logo_img' => $newLogoName]);
        }

        return $this->respond([
            'status' => 'success',
            'logo_img' => base_url('upload/' . $newLogoName),
            'message' => 'Company logo updated successfully!'
        ]);
    }
}
