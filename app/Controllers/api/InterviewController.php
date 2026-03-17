<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\InterviewModel;
use App\Models\CandidateModel;
use App\Models\JobModel;
use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\EmailService;

class InterviewController extends ResourceController
{
    private $interviewModel;
    private $authService;

    public function __construct()
    {
        $this->interviewModel = new InterviewModel();
        $this->authService = new AuthService(service('request'));
    }

    public function create()
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }


        // Validate input data
        $validationRules = [
            'candidate_id' => 'required', // Ensure candidate_id exists
            // 'description'  => 'required|string',
            // 'status'       => 'required|string',
            'schedule_date'  => 'required|valid_date|after_created_at[created_by]', // Custom rule
        ];
        $validationMessages = [
            'candidate_id' => [
                'required' => 'Candidate is required.',
            ],
            // 'description' => [
            //     'required' => 'Description is required.',
            //     'string'   => 'Description must be a valid text.'
            // ],
            // 'status' => [
            //     'required' => 'The status field is required.',
            //     'string'   => 'The status must be a valid text.'
            // ],
            'schedule_date' => [
                'required'   => 'Schedule date field is required.',
                'valid_date' => 'Schedule date must be a valid date.',
                'after_created_at' => 'Schedule date must be after today.',
            ],
        ];


        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Get form data
        $data = $this->request->getPost();
        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = 'scheduled';
        }
        $userInfoModel = new \App\Models\UserInfoModel();
        $candidateModel = new \App\Models\CandidateModel();
        // Fetch job_id from candidate table based on selected candidate_id
        $candidate = $candidateModel->select('id,job_id,email')->where('id', $data['candidate_id'])->first();

        if (!$candidate) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Invalid Candidate ID'
            ], 400);
        }
        $existingInterview = $this->interviewModel->where('candidate_id', $data['candidate_id'])
            ->where('job_id', $candidate['job_id'])
            ->whereIn('status', ['scheduled', 'completed']) // Check both statuses
            ->first();

        if ($existingInterview) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'This candidate is already scheduled for an interview.'
            ], 400);
        }
        $userInfo = $userInfoModel->where('email', $candidate['email'])->first();
        if (!$userInfo) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'No matching user found in userinfo table'
            ], 400);
        }

        $data['job_id'] = $candidate['job_id']; // Automatically set job_id
        $data['created_by'] = $user->sub;

        // Insert the interview entry
        if ($this->interviewModel->insert($data)) {

            $updated = $userInfoModel
                ->where('id', $userInfo['id']) // Match the userinfo ID
                ->set(['status' => 'scheduled'])
                ->update();

            if (!$updated) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Failed to update userinfo status to scheduled'
                ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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
                    'sender_id'    => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'message'  => 'New interview scheduled for candidate ID: ' . $data['candidate_id'],
                        'type'     => 'interview',
                        'username' => $sender['username'],
                        'candidate_id'  => $data['candidate_id'],
                        'candidate_name' => $candidateName
                    ]),
                    'is_read' => 0
                ]);
            }

            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendInterviewEmail($data);
            return $this->respond([
                'status'  => 'success',
                'message' => 'Interview created successfully'
            ], ResponseInterface::HTTP_CREATED);
        }

        return $this->respond([
            'status'  => 'error',
            'message' => 'Failed to create Interview entry'
        ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function getAll()
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
        // Retrieve onboarding entries
        $interviews = $this->interviewModel->select('interviews.id, jobs.job_title, interviews.status , interviews.schedule_date , candidate.candidate_name')
            ->join('candidate', 'interviews.candidate_id = candidate.id')
            ->join('jobs', 'interviews.job_id = jobs.id')
            ->orderBy('interviews.created_at', 'DESC')
            ->findAll();
        return $this->respond(['status' => 'success', 'data' => $interviews]);
    }

    // Show specific Interview
    public function get($id = null)
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        $query = $this->interviewModel->select('
        interviews.id, 
        interviews.schedule_date, 
        interviews.status AS interview_status, 
        candidate.candidate_name,
        jobs.job_title,
        interviews.description
    ')
            ->join('candidate', 'interviews.candidate_id = candidate.id')
            ->join('jobs', 'interviews.job_id = jobs.id');

        // If an ID is provided, filter by ID; otherwise, get all jobs
        if ($id !== null) {
            $interview = $query->where('interviews.id', $id)->first();

            if ($interview) {
                return $this->respond(['status' => 'success', 'data' => $interview]);
            }

            return $this->respond(['status' => 'error', 'message' => 'interview not found'], ResponseInterface::HTTP_NOT_FOUND);
        }
        // Fetch onboarding entry
        // $entry = $this->interviewModel->find($id);
        // if ($entry) {
        //     return $this->respond(['status' => 'success', 'data' => $entry]);
        // }

        // return $this->respond(['status' => 'error', 'message' => 'Interview entry not found'], ResponseInterface::HTTP_NOT_FOUND);
    }


    // Update Interview
    public function update($id = null)
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Get input data for update
        $data = $this->request->getPost();

        // Validate input data
        if (empty($data) || !is_array($data)) {
            return $this->respond(['status' => 'error', 'message' => 'No data provided to update'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Filter out empty values
        $data = array_filter($data, fn ($value) => !empty($value));

        // Check if the Interview entry exists
        $entry = $this->interviewModel->find($id);
        if (!$entry) {
            return $this->respond(['status' => 'error', 'message' => 'Interview entry not found'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Update the Interview entry
        if ($this->interviewModel->update($id, $data)) {
            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendInterviewEmail($data);
            return $this->respond(['status' => 'success', 'message' => 'Interview entry updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update Interview entry'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Delete Interview
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
        $interview = $this->interviewModel->find($id);

        if (!$interview) {
            return $this->failNotFound('Interview record not found');
        }
    
        if (strtolower($interview['status']) !== 'cancelled') {
            return $this->failForbidden('Interview can only be deleted if the status is "cancelled"');
        }
    
        // Delete the onboarding entry
        if ($this->interviewModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Interview entry deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete Interview entry'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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

        $record = $this->interviewModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Interview type not found'], 404);
    }
    public function creates($id = null)
    {
        $candidateModel = new CandidateModel();
        $candidates = $candidateModel->findAll();;


        return view('interview/interviews', ['candidates' => $candidates]);
    }


    public function display()
    {
        return view('interview/view');
    }

    // public function edits($id)
    // {
    //     $interviewModel = new \App\Models\InterviewModel(); 
    //     $interview = $interviewModel->find($id); 


    //     if (!$interview) {
    //         return redirect()->to('/interviews')->with('error', 'Interview not found');
    //     }

    //     return view('interview/interviews', ['interview' => $interview]);
    // }
    public function singlejob($id = null)
    {

        return view('interview/display');
    }
    public function getCandidateJob($candidate_id)
    {
        // Initialize CandidateModel and JobsModel
        $candidateModel = new \App\Models\CandidateModel();
        $jobModel = new \App\Models\JobModel(); // Make sure JobsModel is created

        // Fetch job_id from candidate table
        $candidate = $candidateModel->select('job_id')->where('id', $candidate_id)->first();

        if ($candidate && isset($candidate['job_id'])) {
            // Fetch job title from jobs table using job_id
            $job = $jobModel->select('job_title')->where('id', $candidate['job_id'])->first();

            if ($job && isset($job['job_title'])) {
                return $this->respond([
                    'status' => 'success',
                    'job_title' => $job['job_title'] // Return job title instead of job_id
                ]);
            }

            return $this->respond([
                'status'  => 'error',
                'message' => 'Job not found for the selected candidate'
            ], 404);
        }

        return $this->respond([
            'status'  => 'error',
            'message' => 'Candidate not found or job_id missing'
        ], 404);
    }
    public function updateStatus($id)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $status = $this->request->getJSON()->status ?? null;
        if (!in_array($status, ['scheduled', 'completed', 'cancelled'])) {
            return $this->failValidationErrors('Invalid status provided');
        }

        // Find interview record
        $interview = $this->interviewModel->find($id);
        if (!$interview) {
            return $this->failNotFound('Interview not found');
        }

        // Prevent update if already completed
        if ($interview['status'] === 'completed') {
            return $this->respond([
                'status'  => 'error',
                'message' => 'The interview has already been completed and cannot be changed.'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Update the status
        if ($this->interviewModel->update($id, ['status' => $status])) {
            return $this->respond(['status' => 'success', 'message' => 'Interview status updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update status'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }
}
