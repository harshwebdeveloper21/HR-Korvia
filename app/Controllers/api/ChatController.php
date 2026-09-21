<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ChatModel;
use App\Models\UserInfoModel;
use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\Controller;

class ChatController extends ResourceController
{
    private $authService;
    public function __construct()
    {
        // $this->attendanceModel = new AttendanceModel();
        $this->authService = new AuthService(service('request'));
    }
    public function view()
    {
        $userId = session()->get('id'); // Assuming user ID is stored in session
        $userModel = new UserModel();
        $users = $userModel->where('id !=', $userId)->findAll();
        return view('chat/chat', ['users' => $users]);
    }

    public function getUsers()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $userModel = new UserModel();
        $userInfoModel = new UserInfoModel();
        $session = session();
        $currentUserId = $session->get('id');
        $userModel = new UserModel();
        $currentUserRole = $session->get('role'); // Get the logged-in user's role

        if ($currentUserRole == 'admin') {
            // Admin can see all HR and employees
            $users = $userInfoModel->select('user_info.user_id as id, user_info.firstname as name, users.username, user_info.role,user_info.profile_image,users.chat_status,users.last_activity')
                ->join('users', 'users.id = user_info.user_id') // Join with users table
                ->where('users.id !=', $currentUserId) // Exclude current user
                ->whereIn('user_info.role', ['hr', 'employee'])

                ->findAll();
        } elseif ($currentUserRole == 'hr') {
            // HR can see all employees
            $users = $userInfoModel->select('user_info.user_id as id, user_info.firstname as name, users.username, user_info.role,user_info.profile_image,users.chat_status')
                ->join('users', 'users.id = user_info.user_id') // Join with users table
                ->where('users.id !=', $currentUserId) // Exclude current user
                ->whereIn('user_info.role', ['admin', 'employee']) // HR can see both Admin and Employees


                ->findAll();
        } else {
            // Employees can only see HR and Admin
            $users = $userInfoModel->select('user_info.user_id as id, user_info.firstname as name, users.username, user_info.role,user_info.profile_image,users.chat_status')
                ->join('users', 'users.id = user_info.user_id') // Join with users table
                ->where('users.id !=', $currentUserId) // Exclude current user
                ->whereIn('user_info.role', ['admin', 'hr'])
                ->findAll();
        }

        return $this->response->setJSON([
            'success' => true,
            'users' => $users
        ]);
    }
    // public function getUsers()
    // {
    //     // Check authentication
    //     $user = $this->authService->check();
    //     if (!$user) {
    //         return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
    //     }

    //     $session = session();
    //     $currentUserId = $session->get('id');
    //     $currentUserRole = $session->get('role');

    //     // Fallback to POST if session data not available
    //     if (empty($currentUserId) || empty($currentUserRole)) {
    //         $request = \Config\Services::request();
    //         $currentUserId = $request->getPost('id');
    //         $currentUserRole = $request->getPost('role');
    //     }

    //     if (empty($currentUserId) || empty($currentUserRole)) {
    //         return $this->respond(['status' => 'error', 'message' => 'User session or post data missing'], 400);
    //     }

    //     $userInfoModel = new UserInfoModel();

    //     // Common select and join
    //     $builder = $userInfoModel->select('user_info.user_id as id, user_info.firstname as name, users.username, user_info.role, user_info.profile_image, users.chat_status, users.last_activity')
    //         ->join('users', 'users.id = user_info.user_id')
    //         ->where('users.id !=', $currentUserId);

    //     // Apply role-based filtering
    //     if ($currentUserRole === 'admin') {
    //         $builder->whereIn('user_info.role', ['hr', 'employee']);
    //     } elseif ($currentUserRole === 'hr') {
    //         $builder->whereIn('user_info.role', ['admin', 'employee']);
    //     } else {
    //         $builder->whereIn('user_info.role', ['admin', 'hr']);
    //     }

    //     $users = $builder->findAll();

    //     return $this->respond([
    //         'success' => true,
    //         'users' => $users
    //     ]);
    // }

    public function getUsers_api()
    {
        // Check authentication
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $session = session();
        $currentUserId = $session->get('id');
        $currentUserRole = $session->get('role');

        // Fallback to POST if session data not available
        if (empty($currentUserId) || empty($currentUserRole)) {
            $request = \Config\Services::request();
            $currentUserId = $request->getPost('id');
            $currentUserRole = $request->getPost('role');
        }

        if (empty($currentUserId) || empty($currentUserRole)) {
            return $this->respond(['status' => 'error', 'message' => 'User session or post data missing'], 400);
        }

        $userInfoModel = new UserInfoModel();

        // Common select and join
        $builder = $userInfoModel->select('user_info.user_id as id, user_info.firstname as name, users.username, user_info.role, user_info.profile_image, users.chat_status, users.last_activity')
            ->join('users', 'users.id = user_info.user_id')
            ->where('users.id !=', $currentUserId);

        // Apply role-based filtering
        if ($currentUserRole === 'admin') {
            $builder->whereIn('user_info.role', ['hr', 'employee']);
        } elseif ($currentUserRole === 'hr') {
            $builder->whereIn('user_info.role', ['admin', 'employee']);
        } else {
            $builder->whereIn('user_info.role', ['admin', 'hr']);
        }

        $users = $builder->findAll();

        return $this->respond([
            'success' => true,
            'users' => $users
        ]);
    }
    public function sendMessage()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $sender_id = $user->sub;

        if ($this->request->isAJAX()) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'receiver_id' => 'required'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $validation->getErrors()
                ]);
            }

            $message = trim($this->request->getVar('message'));
            $receiver_id = $this->request->getPost('receiver_id');

            // Define allowed file types
            $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'png', 'jpg', 'jpeg', 'gif', 'mp4', 'webm', 'ogg'];

            // Handle file upload
            $fileNames = [];
            if ($this->request->getFiles() && isset($this->request->getFiles()['files'])) {
                foreach ($this->request->getFiles()['files'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $fileExtension = $file->getClientExtension();

                        // Validate file type
                        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                            return $this->response->setJSON([
                                'status' => 'error',
                                'message' => "Invalid file type: $fileExtension. Allowed types: " . implode(', ', $allowedExtensions)
                            ]);
                        }

                        // Save file
                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/chat/', $newName);
                        $fileNames[] = $newName;
                    }
                }
            }

            // Ensure at least one of message or file is present
            if (empty($message) && empty($fileNames)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Message cannot be empty unless a file is attached.'
                ]);
            }

            // Convert array to JSON string
            $fileNamesJson = !empty($fileNames) ? json_encode($fileNames) : null;

            // Save message to database
            $chatModel = new ChatModel();
            $chatModel->insert([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $message,
                'files' => $fileNamesJson, // Store file names in JSON format
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Message sent successfully!']);
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
    }
    public function sendMessage_api()
    {
        // if (!$this->request->isAJAX()) {
        //     throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        // }

        // Get sender and receiver
        $sender_id = $this->request->getPost('sender_id');
        $receiver_id = $this->request->getPost('receiver_id');
        $message = trim($this->request->getVar('message'));
        // print_r($receiver_id);
        // die;
        // Validate presence of required fields
        if (empty($sender_id) || empty($receiver_id)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'sender_id, receiver_id are required.'
            ], 400);
        }

        // Allowed file types
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'png', 'jpg', 'jpeg', 'gif', 'mp4', 'webm', 'ogg'];
        $fileNames = [];

        if ($this->request->getFiles() && isset($this->request->getFiles()['files'])) {
            foreach ($this->request->getFiles()['files'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $fileExtension = strtolower($file->getClientExtension());

                    if (!in_array($fileExtension, $allowedExtensions)) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => "Invalid file type: $fileExtension. Allowed types: " . implode(', ', $allowedExtensions)
                        ]);
                    }

                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/chat/', $newName);
                    $fileNames[] = $newName;
                }
            }
        }

        // If both message and files are empty
        if (empty($message) && empty($fileNames)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Message cannot be empty unless a file is attached.'
            ]);
        }

        // Save message
        $chatModel = new ChatModel();
        $chatModel->insert([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'files' => !empty($fileNames) ? json_encode($fileNames) : null,
            'sent_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Message sent successfully!'
        ]);
    }




    public function getMessages($receiver_id)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $sender_id = session()->get('user_id'); // Get logged-in user ID

        $chatModel = new ChatModel();

        // Fetch messages with sender details
        $messages = $chatModel->select('chat.*, user_info.firstname, user_info.profile_image')
            ->join('user_info', 'user_info.user_id = chat.sender_id', 'left')
            ->where("(chat.sender_id = $sender_id AND chat.receiver_id = $receiver_id) OR (chat.sender_id = $receiver_id AND chat.receiver_id = $sender_id)")
            ->orderBy('chat.sent_at', 'ASC')
            ->findAll();

        // Append full profile image URLs and file URLs
        foreach ($messages as &$message) {
            $message['sender_name'] = $message['firstname'];
            $message['profile_image'] = !empty($message['profile_image'])
                ? base_url('upload/' . $message['profile_image'])
                : base_url(env('ImagePath') . 'upload/1789966027_54c5a38ccda20f7c2bac.jpg');

            if (!empty($message['files'])) {
                $filesArray = json_decode($message['files'], true);
                if (is_array($filesArray)) {
                    $message['files'] = array_map(function ($fileName) {
                        return base_url('uploads/chat/' . $fileName);
                    }, $filesArray);
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $messages,
            'logged_in_user_id' => $sender_id
        ]);
    }
    public function updateStatus()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $data = $this->request->getJSON();
        $status = $data->status ?? 'offline';

        $userModel = new UserModel();
        $userModel->update($user->sub, [
            'chat_status' => $status,
            'last_activity' => date('Y-m-d H:i:s')
        ]);

        return $this->respond(['status' => 'success']);
    }
}
