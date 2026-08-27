<?php

namespace App\Controllers\api;

use App\Models\ComplaintModel;
use App\Services\AuthService;
use App\Services\PushNotificationService;
use CodeIgniter\RESTful\ResourceController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ComplaintsController extends ResourceController
{
    protected $complaintModel;
    protected $authService;
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
        if (!$user)
            return redirect()->to('/login');

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
        if (!$user)
            return redirect()->to('/login');

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
        if (!$user)
            return $this->failUnauthorized('Please login');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'mobile' => 'required|numeric|min_length[10]',
            'type' => 'required|in_list[Complaint,Feedback]',
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
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'type' => $this->request->getPost('type'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'file' => $fileName,
            'status' => $this->request->getPost('status') ?? 'Pending'
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
            if ($recipient['id'] == $senderId)
                continue;

            $this->notificationModel->insert([
                'sender_id' => $senderId,
                'recipient_id' => $recipient['id'],
                'data' => json_encode([
                    'type' => strtolower($type), // 'complaint' or 'feedback'
                    'subject' => $subject,
                    'message' => "New $type: $subject",
                    'username' => $senderName,
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
        if (!$complaint)
            return redirect()->to('/complaints/admin')->with('error', 'Record not found');

        $userModel = new \App\Models\UserModel();
        $users = $userModel->select('id, username, role')->findAll();

        return view('complaints_feedback/update', [
            'complaint' => $complaint,
            'user' => $user,
            'role' => $user->role,
            'users' => $users
        ]);
    }

    public function list()
    {
        $user = $this->authService->user();
        if (!$user)
            return $this->failUnauthorized();

        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $date_from = $this->request->getGet('date_from');
        $date_to = $this->request->getGet('date_to');

        $userId = null;
        if (!in_array($user->role, ['admin', 'hr'])) {
            $userId = $user->sub;
        }

        $data = $this->complaintModel->getFilteredComplaints($type, $status, $date_from, $date_to, $userId);
        return $this->respond(['data' => $data]);
    }

    /**
     * API: Update status & remark
     */
    public function updateComplaint($id = null)
    {
        $user = $this->authService->user();
        if (!$user)
            return $this->failUnauthorized();

        $complaint = $this->complaintModel->find($id);
        if (!$complaint)
            return $this->failNotFound('Record not found');

        if (!in_array($user->role, ['admin', 'hr']) && $complaint['user_id'] != $user->sub) {
            return $this->failForbidden('You do not have permission to update this record.');
        }

        $data = [];

        // Admin and HR can update everything
        if (in_array($user->role, ['admin', 'hr'])) {
            $data = [
                'status' => $this->request->getPost('status'),
                'admin_remark' => $this->request->getPost('admin_remark'),
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'mobile' => $this->request->getPost('mobile'),
                'type' => $this->request->getPost('type'),
                'subject' => $this->request->getPost('subject'),
                'message' => $this->request->getPost('message'),
                'user_id' => $this->request->getPost('user_id') ?? $complaint['user_id']
            ];

            $resolutionFile = $this->request->getFile('resolution_file');
            if ($resolutionFile && $resolutionFile->isValid() && !$resolutionFile->hasMoved()) {
                if (!empty($complaint['resolution_file'])) {
                    $oldPath = FCPATH . 'uploads/complaints/' . $complaint['resolution_file'];
                    if (file_exists($oldPath))
                        unlink($oldPath);
                }
                $newName = $resolutionFile->getRandomName();
                $resolutionFile->move(FCPATH . 'uploads/complaints', $newName);
                $data['resolution_file'] = $newName;
            }
        } else {
            // Regular user can maybe update basic fields, but currently no functionality exists for this
            // We'll leave it empty to prevent unauthorized status changes
            return $this->failForbidden('Updating is reserved for HR/Admin at this stage.');
        }

        if (!empty($data) && $this->complaintModel->update($id, $data)) {
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
        if (!$user)
            return $this->failUnauthorized();

        $complaint = $this->complaintModel->find($id);
        if (!$complaint)
            return $this->failNotFound();

        // Permissions: User can only delete their own. Admin/HR can delete any.
        $canDelete = false;
        if (in_array($user->role, ['admin', 'hr'])) {
            $canDelete = true;
        } elseif ($complaint['user_id'] == $user->sub) {
            $canDelete = true;
        }

        if (!$canDelete)
            return $this->failForbidden('You do not have permission to delete this record.');

        // Delete associated file
        if ($complaint['file']) {
            $filePath = FCPATH . 'uploads/complaints/' . $complaint['file'];
            if (file_exists($filePath))
                unlink($filePath);
        }

        if ($this->complaintModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Record deleted permanently.']);
        }

        return $this->fail('Delete failed.');
    }

    /**
     * Export Complaints & Feedback to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->user();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $builder = $this->complaintModel->builder();
        $builder->select('complaints.*, user_info.firstname, user_info.lastname, department.department_name')
            ->join('user_info', 'user_info.user_id = complaints.user_id', 'left')
            ->join('department', 'department.id = user_info.department_id', 'left');

        // Role check
        if (!in_array($user->role, ['admin', 'hr'])) {
            $builder->where('complaints.user_id', $user->sub);
        }

        if (!empty($type)) {
            $builder->where('complaints.type', $type);
        }
        if (!empty($status)) {
            $builder->where('complaints.status', $status);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('complaints.name', $search)
                ->orLike('complaints.subject', $search)
                ->orLike('complaints.message', $search)
                ->orLike('complaints.email', $search)
                ->groupEnd();
        }

        $complaints = $builder->orderBy('complaints.id', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Complaints & Feedback');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Request ID',
            'C1' => 'Name',
            'D1' => 'Department',
            'E1' => 'Email',
            'F1' => 'Mobile',
            'G1' => 'Type',
            'H1' => 'Subject',
            'I1' => 'Description / Message',
            'J1' => 'Status',
            'K1' => 'Admin Remark / Resolution',
            'L1' => 'Submitted Date',
            'M1' => 'Last Updated'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($complaints as $item) {
            $fullName = trim(($item['firstname'] ?? '') . ' ' . ($item['lastname'] ?? ''));
            if (empty($fullName)) {
                $fullName = $item['name'] ?? 'N/A';
            }

            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, 'REQ-' . sprintf('%04d', $item['id']));
            $sheet->setCellValue('C' . $rowNum, $fullName);
            $sheet->setCellValue('D' . $rowNum, $item['department_name'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $item['email'] ?? '-');
            $sheet->setCellValueExplicit('F' . $rowNum, (string)($item['mobile'] ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $rowNum, $item['type'] ?? 'Complaint');
            $sheet->setCellValue('H' . $rowNum, $item['subject'] ?? '-');
            $sheet->setCellValue('I' . $rowNum, $item['message'] ?? '-');
            $sheet->setCellValue('J' . $rowNum, $item['status'] ?? 'Pending');
            $sheet->setCellValue('K' . $rowNum, $item['admin_remark'] ?? '-');
            $sheet->setCellValue('L' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $sheet->setCellValue('M' . $rowNum, !empty($item['updated_at']) ? date('Y-m-d H:i', strtotime($item['updated_at'])) : '-');

            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:M' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Complaints_Feedback_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
