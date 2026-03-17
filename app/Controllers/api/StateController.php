<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use App\Models\StateModel;
use App\Models\UserInfoModel;
use App\Models\JobLocationAddressModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;

class StateController extends ResourceController
{
    private $stateModel;
    private $userInfoModel;
    private $jobLocationModel;
    private $authService;

    public function __construct()
    {
        $this->stateModel = new StateModel();
        $this->userInfoModel = new UserInfoModel();
        $this->jobLocationModel = new JobLocationAddressModel();
        $this->authService = new AuthService(service('request'));
    }

    // Create Department
    public function create()
{
    // Check user authorization
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    // Only Admin and HR can add state records
    if (!in_array($user->role, ['admin', 'hr'])) {
        return $this->failForbidden('Forbidden: You do not have access to this resource');
    }

    // Retrieve input data
    $data = $this->request->getPost();

    // Validate required fields
    if (!$this->validate([
        'state_name' => 'required|string|min_length[2]',
    ])) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $this->validator->getErrors()
        ], 400);
    }

    // Check if the state already exists (case-insensitive)
    $existing = $this->stateModel
        ->where('LOWER(state_name)', strtolower($data['state_name']))
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'State already exists'
        ], 409); // 409 Conflict
    }

    // Add created_by field
    $data['created_by'] = $user->sub;

    // Insert the state
    if ($this->stateModel->insert($data)) {
        return $this->respond([
            'status' => 'success',
            'message' => 'State record added successfully'
        ], 201);
    }

    return $this->respond([
        'status' => 'error',
        'message' => 'Failed to add state record'
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

        $records = $this->stateModel->orderBy('created_at', 'DESC')->findAll();
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

        if ($this->stateModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'state record updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update state record'], 500);
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
                'message' => 'This state is associated with employees or jobs and cannot be deleted.'
            ], 400);
        }
    
        // Proceed with deletion if no dependencies
        if ($this->stateModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'state record deleted successfully']);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'Failed to delete state record'], 500);
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
    
        $record = $this->stateModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'state not found'], 404);
    }

    public function creates()
    {
        return view('state/state');
    }

    public function display()
    {
        return view('state/view');
    }
    public function getAllCountry()
    {
        $countryModel = new stateModel();
        $countries  = $countryModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $countries 
        ]);
    }
     public function addState()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    $stateName = trim($this->request->getPost('state_name'));

    // Validate input
    if (empty($stateName)) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => ['state_name' => 'State name is required.']
        ]);
    }

    $stateModel = new StateModel();

    // 🔍 Check for duplicate state (case-insensitive)
    $existing = $stateModel->where('LOWER(state_name)', strtolower($stateName))->first();
    if ($existing) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'This state already exists.'
        ]);
    }

    $data = [
        'state_name' => $stateName,
        'created_by' => $user->sub,
    ];

    if ($stateModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'country' => [
                'id' => $stateModel->insertID(),
                'state_name' => $stateName
            ]
        ]);
    }

    return $this->response->setJSON([
        'status' => 'error',
        'message' => 'Failed to add state. Please try again.'
    ]);
}

}
