<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Services\AuthService;
use App\Models\CandidateModel;
use App\Models\JobModel;
use App\Models\UserInfoModel;
use App\Models\UserModel;
use App\Models\InterviewModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\EmailService;

class CandidateController extends ResourceController
{
    private $candidateModel;
    private $authService;
    private $userInfoModel;
    private $userModel;
    private $interviewModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userInfoModel = new UserInfoModel();
        $this->candidateModel = new CandidateModel();
        $this->interviewModel = new InterviewModel();
        $this->authService = new AuthService(service('request'));
    }

    public function create($id = null)
    {
        $jobModel = new JobModel();
        $jobs = $jobModel->findAll();

        return view('candidate/candidate', ['jobs' => $jobs]);
    }

    public function display()
    {
        return view('candidate/view');
    }
    // public function CandidateApply()
    // {
    //     $jobModel = new JobModel();
    //     $jobs = $jobModel->findAll();

    //     return view('candidate/candidate_apply', ['jobs' => $jobs]);
    // }

    public function creates()
    {
        // Check if the user is authorized with a valid token
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Validate input data
        $validationRules = [
            'candidate_name' => 'required',
            'email' => 'required|valid_email|is_unique[candidate.email]',
            'job_id' => 'required',
            'phone_number' => 'required|numeric|exact_length[10]',
            'resume' => 'uploaded[resume]|max_size[resume,2048]|ext_in[resume,pdf,doc,docx]',
            // 'notes' => 'required',
        ];
        $validationMessages = [
            'candidate_name' => [
                'required' => 'Candidate name is required.',
            ],
            'email' => [
                'required'    => 'Email is required.',
                'valid_email' => 'Please enter a valid email address.',
                'is_unique'   => 'This email has already been used to apply.'
            ],
            'job_id' => [
                'required' => 'Job is required.',
            ],

            'phone_number' => [
                'required' => 'Phone number is required.',
                'numeric'  => 'Phone number must contain only numbers.',
                'exact_length' => 'Phone number must be exactly 10 digits.',  // Custom message for exact_length
            ],
            'resume' => [
                'uploaded' => 'Resume file is required.',
                'max_size' => 'Resume file size must not exceed 2MB.',
                'ext_in'   => 'Resume must be in PDF, DOC, or DOCX format.',
            ],
            // 'notes' => [
            //     'required' => 'The notes field is required.',
            // ],
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
        $data['job_date'] = date('Y-m-d');
        // Handle optional file upload
        $resume = $this->request->getFile('resume');
        if ($resume && $resume->isValid() && !$resume->hasMoved()) {
            // Set the file path for the public folder
            $filePath = FCPATH . 'uploads/resumes/';

            $newFileName = $resume->getRandomName();
            $resume->move($filePath, $newFileName);

            // Add file name (relative path) to data
            $data['resume'] = 'uploads/resumes/' . $newFileName;  // Store relative path for the resume
        } else {
            // If no file is uploaded, set `resume` to NULL
            $data['resume'] = null;
        }

        // Add user ID to track who created the candidate
        $data['created_by'] = $user->sub;

        // Generate the next employee_id for the candidate
        $userInfoModel = new \App\Models\UserInfoModel();
        $lastEmployee = $userInfoModel->orderBy('employee_id', 'DESC')->first();
        $newEmployeeId = $lastEmployee ? $lastEmployee['employee_id'] + 1 : 1000; // Default to 1000 if no employees exist

        $userData = [
            'username' => $data['candidate_name'],
            'email' => $data['email'],
            'role' => 'candidate',

        ];

        $userId = $this->userModel->insert($userData);

        if (!$userId) {
            return $this->respond(['status' => 'error', 'message' => 'Failed to create user'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Insert into `user_info` table
        $userInfoData = [
            'user_id' => $userId,
            'firstname' => $data['candidate_name'],
            'email' => $data['email'],
            'contact_number' => $data['phone_number'],
            'status' => 'candidate',
            'resume' => $data['resume'],
            'job_id' => $data['job_id'],
            'employee_id' => $newEmployeeId, // Set the newly generated employee ID
        ];
        $userInfoInserted = $this->userInfoModel->insert($userInfoData);
        //Send welcome email
        $emailService = new EmailService();
        $emailService->sendCandidateWelcomeEmail($data);
        if (!$userInfoInserted) {
            return $this->respond(['status' => 'error', 'message' => 'Failed to insert user info'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
        // Insert the candidate into the database
        if ($this->candidateModel->insert($data)) {
            // ✅ Notify Admins, HRs, and the candidate
            $notificationModel = new \App\Models\NotificationModel();
            $userModel = new \App\Models\UserModel();

            // Get all admins and HRs
            $recipients = $userModel->whereIn('role', ['admin', 'hr'])->findAll();

            // Add the candidate themselves as a recipient
            $recipients[] = ['id' => $userId]; // Candidate's user ID

            // Avoid duplicate recipients if candidate is also HR/Admin
            $uniqueRecipients = [];
            foreach ($recipients as $recipient) {
                $uniqueRecipients[$recipient['id']] = $recipient;
            }

            // Send notifications
            foreach ($uniqueRecipients as $recipient) {
                $notificationModel->insert([
                    'sender_id'    => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'username' => $data['candidate_name'],
                        'type'     => 'candidate',
                        'message'  => 'New candidate applied: ' . $data['candidate_name']
                    ]),
                    'is_read' => 0
                ]);
            }

            return $this->respond(['status' => 'success', 'message' => 'Candidate created successfully'], ResponseInterface::HTTP_CREATED);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to create candidate'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Get all candidates
    public function getAll()
    {
        // Check if the user is authorized
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $candidates = $this->candidateModel->select('candidate.id, jobs.job_title,candidate.email,candidate.notes , candidate.candidate_name , candidate.resume, candidate.status')
            ->join('jobs', 'candidate.job_id = jobs.id')
            ->orderBy('candidate.created_at', 'DESC')
            ->findAll();
        return $this->respond(['status' => 'success', 'data' => $candidates]);
    }

    // Get a specific candidate by ID
    public function get($id = null)
    {
        // Check if the user is authorized
        if (!$this->authService->check()) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $query = $this->candidateModel->select('
        candidate.id, 
        candidate.candidate_name,
        candidate.email, 
        candidate.phone_number, 
        candidate.status AS candidate_status, 
        candidate.resume, 
       candidate.notes,
        jobs.job_title,
        jobs.post_date, 
        jobs.status,
       ')
            ->join('jobs', 'candidate.job_id = jobs.id');

        // If an ID is provided, filter by ID; otherwise, get all jobs
        if ($id !== null) {
            $candidate = $query->where('candidate.id', $id)->first();

            if ($candidate) {
                return $this->respond(['status' => 'success', 'data' => $candidate]);
            }
            if ($candidate) {
                // Construct full URL for resume file
                $candidate['resume_url'] = base_url($candidate['resume']);

                return $this->respond(['status' => 'success', 'data' => $candidate]);
            }

            return $this->respond(['status' => 'error', 'message' => 'Candidate not found'], ResponseInterface::HTTP_NOT_FOUND);
        }
    }

    public function update($id = null)
    {
        // Check if the user is authorized with a valid token
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Validate input data
        $validationRules = [
            'candidate_name' => 'required',
            'email' => 'required|valid_email',
            'job_id' => 'required',
            'phone_number' => 'required|numeric|exact_length[10]',
        ];
        $validationMessages = [
            'candidate_name' => ['required' => 'Candidate name is required.'],
            'email' => ['required' => 'Email is required.', 'valid_email' => 'Please enter a valid email address.'],
            'job_id' => ['required' => 'Job is required.'],
            'phone_number' => [
                'required' => 'Phone number is required.',
                'numeric'  => 'Phone number must contain only numbers.',
                'exact_length' => 'Phone number must be exactly 10 digits.',  // Custom message for exact_length
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

        // Fetch existing candidate record
        $candidate = $this->candidateModel->find($id);
        if (!$candidate) {
            return $this->respond(['status' => 'error', 'message' => 'Candidate not found'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Handle optional file upload for resume
        $resume = $this->request->getFile('resume');
        if ($resume && $resume->isValid() && !$resume->hasMoved()) {
            $filePath = FCPATH . 'uploads/resumes/';
            $newFileName = $resume->getRandomName();
            $resume->move($filePath, $newFileName);
            $data['resume'] = 'uploads/resumes/' . $newFileName;
        }

        // Add updated_by field
        $data['updated_by'] = $user->sub;

        // Update candidate record
        $this->candidateModel->update($id, $data);

        // Fetch related user record
        $userRecord = $this->userModel->where('email', $candidate['email'])->first();
        if ($userRecord) {
            $userUpdateData = [
                'username' => $data['candidate_name'],
                'email' => $data['email'],
            ];
            $this->userModel->update($userRecord['id'], $userUpdateData);
        }

        // Fetch related user_info record
        $userInfoRecord = $this->userInfoModel->where('email', $candidate['email'])->first();
        if ($userInfoRecord) {
            $userInfoUpdateData = [
                'firstname' => $data['candidate_name'],
                'email' => $data['email'],
                'contact_number' => $data['phone_number'],
                'job_id' => $data['job_id'],
                'resume' => isset($data['resume']) ? $data['resume'] : $userInfoRecord['resume'],
            ];
            $this->userInfoModel->update($userInfoRecord['id'], $userInfoUpdateData);
        }

        // Send notification email
        $emailService = new EmailService();
        $emailService->sendCandidateWelcomeEmail($data);

        return $this->respond(['status' => 'success', 'message' => 'Candidate updated successfully']);
    }

    // Delete a candidate
    public function delete($id = null)
    {
        // Check if the user is authenticated
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Check if the user is an admin
        if ($user->role !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admin can delete candidate records');
        }

        // Check if the candidate has any associated interviews
        $interviews = $this->interviewModel->where('candidate_id', $id)->findAll();
        if (!empty($interviews)) {
            return $this->failForbidden('This candidate has associated interviews and cannot be deleted');
        }

        // Attempt to delete the candidate record
        if ($this->candidateModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Candidate deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete candidate record'], 500);
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

        $record = $this->candidateModel->find($id);
        if ($record) {
            // Assuming the resume file is stored in 'uploads/resumes/' and the stored path is relative
            if (!empty($record['resume'])) {
                // Return the full URL for the resume, combining base URL and resume path
                $record['resume'] = base_url($record['resume']); // Ensure base_url() uses the correct path to the file
            }

            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Candidate not found'], 404);
    }
    public function singlejob($id = null)
    {

        return view('candidate/display');
    }

    public function downloadResume($id)
    {
        $candidateModel = new CandidateModel();
        $candidate = $candidateModel->find($id);

        if (!$candidate || empty($candidate['resume'])) {
            return $this->failNotFound('Resume not found.');
        }

        $filePath = FCPATH . $candidate['resume'];  // ✅ Use FCPATH instead of ROOTPATH

        if (!file_exists($filePath)) {
            return $this->failNotFound('File does not exist.');
        }

        // Force file download
        return $this->response->download($filePath, null)->setFileName(basename($filePath));
    }
}
