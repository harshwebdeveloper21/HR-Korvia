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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
            'full_name'      => 'required',
            'email'          => 'required|valid_email',
            'mobile_number'  => 'required',
            'schedule_date'  => 'permit_empty|valid_date', // Adjusted to not strictly require schedule_date since it's on step 6
        ];
        $validationMessages = [
            'full_name' => [
                'required' => 'Full name is required.',
            ],
            'email' => [
                'required' => 'Email is required.',
                'valid_email' => 'Please provide a valid email.',
            ],
            'mobile_number' => [
                'required' => 'Phone number is required.',
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
        $this->ensureColumns();
        $this->normalizeSchedule($data);
        if (empty($data['schedule_date'])) {
            $data['schedule_date'] = date('Y-m-d H:i:s');
        }
        $data['job_id'] = !empty($data['job_id']) ? $data['job_id'] : 0;
        $data['description'] = $data['description'] ?? '';
        foreach (['department_id', 'interviewer_id', 'joining_date'] as $nullable) {
            if (isset($data[$nullable]) && $data[$nullable] === '') {
                $data[$nullable] = null;
            }
        }
        if (!isset($data['status']) || empty($data['status'])) {
            if (!empty($data['selection_status'])) {
                $data['status'] = $data['selection_status'];
            } elseif (!empty($data['interview_status'])) {
                $data['status'] = $data['interview_status'];
            } else {
                $data['status'] = 'scheduled';
            }
        }
        $userInfoModel = new \App\Models\UserInfoModel();
        $candidateModel = new \App\Models\CandidateModel();
        
        $candidateName = $data['full_name'] ?? 'Unknown Candidate';

        // Reuse the candidate record with the same email so the same person is not added twice
        if (empty($data['candidate_id'])) {
            $sameEmail = $candidateModel->where('email', $data['email'])->first();
            if ($sameEmail) {
                $data['candidate_id'] = $sameEmail['id'];
            }
        }

        if (!empty($data['candidate_id'])) {
            $candidate = $candidateModel->find($data['candidate_id']);

            if (!$candidate) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Invalid Candidate ID'
                ], 400);
            }

            if (empty($data['job_id'])) {
                $data['job_id'] = $candidate['job_id'] ?: 0;
            }

            $existingInterview = $this->interviewModel->where('candidate_id', $data['candidate_id'])
                ->where('job_id', $data['job_id'])
                ->whereIn('status', ['scheduled', 'completed'])
                ->first();

            if ($existingInterview) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'This candidate is already scheduled for an interview for this position.'
                ], 400);
            }

            $userInfo = $userInfoModel->where('email', $candidate['email'])->first() ?: null;
            $this->syncCandidate((int) $data['candidate_id'], $data);
        } else {
            $newCandidateId = $candidateModel->insert([
                'candidate_name'  => $data['full_name'],
                'email'           => $data['email'],
                'phone_number'    => $data['mobile_number'],
                'job_id'          => $data['job_id'] ?: 0,
                'job_date'        => date('Y-m-d'),
                'current_address' => $data['current_address'] ?? null,
                'status'          => 'applied',
                'created_by'      => $user->sub,
            ]);
            if (!$newCandidateId) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Failed to create candidate record'
                ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data['candidate_id'] = $newCandidateId;
        }

        $data['created_by'] = $user->sub;

        // Extract multiple entries arrays
        $educations = $data['education'] ?? [];
        $experiences = $data['experience'] ?? [];
        $rounds = $data['rounds'] ?? [];
        $total_score = $data['total_score'] ?? 0;
        $data['interview_score'] = $total_score;
        unset($data['education'], $data['experience'], $data['rounds'], $data['total_score']);

        // Insert the interview entry
        if ($this->interviewModel->insert($data)) {

            if (!empty($userInfo) && ($userInfo['status'] ?? '') === 'candidate') {
                $userInfoModel->update($userInfo['id'], ['status' => 'scheduled']);
            }

            // Save Educations
            $eduModel = new \App\Models\CandidateEducationModel();
            foreach ($educations as $edu) {
                if (empty($edu['degree']) && empty($edu['course']) && empty($edu['university']) && empty($edu['passing_year']) && empty($edu['percentage'])) {
                    continue;
                }
                $edu['candidate_id'] = $data['candidate_id'];
                $eduModel->insert($edu);
            }

            // Save Experiences
            $expModel = new \App\Models\CandidateExperienceModel();
            foreach ($experiences as $exp) {
                if (empty($exp['company']) && empty($exp['role']) && empty($exp['total_experience']) && empty($exp['last_salary']) && empty($exp['notice_period']) && empty($exp['reason_for_leaving'])) {
                    continue;
                }
                $exp['candidate_id'] = $data['candidate_id'];
                $expModel->insert($exp);
            }

            // Save Rounds
            $interviewId = $this->interviewModel->getInsertID();
            $roundModel = new \App\Models\InterviewRoundModel();
            foreach ($rounds as $round) {
                if (empty($round['interviewer_id'])) {
                    continue;
                }
                $round['interview_id'] = $interviewId;
                $roundModel->insert($round);
            }

            $notificationModel = new \App\Models\NotificationModel();
            $userModel = new \App\Models\UserModel();

            $sender = $userModel->find($user->sub);

            // Get Admin and HR users
            $recipients = $userModel->whereIn('role', ['admin', 'hr'])->findAll();

            foreach ($recipients as $recipient) {
                $notificationModel->insert([
                    'sender_id'    => $user->sub,
                    'recipient_id' => $recipient['id'],
                    'data'         => json_encode([
                        'message'  => 'New interview scheduled for candidate: ' . $candidateName,
                        'type'     => 'interview',
                        'username' => $sender['username'],
                        'candidate_id'  => $data['candidate_id'] ?? null,
                        'candidate_name' => $candidateName
                    ]),
                    'is_read' => 0
                ]);
            }

            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendInterviewEmail($data);
            
            if (isset($data['convert_to_employee']) && $data['convert_to_employee'] == 1 && isset($data['branch_id']) && isset($data['department_id'])) {
                $this->_processEmployeeConversion($interviewId, $data['branch_id'], $data['department_id']);
            }

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
        $interviews = $this->interviewModel->select('interviews.id, COALESCE(jobs.job_title, interviews.position_applied_for) as job_title, interviews.status, interviews.selection_status, interviews.schedule_date , COALESCE(candidate.candidate_name, interviews.full_name) as candidate_name, interviews.convert_to_employee, interviews.candidate_id, (SELECT MAX(ia.id) FROM interview_assessments ia WHERE ia.interview_id = interviews.id) as assessment_id', false)
            ->join('candidate', 'interviews.candidate_id = candidate.id', 'left')
            ->join('jobs', 'interviews.job_id = jobs.id', 'left')
            ->orderBy('interviews.created_at', 'DESC')
            ->findAll();
        return $this->respond(['status' => 'success', 'data' => $interviews]);
    }

    public function get($id = null)
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $record = $this->getInterviewDetail($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'interview not found'], ResponseInterface::HTTP_NOT_FOUND);
    }

    private function getInterviewDetail($id): ?array
    {
        $record = $this->interviewModel->find($id);
        if (!$record) {
            return null;
        }

        if (!empty($record['candidate_id'])) {
            $eduModel = new \App\Models\CandidateEducationModel();
            $expModel = new \App\Models\CandidateExperienceModel();
            $record['educations'] = $eduModel->where('candidate_id', $record['candidate_id'])->findAll();
            $record['experiences'] = $expModel->where('candidate_id', $record['candidate_id'])->findAll();
        } else {
            $record['educations'] = [];
            $record['experiences'] = [];
        }

        $roundModel = new \App\Models\InterviewRoundModel();
        $record['rounds'] = $roundModel->where('interview_id', $record['id'])->findAll();

        $db = \Config\Database::connect();
        $candidateRow = !empty($record['candidate_id']) ? $db->table('candidate')->select('candidate_name')->where('id', $record['candidate_id'])->get()->getRowArray() : null;
        $jobRow = !empty($record['job_id']) ? $db->table('jobs')->select('job_title')->where('id', $record['job_id'])->get()->getRowArray() : null;
        $departmentRow = !empty($record['department_id']) ? $db->table('department')->select('department_name')->where('id', $record['department_id'])->get()->getRowArray() : null;
        $interviewerId = $record['interviewer_id'] ?: ($record['rounds'][0]['interviewer_id'] ?? null);
        $interviewerRow = $interviewerId ? $db->table('users')->select('username')->where('id', $interviewerId)->get()->getRowArray() : null;

        $record['candidate_name'] = $record['full_name'] ?: ($candidateRow['candidate_name'] ?? null);
        $record['job_title'] = $record['position_applied_for'] ?: ($jobRow['job_title'] ?? null);
        $record['department_name'] = $departmentRow['department_name'] ?? null;
        $record['interviewer_name'] = $interviewerRow['username'] ?? null;

        return $record;
    }

    public function pdf($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return redirect()->to('/login');
        }
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $record = $this->getInterviewDetail($id);
        if (!$record) {
            return $this->response->setStatusCode(404)->setBody('Interview not found');
        }

        $edu = $record['educations'][0] ?? [];
        $exp = $record['experiences'][0] ?? [];
        $round = $record['rounds'][0] ?? [];
        $pick = static fn ($primary, $fallback = null) => ($primary !== null && trim((string) $primary) !== '') ? $primary : $fallback;
        $date = static function ($value, bool $withTime = false) {
            if (empty($value) || strpos((string) $value, '0000-00-00') === 0) {
                return '';
            }
            $ts = strtotime($value);
            if ($ts === false) {
                return (string) $value;
            }
            return ($withTime && date('H:i', $ts) !== '00:00') ? date('d M Y, h:i A', $ts) : date('d M Y', $ts);
        };

        $sections = [
            'POSITION DETAILS' => [
                ['Position Applied For', $record['job_title']],
                ['Date of Interview', $date($record['schedule_date'], true) ?: $date($record['interview_date'])],
                ['Department', $record['department_name']],
                ['Interviewer Name', $record['interviewer_name']],
                ['Source of Application', $record['source_of_application'] ?? null],
                ['Interview Round', $pick($record['interview_round'], $round['interview_round'] ?? null)],
            ],
            'CANDIDATE DETAILS' => [
                ['Candidate Name', $record['candidate_name']],
                ['Contact Number', $record['mobile_number']],
                ['Email Address', $record['email'], true],
                ['Current Address', $record['current_address'], true],
            ],
            'EDUCATION & EXPERIENCE' => [
                ['Highest Qualification', $pick($record['highest_qualification'], $edu['degree'] ?? null)],
                ['Institute / University', $pick($record['college_university'], $edu['university'] ?? null)],
                ['Total Experience', $pick($record['total_experience'], $exp['total_experience'] ?? null)],
                ['Relevant Experience', $record['relevant_experience']],
                ['Current / Last Employer', $pick($record['previous_company'], $exp['company'] ?? null)],
                ['Current Designation', $pick($record['previous_job_title'], $exp['role'] ?? null)],
                ['Key Skills / Areas of Expertise', $record['technical_skills'], true],
            ],
            'COMPENSATION & AVAILABILITY' => [
                ['Current CTC', $pick($record['previous_salary'], $exp['last_salary'] ?? null)],
                ['Expected CTC', $record['expected_salary']],
                ['Notice Period', $pick($record['notice_period'], $exp['notice_period'] ?? null)],
                ['Earliest Joining Date', $date($record['joining_date'])],
                ['Reason for Change / Leaving Current Role', $pick($record['reason_for_leaving'], $exp['reason_for_leaving'] ?? null), true],
            ],
        ];

        $company = (new \App\Models\CompanyLogoModel())->first();
        $logoSrc = '';
        $logoFile = $company['pdf_logo'] ?? ($company['logo_img'] ?? '');
        if ($logoFile && is_file(FCPATH . 'upload/' . $logoFile)) {
            $path = FCPATH . 'upload/' . $logoFile;
            $mime = mime_content_type($path) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }

        $html = view('interview/print_form', [
            'sections'     => $sections,
            'company_name' => $company['company_name'] ?? getCompanyName(),
            'logo_src'     => $logoSrc,
        ]);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeName = preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($record['candidate_name'] ?: 'candidate'));
        $filename = 'Interview_Form_' . trim($safeName, '_') . '_' . $record['id'] . '.pdf';
        $disposition = $this->request->getGet('download') ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setBody($dompdf->output());
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

        $this->ensureColumns();
        $this->normalizeSchedule($data);

        $hasEducations = array_key_exists('education', $data);
        $hasExperiences = array_key_exists('experience', $data);
        $hasRounds = array_key_exists('rounds', $data);
        $educations = $data['education'] ?? [];
        $experiences = $data['experience'] ?? [];
        $rounds = $data['rounds'] ?? [];
        if (array_key_exists('total_score', $data)) {
            $data['interview_score'] = $data['total_score'];
        }
        unset($data['education'], $data['experience'], $data['rounds'], $data['total_score']);

        // Blank inputs clear the stored value, except for required columns
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        foreach (['candidate_id', 'job_id', 'schedule_date', 'status', 'description', 'full_name', 'email', 'mobile_number'] as $required) {
            if (array_key_exists($required, $data) && $data[$required] === null) {
                unset($data[$required]);
            }
        }

        // Check if the Interview entry exists
        $entry = $this->interviewModel->find($id);
        if (!$entry) {
            return $this->respond(['status' => 'error', 'message' => 'Interview entry not found'], ResponseInterface::HTTP_NOT_FOUND);
        }

        // Update the Interview entry
        if ($this->interviewModel->update($id, $data)) {
            $candidate_id = $data['candidate_id'] ?? $entry['candidate_id'];
            if ($candidate_id) {
                $this->syncCandidate((int) $candidate_id, $data);
            }

            if ($candidate_id && $hasEducations) {
                $eduModel = new \App\Models\CandidateEducationModel();
                $eduModel->where('candidate_id', $candidate_id)->delete();
                foreach ($educations as $edu) {
                    if (!empty($edu['degree']) || !empty($edu['course']) || !empty($edu['university'])) {
                        $edu['candidate_id'] = $candidate_id;
                        $eduModel->insert($edu);
                    }
                }
            }

            if ($candidate_id && $hasExperiences) {
                $expModel = new \App\Models\CandidateExperienceModel();
                $expModel->where('candidate_id', $candidate_id)->delete();
                foreach ($experiences as $exp) {
                    if (!empty($exp['company']) || !empty($exp['role'])) {
                        $exp['candidate_id'] = $candidate_id;
                        $expModel->insert($exp);
                    }
                }
            }

            if ($hasRounds) {
                $roundModel = new \App\Models\InterviewRoundModel();
                $roundModel->where('interview_id', $id)->delete();
                foreach ($rounds as $round) {
                    if (!empty($round['interviewer_id'])) {
                        $round['interview_id'] = $id;
                        $roundModel->insert($round);
                    }
                }
            }

            //Send welcome email
            $emailService = new EmailService();
            $emailService->sendInterviewEmail($data);
            
            if (isset($data['convert_to_employee']) && $data['convert_to_employee'] == 1 && isset($data['branch_id']) && isset($data['department_id'])) {
                $this->_processEmployeeConversion($id, $data['branch_id'], $data['department_id']);
            }

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
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: Only Admin or HR can delete interview records');
        }

        $interview = $this->interviewModel->find($id);

        if (!$interview) {
            return $this->failNotFound('Interview record not found');
        }

        $status = strtolower($interview['status']);

        // Both 'cancelled', 'scheduled', and 'completed' interviews may be deleted

        // Both 'cancelled' and 'completed' interviews may be deleted
        // (Frontend already shows a strong warning for completed interviews)
        if ($this->interviewModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Interview deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete interview'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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
            if (!empty($record['candidate_id'])) {
                $eduModel = new \App\Models\CandidateEducationModel();
                $expModel = new \App\Models\CandidateExperienceModel();
                $record['educations'] = $eduModel->where('candidate_id', $record['candidate_id'])->findAll();
                $record['experiences'] = $expModel->where('candidate_id', $record['candidate_id'])->findAll();
            } else {
                $record['educations'] = [];
                $record['experiences'] = [];
            }
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Interview type not found'], 404);
    }
    private function normalizeSchedule(array &$data): void
    {
        if (empty($data['schedule_date'])) {
            return;
        }
        $timestamp = strtotime(str_replace('T', ' ', $data['schedule_date']));
        if ($timestamp === false) {
            unset($data['schedule_date']);
            return;
        }
        $data['schedule_date'] = date('Y-m-d H:i:s', $timestamp);
        $data['interview_date'] = date('Y-m-d', $timestamp);
        $data['interview_time'] = date('H:i:s', $timestamp);
    }

    private function syncCandidate(int $candidateId, array $data): void
    {
        $map = [
            'full_name'       => 'candidate_name',
            'email'           => 'email',
            'mobile_number'   => 'phone_number',
            'current_address' => 'current_address',
            'job_id'          => 'job_id',
        ];
        $update = [];
        foreach ($map as $from => $to) {
            if (!empty($data[$from])) {
                $update[$to] = $data[$from];
            }
        }
        if (empty($update)) {
            return;
        }
        try {
            (new CandidateModel())->update($candidateId, $update);
        } catch (\Throwable $e) {
            log_message('error', 'Candidate sync failed: ' . $e->getMessage());
        }
    }

    private function ensureColumns(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('source_of_application', 'interviews')) {
                $db->query("ALTER TABLE interviews ADD COLUMN source_of_application VARCHAR(100) NULL DEFAULT NULL AFTER interview_round");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Interview column check failed: ' . $e->getMessage());
        }
    }

    public function creates($id = null)
    {
        $interviewModel = new \App\Models\InterviewModel();
        $existingCandidateIds = $interviewModel->select('candidate_id')->where('candidate_id IS NOT NULL')->findAll();
        $existingIds = array_column($existingCandidateIds, 'candidate_id');

        if ($id !== null) {
            $currentInterview = $interviewModel->find($id);
            if ($currentInterview && $currentInterview['candidate_id']) {
                $existingIds = array_diff($existingIds, [$currentInterview['candidate_id']]);
            }
        }

        $candidateModel = new CandidateModel();
        if (!empty($existingIds)) {
            $candidates = $candidateModel->whereNotIn('id', $existingIds)->groupBy('email')->findAll();
        } else {
            $candidates = $candidateModel->groupBy('email')->findAll();
        }
        $userModel = new UserModel();
        $interviewers = $userModel->whereIn('role', ['admin', 'hr', 'employee'])->findAll();

        $jobModel = new \App\Models\JobModel();
        $jobs = $jobModel->findAll();

        $departmentModel = new \App\Models\DepartmentModel();
        $departments = $departmentModel->findAll();

        $branchModel = new \App\Models\BranchModel();
        $branches = $branchModel->findAll();

        return view('interview/interviews', [
            'candidates' => $candidates,
            'interviewers' => $interviewers,
            'jobs' => $jobs,
            'departments' => $departments,
            'branches' => $branches
        ]);
    }


    public function display()
    {
        $departmentModel = new \App\Models\DepartmentModel();
        $branchModel = new \App\Models\BranchModel();
        
        $data = [
            'departments' => $departmentModel->findAll(),
            'branches' => $branchModel->findAll(),
        ];
        return view('interview/view', $data);
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

    public function updateConvertToEmployee($id)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $payload = $this->request->getJSON();
        $convertToEmployee = $payload->convert_to_employee ?? 0;
        $branchId = $payload->branch_id ?? null;
        $departmentId = $payload->department_id ?? null;
        
        $interview = $this->interviewModel->find($id);
        if (!$interview) {
            return $this->failNotFound('Interview not found');
        }

        // If converting to employee, update/create the user
        if ($convertToEmployee == 1) {
            if (!$branchId || !$departmentId) {
                return $this->respond(['status' => 'error', 'message' => 'Branch and Department are required to convert to employee'], 400);
            }
            $this->_processEmployeeConversion($id, $branchId, $departmentId);
        }

        if ($this->interviewModel->update($id, ['convert_to_employee' => $convertToEmployee])) {
            return $this->respond(['status' => 'success', 'message' => 'Convert to Employee updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update convert status'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function _processEmployeeConversion($interviewId, $branchId, $departmentId)
    {
        $interview = $this->interviewModel->find($interviewId);
        if (!$interview) return false;

        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();

        // Check if user already exists
        $existingUser = $userModel->where('email', $interview['email'])->first();
        
        $userId = null;
        if (!$existingUser) {
            // Insert into users
            $userData = [
                'username' => $interview['full_name'] ?? $interview['candidate_name'] ?? 'Employee',
                'email' => $interview['email'],
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'employee',
                'branch_id' => $branchId,
                'department_id' => $departmentId
            ];
            $userId = $userModel->insert($userData);
        } else {
            $userId = $existingUser['id'];
            // Update their role to employee
            $userModel->update($userId, [
                'role' => 'employee',
                'branch_id' => $branchId,
                'department_id' => $departmentId
            ]);
        }

        // Upsert into user_info
        $existingInfo = $userInfoModel->where('user_id', $userId)->first();
        $nameParts = explode(' ', ($interview['full_name'] ?? $interview['candidate_name'] ?? 'Employee'), 2);
        $monthlySalary = (new \App\Models\OnboardingModel())->monthlySalaryForCandidate($interview['candidate_id'] ?? null);
        if ($monthlySalary === null && is_numeric($interview['offered_salary'] ?? null)) {
            $monthlySalary = (float) $interview['offered_salary'];
        }
        if ($monthlySalary === null) {
            $monthlySalary = (float) ($existingInfo['salary'] ?? 0);
        }
        $userInfoData = [
            'user_id' => $userId,
            'firstname' => $nameParts[0] ?? '',
            'lastname' => $nameParts[1] ?? '',
            'email' => $interview['email'],
            'gender' => $interview['gender'] ?? '',
            'date_of_birth' => $interview['date_of_birth'] ?? null,
            'address_1' => $interview['current_address'] ?? '',
            'contact_number' => $interview['mobile_number'] ?? '',
            'department_id' => $departmentId,
            'joining_date' => $interview['joining_date'] ?? null,
            'job_id' => $interview['job_id'] ?? null,
            'salary' => $monthlySalary,
            'status' => 'active'
        ];
        
        if (!$existingInfo) {
            $userInfoModel->insert($userInfoData);
        } else {
            $userInfoModel->update($existingInfo['id'], $userInfoData);
        }
        
        return true;
    }

    /**
     * Export Interviews to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->user();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $status = $this->request->getGet('status');
        $search = trim((string)$this->request->getGet('search'));

        $builder = $this->interviewModel->builder();
        $builder->select('interviews.*, candidate.candidate_name, candidate.email, candidate.phone_number, jobs.job_title, department.department_name')
            ->join('candidate', 'candidate.id = interviews.candidate_id', 'left')
            ->join('jobs', 'jobs.id = COALESCE(interviews.job_id, candidate.job_id)', 'left')
            ->join('department', 'department.id = jobs.department_id', 'left');

        if (!empty($status)) {
            $builder->where('interviews.status', $status);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('candidate.candidate_name', $search)
                ->orLike('candidate.email', $search)
                ->orLike('candidate.phone_number', $search)
                ->orLike('jobs.job_title', $search)
                ->orLike('department.department_name', $search)
                ->orLike('interviews.description', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('interviews.id', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Interviews');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Candidate Name',
            'C1' => 'Email',
            'D1' => 'Phone Number',
            'E1' => 'Job Position',
            'F1' => 'Department',
            'G1' => 'Scheduled Date & Time',
            'H1' => 'Status',
            'I1' => 'Description / Remarks',
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
            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['candidate_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['email'] ?? '-');
            $sheet->setCellValueExplicit('D' . $rowNum, (string)($item['phone_number'] ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, $item['job_title'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $item['department_name'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, !empty($item['schedule_date']) ? date('Y-m-d H:i', strtotime($item['schedule_date'])) : '-');
            $sheet->setCellValue('H' . $rowNum, ucfirst($item['status'] ?? 'Scheduled'));
            $sheet->setCellValue('I' . $rowNum, strip_tags($item['description'] ?? '-'));
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

        $filename = 'Interviews_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
