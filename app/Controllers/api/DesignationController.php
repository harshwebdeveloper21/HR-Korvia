<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DesignationModel;
use App\Models\DepartmentModel;
use App\Services\AuthService;
use CodeIgniter\Config\Services;

class DesignationController extends ResourceController
{
    protected $authService;
    private $designationModel;

    public function __construct()
    {
        // Inject the AuthService
        $this->authService = Services::auth($this->request);  
        // Initialize the DesignationModel
        $this->designationModel = new DesignationModel();
    }

    // Create Designation
   public function create()
{
    $user = $this->authorize(['admin', 'hr']);
    if (!$user) {
        return $this->failUnauthorized('Unauthorized access');
    }

    $data = $this->request->getPost();

    // Validate input
    if (!$this->validate([
        'designation_name' => 'required|min_length[3]',
        'department_id' => 'required|numeric',
    ])) {
        return $this->failValidationErrors($this->validator->getErrors());
    }

    // Normalize input
    $designationName = ucwords(strtolower(trim($data['designation_name'])));
    $departmentId = $data['department_id'];

    // ✅ Check for existing designation with same name in the same department
    $existing = $this->designationModel
        ->where('LOWER(designation_name)', strtolower($designationName))
        ->where('department_id', $departmentId)
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Designation already exists for the selected department.'
        ], 409); // Conflict
    }

    // Insert
    $designationId = $this->designationModel->insert([
        'designation_name' => $designationName,
        'department_id' => $departmentId,
    ]);

    return $this->respondCreated([
        'message' => 'Designation created successfully!',
        'id' => $designationId,
    ]);
}

    // Display All Designations
    public function index()
    {
        $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }
    
        $designations = $this->designationModel->getDesignationsWithDepartment();
    
        return $this->respond([
            'designations' => $designations,
        ]);
    }
    

    // Display Single Designation
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
    
        $record = $this->designationModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'designation not found'], 404);
    }
    // Update Designation
    public function update($id = null)
    {
        $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $data = $this->request->getPost();

        // Validate incoming data
        if (!$this->validate([
            'designation_name' => 'required|min_length[3]',
            'department_id' => 'required|numeric',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Update the designation
        $updated = $this->designationModel->update($id, [
            'designation_name' => $data['designation_name'],
            'department_id' => $data['department_id'],
        ]);

        if (!$updated) {
            return $this->failServerError('Failed to update designation');
        }

        return $this->respond([
            'message' => 'Designation updated successfully!',
        ]);
    }

    // Delete Designation
   public function delete($id = null)
{
    $user = $this->authorize(['admin', 'hr']); // Only allow admin and HR roles
    if (!$user) {
        return $this->failUnauthorized('Unauthorized access');
    }

    // Check if designation exists
    $designation = $this->designationModel->find($id);
    if (!$designation) {
        return $this->failNotFound('Designation not found');
    }

    $db = \Config\Database::connect();

    // Check if designation is being used in user_info or performance table
    $usersCount = $db->table('user_info')->where('designation_id', $id)->countAllResults();
    $performanceCount = $db->table('performance')->where('designation_id', $id)->countAllResults();

    if ($usersCount > 0 || $performanceCount > 0) {
        return $this->respond([
            'status' => 'error',
            'message' => 'This Designation is associated with employees or performance records and cannot be deleted.'
        ], 400);
    }

    // Proceed with deletion
    if (!$this->designationModel->delete($id)) {
        return $this->failServerError('Failed to delete Designation');
    }

    return $this->respond([
        'status' => 'success',
        'message' => 'Designation deleted successfully!'
    ]);
}




    // Add JWT authorization to protected routes with role checking
    private function authorize($roles = [])
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, $roles)) {
            log_message('error', 'Unauthorized access: User does not have the required role');
            return false;
        }
        return $user;
    }

    public function creates()
    {
        $departmentModel = new DepartmentModel(); // Assuming you have a DepartmentModel
        $departments = $departmentModel->findAll(); // Fetch all departments
    
        return view('designation/designation', [
            'departments' => $departments
        ]);
    }
    

    public function display()
    {
        return view('designation/view');
    }
    public function getAllDesignation()
    {
        $countryModel = new DesignationModel();
        $designations  = $countryModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $designations 
        ]);
    }
  public function addDesignation()
{
    $designationModel = new \App\Models\DesignationModel();
    $departmentId = trim($this->request->getPost('department_id'));
    $designationName = trim($this->request->getPost('designation_name'));

    $errors = [];

    if (empty($departmentId)) {
        $errors['department_id'] = 'Department is required.';
    }

    if (empty($designationName)) {
        $errors['designation_name'] = 'Designation Name is required.';
    }

    if (!empty($errors)) {
        return $this->response->setJSON(['success' => false, 'errors' => $errors]);
    }

    // Check for duplicates (optional)
    $existing = $designationModel->where([
        'department_id' => $departmentId,
        'designation_name' => $designationName
    ])->first();

    if ($existing) {
        return $this->response->setJSON(['success' => false, 'errors' => [
            'designation_name' => 'This designation already exists for the selected department.'
        ]]);
    }

    $data = [
        'department_id' => $departmentId,
        'designation_name' => $designationName
    ];

    $designationId = $designationModel->insert($data);

    if ($designationId) {
        return $this->response->setJSON([
            'success' => true,
            'designation' => [
                'id' => $designationId,
                'designation_name' => $designationName
            ]
        ]);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to add designation.']);
    }
}


}
