<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\TrainingModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\EmailService;

class TrainingController extends ResourceController
{
    private $trainingModel;
    private $authService;

    public function __construct()
    {
        $this->trainingModel = new TrainingModel();
        $this->authService = new AuthService(service('request'));
    }

    public function create()
    {
        $userModel = new \App\Models\UserModel();
        $departmentModel = new \App\Models\DepartmentModel();

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // Fetch departments
        $departments = $departmentModel->findAll();

        return view('training/training', [
            'employees' => $employees,
            'departments' => $departments
        ]);
    }

    public function display()
    {
        return view('training/view');
    }
    public function profilePage()
    {
        return view('training/profile');
    }
    public function creates()
    {
        // Check if user is authorized with a valid token
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Role-based access control (RBAC)
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to create performance records');
        }

        // Validate input data
        $data = $this->request->getPost();
        if (!$this->validate([
            'training_title' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Training title is required.'
                ]
            ],
            'user_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Employee name is required.'
                ]
            ],
            'department_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Department selection is required.'
                ]
            ],
            // 'description' => [
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'Description of the training is required.'
            //     ]
            // ],
            'start_date' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Please enter a valid start date (YYYY-MM-DD).'
                ]
            ],
            'end_date' => [
                'rules' => 'required|valid_date[Y-m-d]|check_end_date[start_date]',
                'errors' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'Please enter a valid end date (YYYY-MM-DD).',
                    'check_end_date' => 'End date must be after the start date.'
                ]
            ],
            'location' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Training location is required.'
                ]
            ],
        ])) {
            return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
        }


        if ($this->trainingModel->insert($data)) {

            $trainingId = $this->trainingModel->insertID();

            // Send notification after insert
            $notificationModel = new \App\Models\NotificationModel();
            $userModel = new \App\Models\UserModel();
            $employee = $userModel->find($data['user_id']);
            $sender = $userModel->find($user->sub);

            // Notify admin, HR, and the selected employee
            $recipients = $userModel
                ->whereIn('role', ['admin', 'hr'])
                ->orWhere('id', $data['user_id']) // Include employee
                ->findAll();

            foreach ($recipients as $recipient) {
                $notificationModel->insert([
                    'sender_id'    => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'type'     => 'training',
                        'username' => $sender['username'],
                        'employee' => $employee['username'],
                        'message'  => 'New training assigned to ' . $employee['username'],
                        'user_id'  => $data['user_id']
                    ]),
                    'is_read' => 0
                ]);
            }

            // Optional: Send email
            if ($trainingId) {
                $emailService = new EmailService();
                $emailService->sendTrainingEmail($trainingId);
            }

            return $this->respond(['status' => 'success', 'message' => 'Training added successfully'], 201);
        }


        return $this->respond(['status' => 'error', 'message' => 'Failed to add training record'], 500);
    }

    // Get all performance records (Admin only)
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $this->trainingModel->select('training.*,training.created_at, training.training_title, users.username as employee_name, training.start_date, training.end_date, training.location,user_info.profile_image')
            ->join('users', 'users.id = training.user_id')
             ->join('user_info', 'user_info.user_id = training.user_id');

        // Role-based filtering
        if ($user->role === 'admin') {
            // Admin can see all records (no filter)
            $records = $this->trainingModel->orderBy('created_at', 'DESC')->findAll();
        } elseif ($user->role === 'hr') {
            // HR can only see employee records (exclude admin & HR)
            $records = $this->trainingModel->where('users.role', 'employee')->orderBy('created_at', 'DESC')->findAll();
        } elseif ($user->role === 'employee') {
            // Employee can only see their own records
            $records = $this->trainingModel->where('training.user_id', $user->sub)->orderBy('created_at', 'DESC')->findAll();
        } else {
            return $this->failForbidden('Forbidden: Unauthorized role');
        }


        return $this->respond(['status' => 'success', 'data' => $records]);
    }

    // Get performance records by employee (Admin, HR, Employee)
    public function getByEmployee($employeeId = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Employee can only view their own performance records
        if ($user->role === 'employee' && $user->sub !== $employeeId) {
            return $this->failForbidden('Forbidden: You can only access your own performance records');
        }


        // Change 'employee_id' to 'user_id' (as per your model)
        $records = $this->trainingModel->where('id', $employeeId)->findAll();

        return $this->respond(['status' => 'success', 'data' => $records]);
    }


    // Update performance record (Admin or HR can update)
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Check user role for authorization
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to update training records');
        }

        // Get input data
        $data = $this->request->getPost();

        // Validate input data before updating
        if (!$this->validate([
            'training_title' => [
                'rules' => 'required',
                'errors' => ['required' => 'Training title is required.']
            ],
            'user_id' => [
                'rules' => 'required',
                'errors' => ['required' => 'Employee name is required.']
            ],
            'department_id' => [
                'rules' => 'required',
                'errors' => ['required' => 'Department selection is required.']
            ],
            // 'description' => [
            //     'rules' => 'required',
            //     'errors' => ['required' => 'Description of the training is required.']
            // ],
            'start_date' => [
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Please enter a valid start date (YYYY-MM-DD).'
                ]
            ],
            'end_date' => [
                'rules' => 'required|valid_date[Y-m-d]|check_end_date[start_date]',
                'errors' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'Please enter a valid end date (YYYY-MM-DD).',
                    'check_end_date' => 'End date must be after the start date.'
                ]
            ],
            'location' => [
                'rules' => 'required',
                'errors' => ['required' => 'Training location is required.']
            ],
        ])) {
            return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
        }

        // Check if the record exists before updating
        $existingRecord = $this->trainingModel->find($id);
        if (!$existingRecord) {
            return $this->respond(['status' => 'error', 'message' => 'Training record not found'], 404);
        }

        // Update training record in database
        if ($this->trainingModel->update($id, $data)) {
            $emailService = new EmailService();
            $emailService->sendTrainingEmail($id);
            return $this->respond(['status' => 'success', 'message' => 'Training record updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update training record'], 500);
    }


    // Delete performance record (Admin or HR can delete)
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Check user role for deletion permission
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to delete performance records');
        }

        if ($this->trainingModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'training record deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete training record'], 500);
    }
    public function getProfile($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Fetch training details along with user and designation info
        // $record = $this->trainingModel
        //     ->select('training.*, users.username as employee_name, department.department_name')
        //     ->join('users', 'users.id = training.user_id')
        //     ->join('department', 'department.id = training.department_id')
        //     ->where('training.id', $id)
        //     ->first();
        $record = $this->trainingModel
        ->select('training.*, 
                  users.username as employee_name, 
                  ui.email, ui.employee_id, 
                  des.designation_name, 
                  dep.department_name')
        ->join('users', 'users.id = training.user_id','left')
        ->join('user_info ui', 'ui.user_id = users.id','left')
        ->join('designation des', 'des.id = ui.designation_id','left')
        ->join('department dep', 'dep.id = ui.department_id','left')
        ->where('training.id', $id)
        ->first();
        if (!$record) {
            return $this->failNotFound('training record not found');
        }

        return $this->respond(['status' => 'success', 'data' => $record]);
    }
    public function addDepartment()
    {
        $departmentModel = new \App\Models\DepartmentModel();
        $departmentName = $this->request->getPost('department_name');

        if (empty($departmentName)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Department Name is required.']);
        }

        $data = [
            'department_name' => $departmentName
        ];

        $departmentId = $departmentModel->insert($data);

        if ($departmentId) {
            return $this->response->setJSON([
                'success' => true,
                'department' => [
                    'id' => $departmentId,
                    'department_name' => $departmentName
                ]
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to add department.']);
        }
    }
}
