<?php

namespace App\Controllers\Api;

use App\Models\AnnouncementModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;

class AnnouncementController extends ResourceController
{
    protected $announcementModel;
    protected $authService;
    protected $userModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->authService = new AuthService(service('request'));
        $this->userModel = new UserModel();
    }

    /**
     * User View: Listing Page
     */
    public function index()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }

        $user = $this->authService->user();
        $userId = $user->sub;
        $userRole = $user->role;

        $type = $this->request->getGet('type');
        $date = $this->request->getGet('date');

        $query = $this->announcementModel->where('status', 'Active')
            ->where('is_deleted', 0);

        // Filter by target audience
        $query->groupStart()
                ->where('target_audience', 'All Users')
                ->orGroupStart()
                    ->where('target_audience', 'Specific Role')
                    ->where("FIND_IN_SET('$userRole', target_roles) >", 0)
                ->groupEnd()
                ->orGroupStart()
                    ->where('target_audience', 'Specific Users')
                    ->where("FIND_IN_SET('$userId', target_users) >", 0)
                ->groupEnd()
            ->groupEnd();

        if ($type) {
            $query->where('type', $type);
        }

        if ($date) {
            $query->where('start_date <=', $date)
                  ->where('end_date >=', $date);
        }

        $announcements = $query->orderBy('created_at', 'DESC')->findAll();
        
        // Get read status
        $readStatus = $this->announcementModel->getReadStatus($userId);
        $readIds = array_column($readStatus, 'announcement_id');

        return view('announcements/index', [
            'announcements' => $announcements,
            'readIds' => $readIds,
            'role' => $userRole
        ]);
    }

    /**
     * Admin View: Management Page
     */
    public function adminIndex()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }

        $user = $this->authService->user();
        if (!in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/dashboard');
        }

        $announcements = $this->announcementModel->where('is_deleted', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('announcements/admin_index', [
            'announcements' => $announcements,
            'role' => $user->role
        ]);
    }

    /**
     * Admin View: Create Form
     */
    public function create()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }

        $user = $this->authService->user();
        if (!in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/dashboard');
        }

        $users = $this->userModel->where('is_deleted', 0)->findAll();
        
        return view('announcements/create', [
            'users' => $users,
            'role' => $user->role
        ]);
    }

    /**
     * Admin Action: Store
     */
    public function store()
    {
        if (!$this->authService->check()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission denied'])->setStatusCode(403);
        }
        
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'target_audience' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'messages' => $this->validator->getErrors()]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'target_audience' => $this->request->getPost('target_audience'),
            'status' => $this->request->getPost('status') ?? 'Active',
            'created_by' => $user->sub,
            'is_deleted' => 0
        ];

        if ($data['target_audience'] == 'Specific Role') {
            $roles = $this->request->getPost('target_roles');
            $data['target_roles'] = is_array($roles) ? implode(',', $roles) : $roles;
            $data['target_users'] = null;
        } elseif ($data['target_audience'] == 'Specific Users') {
            $users = $this->request->getPost('target_users');
            $data['target_users'] = is_array($users) ? implode(',', $users) : $users;
            $data['target_roles'] = null;
        } else {
            $data['target_roles'] = null;
            $data['target_users'] = null;
        }

        // File Upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            if ($file->move(FCPATH . 'uploads/announcements', $newName)) {
                $data['attachment'] = $newName;
            }
        }

        try {
            $this->announcementModel->insert($data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Announcement created successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin View: Edit Form
     */
    public function edit($id = null)
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }

        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/dashboard');
        }

        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            return redirect()->to('/announcements/admin')->with('error', 'Announcement not found');
        }

        $users = $this->userModel->where('is_deleted', 0)->findAll();

        return view('announcements/edit', [
            'announcement' => $announcement,
            'users' => $users,
            'role' => $user ? $user->role : null
        ]);
    }

    /**
     * Admin Action: Update
     */
    public function update($id = null)
    {
        if (!$this->authService->check()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission denied'])->setStatusCode(403);
        }

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'target_audience' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'messages' => $this->validator->getErrors()]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'target_audience' => $this->request->getPost('target_audience'),
            'status' => $this->request->getPost('status') ?? 'Active',
        ];

        if ($data['target_audience'] == 'Specific Role') {
            $roles = $this->request->getPost('target_roles');
            $data['target_roles'] = is_array($roles) ? implode(',', $roles) : $roles;
            $data['target_users'] = null;
        } elseif ($data['target_audience'] == 'Specific Users') {
            $users = $this->request->getPost('target_users');
            $data['target_users'] = is_array($users) ? implode(',', $users) : $users;
            $data['target_roles'] = null;
        } else {
            $data['target_roles'] = null;
            $data['target_users'] = null;
        }

        // File Upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            if ($file->move(FCPATH . 'uploads/announcements', $newName)) {
                // Delete old file
                $old = $this->announcementModel->find($id);
                if ($old && $old['attachment'] && file_exists(FCPATH . 'uploads/announcements/' . $old['attachment'])) {
                    @unlink(FCPATH . 'uploads/announcements/' . $old['attachment']);
                }
                $data['attachment'] = $newName;
            }
        }

        try {
            $this->announcementModel->update($id, $data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Announcement updated successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin Action: Delete
     */
    public function delete($id = null)
    {
        if (!$this->authService->check()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $user = $this->authService->user();
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission denied']);
        }

        $this->announcementModel->update($id, ['is_deleted' => 1]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Announcement deleted successfully']);
    }

    /**
     * User Action: Mark as Read
     */
    public function markRead($id = null)
    {
        if (!$this->authService->check()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $user = $this->authService->user();
        $this->announcementModel->markAsRead($id, $user->sub);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Marked as read']);
    }
}
