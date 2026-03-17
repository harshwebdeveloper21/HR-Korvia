<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use App\Models\LeaveTypeModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;

class LeaveTypeController extends ResourceController
{
    private $leaveTypeModel;
    private $authService;

    public function __construct()
    {
        $this->leaveTypeModel = new LeaveTypeModel();
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

        // Only Admin and HR can add payroll records
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        // Retrieve input data
        $data = $this->request->getPost();

        // Validation
        if (!$this->validate([
            'leave_type' => 'required|string',
            'number_of_leaves' => 'required|integer',
            'allow_half_day' => 'required|in_list[0,1]'
        ])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Check if leave_type already exists (case-insensitive)
        $existing = $this->leaveTypeModel
            ->where('LOWER(leave_type)', strtolower($data['leave_type']))
            ->first();

        if ($existing) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This leave type already exists.',
            ], 409); // 409 Conflict
        }

        // Add the creator's ID
        $data['created_by'] = $user->sub;
        // Insert data into the database
        if ($this->leaveTypeModel->insert($data)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Leave type record added successfully'
            ], 201);
        }
        return $this->respond([
            'status' => 'error',
            'message' => 'Failed to add leave record'
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

        $records = $this->leaveTypeModel->orderBy('created_at', 'DESC')->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }
    // Display Single Department
    public function getByEmployee($employeeId = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
     
        // Employees can only view their own payroll records
        if ($user->role === 'employee' && $user->sub !== $employeeId) {
            return $this->failForbidden('Forbidden: You can only access your own performance records');
         }

        $records = $this->leaveTypeModel->where('user_id', $employeeId)->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }

    // Update the leave type record
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

        // Validate leave_type and new fields
        if (!$this->validate([
            'leave_type' => 'required|string',
            'number_of_leaves' => 'required|integer',
            'allow_half_day' => 'required|in_list[0,1]',
        ])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Update the record
        if ($this->leaveTypeModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Leave type updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update leave type'], 500);
    }


    // Delete Payroll Record (Admin Only)
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        if ($user->role !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admin can delete leave types');
        }
    
        $db = \Config\Database::connect();
    
        // Check if the leave type is being used in the leaves table
        $leaveCount = $db->table('leaves')->where('leave_id', $id)->countAllResults();
    
        if ($leaveCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This leave type is assigned to users and cannot be deleted.'
            ], 400);
        }
    
        // Proceed with deletion
        if ($this->leaveTypeModel->delete($id)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Leave type deleted successfully!'
            ]);
        }
    
        return $this->respond([
            'status' => 'error',
            'message' => 'Failed to delete leave type'
        ], 500);
    }
      
   // Display a single leave type by ID
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

        $record = $this->leaveTypeModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Leave type not found'], 404);
    }

    public function creates()
    {        
        return view('leave_type/leave_type');
    }

    public function display()
    {
        return view('leave_type/view');
    }  

}
