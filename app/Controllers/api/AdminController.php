<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Controller;
use App\Services\AuthService;
use App\Models\UserModel;
use App\Models\LeaveModel;
use App\Models\AttendanceModel;
use App\Models\TaskModel;
use App\Models\UserInfoModel;
use App\Models\PerformanceModel;
use App\Models\OnboardingModel;
use App\Models\InterviewModel;
use App\Models\CandidateModel;
use App\Models\HolidayCalendarModel;
use App\Models\CompanyRulesModel;
use App\Models\StateModel;

class AdminController extends ResourceController
{
    protected $authService;
    protected $userModel;
    protected $leaveModel;
    protected $attendanceModel;
    protected $taskModel;
    protected $userInfoModel;
    protected $performanceModel;
    protected $onboardingModel;
    protected $interviewModel;
    protected $candidateModel;
    protected $stateModel;

    public function __construct()

    {
        $this->authService = new AuthService(service('request'));
        $this->userModel = new UserModel();
        $this->leaveModel = new LeaveModel();
        $this->attendanceModel = new AttendanceModel();
        $this->taskModel = new TaskModel();
        $this->userInfoModel = new UserInfoModel();  // Add the new model
        $this->performanceModel = new PerformanceModel();
        $this->onboardingModel = new OnboardingModel();
        $this->stateModel = new StateModel();
        $this->interviewModel = new InterviewModel();
        $this->candidateModel = new CandidateModel();
    }

    public function profile()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }
        $user = $this->authService->user(); // Get logged-in user
        $role = $user->role; // User role
        $cityModel = new \App\Models\CityModel();
        $countryModel = new \App\Models\CountryModel();
        $departmentModel = new \App\Models\DepartmentModel();
        $designationModel = new \App\Models\DesignationModel();
        $stateModel = new \App\Models\StateModel();

        $cities = $cityModel->findAll();

        $countries = $countryModel->findAll();
        $states = $stateModel->findAll();

        $departments = $departmentModel->findAll();

        $designations = $designationModel->findAll();

        return view('dashboard/profile', [
            'cities' => $cities,
            'countries' => $countries,
            'departments' => $departments,
            'designations' => $designations,
            'states' => $states,
            'role' => $role
        ]);
    }

    public function index()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }
        $user = $this->authService->user(); // Get logged-in user
        $role = $user->role; // User role

        // Fetch user info from the users table
        $users = $this->userModel->where('id', $user->sub)->first();

        $userInfo = $this->userInfoModel->where('user_id', $user->sub)->first();

        if (!$users) {
            // Default to a guest user
            $users = [
                'username' => 'GuestUser', // Default username
            ];
        }
        // Store the user info in session
        session()->set([
            'userInfo' => $userInfo,
            'role' => $role,
            'username' => $users['username'], // Store username directly
        ]);

        if ($role == 'admin' || $role == 'hr') {
            $candidates = $this->candidateModel
                ->select('candidate.id,candidate.candidate_name, candidate.email, candidate.phone_number, candidate.status,jobs.job_title')
                ->join('jobs', 'jobs.id = candidate.job_id', 'left')
                ->join('onboarding', 'onboarding.candidate_id = candidate.id', 'left') // Left join to include candidates without onboarding records
                ->where('(onboarding.onboarding_status IS NULL OR onboarding.onboarding_status != "completed")') // Exclude completed onboarding
                ->orderBy('candidate.created_at', 'DESC')
                ->findAll();
        } else {
            $candidates = $this->candidateModel
                ->select('candidate.id,candidate.candidate_name,candidate.email,candidate.phone_number,candidate.status,jobs.job_title')
                ->join('jobs', 'jobs.id = candidate.job_id', 'left')
                ->join('onboarding', 'onboarding.candidate_id = candidate.id', 'left') // Left join to include candidates without onboarding records
                ->where('(onboarding.onboarding_status IS NULL OR onboarding.onboarding_status != "completed")') // Exclude completed onboarding
                ->orderBy('candidate.created_at', 'DESC')
                ->findAll();
        }
        // Fetch latest 5 employees with designation and department
        $currentMonth = date('m');
        $currentYear = date('Y');
        if ($role == 'admin' || $role == 'hr') {
            $employees = $this->userInfoModel
                ->select('user_info.*, designation.designation_name, department.department_name')
                ->join('designation', 'designation.id = user_info.designation_id', 'left')
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->where('user_info.role', 'employee')
                ->where('MONTH(user_info.joining_date)', $currentMonth)
                ->where('YEAR(user_info.joining_date)', $currentYear)
                ->orderBy('user_info.joining_date', 'DESC')
                ->limit(5)
                ->findAll();
        } else {
            $employees = $this->userInfoModel
                ->select('user_info.*, designation.designation_name, department.department_name')
                ->join('designation', 'designation.id = user_info.designation_id', 'left')
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->where('user_info.user_id', $user->sub)
                ->where('user_info.role', 'employee')
                ->where('MONTH(user_info.joining_date)', $currentMonth)
                ->where('YEAR(user_info.joining_date)', $currentYear)
                ->orderBy('user_info.joining_date', 'DESC')
                ->limit(5)
                ->findAll();
        }
        
        $this->triggerAutoLeaveOnceDaily();

        $todayCheckinCheckoutHistory = $this->attendanceModel
            ->where('DATE(date)', date('Y-m-d'))
            ->where('user_id', $user->sub)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Calculate today's hours worked and remaining hours for employees
        $todayHoursData = null;
        if ($role == 'employee') {
            $todayHoursData = $this->calculateTodayHours($user->sub);
        }

        // Fetch active announcements
        $announcementModel = new \App\Models\AnnouncementModel();
        $activeAnnouncements = $announcementModel->getActiveAnnouncements($user->sub, $role, 5);

        return view('dashboard/dashboard', [
            'role' => $role,
            'employees' => $employees,
            'userInfo' => $userInfo,
            'candidates' => $candidates ?? 0,
            'todayCheckinCheckoutHistory' => $todayCheckinCheckoutHistory,
            'todayHoursData' => $todayHoursData,
            'activeAnnouncements' => $activeAnnouncements
        ]);
    }

    public function DashboardData()
    {
        if (!$this->authService->check()) {
            return redirect()->to('/login');
        }

        $user = $this->authService->user(); // Get logged-in user
        $role = $user->role;
        $userId = $user->sub;
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        $today = date('Y-m-d');
        $startOfMonth = date('Y-m-01'); // 1st of current month
        $endOfMonth = date('Y-m-t');    // Last day of current month
        $startOfYear = date('Y-01-01'); // 1st Jan this year
        $endOfYear = date('Y-m-d'); // today
        // Only admin and HR can see new employees this week
        if (in_array($role, ['admin', 'hr'])) {
            $totalThisWeekEmployees = $this->userModel
                ->where('role', 'employee')
                ->where('DATE(created_at) >=', $startOfWeek)
                ->where('DATE(created_at) <=', $today)
                ->where('is_deleted', 0)
                ->countAllResults();
            $startOfMonth = date('Y-m-01'); // 1st of current month
            $endOfMonth = date('Y-m-t');    // Last day of current month
            $totalEmployeesThisMonth = $this->userModel
                ->where('role', 'employee')
                ->where('DATE(created_at) >=', $startOfMonth)
                ->where('DATE(created_at) <=', $endOfMonth)
                ->where('is_deleted', 0)
                ->countAllResults();
            $totalEmployeesThisYear = $this->userModel
                ->where('role', 'employee')
                ->where('YEAR(created_at)', date('Y')) // Filters based on the current year (e.g., 2025)
                ->where('is_deleted', 0)
                ->countAllResults();
        }

        $todayDate = date('Y-m-d');
        $totalLeavesToday = $this->leaveModel
            ->select('leaves.*, users.username, user_info.profile_image')
            ->join('users', 'users.id = leaves.user_id')
            ->join('user_info', 'user_info.user_id = users.id')
            ->where('start_date <=', $todayDate)
            ->where('end_date >=', $todayDate)
            ->where('leaves.status', 'approved') // ✅ Fully qualified
            ->findAll();
        $todayAttendance = $this->attendanceModel
            ->select('attendance.check_in_time, attendance.check_out_time, users.username, user_info.profile_image')
            ->join('users', 'users.id = attendance.user_id', 'inner')
            ->join('user_info', 'user_info.user_id = users.id', 'left')
            ->where('attendance.date', $todayDate)
            ->where('users.is_deleted', 0)
            ->groupBy('attendance.user_id')
            ->orderBy('attendance.check_in_time', 'ASC')
            ->findAll();
        // Leave count for all roles
        if (in_array($role, ['admin', 'hr'])) {
            $totalLeavesThisWeek = $this->leaveModel
                ->where('start_date >=', $startOfWeek)
                ->where('start_date <=', $endOfWeek)
                ->countAllResults();
            $totalLeavesThisYear = $this->leaveModel
                ->where('YEAR(created_at)', date('Y')) // Filter by current year
                ->countAllResults();
            $attendanceCountThisWeek = $this->attendanceModel
                ->select('user_id')
                ->where('date >=', $startOfWeek)
                ->where('date <=', $endOfWeek)
                ->groupBy('user_id')
                ->countAllResults();
            $totalTasksThisWeek = $this->taskModel
                ->where('assigned_date >=', $startOfWeek)
                ->where('assigned_date <=', $endOfWeek)
                ->countAllResults();
            $totalLeaves = $this->leaveModel
                ->where('start_date >=', $startOfMonth)
                ->where('start_date <=', $endOfMonth)
                ->countAllResults();
            $attendanceCountThisMonth = $this->attendanceModel
                ->select('user_id')
                ->where('date >=', $startOfMonth)
                ->where('date <=', $endOfMonth)
                ->groupBy('user_id')
                ->countAllResults();
            // HR/Admin: count unique employees who marked attendance this year
            $attendanceCountThisYear = $this->attendanceModel
                ->select('user_id')
                ->where('YEAR(date)', date('Y'))
                ->groupBy('user_id')
                ->countAllResults();
            $totalTasksThisMonth = $this->taskModel
                ->where('assigned_date >=', $startOfMonth)
                ->where('assigned_date <=', $endOfMonth)
                ->countAllResults();
            $totalTasksThisYear = $this->taskModel
                ->where('assigned_date >=', $startOfYear)
                ->where('assigned_date <=', $endOfYear)
                ->countAllResults();
            $remoteEmployees = $this->userInfoModel->where('working_location', 'remote')->countAllResults();
            $onSiteEmployees = $this->userInfoModel->where('working_location', 'on-site')->countAllResults();
            $workingFormatTotal = $remoteEmployees + $onSiteEmployees;
            $remotePercentage = 0;
            $onSitePercentage = 0;
            if ($workingFormatTotal > 0) {
                $remotePercentage = $remoteEmployees / $workingFormatTotal;
                $onSitePercentage = 1 - $remotePercentage;
            }
        } else {
            $employeeId = $user->sub; // Get the logged-in employee's ID
            $startOfYear = date('Y-01-01'); // 1st January this year
            $todayDate = date('Y-m-d');  // Define todayDate for employee
            $totalLeavesThisWeek = $this->leaveModel
                ->where('user_id', $userId)
                ->where('start_date >=', $startOfWeek)
                ->where('start_date <=', $endOfWeek)
                ->countAllResults();
            // Total leaves this year
            $totalLeavesThisYear = $this->leaveModel
                ->where('user_id', $employeeId) // Only logged-in employee's leaves
                ->where('YEAR(created_at)', date('Y')) // start_date is inside current year
                ->countAllResults();
            $todayDate = date('Y-m-d');
            $totalLeavesToday = $this->leaveModel
                ->select('leaves.*, users.username, user_info.profile_image')
                ->join('users', 'users.id = leaves.user_id')
                ->join('user_info', 'user_info.user_id = users.id')
                ->where('start_date <=', $todayDate)
                ->where('end_date >=', $todayDate)
                ->where('leaves.status', 'approved') // ✅ Fully qualified
                ->findAll();
            $todayAttendance = $this->attendanceModel
                    ->select('attendance.check_in_time, users.username, user_info.profile_image')
                    ->join('users', 'users.id = attendance.user_id', 'inner')
                    ->join('user_info', 'user_info.user_id = users.id', 'left')
                    ->where('attendance.date', $todayDate)
                    ->where('users.is_deleted', 0)
                    ->groupBy('attendance.user_id')
                    ->orderBy('attendance.check_in_time', 'ASC')
                    ->findAll();
            $attendanceCountThisWeek = $this->attendanceModel
                ->select('user_id')
                ->where('user_id', $employeeId)
                ->where('date >=', $startOfWeek)
                ->where('date <=', $endOfWeek)
                ->groupBy('user_id')
                ->countAllResults();
            $totalTasksThisWeek = $this->taskModel->where('user_id', $employeeId)
                ->where('assigned_date >=', $startOfWeek)
                ->where('assigned_date <=', $endOfWeek)
                ->countAllResults();

            $totalLeaves = $this->leaveModel
                ->where('user_id', $employeeId)
                ->where('start_date >=', $startOfMonth)
                ->where('start_date <=', $endOfMonth)
                ->countAllResults();

            $attendanceCountThisMonth = $this->attendanceModel
                ->where('user_id', $employeeId)
                ->where('date >=', $startOfMonth)
                ->where('date <=', $endOfMonth)
                ->groupBy('user_id')
                ->countAllResults();
            $attendanceCountThisYear = $this->attendanceModel
                ->where('user_id', $employeeId)
                ->where('YEAR(date)', date('Y')) // Filter by current year
                ->countAllResults();
            $totalTasksThisMonth = $this->taskModel
                ->where('user_id', $employeeId)
                ->where('assigned_date >=', $startOfMonth)
                ->where('assigned_date <=', $endOfMonth)
                ->countAllResults();
            $totalTasksThisYear = $this->taskModel
                ->where('user_id', $employeeId)
                ->where('assigned_date >=', $startOfYear)
                ->where('assigned_date <=', $endOfYear)
                ->countAllResults();
        }

        if ($role == 'admin' || $role == 'hr') {
            $departmentData = $this->userInfoModel
                ->select("department.department_name, COUNT(user_info.id) as employee_count")
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->join('users', 'users.id = user_info.user_id', 'inner')
                ->where('users.is_deleted', 0)
                ->where('department.department_name IS NOT NULL') // Remove unassigned
                ->groupBy('department.department_name')
                ->orderBy('department.id', 'DESC') // Sort by latest departments
                ->limit(4) // Get only latest 6 departments
                ->findAll();
        } else {

            $departmentData = $this->userInfoModel
                ->select("department.department_name, COUNT(user_info.id) as employee_count")
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->join('users', 'users.id = user_info.user_id', 'inner')
                ->where('users.is_deleted', 0)
                ->where('department.department_name IS NOT NULL') // Remove unassigned
                ->where('user_info.user_id', $user->sub)
                ->groupBy('department.department_name')
                ->orderBy('department.id', 'DESC') // Sort by latest departments
                ->limit(4) // Get only latest 6 departments
                ->findAll();
        }
        // Determine if there is any data available
        $hasData = !empty($departmentData);
        // Prepare data for JavaScript
        $departmentLabels = [];
        $employeeCounts = [];
        foreach ($departmentData as $data) {
            $departmentLabels[] = $data['department_name'];
            $employeeCounts[] = (int) $data['employee_count'];
        }
        // Fetch today's date for comparison
        $today = date('m-d');
        
        // $birthdayUsers = $this->userInfoModel->where('DATE_FORMAT(date_of_birth, "%m-%d")', $today)->findAll();
        $birthdayUsers = $this->userInfoModel->select('user_info.*')
                    ->join('users', 'users.id = user_info.user_id', 'inner')
                    ->where('users.is_deleted', 0)
                    ->where('DATE_FORMAT(user_info.date_of_birth, "%m-%d")', $today)
                    ->findAll();

        $currentMonth = date('m');
        $currentYear = date('Y');
        if ($role == 'admin' || $role == 'hr') {
            $employees = $this->userInfoModel
                ->select('user_info.*, designation.designation_name, department.department_name')
                ->join('designation', 'designation.id = user_info.designation_id', 'left')
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->join('users', 'users.id = user_info.user_id', 'inner')
                ->where('users.is_deleted', 0)
                ->where('user_info.role', 'employee')
                ->where('MONTH(user_info.joining_date)', $currentMonth)
                ->where('YEAR(user_info.joining_date)', $currentYear)
                ->orderBy('user_info.joining_date', 'DESC')
                ->limit(5)
                ->findAll();
        } else {
            $employees = $this->userInfoModel
                ->select('user_info.*, designation.designation_name, department.department_name')
                ->join('designation', 'designation.id = user_info.designation_id', 'left')
                ->join('department', 'department.id = user_info.department_id', 'left')
                ->join('users', 'users.id = user_info.user_id', 'inner')
                ->where('users.is_deleted', 0)
                ->where('user_info.user_id', $user->sub)
                ->where('user_info.role', 'employee')
                ->where('MONTH(user_info.joining_date)', $currentMonth)
                ->where('YEAR(user_info.joining_date)', $currentYear)
                ->orderBy('user_info.joining_date', 'DESC')
                ->limit(5)
                ->findAll();
        }
        // Fetch the count of completed and scheduled interviews
        if ($role == 'admin' || $role == 'hr') {
            $completedInterviews = $this->interviewModel->where('status', 'completed')->countAllResults();
            $scheduledInterviews = $this->interviewModel->where('status', 'scheduled')->countAllResults();
            // Total interviews (prevent division by zero)
            $totalInterviews = $completedInterviews + $scheduledInterviews;
        } else {
            $completedInterviews = 0;
            $scheduledInterviews = 0;
            $totalInterviews = 0;
        }

        if ($role == 'admin' || $role == 'hr') {
            $candidates = $this->candidateModel
                ->select('candidate.candidate_name, candidate.email, candidate.phone_number, candidate.status,jobs.job_title')
                ->join('jobs', 'jobs.id = candidate.job_id', 'left')
                ->join('onboarding', 'onboarding.candidate_id = candidate.id', 'left') // Left join to include candidates without onboarding records
                ->where('(onboarding.onboarding_status IS NULL OR onboarding.onboarding_status != "completed")') // Exclude completed onboarding
                ->orderBy('candidate.created_at', 'DESC')
                ->findAll();
        } else {
            $candidates = $this->candidateModel
                ->select('candidate.candidate_name,candidate.email,candidate.phone_number,candidate.status,jobs.job_title')
                ->join('jobs', 'jobs.id = candidate.job_id', 'left')
                ->join('onboarding', 'onboarding.candidate_id = candidate.id', 'left') // Left join to include candidates without onboarding records
                ->where('(onboarding.onboarding_status IS NULL OR onboarding.onboarding_status != "completed")') // Exclude completed onboarding
                ->orderBy('candidate.created_at', 'DESC')
                ->findAll();
        }
        // Check if data exists
        $hasInterviewData = ($totalInterviews > 0);
        return $this->response->setJSON([
            'totalThisWeekEmployees' => $totalThisWeekEmployees ?? 0,
            'totalLeavesThisWeek' => $totalLeavesThisWeek ?? 0,
            'attendanceCountThisWeek' => $attendanceCountThisWeek ?? 0,
            'totalTasksThisWeek' => $totalTasksThisWeek ?? 0,
            'totalEmployeesThisMonth' => $totalEmployeesThisMonth ?? 0,
            'totalLeaves' => $totalLeaves ?? 0,
            'attendanceCountThisMonth' => $attendanceCountThisMonth ?? 0,
            'totalTasksThisMonth' => $totalTasksThisMonth ?? 0,
            'totalEmployeesThisYear' => $totalEmployeesThisYear ?? 0,
            'totalLeavesThisYear' => $totalLeavesThisYear ?? 0,
            'totalLeavesToday' => !empty($totalLeavesToday) ? $totalLeavesToday : [],
            'todayAttendance' => !empty($todayAttendance) ? $todayAttendance : [],
            'attendanceCountThisYear' => $attendanceCountThisYear ?? 0,
            'totalTasksThisYear' => $totalTasksThisYear ?? 0,
            'departmentLabels' => $departmentLabels,
            'employeeCounts' => $employeeCounts, // ✅ add this line
            'workingFormatTotal' => $workingFormatTotal ?? 0,
            'remotePercentage' => $remotePercentage ?? 0,
            'onSitePercentage' => $onSitePercentage ?? 0,
            'birthdayUsers' => !empty($birthdayUsers) ? $birthdayUsers : [],
            'employees' => $employees,
            'completedInterviews' => $completedInterviews,
            'scheduledInterviews' => $scheduledInterviews,
            'hasInterviewData' => $hasInterviewData,
            'totalInterviews' => $totalInterviews,
            'candidates' => $candidates ?? 0,
        ]);
    }

    public function getYearlyPerformanceData()
    {
        $user = $this->authService->user();
        $role = $user->role;
        $employeeId = $user->sub;
        $currentYear = date('Y');
        $query = $this->performanceModel
            ->select('MONTH(review_date) as month, AVG(rating) as avg_rating')
            ->where('YEAR(review_date)', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'ASC');
        if ($role === 'employee') {
            $query->where('user_id', $employeeId);
        }
        $data = $query->findAll();
        return $this->respond([
            'year' => $currentYear,
            'monthly_avg' => $data
        ]);
    }

    public function getTaskData()
    {
        $user = $this->authService->user(); // Get logged-in user
        $role = $user->role; // User role
        $taskModel = new TaskModel();
        $currentYear = date('Y'); // Get current year
        // Base query
        $builder = $taskModel
            ->select("MONTH(assigned_date) as month, COUNT(id) as assigned_count, COUNT(CASE WHEN task_status = 'completed' THEN 1 END) as completed_count")
            ->where('YEAR(assigned_date)', $currentYear) // Only current year
            ->groupBy("month")
            ->orderBy("month", "ASC");
        if ($role != 'admin' && $role != 'hr') {
            // Employee role: Add condition for the logged-in user
            $builder->where('user_id', $user->sub);
        }
        $taskData = $builder->findAll();
        // Format response
        $formattedData = [
            'labels' => [],
            'assigned' => [],
            'completed' => [],
        ];
        foreach ($taskData as $task) {
            $formattedData['labels'][] = date("M", mktime(0, 0, 0, $task['month'], 1));
            $formattedData['assigned'][] = (int) $task['assigned_count'];
            $formattedData['completed'][] = (int) $task['completed_count'];
        }
        return $this->response->setJSON($formattedData);
    }
    // day dashbord call
    public function triggerAutoLeaveOnceDaily()
    {        
        $today = date('Y-m-d');
        
        $lastRunFile = WRITEPATH . 'auto_leave_last_run.txt';
        $lastRun = file_exists($lastRunFile) ? trim(file_get_contents($lastRunFile)) : '';
        
        // if ($lastRun !== $today) {
            $this->autoMarkAbsentLeaves();
            file_put_contents($lastRunFile, $today);
        // }
    }

    public function autoMarkAbsentLeaves()
    {
        $attendanceModel = new AttendanceModel();
        $leaveModel = new LeaveModel();
        $holidayModel = new HolidayCalendarModel();
        $rulesModel = new CompanyRulesModel();
        $userModel = new UserModel();

        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // 🔹 Get all holidays in the current month
        $holidays = $holidayModel
            ->where('holiday_date >=', $monthStart)
            ->where('holiday_date <=', $monthEnd)
            ->findAll();
        $holidayDates = array_column($holidays, 'holiday_date');

        // 🔹 Get Saturday off pattern from company rules
        $rule = $rulesModel->first();
        if (!$rule || empty($rule['start_time'])) {
            return;
        }
        $currentTime = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
        $officeTime = new \DateTime(
            date('Y-m-d') . ' ' . $rule['start_time'],
            new \DateTimeZone('Asia/Kolkata')
        );

        if ($currentTime < $officeTime) {
            return;
        }
        
        if ($rule['saturday_off_enabled'] == 1) {
            if ($rule['saturday_off_type'] == 'all') {
                $saturdayPattern = "1, 2, 3, 4, 5";
            }else if ($rule['saturday_off_type'] == 'alternate-even') {
                $saturdayPattern = "2, 4";
            }else if ($rule['saturday_off_type'] == 'alternate-odd') {
                $saturdayPattern = "1, 3, 5";
            }else if ($rule['saturday_off_type'] == 'custom') {
                $saturdayPattern = $rule['saturday_off_pattern'];
            }else {
                $saturdayPattern = "0,0";
            }
        }else{
            $saturdayPattern = "0,0";
        }
        $offSaturdays = $this->getOffSaturdaysInMonth($saturdayPattern);

        // ✅ Get all employees and HRs
        $users = $userModel->where('is_deleted', 0)->whereIn('role', ['employee', 'hr'])->findAll();

        // ✅ Get logged-in user ID and role
        $currentUserId = session()->get('user_id');
        $currentUser = $userModel->find($currentUserId);

        // 🔄 Determine created_by ID
        if ($currentUser && in_array($currentUser['role'], ['admin', 'hr'])) {
            $createdBy = $currentUser['id'];
        } else {
            $admin = $userModel->where('role', 'admin')->first();
            $createdBy = $admin ? $admin['id'] : 1; // fallback to ID 1
        }
        foreach ($users as $user) {
            $userId = $user['id'];

            $current = strtotime($monthStart);
            $end = strtotime($today);

            while ($current <= $end) {
                $date = date('Y-m-d', $current);
                $dayOfWeek = date('w', $current); // 0 = Sunday

                if (
                    $dayOfWeek == 0 ||
                    in_array($date, $holidayDates) ||
                    ($dayOfWeek == 6 && in_array($date, $offSaturdays))
                ) {
                    $current = strtotime('+1 day', $current);
                    continue;
                }

                // ✅ Skip if already marked present or half-day
                $existingAttendance = $attendanceModel
                    ->where('user_id', $userId)
                    ->where('date', $date)
                    ->whereIn('status', ['present', 'half-day'])
                    ->first();

                if ($existingAttendance) {
                    $current = strtotime('+1 day', $current);
                    continue;
                }

                $existingLeave = $leaveModel
                    ->where('user_id', $userId)
                    ->where('start_date <=', $date)
                    ->where('end_date >=', $date)
                    ->first();

                if (!$existingLeave) {
                    $leaveModel->insert([
                        'user_id'     => $userId,
                        'reason'      => 'Auto leave for full-day absence',
                        'start_date'  => $date,
                        'end_date'    => $date,
                        'no_of_day'   => 1,
                        'leave_id'    => 1,
                        'status'      => 'approved',
                        'paid_days'   => 0,
                        'unpaid_days' => 1,
                        'created_by'  => $createdBy,
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
                }

                $current = strtotime('+1 day', $current);
            }
        }
    }

    private function getOffSaturdaysInMonth($pattern)
    {
        $offSaturdays = [];
        $month = date('m');
        $year = date('Y');

        // Convert "1,3" to [1, 3]
        $patternArray = array_map('intval', explode(',', str_replace(' ', '', $pattern)));

        $saturdayCount = 0;

        for ($day = 1; $day <= 31; $day++) {
            if (!checkdate($month, $day, $year)) break;

            $date = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $dayOfWeek = date('w', strtotime($date)); // 6 = Saturday

            if ($dayOfWeek == 6) {
                $saturdayCount++;
                if (in_array($saturdayCount, $patternArray)) {
                    $offSaturdays[] = $date;
                }
            }
        }

        return $offSaturdays;
    }

    public function upload_profile_image_admin(){
        $id = $this->request->getPost('user_id');
        $profileImage = $this->request->getFile('image'); 
        $userInfo = $this->userInfoModel->where('user_id', $id)->first();
        
        if (!$userInfo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        $profileImageName = $userInfo['profile_image'];

        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $newImageName = $profileImage->getRandomName();
            $profileImage->move(FCPATH . 'upload/', $newImageName);

            if (!empty($profileImageName) && file_exists(FCPATH . 'upload/' . $profileImageName)) {
                unlink(FCPATH . 'upload/' . $profileImageName);
            }

            $profileImageName = $newImageName;
        }

        $this->userInfoModel->where('user_id', $id)->set([
            'profile_image' => $profileImageName,
        ])->update();
                $userInfo = session()->get('userInfo');
                $userInfo['profile_image'] = $profileImageName;
                session()->set('userInfo', $userInfo);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Profile image updated successfully.'
        ]);
    }

    /**
     * Calculate today's hours worked and remaining hours for an employee
     * @param int $userId
     * @return array|null
     */
    private function calculateTodayHours($userId)
    {
        date_default_timezone_set('Asia/Kolkata');
        
        $today = date('Y-m-d');
        
        // Get ALL today's attendance records (not just the latest)
        $todayAttendanceRecords = $this->attendanceModel
            ->where('date', $today)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Get company rules for standard working hours
        $companyRulesModel = new CompanyRulesModel();
        $companyRule = $companyRulesModel->orderBy('id', 'DESC')->first();
        
        $standardHoursPerDay = 8.0; // Default 8 hours
        if (!empty($companyRule) && isset($companyRule['working_hours_per_day'])) {
            $standardHoursPerDay = (float)$companyRule['working_hours_per_day'];
        }
        
        $standardHoursSeconds = $standardHoursPerDay * 3600;
        
        // If no attendance records, return default values
        if (empty($todayAttendanceRecords)) {
            return [
                'hours_worked' => '00:00:00',
                'hours_worked_seconds' => 0,
                'remaining_hours' => '00:00:00',
                'remaining_hours_seconds' => 0,
                'standard_hours' => sprintf('%02d:%02d:%02d', floor($standardHoursPerDay), floor(($standardHoursPerDay - floor($standardHoursPerDay)) * 60), 0),
                'is_checked_in' => false,
                'is_checked_out' => false
            ];
        }
        
        $totalWorkedSeconds = 0;
        $completedHoursSeconds = 0; // Hours from completed check-in/check-out pairs
        $currentTime = date('H:i:s');
        $latestCheckInTime = null;
        $latestCheckOutTime = null;
        $isCheckedIn = false;
        $isCheckedOut = false;
        $mealBreakSeconds = 0;
        
        // Default meal break (30 minutes)
        if (!empty($companyRule) && isset($companyRule['lunch_break'])) {
            $mealBreakParts = explode(':', $companyRule['lunch_break']);
            $mealBreakSeconds = ($mealBreakParts[0] * 3600) + ($mealBreakParts[1] * 60) + ($mealBreakParts[2] ?? 0);
        } else {
            $mealBreakSeconds = 30 * 60; // Default 30 minutes
        }
        
        // Get the latest record (most recent) to determine current status
        // Records are ordered by created_at ASC, so the last one in array is latest
        $latestRecord = end($todayAttendanceRecords);
        $latestRecordIndex = key($todayAttendanceRecords);
        reset($todayAttendanceRecords);
        
        // Determine current check-in/check-out status from latest record
        if ($latestRecord) {
            if (!empty($latestRecord['check_in_time']) && empty($latestRecord['check_out_time'])) {
                // Latest record has check-in but no check-out = currently checked in
                $isCheckedIn = true;
                $isCheckedOut = false;
                $latestCheckInTime = $latestRecord['check_in_time'];
            } elseif (!empty($latestRecord['check_in_time']) && !empty($latestRecord['check_out_time'])) {
                // Latest record has both = checked out
                $isCheckedIn = false;
                $isCheckedOut = true;
                $latestCheckInTime = $latestRecord['check_in_time'];
                $latestCheckOutTime = $latestRecord['check_out_time'];
            }
        }
        
        // Process all attendance records to calculate total hours
        $recordIndex = 0;
        foreach ($todayAttendanceRecords as $attendance) {
            $isLatestRecord = ($recordIndex === $latestRecordIndex);
            
            // If this record has both check-in and check-out, it's a completed session
            if (!empty($attendance['check_in_time']) && !empty($attendance['check_out_time'])) {
                $workHoursSeconds = 0;
                
                // Calculate actual session duration first to validate stored work_hours
                $checkInParts = explode(':', $attendance['check_in_time']);
                $checkOutParts = explode(':', $attendance['check_out_time']);
                
                $checkInSeconds = ($checkInParts[0] * 3600) + ($checkInParts[1] * 60) + ($checkInParts[2] ?? 0);
                $checkOutSeconds = ($checkOutParts[0] * 3600) + ($checkOutParts[1] * 60) + ($checkOutParts[2] ?? 0);
                
                $rawSessionSeconds = max(0, $checkOutSeconds - $checkInSeconds);
                
                // Try to use stored work_hours if available and valid (already has meal break subtracted)
                if (!empty($attendance['work_hours']) && $attendance['work_hours'] !== '00:00:00') {
                    // Parse work_hours (format: HH:MM:SS)
                    $workHoursParts = explode(':', $attendance['work_hours']);
                    $storedWorkHoursSeconds = ($workHoursParts[0] * 3600) + ($workHoursParts[1] * 60) + ($workHoursParts[2] ?? 0);
                    
                    // Validate: stored work_hours should be reasonable (not more than raw session, not negative)
                    // If stored value seems wrong, recalculate
                    if ($storedWorkHoursSeconds > 0 && $storedWorkHoursSeconds <= $rawSessionSeconds) {
                        $workHoursSeconds = $storedWorkHoursSeconds;
                    }
                }
                
                // If work_hours is missing, zero, or invalid, calculate manually
                if ($workHoursSeconds <= 0) {
                    $sessionSeconds = $rawSessionSeconds;
                    
                    // Only subtract meal break if session is longer than break duration
                    // For short sessions (less than break time), don't subtract break
                    if ($sessionSeconds > $mealBreakSeconds) {
                        $sessionSeconds = max(0, $sessionSeconds - $mealBreakSeconds);
                    }
                    // If session is shorter than or equal to break time, use the actual session time
                    // (This handles cases like quick check-ins/check-outs)
                    
                    $workHoursSeconds = $sessionSeconds;
                }
                
                // Add to totals
                $totalWorkedSeconds += $workHoursSeconds;
                $completedHoursSeconds += $workHoursSeconds;
            }
            // If this record has check-in but no check-out, it's an active session
            // Only count the latest active session
            elseif (!empty($attendance['check_in_time']) && empty($attendance['check_out_time'])) {
                // Only process if this is the latest active session
                if ($isLatestRecord) {
                    // Calculate hours from check-in to current time using proper datetime comparison
                    $checkInDateTime = strtotime($today . ' ' . $attendance['check_in_time']);
                    $currentDateTime = time();
                    
                    // Calculate worked seconds for active session
                    $activeSessionSeconds = max(0, $currentDateTime - $checkInDateTime);
                    
                    // Don't subtract meal break for active session - show actual elapsed time
                    // Meal break will be handled when they check out
                    $totalWorkedSeconds += $activeSessionSeconds;
                }
            }
            
            $recordIndex++;
        }
        
        // Format hours worked
        $hours = floor($totalWorkedSeconds / 3600);
        $minutes = floor(($totalWorkedSeconds % 3600) / 60);
        $seconds = $totalWorkedSeconds % 60;
        $hoursWorkedFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
        // Calculate remaining hours
        $remainingSeconds = max(0, $standardHoursSeconds - $totalWorkedSeconds);
        $remainingHours = floor($remainingSeconds / 3600);
        $remainingMinutes = floor(($remainingSeconds % 3600) / 60);
        $remainingSecs = $remainingSeconds % 60;
        $remainingHoursFormatted = sprintf('%02d:%02d:%02d', $remainingHours, $remainingMinutes, $remainingSecs);
        
        // Format standard hours
        $standardHoursFormatted = sprintf('%02d:%02d:%02d', floor($standardHoursPerDay), floor(($standardHoursPerDay - floor($standardHoursPerDay)) * 60), 0);
        
        return [
            'hours_worked' => $hoursWorkedFormatted,
            'hours_worked_seconds' => $totalWorkedSeconds,
            'completed_hours_seconds' => $completedHoursSeconds, // Hours from completed sessions only
            'remaining_hours' => $remainingHoursFormatted,
            'remaining_hours_seconds' => $remainingSeconds,
            'standard_hours' => $standardHoursFormatted,
            'standard_hours_decimal' => $standardHoursPerDay,
            'standard_hours_seconds' => $standardHoursSeconds,
            'meal_break_seconds' => $mealBreakSeconds,
            'is_checked_in' => $isCheckedIn,
            'is_checked_out' => $isCheckedOut,
            'check_in_time' => $latestCheckInTime,
            'check_out_time' => $latestCheckOutTime
        ];
    }
}
