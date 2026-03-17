<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskModel;
use App\Models\SubtaskModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Services\AuthService;
use App\Libraries\EmailService;

class SubTaskController extends ResourceController
{
    private $taskModel;
    private $subtaskModel;
    private $userModel;
    private $authService;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->subtaskModel = new SubtaskModel();
        $this->userModel = new UserModel(); // Instantiate the UserModel
        $this->authService = new AuthService(service('request'));
    }

    public function CreatePage()
    {
        $userModel = new \App\Models\UserModel();
        $departmentModel = new \App\Models\DepartmentModel();

        // Get the role of the logged-in user
        $role = session()->get('role');  // Assuming the user's role is stored in the session

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // If the user is an admin, fetch both HR and employees for the dropdown
        if ($role === 'admin') {
            $employees = $userModel->whereIn('role', ['employee'])->findAll();
        } elseif ($role === 'hr') {
            // If the user is HR, only fetch employees for the dropdown
            $employees = $userModel->where('role', 'employee')->findAll();
        } else {
            // If neither admin nor HR, you can choose to handle it or return an empty array
            $employees = [];
        }

        // Fetch departments
        $departments = $departmentModel->findAll();

        // Pass the filtered employees and departments to the view
        return view('subtask/create_subtask', [
            'employees' => $employees,  // Pass the filtered employees
            'departments' => $departments // Pass departments
        ]);
    }

    public function getTasksByUser($userId)
    {
        $taskModel = new \App\Models\TaskModel();

        // Check if the user has any tasks assigned
        $assignedTasks = $taskModel->where('user_id', $userId)->findAll();

        if (empty($assignedTasks)) {
            // If no tasks assigned, return all tasks
            $assignedTasks = $taskModel->findAll();
        }

        return $this->response->setJSON($assignedTasks);
    }
  public function create()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Only 'admin' or 'hr' can create subtasks
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have permission to create tasks');
        }

        $subtaskModel = new SubtaskModel();
        $request = service('request');
        $post = $request->getPost();
        $files = $request->getFiles();

        $subtasks = json_decode($post['subtasks'], true); // JSON string from JS
        $task_id = $post['task_id'] ?? 0;
        $user_id = $post['user_id'] ?? 0;

        // Loop through each subtask and validate
        foreach ($subtasks as $index => $subtask) {
             $validationMessages  = [
                'subtask_title' => [
                    'label' => 'Subtask Title',
                    'rules' => 'required|min_length[3]',
                    'errors' => [
                        'required' => 'Subtask is required',
                        'min_length' => 'Subtask title must be at least 3 characters long.'
                    ]
                ],
                'subtask_status' => [
                    'label' => 'Subtask Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Please select a subtask status.'
                    ]
                ],
                'subtask_assigned_date' => [
                    'rules' => 'required|valid_date[Y-m-d]',
                    'errors' => [
                        'required'   => 'Assigned date is required.',
                        'valid_date' => 'Assigned date must be a valid date format (YYYY-MM-DD).'
                    ]
                ],
                'subtask_due_date' => [
                    'rules' => 'required|valid_date|check_end_date[subtask_assigned_date]',
                    'errors' => [
                        'required'        => 'Due date is required.',
                        'valid_date'      => 'Due date must be a valid date format (YYYY-MM-DD).',
                        'check_end_date'  => 'Due date must be after the assigned date.'
                    ]
                ],
             
            ];

            if (!$this->validateData($subtask, $validationMessages)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Now Validate Files
            $uploadedFiles = [];
            if (isset($files['files'][$index]) && !empty($files['files'][$index])) {
            foreach ($files['files'][$index] as $file) {
                if (!$file->isValid()) {
                    return $this->failValidationErrors(["File error at index $index" => $file->getErrorString()]);
                }

                $allowedMimeTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/webp',
                    'video/mp4',
                    'video/avi',
                    'audio/mpeg',
                    'audio/wav'
                ];
                $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp', 'mp4', 'avi', 'mp3', 'wav'];

                $mimeType = $file->getClientMimeType();
                $extension = strtolower($file->getClientExtension());

                if (!in_array($mimeType, $allowedMimeTypes) || !in_array($extension, $allowedExtensions)) {
                    return $this->failValidationErrors(["Invalid file at index $index" => 'Only allowed file types are permitted.']);
                }

                if ($file->getSize() > 5 * 1024 * 1024) {
                    return $this->failValidationErrors(["File too large at index $index" => 'Each file must not exceed 5MB.']);
                }

                $originalName = $file->getClientName();
                $storedName   = $file->getRandomName();

                $file->move(FCPATH . 'uploads/subtasks', $storedName);

                $uploadedFiles[] = [
                    'original' => $originalName,
                    'stored'   => $storedName
                ];
            }
        }
            $data = [
                'subtask_title'         => $subtask['subtask_title'],
                'subtask_assigned_date' => $subtask['subtask_assigned_date'],
                'subtask_due_date'      => $subtask['subtask_due_date'],
                'subtask_status'        => $subtask['subtask_status'],
                'task_id'               => $task_id,
                'user_id'               => $user_id,
                'description'           => $subtask['description'] ?? '',
                'files'                 => json_encode($uploadedFiles),
                'created_by'            => $user->sub
            ];

            $subtaskId = $subtaskModel->insert($data);

            if ($subtaskId) {
                $notificationModel = new \App\Models\NotificationModel();
                $userModel = new \App\Models\UserModel();

                $assignedEmployee = $userModel->find($data['user_id']);
                $creator = $userModel->find($user->sub);

                $recipients = $userModel
                    ->whereIn('role', ['admin', 'hr'])
                    ->orWhere('id', $data['user_id'])
                    ->findAll();

                foreach ($recipients as $recipient) {
                    $notificationModel->insert([
                        'sender_id'    => $user->sub,
                        'recipient_id' => $recipient['id'],
                        'data'         => json_encode([
                            'type'      => 'subtask',
                            'username'  => $creator['username'],
                            'employee'  => $assignedEmployee['username'],
                            'user_id'   => $data['user_id'],
                            'message'   => 'New SubTask assigned to ' . $assignedEmployee['username']
                        ]),
                        'is_read' => 0
                    ]);
                }
            }
        }

        return $this->respond(['status' => 'success', 'message' => 'Subtasks added']);
    }

    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $this->subtaskModel
            ->select('subtasks.*, users.username, task.task_title,user_info.profile_image')
            ->join('users', 'users.id = subtasks.user_id')
            ->join('user_info', 'user_info.user_id = subtasks.user_id')
            ->join('task', 'task.id = subtasks.task_id');

        if ($user->role === 'admin') {
            $records = $this->subtaskModel->findAll();
        } elseif ($user->role === 'hr') {
            $records = $this->subtaskModel
                ->where('users.role', 'employee')
                ->findAll();
        } elseif ($user->role === 'employee') {
            $records = $this->subtaskModel
                ->where('subtasks.user_id', $user->sub)
                ->findAll();
        } else {
            return $this->failForbidden('Forbidden: Unauthorized role');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $records
        ]);
    }

    public function displays()
    {
        return view('subtask/all_subtask');
    }
    public function subTaskdelete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have permission to delete subtasks');
        }

        // Fetch the task details
        $subtask = $this->subtaskModel->find($id);

        if (!$subtask) {
            return $this->failNotFound('Task not found');
        }

        // Only allow deletion if the status is "completed"
        if ($subtask['subtask_status'] !== 'Completed') {

            return $this->respond([
                'status' => 'error',
                'message' => 'SubTask is still in progress. Only completed subtasks can be deleted.'
            ], 400);
        }

        // Delete task if status is completed
        if ($this->subtaskModel->delete($id)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'SubTask deleted successfully'
            ]);
        }

        return $this->failServerError('Failed to delete subtask');
    }
    public function subTaskupdateStatus()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have permission to update tasks');
        }

        $json = $this->request->getJSON();
        $taskId = $json->id ?? null;
        $newStatus = $json->status ?? null;

        if (!$taskId || !$newStatus) {
            return $this->fail('Invalid data', 400);
        }

        $subtaskModel = new \App\Models\SubtaskModel();
        $task = $subtaskModel->find($taskId);

        if (!$task) {
            return $this->failNotFound('SubTask not found');
        }

        $subtaskModel->update($taskId, ['subtask_status' => $newStatus]);

        // 🔔 Send Notifications
        $notificationModel = new \App\Models\NotificationModel();
        $userModel = new \App\Models\UserModel();

        $assignedEmployee = $userModel->find($task['user_id']);
        $updater = $userModel->find($user->sub);

        // Admin, HR + the assigned employee
        $recipients = $userModel
            ->whereIn('role', ['admin', 'hr'])
            ->orWhere('id', $task['user_id'])
            ->findAll();

        foreach ($recipients as $recipient) {
            $notificationModel->insert([
                'sender_id'    => $user->sub,
                'recipient_id' => $recipient['id'],
                'data'         => json_encode([
                    'type'      => 'subtask_update',
                    'username'  => $updater['username'],
                    'employee'  => $assignedEmployee['username'],
                    'user_id'   => $task['user_id'],
                    'message' => 'Subtask status updated to "' . $newStatus . '" for ' . $assignedEmployee['username'] . ' by ' . $updater['username'],
                ]),
                'is_read' => 0
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Subtask status updated and notifications sent.'
        ]);
    }
    public function EditPage($id)
    {
        $userModel = new \App\Models\UserModel();

        // Get the role of the logged-in user
        $role = session()->get('role');  // Assuming the user's role is stored in the session

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // If the user is an admin, fetch both HR and employees for the dropdown
        if ($role === 'admin') {
            $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();
        } elseif ($role === 'hr') {
            // If the user is HR, only fetch employees for the dropdown
            $employees = $userModel->where('role', 'employee')->findAll();
        } else {
            // If neither admin nor HR, you can choose to handle it or return an empty array
            $employees = [];
        }

        // Fetch departments
        return view('subtask/edit_subtask', ['id' => $id, 'employees' => $employees,]);
    }
    public function getSubtaskDetail($id)
    {
        $model = new \App\Models\SubtaskModel();
        $data = $model->find($id);

        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Record not found.']);
        }
    }
    public function updateSubtask($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden');
        }

        $data = $this->request->getPost();
        $id = $id ?? $data['id'] ?? null;

        // if (!$id) {
        //     return $this->failValidationError('Missing subtask ID');
        // }

        $model = new \App\Models\SubtaskModel();
        $existing = $model->find($id);
        if (!$existing) {
            return $this->failNotFound('Subtask not found.');
        }

        $updateData = [
            'subtask_title' => $data['subtask_title'] ?? '',
            'subtask_assigned_date' => $data['subtask_assigned_date'] ?? '',
            'subtask_due_date' => $data['subtask_due_date'] ?? '',
            'subtask_status' => $data['subtask_status'] ?? ''
        ];

        // File handling with validation
        $files = $this->request->getFiles();
        $uploadedFiles = [];

        if (isset($files['files'])) {
            foreach ($files['files'] as $index => $file) {
                if (!$file->isValid()) {
                    return $this->failValidationErrors(["File error at index $index" => $file->getErrorString()]);
                }

                if (!in_array($file->getClientMimeType(), ['application/pdf', 'image/jpeg', 'image/png'])) {
                    return $this->failValidationErrors(["Invalid file type at index $index" => 'Only PDF, JPG, and PNG files are allowed.']);
                }

                if ($file->getSize() > 2 * 1024 * 1024) {
                    return $this->failValidationErrors(["File too large at index $index" => 'Max file size is 2MB.']);
                }

                $originalName = $file->getClientName();
                $storedName = $file->getRandomName();

                $file->move(FCPATH . 'uploads/subtasks', $storedName);

                $uploadedFiles[] = [
                    'original' => $originalName,
                    'stored' => $storedName,
                    'url' => base_url("writable/uploads/subtasks/" . $storedName)
                ];
            }

            $updateData['files'] = json_encode($uploadedFiles);
        }

        if ($model->update($id, $updateData)) {
            return $this->respond([
                'status' => true,
                'message' => 'Subtask updated successfully.',
                'files' => $uploadedFiles
            ]);
        } else {
            return $this->fail('Failed to update subtask.');
        }
    }


    public function ProfileSubtaskUpdateStatus($id)
    {
        $data = $this->request->getJSON(true);
        $status = $data['subtask_status'] ?? null;

        if (!$status) {
            return $this->failValidationErrors('Subtask status is required.');
        }

        $subtaskModel = new SubtaskModel();
        $subtask = $subtaskModel->find($id);

        if (!$subtask) {
            return $this->failNotFound('Subtask not found.');
        }

        // Prevent changing status back from 'completed' to 'pending'
        if ($subtask['subtask_status'] === 'Completed' && $status === 'Pending') {
            return $this->failValidationErrors('Completed subtask cannot be reverted.');
        }

        $updated = $subtaskModel->update($id, ['subtask_status' => $status]);

        if ($updated) {
            return $this->respond(['status' => 'success', 'message' => 'Subtask status updated']);
        }

        return $this->failServerError('Failed to update subtask status');
    }
}
