<?php

namespace App\Controllers\Api;

use App\Models\LeaveModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use App\Models\LeaveTypeModel;
use App\Models\UserModel;
use App\Models\UserInfoModel;
use App\Models\NotificationSettingsModel;
use App\Services\PushNotificationService;

class LeaveController extends ResourceController
{
    private $leaveModel;
    protected $authService;
    protected $userModel;
    private $pushNotificationService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->leaveModel = new LeaveModel();
        $this->pushNotificationService = new PushNotificationService();
        $this->authService = new AuthService(service('request'));
    }

    public function getAll()
    {
        $authUser = $this->authService->user(); // Decode JWT to get user details
        if (!$authUser) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $userModel = new UserInfoModel();
        $leaveModel = new LeaveModel();
        $usersModel = new UserModel();

        $requestedUserId = $this->request->getGet('user_id'); // Get user_id from query parameter

        if ($authUser->role === 'admin') {
            // ✅ Admin: Fetch all HR and Employee leave records OR a specific user's leave if requested
            $users = $userModel->whereIn('role', ['employee', 'hr']);
            if ($requestedUserId) {
                $users = $users->where('id', $requestedUserId);
            }
            // $users = $users->where('is_deleted', 0);
            $users = $users->findAll();
            $leaveRecords = $leaveModel->select('leaves.*, user_info.firstname, leave_type.leave_type,users.username AS created_by_username')
                ->join('user_info', 'leaves.user_id = user_info.user_id', 'left')
                ->join('users', 'leaves.created_by = users.id', 'left') // creator of leave
                ->join('leave_type', 'leaves.leave_id = leave_type.id', 'left');

            if ($requestedUserId) {
                $leaveRecords = $leaveRecords->where('leaves.user_id', $users[0]['user_id']);
            }

            $leaveRecords = $leaveRecords->findAll();
        } elseif ($authUser->role === 'hr') {
            // ✅ HR: Fetch their own leave + all employee leave records OR specific user if requested
            $users = $userModel->whereIn('role', ['employee', 'hr']);
            if ($requestedUserId) {
                $users = $users->where('id', $requestedUserId);
            }
            // $users = $users->where('is_deleted', 0);
            $users = $users->findAll();

            $leaveRecords = $leaveModel->select('leaves.*, user_info.firstname, leave_type.leave_type, users.username AS created_by_username')
                ->join('user_info', 'leaves.user_id = user_info.user_id', 'left')
                ->join('users', 'leaves.created_by = users.id', 'left') // creator of leave
                ->join('leave_type', 'leaves.leave_id = leave_type.id', 'left');


            if ($requestedUserId) {
                $leaveRecords = $leaveRecords->where('leaves.user_id', $users[0]['user_id']);
            }

            $leaveRecords = $leaveRecords->findAll();
        } else {
            // ✅ Employee: Fetch only their own leave records
            $users = $userModel->where('user_id', $authUser->sub)->findAll();
            $leaveRecords = $leaveModel->select('leaves.*, user_info.firstname, leave_type.leave_type,users.username AS created_by_username')
                ->join('user_info', 'leaves.user_id = user_info.user_id', 'left')
                ->join('users', 'leaves.created_by = users.id', 'left') // creator of leave
                ->join('leave_type', 'leaves.leave_id = leave_type.id', 'left')
                ->where('leaves.user_id', $authUser->sub)
                ->findAll();
        }
        // Map leave records to corresponding users
        $userLeaveData = [];
        foreach ($leaveRecords as $leave) {
            $userLeaveData[$leave['user_id']][] = $leave;
        }

        $leaveData = [];
        // Attach leave data to each user
        foreach ($users as &$user) {
            $user['leaves'] = $userLeaveData[$user['user_id']] ?? [];
            $leaveData[] = $user;
        }

        return $this->respond([
            'status' => 'success',
            'data' => $leaveData,
            'role' => $authUser->role // 👈 add this
        ]);
    }

    public function view()
    {
        return view('leave/leaveview');
    }

    public function display()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $userId = $user->sub;
        $role = $user->role;

        $leaveTypeModel = new LeaveTypeModel();
        $userModel = new UserModel();

        $leaveTypes = $leaveTypeModel->findAll();

        if ($role === 'admin') {
            // Admin sees all HRs and Employees
            $users = $userModel->whereIn('role', ['hr', 'employee'])->where('is_deleted', 0)->findAll();
        } elseif ($role === 'hr') {
            // HR sees all employees and themselves
            $users = $userModel->whereIn('role', ['hr', 'employee'])->where('is_deleted', 0)->findAll();
        } else {
            // Employee sees only themselves
            $users = [$userModel->find($userId)];
        }

        return view('leave/addleave', [
            'leaveTypes' => $leaveTypes,
            'users' => $users,
            'role' => $role,
            'currentUserId' => $userId,
        ]);
    }

    public function create()
    {
        // Check user authentication
        $user = $this->authService->check();

        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $userId = $user->sub;
        $role = $user->role;

        $data = $this->request->getJSON(true);
        $userModel = new UserModel();
        // Authorization check
        if ($role == 'admin') {
            // admin can insert for anyone
        } elseif ($role == 'hr') {
            $allowedUsers = $userModel->whereIn('role', ['hr', 'employee'])->findColumn('id');
            if (!in_array($data['user_id'], $allowedUsers)) {
                return $this->failForbidden('Forbidden: HR can only add leave for themselves and employees.');
            }
        } else {
            if ($data['user_id'] != $userId) {
                return $this->failForbidden('Forbidden: Employees can only add leave for themselves.');
            }
        }

        // Calculate no_of_day
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);
            $data['no_of_day'] = (string) (($endDate - $startDate) / 86400 + 1);
        }

        // Default status if not set
        if (empty($data['status'])) {
            $data['status'] = 'Pending';
        }

        // Validation
        $validationRules = [
            'user_id'    => 'required',
            'leave_id'   => 'required',
            'start_date' => 'required|valid_date[Y-m-d]',
            'end_date'   => 'required|valid_date[Y-m-d]|check_end_date[start_date]',
            'reason'     => 'required',
            'no_of_day'  => 'required|validate_no_of_day[start_date,end_date]',
        ];

        $validationMessages = [
            'user_id' => ['required' => 'User is required.'],
            'leave_id' => ['required' => 'Leave is required.'],
            'start_date' => [
                'required' => 'Start date is required.',
                'valid_date' => 'Start date must be in YYYY-MM-DD format.'
            ],
            'end_date' => [
                'required' => 'End date is required.',
                'valid_date' => 'End date must be in YYYY-MM-DD format.',
                'check_end_date' => 'End date must be after or equal to start date.'
            ],
            'reason' => ['required' => 'Reason is required.'],
            'no_of_day' => [
                'required' => 'Number of days is required.',
                'validate_no_of_day' => 'Number of days must be correct.'
            ],
        ];

        if (!$this->validateData($data, $validationRules, $validationMessages)) {
            log_message('error', 'Leave creation validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        $data['created_by'] = $userId;

        if ($this->leaveModel->insert($data)) {
            // Notifications
            $notificationModel = new \App\Models\NotificationModel();
            $sender = $userModel->find($data['user_id']);

            $recipients = $userModel->whereIn('role', ['admin', 'hr'])->findAll();
            $recipients[] = [
                'id' => $sender['id'],
                'role' => $sender['role'],
                'username' => $sender['username']
            ];

            $uniqueRecipients = [];
            foreach ($recipients as $recipient) {
                $uniqueRecipients[$recipient['id']] = $recipient;
            }

            foreach ($uniqueRecipients as $recipient) {
                $notificationModel->insert([
                    'sender_id'    => $data['user_id'],
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'username' => $sender['username'],
                        'type'     => 'leave'
                    ]),
                    'is_read' => 0
                ]);
                
            }

            // Check if leave notifications are enabled
            try {
                $notificationSettingsModel = new NotificationSettingsModel();
                if ($notificationSettingsModel->isLeaveNotificationsEnabled()) {
                    $leaveStart = $data['start_date'];
                    $leaveEnd   = $data['end_date'];
                    $noOfDays   = $data['no_of_day'];
                    $employeeName = $sender['username'];
                    
                    log_message('info', '📝 Employee leave request: ' . $employeeName . ' from ' . $leaveStart . ' to ' . $leaveEnd);
                    
                    $this->pushNotificationService->notifyAdmins(
                        'Employee Leave Request',
                        $employeeName . ' requested leave from ' . $leaveStart . ' to ' . $leaveEnd . ' (' . $noOfDays . ' day' . ($noOfDays > 1 ? 's' : '') . ')',
                        [
                            'type'        => 'leave_request',
                            'user_id'     => $data['user_id'],
                            'username'    => $employeeName,
                            'start_date'  => $leaveStart,
                            'end_date'    => $leaveEnd,
                            'no_of_days'  => $noOfDays,
                            'leave_id'    => $data['leave_id'],
                            'status'      => $data['status'],
                            'url'         => base_url('/leaveview')
                        ]
                    );
                } else {
                    log_message('info', '📝 Leave notifications are disabled - skipping push notification');
                }
            } catch (\Exception $e) {
                // If notification settings table doesn't exist, send notification anyway (default behavior)
                log_message('warning', 'Notification settings table not found, sending leave notification by default: ' . $e->getMessage());
                
                $leaveStart = $data['start_date'];
                $leaveEnd   = $data['end_date'];
                $noOfDays   = $data['no_of_day'];
                $employeeName = $sender['username'];
                
                $this->pushNotificationService->notifyAdmins(
                    'Employee Leave Request',
                    $employeeName . ' requested leave from ' . $leaveStart . ' to ' . $leaveEnd . ' (' . $noOfDays . ' day' . ($noOfDays > 1 ? 's' : '') . ')',
                    [
                        'type'        => 'leave_request',
                        'user_id'     => $data['user_id'],
                        'username'    => $employeeName,
                        'start_date'  => $leaveStart,
                        'end_date'    => $leaveEnd,
                        'no_of_days'  => $noOfDays,
                        'leave_id'    => $data['leave_id'],
                        'status'      => $data['status'],
                        'url'         => base_url('/leaveview')
                    ]
                );
            }


            return $this->respond([
                'status' => 'success',
                'message' => 'Leave record added successfully'
            ], 201);
        }

        return $this->respond([
            'status' => 'error',
            'message' => 'Failed to add leave record'
        ], 500);
    }


    public function getLeaveRequests()
    {
        if (!$this->authService->check()) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $user = $this->authService->user();

        $leaveModel = new LeaveModel();
        $leaveRequests = $leaveModel->where('user_id', $user->sub)->findAll();

        return $this->respond(['status' => 'success', 'data' => $leaveRequests]);
    }

    public function approveLeave($id)
    {
        if (!$this->authService->check()) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $leaveModel = new LeaveModel();
        $leave = $leaveModel->find($id);

        if ($leave) {
            $leave['status'] = 'approved';
            $leaveModel->save($leave);

            return $this->respond(['status' => 'success', 'message' => 'Leave approved successfully']);
        }

        return $this->fail('Leave request not found');
    }

    public function getLeaves()
    {
        $leaveModel = new \App\Models\LeaveModel();
        $userInfoModel = new \App\Models\UserInfoModel();

        $employeeId = $this->request->getGet('employee_id'); // Get employee_id from request

        $query = $leaveModel->join('user_info', 'leaves.user_id = user_info.user_id')
            ->select('leaves.*, user_info.firstname, user_info.lastname, user_info.profile_image');

        if (!empty($employeeId)) {
            $query->where('leaves.user_id', $employeeId); // Filter by employee_id
        }

        $leaves = $query->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $leaves
        ]);
    }

    public function updateStatus($leaveId)
    {
        if (!$leaveId) {
            return $this->respond(['status' => 'error', 'message' => 'Missing leave ID'], 400);
        }

        // Check if user is authenticated
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $userRole = $user->role; // 'admin', 'hr', or 'employee'
        $userId = $user->sub; // Logged-in user ID

        // Employees cannot update leave status
        if ($userRole === 'employee') {
            return $this->failForbidden('Employees cannot update leave status');
        }

        // Validate status input
        $status = $this->request->getJSON()->status;
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return $this->respond(['status' => 'error', 'message' => 'Invalid status'], 400);
        }

        // Fetch leave request
        $leaveModel = new LeaveModel();
        $leave = $leaveModel->find($leaveId);

        if (!$leave) {
            return $this->respond(['status' => 'error', 'message' => 'Leave request not found'], 404);
        }

        // HR can update only employee leave requests (not other HRs)
        if ($userRole === 'hr') {
            $userModel = new UserModel();
            $employee = $userModel->find($leave['user_id']); // Ensure 'user_id' exists in 'leaves' table

            if (!$employee) {
                return $this->respond(['status' => 'error', 'message' => 'Employee not found'], 404);
            }

            if ($employee['role'] !== 'employee') {
                return $this->failForbidden('You can only update employee leave requests.');
            }
        }

        // ===============================
        // 🔔 PUSH NOTIFICATION TO EMPLOYEE
        // ===============================

        // $userModel = new UserModel();
        // $employee  = $userModel->find($leave['user_id']);
        // $updatedBy = $userModel->find($userId);

        // $title = 'Leave Request ' . ucfirst($status);

        // $message = match ($status) {
        //     'approved' => 'Your leave request has been approved.',
        //     'rejected' => 'Your leave request has been rejected.',
        //     default    => 'Your leave request status has been updated.'
        // };

        // $this->pushNotificationService->notifyUser(
        //     $leave['user_id'],
        //     $title,
        //     $message,
        //     [
        //         'type'        => 'leave_status',
        //         'leave_id'    => $leaveId,
        //         'status'      => $status,
        //         'start_date'  => $leave['start_date'],
        //         'end_date'    => $leave['end_date'],
        //         'no_of_days'  => $leave['no_of_day'],
        //         'updated_by'  => $updatedBy['username'] ?? 'HR/Admin',
        //         'url'         => base_url('/leaveview')
        //     ]
        // );

        // Update leave status
        $leaveModel->update($leaveId, [
            'status' => $status,
            'created_by' => $userId  // 👈 this sets the new updater as the creator
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Leave status updated successfully']);
    }

    public function add()
    {
        $validation = \Config\Services::validation();

        // Define validation rules
        $validation->setRules([
            'leave_type' => [
                'label' => 'Leave Type',
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Leave Type is required.',
                    'min_length' => 'Leave Type must be at least 3 characters long.',
                    'max_length' => 'Leave Type cannot exceed 255 characters.',
                ]
            ],
            'number_of_leaves' => [
                'label' => 'Number of Leaves',
                'rules' => 'required|integer|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Number of Leaves is required.',
                    'integer' => 'Number of Leaves must be a valid number.',
                    'greater_than_equal_to' => 'Number of Leaves must be 0 or greater.',
                ]
            ]
        ]);

        // Run validation
        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        // Validation passed, proceed to insert
        $model = new LeaveTypeModel();

        // Prepare the data
        $data = [
            'leave_type' => $this->request->getVar('leave_type'),
            'number_of_leaves' => $this->request->getVar('number_of_leaves'),
            'created_by' => session()->get('user_id') // Assuming user_id is stored in session
        ];

        // Insert the data
        if ($model->insert($data)) {
            // Fetch the newly inserted record
            $leaveType = $model->find($model->insertID());

            return $this->response->setJSON([
                'success' => true,
                'leave_type' => $leaveType
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add leave type.'
            ]);
        }
    }
}
