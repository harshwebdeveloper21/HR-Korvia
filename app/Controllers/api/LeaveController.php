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
        if (isset($data['leave_duration']) && $data['leave_duration'] === 'half_day') {
            $data['no_of_day'] = '0.5';
            $data['end_date'] = $data['start_date'];
        } elseif (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = strtotime($data['start_date']);
            $endDate = strtotime($data['end_date']);
            if ($startDate && $endDate) {
                $data['no_of_day'] = (string) (($endDate - $startDate) / 86400 + 1);
            }
        }

        if (empty($data['leave_duration']) || $data['leave_duration'] !== 'half_day') {
            $data['leave_duration'] = 'full_day';
            $data['half_day_type'] = null;
        }

        // Default status if not set
        if (empty($data['status'])) {
            $data['status'] = 'Pending';
        }

        // Validation
        $validationRules = [
            'user_id' => 'required',
            'leave_id' => 'required',
            'start_date' => 'required|valid_date[Y-m-d]',
            'end_date' => 'required|valid_date[Y-m-d]|check_end_date[start_date]',
            'reason' => 'required',
            'no_of_day' => 'required|validate_no_of_day[start_date,end_date]',
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
            $leaveId = $this->leaveModel->getInsertID();

            // Send notifications (internal and push)
            try {
                $this->sendLeaveNotification($data, $leaveId);
            } catch (\Exception $e) {
                log_message('error', '❌ Failed to trigger leave notifications: ' . $e->getMessage());
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Leave record added successfully',
                'id' => $leaveId
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

        // ─────────────────────────────────────────────────────────────
        // 🔔  SEND NOTIFICATION TO THE EMPLOYEE WHOSE LEAVE WAS UPDATED
        // ─────────────────────────────────────────────────────────────

        $notificationModel = new \App\Models\NotificationModel();
        $userModel2 = new UserModel();

        $employeeUser = $userModel2->find($leave['user_id']);
        $updatedBy = $userModel2->find($userId);
        $updaterName = $updatedBy['username'] ?? 'HR/Admin';

        // Human-readable status message
        $statusLabel = match (strtolower($status)) {
            'approved' => 'approved ✅',
            'rejected' => 'rejected ❌',
            default => 'moved to pending 🕐',
        };

        $notifMessage = 'Your leave request from ' . $leave['start_date'] . ' to ' . $leave['end_date']
            . ' has been ' . $statusLabel . ' by ' . $updaterName . '.';

        // 1. ── In-App (database) notification ──
        $notificationModel->insert([
            'sender_id' => $userId,
            'recipient_id' => $leave['user_id'],
            'data' => json_encode([
                'username' => $employeeUser['username'] ?? 'Employee',
                'type' => 'leave_status',
                'message' => $notifMessage,
                'status' => $status,
                'leave_id' => $leaveId,
                'start_date' => $leave['start_date'],
                'end_date' => $leave['end_date'],
                'url' => base_url('/leaveview'),
            ]),
            'is_read' => 0,
        ]);

        // 2. ── Web Push notification to the employee ──
        try {
            $pushTitle = 'Leave Request ' . ucfirst($status);
            $this->pushNotificationService->notifyUser(
                $leave['user_id'],
                $pushTitle,
                $notifMessage,
                [
                    'type' => 'leave_status',
                    'leave_id' => $leaveId,
                    'status' => $status,
                    'start_date' => $leave['start_date'],
                    'end_date' => $leave['end_date'],
                    'no_of_days' => $leave['no_of_day'] ?? 1,
                    'updated_by' => $updaterName,
                    'url' => base_url('/leaveview'),
                ]
            );
        } catch (\Exception $e) {
            log_message('error', '🔔 [updateStatus] Push notification failed: ' . $e->getMessage());
            // Do NOT abort the response — notification failure should not block the status update
        }

        // Update leave status
        $leaveModel->update($leaveId, [
            'status' => $status,
            'created_by' => $userId,
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Leave status updated successfully']);
    }

    /**
     * API: Employee cancels their own pending leave request.
     */
    public function cancelLeave($leaveId)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $leave = $this->leaveModel->find($leaveId);
        if (!$leave) {
            return $this->failNotFound('Leave request not found');
        }

        // Must be their own leave
        if ($leave['user_id'] != $user->sub) {
            return $this->failForbidden('You can only cancel your own leave requests.');
        }

        // Must be pending
        if (strtolower($leave['status']) !== 'pending') {
            return $this->failForbidden('Only pending leave requests can be cancelled. Current status: ' . $leave['status']);
        }

        if ($this->leaveModel->update($leaveId, ['status' => 'cancelled'])) {
            return $this->respond(['status' => 'success', 'message' => 'Leave request cancelled successfully']);
        }

        return $this->fail('Failed to cancel leave.');
    }

    /**
     * API: Employee updates their own pending leave request dates.
     */
    public function updateDates($leaveId)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized();
        }

        $leave = $this->leaveModel->find($leaveId);
        if (!$leave) {
            return $this->failNotFound('Leave request not found');
        }

        // Must be their own leave
        if ($leave['user_id'] != $user->sub) {
            return $this->failForbidden('You can only update your own leave requests.');
        }

        // Must be pending
        if (strtolower($leave['status']) !== 'pending') {
            return $this->failForbidden('Strictly only pending leave requests can be updated.');
        }

        $data = $this->request->getJSON(true);
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;

        if (!$startDate || !$endDate) {
            return $this->fail('Start date and end date are required');
        }

        // Recalculate duration
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        if ($start > $end) {
            return $this->fail('Start date cannot be after end date.');
        }

        $noOfDays = (string) (($end - $start) / 86400 + 1);

        $updateData = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'no_of_day' => $noOfDays,
        ];

        if ($this->leaveModel->update($leaveId, $updateData)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Leave dates updated successfully',
                'no_of_day' => $noOfDays
            ]);
        }

        return $this->fail('Failed to update leave.');
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

    /**
     * Helper to send internal and push notifications for new leave requests
     */
    private function sendLeaveNotification($data, $leaveId)
    {
        $userModel = new \App\Models\UserModel();
        $notificationModel = new \App\Models\NotificationModel();

        $sender = $userModel->find($data['user_id']);
        $senderName = $sender['username'] ?? 'Employee';

        log_message('info', '🔔 [sendLeaveNotification] Starting notification process for leave #' . $leaveId . ' (User: ' . $senderName . ')');

        // 1. Internal Notifications (Database)
        $recipients = $userModel->whereIn('role', ['admin', 'hr'])->where('is_deleted', 0)->findAll();

        foreach ($recipients as $recipient) {
            $notificationModel->insert([
                'sender_id' => $data['user_id'],
                'recipient_id' => $recipient['id'],
                'data' => json_encode([
                    'username' => $senderName,
                    'type' => 'leave',
                    'leave_id' => $leaveId
                ]),
                'is_read' => 0
            ]);
        }

        // 2. Push Notification to Admin/HR
        try {
            $notificationSettingsModel = new \App\Models\NotificationSettingsModel();

            // Safe check for settings
            $shouldNotify = true;
            try {
                $shouldNotify = $notificationSettingsModel->isLeaveNotificationsEnabled();
            } catch (\Exception $e) {
                log_message('warning', '🔔 [sendLeaveNotification] Could not check settings, defaulting to TRUE: ' . $e->getMessage());
            }

            if ($shouldNotify) {
                $leaveStart = $data['start_date'] ?? '';
                $leaveEnd = $data['end_date'] ?? '';
                $noOfDays = $data['no_of_day'] ?? 1;

                // Fetch leave type name
                $leaveTypeName = 'Leave';
                try {
                    $leaveTypeModel = new \App\Models\LeaveTypeModel();
                    $leaveType = $leaveTypeModel->find($data['leave_id']);
                    if ($leaveType) {
                        $leaveTypeName = $leaveType['leave_type'];
                    }
                } catch (\Exception $e) {
                    log_message('warning', '🔔 [sendLeaveNotification] Could not fetch leave type: ' . $e->getMessage());
                }

                log_message('info', '🔔 [sendLeaveNotification] Sending push notification to admins/HR');

                $this->pushNotificationService->notifyAdmins(
                    'Employee Leave Request (New Application)',
                    $senderName . ' has requested ' . $leaveTypeName . ' from ' . $leaveStart . ' to ' . $leaveEnd . ' (' . $noOfDays . ' day' . ($noOfDays > 1 ? 's' : '') . '). Reason: ' . ($data['reason'] ?? 'Not specified'),
                    [
                        'type' => 'leave_request',
                        'user_id' => $data['user_id'],
                        'username' => $senderName,
                        'leave_id' => $leaveId,
                        'leave_type' => $leaveTypeName,
                        'url' => base_url('/leaveview')
                    ]
                );
            }
        } catch (\Exception $e) {
            log_message('error', '🔔 [sendLeaveNotification] Push notification failed: ' . $e->getMessage());
        }
    }


    public function index()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/login');
        }

        // Fetch all active employees for the dropdown
        $employees = $this->userModel->where('is_deleted', 0)
            ->whereIn('role', ['employee', 'hr'])
            ->findAll();

        return view('leave/manage_leaves', [
            'employees' => $employees,
            'role' => $user->role
        ]);
    }

}
