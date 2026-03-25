<?php

namespace App\Controllers\api;

use App\Models\ComplaintModel;
use App\Services\AuthService;
use App\Services\PushNotificationService;
use CodeIgniter\RESTful\ResourceController;

class ComplaintsController extends ResourceController
{
    protected $userModel;
    protected $notificationModel;
    protected $pushNotificationService;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->authService = new AuthService(\Config\Services::request());
        $this->userModel = new \App\Models\UserModel();
        $this->notificationModel = new \App\Models\NotificationModel();
        $this->pushNotificationService = new PushNotificationService();
    }

    /**
     * User View: List own submissions
     */
    public function index()
    {
        $user = $this->authService->user();
        if (!$user) return redirect()->to('/login');

        // If Admin/HR, they can see all in adminIndex. 
        // Regular users see only their own.
        $complaints = $this->complaintModel->where('user_id', $user->sub)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('complaints_feedback/index', [
            'complaints' => $complaints,
            'user' => $user,
            'role' => $user->role
        ]);
    }

    /**
     * User View: Create Form
     */
    public function create()
    {
        $user = $this->authService->user();
        if (!$user) return redirect()->to('/login');

        $userModel = new \App\Models\UserModel();
        $users = [];
        if (in_array($user->role, ['admin', 'hr'])) {
            $users = $userModel->select('id, username, role')->findAll();
        }

        return view('complaints_feedback/create', [
            'user' => $user,
            'role' => $user->role,
            'users' => $users
        ]);
    }

    /**
     * Store new submission
     */
    public function store()
    {
        $user = $this->authService->user();
        if (!$user) return $this->failUnauthorized('Please login');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'mobile'  => 'required|numeric|min_length[10]',
            'type'    => 'required|in_list[Complaint,Feedback]',
            'subject' => 'required|min_length[5]',
            'message' => 'required|min_length[10]'
        ]);

        if (!$this->validate($validation->getRules())) {
            return $this->respond(['status' => 'error', 'messages' => $this->validator->getErrors()], 400);
        }

        $file = $this->request->getFile('file');
        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/complaints', $fileName);
        }

        // If Admin/HR is creating on behalf of someone else
        $targetUserId = $user->sub;
        if (in_array($user->role, ['admin', 'hr']) && $this->request->getPost('user_id')) {
            $targetUserId = $this->request->getPost('user_id');
        }

        $data = [
            'user_id' => $targetUserId,
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'mobile'  => $this->request->getPost('mobile'),
            'type'    => $this->request->getPost('type'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'file'    => $fileName,
            'status'  => $this->request->getPost('status') ?? 'Pending'
        ];

        if ($this->complaintModel->insert($data)) {
            $complaintId = $this->complaintModel->getInsertID();
            $this->sendComplaintNotifications($data, $complaintId);
            return $this->respond(['status' => 'success', 'message' => 'The ' . strtolower($data['type']) . ' has been submitted successfully.'], 200);
        }

        return $this->fail('Failed to submit. Try again.');
    }

    /**
     * Helper to send notifications for new complaints/feedback
     */
    private function sendComplaintNotifications($complaintData, $complaintId)
    {
        $type = $complaintData['type']; // Complaint or Feedback
        $subject = $complaintData['subject'];
        $senderName = $complaintData['name'];
        $senderId = $complaintData['user_id'];

        // Get all Admins and HRs
        $recipients = $this->userModel->whereIn('role', ['admin', 'hr'])->where('is_deleted', 0)->findAll();

        foreach ($recipients as $recipient) {
            // Optional: Skip if sender is one of them (e.g., HR complaining/feedback)
            if ($recipient['id'] == $senderId) continue;

            $this->notificationModel->insert([
                'sender_id'    => $senderId,
                'recipient_id' => $recipient['id'],
                'data'         => json_encode([
                    'type'         => strtolower($type), // 'complaint' or 'feedback'
                    'subject'      => $subject,
                    'message'      => "New $type: $subject",
                    'username'     => $senderName,
                    'complaint_id' => $complaintId
                ]),
                'is_read' => 0
            ]);
        }

        // Send Push Notification to all Admins and HRs
         $this->pushNotificationService->notifyAdmins(
            "Employee $type (New Submission)",
            "$senderName has submitted a new " . strtolower($type) . ": " . $subject,
            [
                'type' => strtolower($type),
                'complaint_id' => $complaintId,
                'username' => $senderName,
                'subject' => $subject,
                'url' => base_url('/complaints/admin')
            ]
        );
    }

    /**
     * Admin View: Management Page
     */
    public function adminIndex()
    {
        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/dashboard');
        }

        return view('complaints_feedback/admin_index', [
            'role' => $user->role
        ]);
    }

    /**
     * Admin View: Update Page
     */
    public function updateView($id = null)
    {
        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/dashboard');
        }

        $complaint = $this->complaintModel->find($id);
        if (!$complaint) return redirect()->to('/complaints/admin')->with('error', 'Record not found');

        $userModel = new \App\Models\UserModel();
        $users = $userModel->select('id, username, role')->findAll();

        return view('complaints_feedback/update', [
            'complaint' => $complaint,
            'user' => $user,
            'role' => $user->role,
            'users' => $users
        ]);
    }

    /**
     * API: List for DataTables
     */
    public function list()
    {
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $date_from = $this->request->getGet('date_from');
        $date_to = $this->request->getGet('date_to');

        $data = $this->complaintModel->getFilteredComplaints($type, $status, $date_from, $date_to);
        return $this->respond(['data' => $data]);
    }

    /**
     * API: Update status & remark
     */
    public function updateComplaint($id = null)
    {
        $user = $this->authService->user();
        if (!$user) return $this->failUnauthorized();

        $complaint = $this->complaintModel->find($id);
        if (!$complaint) return $this->failNotFound('Record not found');

        // Check permission: Admin/HR can update any. User can maybe only update their own if it's still pending?
        // But user says: "Admin and HR are all access"
        if (!in_array($user->role, ['admin', 'hr']) && $complaint['user_id'] != $user->sub) {
            return $this->failForbidden('You do not have permission to update this record.');
        }

        $data = [
            'status'       => $this->request->getPost('status'),
            'admin_remark' => $this->request->getPost('admin_remark')
        ];

        // Handle Resolution File Upload (Admin/HR Only Response)
        if (in_array($user->role, ['admin', 'hr'])) {
            $resolutionFile = $this->request->getFile('resolution_file');
            if ($resolutionFile && $resolutionFile->isValid() && !$resolutionFile->hasMoved()) {
                // Delete old resolution file if exists
                if (!empty($complaint['resolution_file'])) {
                    $oldPath = FCPATH . 'uploads/complaints/' . $complaint['resolution_file'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $newName = $resolutionFile->getRandomName();
                $resolutionFile->move(FCPATH . 'uploads/complaints', $newName);
                $data['resolution_file'] = $newName;
            }
        }

        // If Admin/HR, allow editing EVERYTHING
        if (in_array($user->role, ['admin', 'hr'])) {
            $data['name']    = $this->request->getPost('name');
            $data['email']   = $this->request->getPost('email');
            $data['mobile']  = $this->request->getPost('mobile');
            $data['type']    = $this->request->getPost('type');
            $data['subject'] = $this->request->getPost('subject');
            $data['message'] = $this->request->getPost('message');
            $data['user_id'] = $this->request->getPost('user_id') ?? $complaint['user_id'];
        }

        if ($this->complaintModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Record updated successfully.']);
        }

        return $this->fail('Update failed.');
    }

    /**
     * API: Delete record
     */
    public function deleteComplaint($id = null)
    {
        $user = $this->authService->user();
        if (!$user) return $this->failUnauthorized();

        $complaint = $this->complaintModel->find($id);
        if (!$complaint) return $this->failNotFound();

        // Permissions: User can only delete their own. Admin/HR can delete any.
        $canDelete = false;
        if (in_array($user->role, ['admin', 'hr'])) {
            $canDelete = true;
        } elseif ($complaint['user_id'] == $user->sub) {
            $canDelete = true;
        }

        if (!$canDelete) return $this->failForbidden('You do not have permission to delete this record.');

        // Delete associated file
        if ($complaint['file']) {
            $filePath = FCPATH . 'uploads/complaints/' . $complaint['file'];
            if (file_exists($filePath)) unlink($filePath);
        }

        if ($this->complaintModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Record deleted permanently.']);
        }

        return $this->fail('Delete failed.');
    }
}
