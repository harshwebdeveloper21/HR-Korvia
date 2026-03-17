<?php

namespace App\Libraries;

use App\Models\SmtpModel;
use App\Models\UserModel;
use App\Models\CompanyLogoModel;
use App\Models\UserInfoModel;

use Config\Services;

class EmailService
{
    protected $email;
    protected $mailEnabled;

    public function __construct()
    {
        $smtpModel = new SmtpModel();
        $smtp = $smtpModel->getSettings();

        if (!$smtp) {
            throw new \Exception("SMTP settings not found!");
        }
        
        // Check if mail sending is enabled
        $this->mailEnabled = ($smtp['sent_mail_enable'] == 1);
        
        if (!$this->mailEnabled) {
            // Mail is disabled, set email to null
            $this->email = null;
            return;
        }

        $config = [
            'protocol'   => $smtp['smtp_protocol'],
            'SMTPHost'   => $smtp['smtp_host'],
            'SMTPPort'   => (int) $smtp['smtp_port'], // Ensure it's an integer
            'SMTPUser'   => $smtp['smtp_username'],
            'SMTPPass'   => $smtp['smtp_password'],
            'SMTPCrypto' => $smtp['smtp_encryption'],
            'mailType'   => 'html',
            'charset'    => 'utf-8',
            'wordWrap'   => true
        ];

        $this->email = Services::email();
        $this->email->initialize($config);
        $this->email->setFrom($smtp['smtp_from_email'], $smtp['smtp_from_name']);
    }

    /**
     * Check if email service is available and enabled
     * @return bool
     */
    protected function isEmailEnabled()
    {
        return $this->mailEnabled && $this->email !== null;
    }

    public function sendForgotEmail($to, $subject, $message)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            throw new \Exception('Mail sending is disabled in SMTP settings.');
        }

        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMailType('html'); // ✅ Must be before setMessage
        $this->email->setMessage($message);

        if (!$this->email->send()) {
            throw new \Exception($this->email->printDebugger(['headers']));
        }

        return true;
    }

    public function sendEmail($userId, $password)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return ['status' => true, 'message' => 'Mail sending is disabled in SMTP settings.'];
        }

        $userModel = new UserModel();
        $userInfoModel = new UserInfoModel();
        $companyModel = new CompanyLogoModel();

        // Fetch user details
        $user = $userModel->where('id', $userId)->first();
        if (!$user) {
            return ['status' => false, 'message' => 'User not found'];
        }

        // Fetch job position (join with designation table)
        $userInfo = $userInfoModel
            ->select('designation.designation_name')
            ->join('designation', 'designation.id = user_info.designation_id', 'left')
            ->where('user_info.user_id', $userId)
            ->first();

        $jobPosition = $userInfo['designation_name'] ?? 'Employee'; // Default to 'Employee' if not found

        // Fetch company details
        $company = $companyModel->first();
        if (!$company) {
            return ['status' => false, 'message' => 'Company details not found'];
        }

        // Ensure logo exists, otherwise use default
        $companyLogo = base_url('upload/' . (!empty($company['logo_img']) ? $company['logo_img'] : 'fab_logo.jpg'));

        // Prepare email data
        $data = [
            'company_logo' => $companyLogo, // Ensure correct variable
            'employee_name' => $user['username'],
            'sender_name' => 'HR Team',
            'job_position' => $jobPosition,
            'company_name' => $company['company_name'] ?? 'Our Company',
            'company_mission' => 'To provide the best workplace environment.',
            'employee_email' => $user['email'],
            'generated_password' => $password,
            'portal_url' => base_url('/login'),
            'year' => date('Y'),
        ];

        log_message('debug', 'Email Data: ' . print_r($data, true)); // Debugging

        // Load email template
        $message = view('mail/welcome_mail', $data);

        // Send email
        $this->email->setTo($user['email']);
        $this->email->setSubject("Welcome to " . $data['company_name']);
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return ['status' => true, 'message' => 'Welcome email sent successfully'];
        } else {
            return ['status' => false, 'message' => $this->email->printDebugger()];
        }
    }
    public function sendCandidateWelcomeEmail($candidateData)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return false;
        }

        $jobModel = new \App\Models\JobModel();
        $companyModel = new \App\Models\CompanyLogoModel();

        // Fetch job details
        $job = $jobModel->find($candidateData['job_id'] ?? null);
        $jobTitle = $job['job_title'] ?? 'Not Specified';

        // Fetch company details
        $company = $companyModel->first(); // Assuming only one company logo record exists
        $companyName = $company['company_name'] ?? 'Our Company';
        $companyLogo = base_url(!empty($company['logo_img']) ? 'upload/' . $company['logo_img'] : 'upload/fab_logo.jpg');
        $companyWebsite = base_url(); // Your website URL

        // Ensure resume exists
        $resumeLink = isset($candidateData['resume']) && !empty($candidateData['resume'])
            ? base_url($candidateData['resume'])
            : 'Not Uploaded';

        // Load email template
        $emailTemplate = file_get_contents(APPPATH . 'Views/mail/candidate_mail.php');

        // Replace placeholders with actual data
        $emailBody = str_replace(
            ['{{company_logo}}', '{{candidate_name}}', '{{job_title}}', '{{company_name}}', '{{email}}', '{{company_website}}', '{{phone_number}}', '{{resume}}'],
            [
                $companyLogo,
                $candidateData['candidate_name'] ?? 'Candidate',
                $jobTitle,
                $companyName,
                $candidateData['email'] ?? 'Not Provided',
                $companyWebsite,
                $candidateData['phone_number'] ?? 'Not Available',
                $resumeLink
            ],
            $emailTemplate
        );

        // Send Email
        $this->email->setTo($candidateData['email'] ?? '');
        $this->email->setSubject("Welcome to $companyName - Job Application Received");
        $this->email->setMessage($emailBody);

        return $this->email->send();
    }

    public function sendInterviewEmail($candidateData)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return false;
        }

        $jobModel = new \App\Models\JobModel();
        $companyModel = new \App\Models\CompanyLogoModel();
        $interviewModel = new \App\Models\InterviewModel();
        $candidateModel = new \App\Models\CandidateModel();

        // Fetch interview details
        $interview = $interviewModel->where('candidate_id', $candidateData['candidate_id'])->first();
        if (!$interview) {
            return false; // No interview scheduled
        }

        // Fetch candidate details
        $candidate = $candidateModel->find($interview['candidate_id']);
        $candidateName = $candidate ? $candidate['candidate_name'] : 'Candidate';

        // Fetch job details
        $job = $jobModel->find($candidate['job_id']);
        $jobTitle = $job ? $job['job_title'] : 'Not Specified';

        // Fetch company details
        $company = $companyModel->first(); // Assuming one company logo exists
        $companyName = $company ? $company['company_name'] : 'Our Company';
        $companyLogo = base_url($company ? 'upload/' . $company['logo_img'] : 'upload/fab_logo.jpg');
        $companyWebsite = base_url(); // Your website URL
        $companyEmail = "contact@company.com"; // Replace with actual data
        $companyPhone = "+123456789"; // Replace with actual data

        // Format interview schedule
        $scheduleDate = date('d M Y', strtotime($interview['schedule_date']));
        $scheduleTime = date('H:i A', strtotime($interview['schedule_date']));
        $interviewLocation = "Online (Google Meet)"; // Replace with actual value if applicable

        // Load email template
        $emailTemplate = file_get_contents(APPPATH . 'Views/mail/interview_mail.php');

        // Replace placeholders with actual data
        $emailBody = str_replace(
            ['{{company_logo}}', '{{candidate_name}}', '{{job_title}}', '{{company_name}}', '{{schedule_date}}', '{{schedule_time}}', '{{interview_location}}', '{{company_email}}', '{{company_phone}}'],
            [
                $companyLogo,
                $candidateName,
                $jobTitle,
                $companyName,
                $scheduleDate,
                $scheduleTime,
                $interviewLocation,
                $companyEmail,
                $companyPhone
            ],
            $emailTemplate
        );

        // Send Email
        $this->email->setTo($candidate['email']);
        $this->email->setSubject("Interview Invitation - $companyName");
        $this->email->setMessage($emailBody);

        return $this->email->send();
    }
    public function sendOnboardingEmail($candidateId)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return false;
        }

        $candidateModel = new \App\Models\CandidateModel();
        $jobModel = new \App\Models\JobModel();
        $onboardingModel = new \App\Models\OnboardingModel();
        $companyModel = new \App\Models\CompanyLogoModel();
        $departmentModel = new \App\Models\DepartmentModel();

        // Ensure $candidateId is an integer
        if (is_array($candidateId) && isset($candidateId['candidate_id'])) {
            $candidateId = (int) $candidateId['candidate_id'];
        } else {
            $candidateId = (int) $candidateId;
        }

        // Fetch candidate details
        $candidate = $candidateModel->find($candidateId);
        if (!$candidate) {
            return false; // Candidate not found
        }

        // Fetch job details
        $jobTitle = 'Not Specified';
        if (!empty($candidate['job_id'])) {
            $job = $jobModel->find($candidate['job_id']);
            $jobTitle = $job['job_title'] ?? 'Not Specified';
        }

        // Fetch onboarding details
        $onboarding = $onboardingModel->where('candidate_id', $candidateId)->first();
        if (!$onboarding) {
            return false; // Onboarding details not found
        }

        // Fetch department name using department_id
        $departmentName = 'Not Provided';
        if (!empty($onboarding['department_id'])) {
            $department = $departmentModel->find($onboarding['department_id']);
            $departmentName = $department['department_name'] ?? 'Not Provided';
        }
        // Fetch company details
        $company = $companyModel->first(); // Assuming only one company exists
        $companyName = $company['company_name'] ?? 'Our Company';
        $companyLogo = base_url(!empty($company['logo_img']) ? 'upload/' . $company['logo_img'] : 'upload/fab_logo.jpg');
        $companyWebsite = $company['website'] ?? 'Not Provided';
        $phoneNumber = $company['phone_number'] ?? 'Not Provided';

        // HR Contact Details (Hardcoded or fetched from DB if needed)
        $hrContact = 'HR Department';
        $hrEmail = 'hr@company.com';
        $yourName = 'Your Name';

        // Load email template
        $emailTemplate = file_get_contents(APPPATH . 'Views/mail/onboarding_mail.php');

        // Replace placeholders with actual data
        $search = [
            '{{company_logo}}',
            '{{candidate_name}}',
            '{{job_title}}',
            '{{company_name}}',
            '{{email}}',
            '{{company_website}}',
            '{{phone_number}}',
            '{{resume}}',
            '{{department}}',
            '{{job_role}}',
            '{{start_date}}',
            '{{onboarding_status}}',
            '{{bank_name}}',
            '{{acc_number}}',
            '{{docu_submitted}}'
        ];

        $replace = [
            $companyLogo,
            $candidate['candidate_name'] ?? 'Candidate',
            $jobTitle,
            $companyName,
            $candidate['email'] ?? 'Not Provided',
            $companyWebsite,
            $phoneNumber,
            'Not Available', // Assuming resume link is not available
            // $onboarding['department_id'] ?? 'Not Provided',
            $departmentName,
            $jobTitle,
            $onboarding['start_date'] ?? 'Not Provided',
            ucfirst($onboarding['onboarding_status'] ?? 'Pending'),
            $onboarding['bank_name'] ?? 'Not Provided',
            $onboarding['acc_number'] ?? 'Not Provided',
            // ucfirst($onboarding['docu_submitted'] ?? 'Not Provided'),
            // $onboarding['created_at'] ?? 'Not Provided',
            // $onboarding['created_by'] ?? 'Not Provided',
            // $hrContact,
            // $hrEmail,
            // $yourName
        ];

        $emailBody = str_replace($search, $replace, $emailTemplate);

        // Send Email
        $this->email->setTo($candidate['email']);
        $this->email->setSubject("Welcome to $companyName - Your Onboarding Details");
        $this->email->setMessage($emailBody);

        return $this->email->send();
    }
    public function sendPerformanceEmail($performanceId)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return "Mail sending is disabled in SMTP settings.";
        }

        $performanceModel = new \App\Models\PerformanceModel();
        $userModel = new UserModel();
        $userInfoModel = new UserInfoModel();

        // Fetch performance review details
        $performance = $performanceModel->find($performanceId);
        if (!$performance) {
            return "Performance record not found!";
        }
        
        // Fetch employee details
        // Fetch employee details with designation name
        $employee = $userInfoModel
            ->select('user_info.firstname, user_info.lastname, designation.designation_name, users.email')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->join('designation', 'designation.id = user_info.designation_id', 'left')
            ->where('user_info.user_id', $performance['user_id'])
            ->first();

        // Fetch reviewer details
        $reviewer = $userInfoModel
            ->select('user_info.firstname, user_info.lastname')
            ->where('user_info.user_id', $performance['reviewer_id'])
            ->first();

        if (!$employee || !$reviewer) {
            return "Employee or Reviewer not found!";
        }

        // Prepare data for the email template
        $emailData = [
            'employee_name'     => $employee['firstname'] . ' ' . $employee['lastname'],
            'review_date'       => date('F d, Y', strtotime($performance['review_date'])),
            'reviewer_name'     => $reviewer['firstname'] . ' ' . $reviewer['lastname'],
            'designation'       => $employee['designation_name'],  // ✅ Fetch designation name instead of ID
            'goals_achieved'    => $performance['goals_achieved'],
            'team_work'         => $performance['team_work'],
            'management'        => $performance['management'],
            'presentation_skill' => $performance['presentation_skill'],
            'behaviour'         => $performance['behaviour'],
            'rating'            => $performance['rating'],
            'notes'             => nl2br($performance['notes']),
            'feedback_link'     => base_url('/feedback?user_id=' . $performance['user_id'])
        ];

        // Load the email template
        $message = view('mail/performance_mail', $emailData);

        // Send Email (Using already configured `$this->email`)
        $this->email->setTo($employee['email']);

        $this->email->setSubject('Employee Performance Review');
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return "Performance review email sent successfully!";
        } else {
            return "Error sending email: " . $this->email->printDebugger(['headers']);
        }
    }
    public function sendTrainingEmail($trainingId)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return "Mail sending is disabled in SMTP settings.";
        }

        $trainingModel = new \App\Models\TrainingModel();
        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();
        $companyLogoModel = new \App\Models\CompanyLogoModel();  // Load CompanyLogoModel

        // Fetch training details
        $training = $trainingModel->find($trainingId);
        if (!$training) {
            return "Training record not found!";
        }

        // Fetch employee details
        $employee = $userInfoModel
            ->select('user_info.firstname, user_info.lastname, users.email, department.department_name')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->join('department', 'department.id = user_info.department_id', 'left')
            ->where('user_info.user_id', $training['user_id'])
            ->first();

        if (!$employee) {
            return "Employee not found!";
        }

        // Fetch company name from the database
        $companyLogo = $companyLogoModel->first();  // Assuming only one record for company logo
        $companyName = $companyLogo ? $companyLogo['company_name'] : 'Your Company Name';  // Default name if not found

        // Prepare data for the email template
        $emailData = [
            'company_name'  => $companyName,
            'company_logo'  => base_url('upload/' . ($companyLogo['logo_img'] ?? 'fab_logo.jpg')), // Use default logo if none exists
            'employee_name' => $employee['firstname'] . ' ' . $employee['lastname'],
            'training_title' => $training['training_title'],
            'description'   => $training['description'],
            'department_name' => $employee['department_name'],
            'start_date'    => date('F d, Y', strtotime($training['start_date'])),
            'end_date'      => date('F d, Y', strtotime($training['end_date'])),
            'location'      => $training['location'],
            'training_url'  => base_url('/training/details/' . $trainingId)
        ];

        // Load the email template
        $message = view('mail/training_mail', $emailData);

        // Send Email (Using already configured `$this->email`)
        $this->email->setTo($employee['email']);
        $this->email->setSubject('Training Invitation');
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return "Training email sent successfully!";
        } else {
            return "Error sending email: " . $this->email->printDebugger(['headers']);
        }
    }
    public function sendPayrollEmail($payrollId)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return "Mail sending is disabled in SMTP settings.";
        }

        $payrollModel = new \App\Models\PayrollModel();
        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();
        $companyLogoModel = new \App\Models\CompanyLogoModel();  // Load CompanyLogoModel

        // Fetch payroll details
        $payroll = $payrollModel->find($payrollId);
        if (!$payroll) {
            return "Payroll record not found!";
        }

        // Fetch employee details
        $employee = $userInfoModel
            ->select('user_info.firstname, user_info.lastname, users.email, department.department_name')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->join('department', 'department.id = user_info.department_id', 'left')
            ->where('user_info.user_id', $payroll['user_id'])
            ->first();

        if (!$employee) {
            return "Employee not found!";
        }

        // Fetch company name and logo from the database
        $companyLogo = $companyLogoModel->first();  // Assuming only one record for company logo
        $companyName = $companyLogo ? $companyLogo['company_name'] : 'Your Company Name';  // Default name if not found
        $companyLogoPath = $companyLogo ? base_url('upload/' . $companyLogo['logo_img']) : base_url('upload/fab_logo.jpg');

        // Prepare data for the email template
        // $emailData = [
        //     'company_name'  => $companyName,
        //     'company_logo'  => $companyLogoPath,
        //     'employee_name' => $employee['firstname'] . ' ' . $employee['lastname'],
        //     'salary_amount' => $payroll['salary_amount'],
        //     'tax_deduction' => $payroll['tax_deduction'],
        //     'bonuses'       => $payroll['bonuses'],
        //     'net_salary'    => $payroll['net_salary'],
        //     'payment_date'  => date('F d, Y', strtotime($payroll['payment_date'])),
        //     'payment_status' => $payroll['payment_status']
        // ];
        $emailData = [
            'company_name'          => $companyName,
            'company_logo'          => $companyLogoPath,
            'employee_name'         => $employee['firstname'] . ' ' . $employee['lastname'],
            'month_year'            => $payroll['month_year'],
            'salary_amount'         => $payroll['salary_amount'],
            'total_leaves'          => $payroll['total_leaves'] ?? 0,
            'total_half_day'        => $payroll['total_half_day'] ?? 0,
            'worked_hours'          => $payroll['worked_hours'] ?? 0,
            'total_overtime_hours'  => $payroll['total_overtime_hours'] ?? 0,
            'overtime_pay'          => $payroll['overtime_pay'] ?? 0,
            'salary_deduction'      => $payroll['salary_deduction'] ?? 0,
            'tax_deduction'         => $payroll['tax_deduction'] ?? 0,
            'bonuses'               => $payroll['bonuses'] ?? 0,
            'net_salary'            => $payroll['net_salary'],
            'payment_date'          => date('F d, Y', strtotime($payroll['payment_date'])),
            'payment_status'         => $payroll['payment_status']
        ];

        // Load the email template
        $message = view('mail/payroll_mail', $emailData);

        // Send Email (Using already configured `$this->email`)
        $this->email->setTo($employee['email']);
        $this->email->setSubject('Payroll Details');
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return "Payroll email sent successfully!";
        } else {
            return "Error sending email: " . $this->email->printDebugger(['headers']);
        }
    }
    public function sendTaskEmail($taskId)
    {
        // Check if email is enabled
        if (!$this->isEmailEnabled()) {
            return "Mail sending is disabled in SMTP settings.";
        }

        // Load necessary models
        $taskModel = new \App\Models\TaskModel();
        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();
        $companyLogoModel = new \App\Models\CompanyLogoModel();

        // Fetch task details
        $task = $taskModel->find($taskId);
        if (!$task) {
            return "Task record not found!";
        }

        // Fetch employee details
        $employee = $userInfoModel
            ->select('user_info.firstname, user_info.lastname, users.email, department.department_name')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->join('department', 'department.id = user_info.department_id', 'left')
            ->where('user_info.user_id', $task['user_id'])
            ->first();

        if (!$employee) {
            return "Employee not found!";
        }

        // Fetch company logo and name
        $companyLogo = $companyLogoModel->first();
        $companyName = $companyLogo ? $companyLogo['company_name'] : 'Your Company Name';
        $companyLogoPath = $companyLogo ? base_url('upload/' . $companyLogo['logo_img']) : base_url('upload/fab_logo.jpg');

        // Prepare data for email template
        $emailData = [
            'company_name'   => $companyName,
            'company_logo'   => $companyLogoPath,
            'employee_name'  => $employee['firstname'] . ' ' . $employee['lastname'],
            'task_title'     => $task['task_title'],
            'assigned_by'    => $task['created_by'],
            'department'     => $employee['department_name'],
            'due_date'       => $task['due_date'],
            'description'    => $task['description'],
            'task_status'    => $task['task_status']
        ];

        // Load the email template
        $message = view('mail/task_mail', $emailData);

        // Send email using the CodeIgniter email service
        $this->email->setTo($employee['email']);
        $this->email->setSubject('New Task Assigned: ' . $task['task_title']);
        $this->email->setMessage($message);

        if ($this->email->send()) {
            return "Task assignment email sent successfully!";
        } else {
            return "Error sending email: " . $this->email->printDebugger(['headers']);
        }
    }
}
