<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\PerformanceModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use App\Libraries\EmailService;

class PerformanceController extends ResourceController
{
    private $performanceModel;
    private $authService;

    public function __construct()
    {
        $this->performanceModel = new PerformanceModel();
        $this->authService = new AuthService(service('request'));
    }


    // Create performance record
    // public function create()
    // {
    //     // Authentication and authorization check
    //     $user = $this->authService->check();
    //     if (!$user) {
    //         return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    //     }

    //     // Role-based access control
    //     if ($user->role !== 'admin' && $user->role !== 'hr') {
    //         return $this->failForbidden('Forbidden: You do not have permission to create performance records');
    //     }

    //     // Validate input data
    //     $data = $this->request->getPost();
    //     if (!$this->validate([
    //         'user_id' => [
    //             'rules' => 'required|integer',
    //             'errors' => [
    //                 'required' => 'Name is required.',
    //                 'integer' => 'Name must be a valid number.'
    //             ]
    //         ],
    //         'reviewer_id' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Reviewer name is required.'
    //             ]
    //         ],
    //         'designation_id' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Designation is required.'
    //             ]
    //         ],
    //         'review_date' => [
    //             'rules' => 'required|valid_date',
    //             'errors' => [
    //                 'required' => 'Review date is required.',
    //                 'valid_date' => 'Please enter a valid date.'
    //             ]
    //         ],
    //         'goals_achieved' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Please provide details about goals achieved.'
    //             ]
    //         ],
    //         'team_work' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Please provide a rating for team work.'
    //             ]
    //         ],
    //         'management' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Management rating is required.'
    //             ]
    //         ],
    //         'presentation_skill' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Presentation skill rating is required.'
    //             ]
    //         ],
    //         'behaviour' => [
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => 'Behaviour rating is required.'
    //             ]
    //         ],
    //         'rating' => [
    //             'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
    //             'errors' => [
    //                 'required' => 'Overall rating is required.',
    //                 'decimal' => 'Rating must be a decimal value.',
    //                 'greater_than_equal_to' => 'Rating must be at least 0.',
    //                 'less_than_equal_to' => 'Rating must not exceed 10.'
    //             ]
    //         ],
    //         // 'notes' => [
    //         //     'rules' => 'required',
    //         //     'errors' => [
    //         //         'required' => 'Please provide additional notes.'
    //         //     ]
    //         // ]
    //     ])) {
    //         return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
    //     }
    //     if (!isset($data['designation_id']) || empty($data['designation_id'])) {
    //         return $this->respond(['status' => 'error', 'message' => 'Designation ID is missing'], 400);
    //     }


    //     if ($this->performanceModel->insert($data)) {
    //         $performanceId = $this->performanceModel->insertID(); // ✅ Get the last inserted ID

    //         if ($performanceId) {
    //             $emailService = new EmailService();
    //             $emailService->sendPerformanceEmail($performanceId);
    //         }
    //         return $this->respond(['status' => 'success', 'message' => 'Performance added successfully'], 201);
    //     }

    //     return $this->respond(['status' => 'error', 'message' => 'Failed to add performance record'], 500);
    // }
    public function create()
    {
        // Authentication and authorization check
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Role-based access control
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to create performance records');
        }

        // Validate input data
        $data = $this->request->getPost();
        if (!$this->validate([
            'user_id' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'User ID is required.',
                    'integer' => 'User ID must be a valid number.'
                ]
            ],
            'reviewer_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Reviewer name is required.'
                ]
            ],
            'designation_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Designation is required.'
                ]
            ],
            'review_date' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Review date is required.',
                    'valid_date' => 'Please enter a valid date.'
                ]
            ],
            'goals_achieved' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please provide details about goals achieved.'
                ]
            ],
            'team_work' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please provide a rating for teamwork.'
                ]
            ],
            'management' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Management rating is required.'
                ]
            ],
            'presentation_skill' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Presentation skill rating is required.'
                ]
            ],
            'behaviour' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Behaviour rating is required.'
                ]
            ],
            'rating' => [
                'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
                'errors' => [
                    'required' => 'Overall rating is required.',
                    'decimal' => 'Rating must be a decimal value.',
                    'greater_than_equal_to' => 'Rating must be at least 0.',
                    'less_than_equal_to' => 'Rating must not exceed 10.'
                ]
            ]
        ])) {
            return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
        }

        $userId = $data['user_id'];
        $reviewDate = $data['review_date'];

        // Extract the year and month from the review date
        $yearMonth = date('Y-m', strtotime($reviewDate));

        // Check if a record already exists for this user in the same month
        $existingRecord = $this->performanceModel
            ->where('user_id', $userId)
            ->where("DATE_FORMAT(review_date, '%Y-%m')", $yearMonth)
            ->first();

        if ($existingRecord) {
            return $this->respond(['status' => 'error', 'message' => 'This Employee Performance record already exists for this month.'], 400);
        }

        // Insert the new performance record
        if ($this->performanceModel->insert($data)) {
            $performanceId = $this->performanceModel->insertID();

            // Send notification
            $notificationModel = new \App\Models\NotificationModel();
            $userModel = new \App\Models\UserModel();
            $reviewedUser = $userModel->find($data['user_id']);
            $sender = $userModel->find($user->sub);

            // Send to admin, hr, and the reviewed employee
            $recipients = $userModel
                ->whereIn('role', ['admin', 'hr'])
                ->orWhere('id', $data['user_id']) // Also notify reviewed employee
                ->findAll();

            foreach ($recipients as $recipient) {
                $notificationModel->insert([
                    'sender_id'    => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'type'     => 'performance',
                        'username' => $sender['username'],
                        'employee' => $reviewedUser['username'], // You can use full name if you prefer
                        'message'  => 'Performance reviewed for ' . $reviewedUser['username'],
                        'user_id'  => $data['user_id']
                    ]),
                    'is_read' => 0
                ]);
            }

            // Send performance review email (optional)
            if ($performanceId) {
                $emailService = new EmailService();
                $emailService->sendPerformanceEmail($performanceId);
            }

            return $this->respond(['status' => 'success', 'message' => 'Performance added successfully'], 201);
        }


        return $this->respond(['status' => 'error', 'message' => 'Failed to add performance record'], 500);
    }

    public function getUserDesignation($userId)
    {
        $userModel = new \App\Models\UserInfoModel();
        $designationModel = new \App\Models\DesignationModel();
        $departmentModel = new \App\Models\DepartmentModel();

        // Fetch user info
        $user = $userModel->where('user_id', $userId)->first();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // Fetch designation info
        $designation = $designationModel->where('id', $user['designation_id'])->first();
        if (!$designation) {
            return $this->respond(['status' => 'error', 'message' => 'Designation not found'], 404);
        }

        // Fetch department info
        $department = $departmentModel->where('id', $designation['department_id'])->first();
        $departmentName = $department ? $department['department_name'] : 'Unknown';

        return $this->respond([
            'status' => 'success',
            'designation_id' => $designation['id'],
            'designation_name' => $designation['designation_name'],
            'department_name' => $departmentName
        ]);
    }

    // Update performance record
    public function update($id = null)
    {
        // Authentication and authorization check
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Role-based access control
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to update performance records');
        }

        // Get data for update
        $data = $this->request->getPost();

        // Validate input data
        if (!$this->validate([
            'user_id' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'User name is required.',
                    'integer' => 'User name must be a valid number.'
                ]
            ],
            'reviewer_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Reviewer name is required.'
                ]
            ],
            'designation_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Designation is required.'
                ]
            ],
            'review_date' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Review date is required.',
                    'valid_date' => 'Please enter a valid date.'
                ]
            ],
            'goals_achieved' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please provide details about goals achieved.'
                ]
            ],
            'team_work' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please provide a rating for team work.'
                ]
            ],
            'management' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Management rating is required.'
                ]
            ],
            'presentation_skill' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Presentation skill rating is required.'
                ]
            ],
            'behaviour' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Behaviour rating is required.'
                ]
            ],
            'rating' => [
                'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
                'errors' => [
                    'required' => 'Overall rating is required.',
                    'decimal' => 'Rating must be a decimal value.',
                    'greater_than_equal_to' => 'Rating must be at least 0.',
                    'less_than_equal_to' => 'Rating must not exceed 10.'
                ]
            ],
            // 'notes' => [
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'Please provide additional notes.'
            //     ]
            // ]
        ])) {
            return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
        }

        // Update performance record in the database
        if ($this->performanceModel->update($id, $data)) {
            // No need to get insertID(), use the existing $id
            $emailService = new EmailService();
            $emailService->sendPerformanceEmail($id); // ✅ Pass the correct ID
            return $this->respond(['status' => 'success', 'message' => 'Performance record updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update performance record'], 500);
    }



    // Get performance records based on user role
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $this->performanceModel
            ->select('performance.*, performance.created_at, users.username as employee_name, designation.designation_name,user_info.profile_image')
            ->join('users', 'users.id = performance.user_id')
             ->join('user_info', 'user_info.user_id = performance.user_id')
            ->join('designation', 'designation.id = performance.designation_id');


        // Role-based filtering
        if ($user->role === 'admin') {
            // Admin can see all records (no filter)
            $records = $this->performanceModel->orderBy('created_at', 'DESC')->findAll();
        } elseif ($user->role === 'hr') {
            // HR can only see employee records (exclude admin & HR)
            $records = $this->performanceModel->where('users.role', 'employee')->orderBy('created_at', 'DESC')->findAll();
        } elseif ($user->role === 'employee') {
            // Employee can only see their own records
            $records = $this->performanceModel->where('performance.user_id', $user->sub)->orderBy('created_at', 'DESC')->findAll();
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
        if ($user->role === 'employee' && $user->id !== $employeeId) {
            return $this->failForbidden('Forbidden: You can only access your own performance records');
        }

        // Change 'employee_id' to 'user_id' (as per your model)
        $records = $this->performanceModel->where('id', $employeeId)->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
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

        if ($this->performanceModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Performance record deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete performance record'], 500);
    }
    public function creates()
    {
        $userModel = new \App\Models\UserModel();
        $designationModel = new \App\Models\DesignationModel();

        $departmentModel = new \App\Models\DepartmentModel();

        // Fetch the current logged-in user's role
        $role = session()->get('role'); // Assuming role is stored in the session

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // If the user is an admin, fetch both HR and employee
        if ($role === 'admin') {
            $reviewers = $userModel->whereIn('role', ['admin', 'hr'])->findAll();
        } elseif ($role === 'hr') {
            // If the user is HR, fetch only employees
            $reviewers = $userModel->whereIn('role', ['hr'])->findAll(); // Display only employee names for HR
        } else {
            // If the user is neither admin nor HR, we can set an empty array or handle as necessary
            $reviewers = [];
        }
        $departments = $departmentModel->findAll();
        // Fetch all designations dynamically
        $designations = $designationModel->getDesignationsWithDepartment();

        return view('performance/performance', [
            'employees' => $employees,
            'reviewers' => $reviewers,
            'designations' => $designations,
            'departments' => $departments
        ]);
    }


    public function display()
    {
        return view('performance/view');
    }
    public function profilePage()
    {
        return view('performance/profile');
    }
    public function getProfile($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Fetch performance details along with user and designation info
        // $record = $this->performanceModel
        //     ->select('performance.*, users.username as employee_name, designation.designation_name')
        //     ->join('users', 'users.id = performance.user_id')
        //     ->join('designation', 'designation.id = performance.designation_id')
        //     ->where('performance.id', $id)
        //     ->first();
        $record = $this->performanceModel
            ->select('performance.*, 
                  users.username as employee_name, 
                  ui.email, ui.employee_id,
                  des.designation_name,
                  dep.department_name')
            ->join('users', 'users.id = performance.user_id')
            ->join('user_info ui', 'ui.user_id = users.id')
            ->join('designation des', 'des.id = ui.designation_id')
            ->join('department dep', 'dep.id = ui.department_id')
            ->where('performance.id', $id)
            ->first();
        if (!$record) {
            return $this->failNotFound('Performance record not found');
        }

        return $this->respond(['status' => 'success', 'data' => $record]);
    }
    public function add()
    {
        $response = ['success' => false, 'message' => '', 'errors' => []];

        // Define validation rules with custom error messages
        $validation = \Config\Services::validation();
        $validation->setRules([
            'department_id' => [
                'label' => 'Department Name',
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} is required.'
                ]
            ],
            'designation_name' => [
                'label' => 'Designation Name',
                'rules' => 'required|trim',
                'errors' => [
                    'required' => '{field} is required.'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $response['errors'] = $validation->getErrors(); // Return validation errors
        } else {
            $designationData = [
                'department_id' => $this->request->getPost('department_id'),
                'designation_name' => $this->request->getPost('designation_name')
            ];

            $designationModel = new \App\Models\DesignationModel();
            $designationId = $designationModel->insert($designationData);

            if ($designationId) {
                // Fetch newly added designation with department name
                $newDesignation = $designationModel->select('designation.id, designation.designation_name, department.department_name')
                    ->join('department', 'department.id = designation.department_id')
                    ->where('designation.id', $designationId)
                    ->first();

                $response['success'] = true;
                $response['designation'] = $newDesignation;
            } else {
                $response['message'] = "Failed to add designation.";
            }
        }

        return $this->response->setJSON($response);
    }
}
