<?php

namespace App\Controllers\api;

use App\Models\ComplaintModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;

class ComplaintsController extends ResourceController
{
    protected $complaintModel;
    protected $authService;

    public function __construct()
    {
        $this->complaintModel = new ComplaintModel();
        $this->authService = new AuthService(\Config\Services::request());
    }

    /**
     * User View: List own submissions
     */
    public function index()
    {
        $user = $this->authService->user();
        if (!$user) return redirect()->to('/login');

        // Check if user is Admin/HR - if so, maybe they can see all? 
        // But user said: "other user only fore view and this complaints feebacked add on"
        // I'll keep user view to 'own records' as standard.
        
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

        return view('complaints_feedback/create', [
            'user' => $user,
            'role' => $user->role
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

        $data = [
            'user_id' => $user->sub,
            'name'    => $this->request->getPost('name'),
            'email'   => $this->request->getPost('email'),
            'mobile'  => $this->request->getPost('mobile'),
            'type'    => $this->request->getPost('type'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'file'    => $fileName,
            'status'  => 'Pending'
        ];

        if ($this->complaintModel->insert($data)) {
            return $this->respond(['status' => 'success', 'message' => 'Your ' . strtolower($data['type']) . ' has been submitted successfully.'], 200);
        }

        return $this->fail('Failed to submit. Try again.');
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

        return view('complaints_feedback/update', [
            'complaint' => $complaint,
            'user' => $user,
            'role' => $user->role
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
        return $this->respond($data);
    }

    /**
     * API: Update status & remark
     */
    public function updateComplaint($id = null)
    {
        $user = $this->authService->user();
        if (!$user || !in_array($user->role, ['admin', 'hr', 'user'])) { // If user can update? User says "only for view" 
            return $this->failUnauthorized();
        }

        $complaint = $this->complaintModel->find($id);
        if (!$complaint) return $this->failNotFound('Record not found');

        $data = [
            'status'       => $this->request->getPost('status'),
            'admin_remark' => $this->request->getPost('admin_remark')
        ];

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
