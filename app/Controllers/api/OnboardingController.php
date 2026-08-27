<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OnboardingModel;
use App\Models\DepartmentModel;
use App\Models\OfferLetterTemplateModel;
use App\Models\CandidateModel;
use App\Models\JobModel;
use App\Libraries\EmailService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OnboardingController extends ResourceController
{
    private $onboardingModel;
    private $authService;

    public function __construct()
    {
        $this->onboardingModel = new OnboardingModel();
        $this->authService = new AuthService(service('request'));
    }

    // Render create onboarding form (if needed for web views)
    public function create($id = null)
    {

        $offerLetterModel = new OfferLetterTemplateModel();
        $data['offerLetters'] = $offerLetterModel->findAll(); // Fetch all templates

        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $candidateModel = new CandidateModel();
        // Fetch only candidates whose interview status is 'completed'
        $candidates = $candidateModel
            ->select('candidate.*') // Select all candidate fields
            ->join('interviews', 'interviews.candidate_id = candidate.id')
            ->where('interviews.status', 'completed')
            ->findAll();
        $jobModel = new JobModel();
        $jobs = $jobModel->findAll();
        return view('onboarding/onboarding', ['departments' => $departments, 'candidates' => $candidates, 'jobs' => $jobs, 'offerLetters' => $data['offerLetters']]);
    }

    // Render onboarding display view (if needed for web views)
    public function display()
    {
        return view('onboarding/view');
    }

    // Create a new onboarding entry
    public function creates()
    { try{
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Validate input data
        $validationRules = [
            'candidate_id' => 'required',
            'department_id' => 'required|numeric',
            'start_date' => 'required|valid_date',
            'onboarding_status' => 'required',
            // 'bank_name' => 'required',
            // 'acc_number' => 'required',
            'offer_later_id' => 'required',
            'docu_submitted' => 'required',
        ];

        $validationMessages = [
            'candidate_id' => ['required' => 'Candidate is required.'],
            'department_id' => [
                'required' => 'Department is required.',
                'numeric' => 'Department ID must be a number.',
            ],
            'start_date' => [
                'required' => 'Start date is required.',
                'valid_date' => 'Start date must be a valid date in YYYY-MM-DD format.',
            ],
            'onboarding_status' => ['required' => 'Onboarding status is required.'],
            // 'bank_name' => ['required' => 'Bank name is required.'],
            // 'acc_number' => ['required' => 'Account number is required.'],
            'offer_later_id' => [
                'required' => 'Offer Letter is required.',
            ],
            'docu_submitted' => [
                'required' => ' Please provide details of the documents submitted.',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Get the form data
        $data = $this->request->getPost();
        $userInfoModel = new \App\Models\UserInfoModel();
        $usersModel = new \App\Models\UserModel();
        $candidateModel = new \App\Models\CandidateModel();
        $onboardingModel = new \App\Models\OnboardingModel();

        // Fetch candidate details
        $candidate = $candidateModel->select('id,job_id,email,candidate_name')
            ->where('id', $data['candidate_id'])
            ->first();

        if (!$candidate) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Invalid Candidate ID'
            ], 400);
        }

        // Check if candidate has already completed onboarding
        $existingOnboarding = $onboardingModel->where('candidate_id', $data['candidate_id'])
            ->where('onboarding_status', 'completed')
            ->first();

        if ($existingOnboarding) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This candidate has already completed the onboarding process.'
            ], 400);
        }

        // Fetch user info
        $userInfo = $userInfoModel->where('email', $candidate['email'])->first();
        if (!$userInfo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'No matching user found in userinfo table'
            ], 400);
        }

        $data['job_id'] = $candidate['job_id'];
        $data['created_by'] = $user->sub;

        // Insert the onboarding entry into the database
        if ($onboardingModel->insert($data)) {


            // $updated = $userInfoModel
            //     ->where('id', $userInfo['id'])
            //     ->set(['status' => 'completed', 'role' => 'employee'])
            //     ->update();

            // if (!$updated) {
            //     return $this->respond([
            //         'status'  => 'error',
            //         'message' => 'Failed to update userinfo status to completed'
            //     ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            // }
            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendOnboardingEmail($data);
            $employeeNote = ''; // ✅ Default value to avoid "undefined variable" error
            if ($data['onboarding_status'] === 'completed') { // Only update when status is 'Completed'
                $updated = $userInfoModel
                    ->where('id', $userInfo['id'])
                    ->set(['status' => 'completed', 'role' => 'employee','joining_date' => $data['start_date']])
                    ->update();

                if (!$updated) {
                    return $this->respond([
                        'status' => 'error',
                        'message' => 'Failed to update userinfo status to completed'
                    ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
                }


                $existingUser = $usersModel->where('email', $candidate['email'])->first();
                if ($existingUser) {
                    // If user exists, update role to 'employee'
                    $usersModel->where('id', $existingUser['id'])
                        ->set(['role' => 'employee'])
                        ->update();
                }
                $employeeNote = 'Candidate has now been converted to an employee. Please update employee details.';

            }
            $notificationModel = new \App\Models\NotificationModel();
            $userModel = new \App\Models\UserModel();
            $candidateModel = new \App\Models\CandidateModel();

            $sender = $userModel->find($user->sub);

            // Get Admin and HR users
            $recipients = $userModel->whereIn('role', ['admin', 'hr'])->findAll();
            $candidate = $candidateModel->select('candidate_name')->find($data['candidate_id']);

            $candidateName = $candidate ? $candidate['candidate_name'] : 'Unknown Candidate';
            foreach ($recipients as $recipient) {
                $notificationModel->insert([
                    'sender_id' => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data' => json_encode([
                        'message' => 'Onboarding started for ' . $candidateName,
                        'type' => 'onboarding',
                        'username' => $sender['username'],
                        'candidate_id' => $data['candidate_id'],
                        'candidate_name' => $candidateName
                    ]),
                    'is_read' => 0
                ]);
            }
            return $this->respond(['status' => 'success', 'message' => 'Onboarding created successfully',
            'employeeNote' => $employeeNote
        ], ResponseInterface::HTTP_CREATED);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to create onboarding entry'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
    catch (\Throwable $e) {
        // 🔍 Catch unexpected errors
        log_message('error', 'Onboarding create failed: ' . $e->getMessage());
        return $this->respond([
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
    }


    // Retrieve all onboarding entries
   public function getAll()
{
    // Validate user authorization
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    // Get optional department ID from GET parameters
    $departmentId = $this->request->getGet('department_id');

    // Build the base query
    $builder = $this->onboardingModel
        ->select('
            onboarding.id, 
            onboarding.start_date, 
            onboarding.candidate_id,
            candidate.candidate_name,
            jobs.job_title,
            onboarding.offer_later_id,
            department.department_name
        ')
        ->join('candidate', 'onboarding.candidate_id = candidate.id')
        ->join('jobs', 'onboarding.job_id = jobs.id')
        ->join('department', 'onboarding.department_id = department.id')
        ->orderBy('onboarding.created_at', 'DESC');

    // Apply department filter if provided
    if (!empty($departmentId)) {
        $builder->where('onboarding.department_id', $departmentId);
    }

    $onboarding = $builder->findAll();

    return $this->respond(['status' => 'success', 'data' => $onboarding]);
}

    // Retrieve a specific onboarding entry by ID
    public function get($id = null)
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Fetch onboarding entry
        $query = $this->onboardingModel->select('
        onboarding.id, 
        onboarding.start_date,
        onboarding.onboarding_status, 
        onboarding.docu_submitted,       
        candidate.candidate_name,
        candidate.email,
        candidate.job_date,
        jobs.job_title,
        department.department_name
    ')
            ->join('candidate', 'onboarding.candidate_id = candidate.id')
            ->join('jobs', 'onboarding.job_id = jobs.id')
            ->join('department', 'onboarding.department_id = department.id');

        // If an ID is provided, filter by ID; otherwise, get all jobs
        if ($id !== null) {
            $interview = $query->where('onboarding.id', $id)->first();

            if ($interview) {
                return $this->respond(['status' => 'success', 'data' => $interview]);
            }


            return $this->respond(['status' => 'error', 'message' => 'Onboarding entry not found'], ResponseInterface::HTTP_NOT_FOUND);
        }
    }
    // Update an existing onboarding entry
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $data = $this->request->getPost();

        // Validate leave_type
        $validationRules = [
            'candidate_id' => 'required',
            'department_id' => 'required|numeric',
            'start_date' => 'required|valid_date',
            'onboarding_status' => 'required',
            // 'bank_name' => 'required',
            // 'acc_number' => 'required',
            'docu_submitted' => 'required',
            'offer_later_id' => 'required',


        ];
        $validationMessages = [
            'candidate_id' => [
                'required' => 'Candidate is required.',
            ],
            'department_id' => [
                'required' => 'Department is required.',
                'numeric' => 'Department ID must be a number.',
            ],

            'start_date' => [
                'required' => 'Start date is required.',
                'valid_date' => 'Start date must be a valid date in YYYY-MM-DD format.',
            ],
            'onboarding_status' => [
                'required' => 'Onboarding status is required.',
            ],
            // 'bank_name' => [
            //     'required' => 'Bank name is required.',
            // ],
            // 'acc_number' => [
            //     'required' => 'Account number is required.',
            // ],
            'offer_later_id' => [
                'required' => 'Offer Letter is required.',
            ],
            'docu_submitted' => [
                'required' => ' Please provide details of the documents submitted.',
            ],
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }


        $userInfoModel = new \App\Models\UserInfoModel();
        $usersModel = new \App\Models\UserModel();
        // Fetch the existing record
        $candidateModel = new \App\Models\CandidateModel();
        $onboardingModel = new \App\Models\OnboardingModel();

        // Fetch candidate details
        $candidate = $candidateModel->select('id,job_id,email,candidate_name')
            ->where('id', $data['candidate_id'])
            ->first();

        if (!$candidate) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Invalid Candidate ID'
            ], 400);
        }

        $existingRecord = $this->onboardingModel->find($id);

        if (!$existingRecord) {
            return $this->respond(['status' => 'error', 'message' => 'Onboarding record not found'], 404);
        }
        $userInfo = $userInfoModel->where('email', $candidate['email'])->first();
        if (!$userInfo) {
            return $this->respond([
                'status' => 'error',
                'message' => 'No matching user found in userinfo table'
            ], 400);
        }
        // Check if onboarding_status is changing from "Pending" to "Completed"
        if ($existingRecord['onboarding_status'] === 'pending' && $data['onboarding_status'] === 'completed') {

            // Update userinfo table
            $updated = $userInfoModel
                ->where('id', $userInfo['id'])
                ->set(['status' => 'completed', 'role' => 'employee'])
                ->update();

            if (!$updated) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Failed to update userinfo status to completed'
                ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }

            // If user exists, update role in users table
            $existingUser = $usersModel->where('email', $candidate['email'])->first();
            if ($existingUser) {
                $usersModel->where('id', $existingUser['id'])
                    ->set(['role' => 'employee','joining_date' => $data['start_date']])
                    ->update();
            }
        }

        // Update the record
        if ($this->onboardingModel->update($id, $data)) { 
            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendOnboardingEmail($data);
            return $this->respond(['status' => 'success', 'message' => 'onbording updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update leave type'], 500);
    }

    // Delete an onboarding entry
    public function delete($id = null)
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        if ($user->role !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admin can delete job records');
        }
        // Delete the onboarding entry
        if ($this->onboardingModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Onboarding entry deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete onboarding entry'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
    public function getById($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Only Admin and HR can access leave records
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $record = $this->onboardingModel
            ->select('onboarding.*,jobs.job_title')

            ->join('jobs', 'onboarding.job_id = jobs.id')
         

            ->where('onboarding.id', $id)
            ->first();

        if ($record) {

            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Interview type not found'], 404);
    }
    public function singlejob($id = null)
    {
        return view('onboarding/display');
    }

    /**
     * Export Onboarding records to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->user();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $departmentId = $this->request->getGet('department_id');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $builder = $this->onboardingModel->builder();
        $builder->select('onboarding.*, candidate.candidate_name, candidate.email, candidate.phone_number, department.department_name, jobs.job_title')
            ->join('candidate', 'candidate.id = onboarding.candidate_id', 'left')
            ->join('department', 'department.id = onboarding.department_id', 'left')
            ->join('jobs', 'jobs.id = onboarding.job_id', 'left');

        if (!empty($departmentId)) {
            $builder->where('onboarding.department_id', (int)$departmentId);
        }
        if (!empty($status)) {
            $builder->where('onboarding.onboarding_status', $status);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('candidate.candidate_name', $search)
                ->orLike('candidate.email', $search)
                ->orLike('candidate.phone_number', $search)
                ->orLike('department.department_name', $search)
                ->orLike('jobs.job_title', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('onboarding.id', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Onboarding');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Candidate Name',
            'C1' => 'Email',
            'D1' => 'Phone Number',
            'E1' => 'Department',
            'F1' => 'Job Position',
            'G1' => 'Joining Date',
            'H1' => 'Onboarding Status',
            'I1' => 'Documents Submitted',
            'J1' => 'Created At'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $docs = (!empty($item['docu_submitted']) && $item['docu_submitted'] != '0') ? 'Yes' : 'No';

            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['candidate_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['email'] ?? '-');
            $sheet->setCellValueExplicit('D' . $rowNum, (string)($item['phone_number'] ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, $item['department_name'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $item['job_title'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, !empty($item['start_date']) ? date('Y-m-d', strtotime($item['start_date'])) : '-');
            $sheet->setCellValue('H' . $rowNum, ucfirst($item['onboarding_status'] ?? 'Pending'));
            $sheet->setCellValue('I' . $rowNum, $docs);
            $sheet->setCellValue('J' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');

            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:J' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Onboarding_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
