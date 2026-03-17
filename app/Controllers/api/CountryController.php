<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CountryModel;
use App\Models\UserInfoModel;
use App\Models\JobLocationAddressModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;

class CountryController extends ResourceController
{
    private $countryModel;
    private $userInfoModel;
    private $jobLocationModel;
    private $authService;

    public function __construct()
    {
        $this->countryModel = new CountryModel();
        $this->userInfoModel = new UserInfoModel();
        $this->jobLocationModel = new JobLocationAddressModel();
        $this->authService = new AuthService(service('request'));
    }

    // Create Department
    public function create()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    if (!in_array($user->role, ['admin', 'hr'])) {
        return $this->failForbidden('Forbidden: You do not have access to this resource');
    }

    $data = $this->request->getPost();

    if (!$this->validate([
        'country_name' => 'required|string',
    ])) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $this->validator->getErrors()
        ], 400);
    }

    // ✅ Normalize input for comparison and save
    $data['country_name'] = ucwords(strtolower(trim($data['country_name'])));

    // ✅ Check for duplicate (case-insensitive)
    $existing = $this->countryModel
        ->where('LOWER(country_name)', strtolower($data['country_name']))
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Country already exists.'
        ], 409); // 409 Conflict
    }

    $data['created_by'] = $user->sub;

    if ($this->countryModel->insert($data)) {
        return $this->respond([
            'status' => 'success',
            'message' => 'Country record added successfully'
        ], 201);
    }

    return $this->respond([
        'status' => 'error',
        'message' => 'Failed to add Country record'
    ], 500);
}

    // Display All Departments
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $records = $this->countryModel->orderBy('created_at', 'DESC')->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }
   
    // Update Department
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $data = $this->request->getPost();

        if ($this->countryModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Country record updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update Country record'], 500);
    }
 
    // Delete Payroll Record (Admin Only)
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
    
        // Check if the country is linked to employees or jobs
        $employeeCount = $this->userInfoModel->where('country_id', $id)->countAllResults();
        $jobCount = $this->jobLocationModel->where('country_id', $id)->countAllResults();
    
        if ($employeeCount > 0 || $jobCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This country is associated with employees or jobs and cannot be deleted.'
            ], 400);
        }
    
        // Proceed with deletion if no dependencies
        if ($this->countryModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Country record deleted successfully']);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'Failed to delete Country record'], 500);
    }
    

    // Add JWT authorization to protected routes with role checking
    public function getById($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        // Only Admin and HR can access leave records
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
    
        $record = $this->countryModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'Leave type not found'], 404);
    }

    public function creates()
    {
        return view('country/country');
    }

    public function display()
    {
        return view('country/view');
    }
    public function getAllCountry()
    {
        $countryModel = new CountryModel();
        $countries  = $countryModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $countries 
        ]);
    }
   public function addCountry()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    $countryName = trim($this->request->getPost('country_name'));

    if (!$this->validate([
        'country_name' => 'required|string|min_length[2]',
    ])) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->validator->getErrors()
        ]);
    }

    $countryModel = new CountryModel();

    // 🔍 Check for existing country (case-insensitive)
    $existing = $countryModel->where('LOWER(country_name)', strtolower($countryName))->first();
    if ($existing) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'This country already exists.'
        ]);
    }

    $data = [
        'country_name' => $countryName,
        'created_by' => $user->sub
    ];

    if ($countryModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'country' => [
                'id' => $countryModel->insertID(),
                'country_name' => $countryName
            ]
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to add country. Please try again.'
        ]);
    }
}
    

}
