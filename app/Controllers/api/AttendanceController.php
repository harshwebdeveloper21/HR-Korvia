<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use App\Models\UserModel;
use App\Models\CompanyRulesModel;
use App\Models\HolidayCalendarModel;
use App\Models\LocationSettingsModel;
use App\Models\NotificationSettingsModel;
use App\Services\PushNotificationService;

class AttendanceController extends ResourceController
{
    private $attendanceModel;
    private $leaveModel;
    private $authService;
    private $companyRulesModel;
    private $pushNotificationService;
    private $userModel;
    private $holidayCalendarModel;
    private $locationSettingsModel;
    private $notificationSettingsModel;

    private function calculateAttendanceStatus( int $workedSeconds, array $companyRule ): string {

        $payrollType = $companyRule['payroll_type'] ?? 'monthly';

        // 🔹 HOURLY PAYROLL → half-day does NOT exist
        if ($payrollType === 'hourly') {
            return $workedSeconds > 0 ? 'present' : 'absent';
        }

        // 🔹 DAILY / MONTHLY
        $graceSeconds = (isset($companyRule['grace_minutes']) ? (int)$companyRule['grace_minutes'] : 10) * 60;
        $effectiveSeconds = $workedSeconds + $graceSeconds;

        $fullDayThreshold = 4 * 3600; // 4 hours
        $halfDayThreshold = 1 * 3600; // 1 hour

        if ($effectiveSeconds > $fullDayThreshold) {
            return 'present';
        }

        if ($effectiveSeconds >= $halfDayThreshold) {
            return 'half-day';
        }

        return 'absent';
    }

    private function timeToSeconds(?string $time): int
    {
        if (empty($time)) {
            return 0;
        }

        $parts = array_map('intval', explode(':', $time));

        $h = $parts[0] ?? 0;
        $m = $parts[1] ?? 0;
        $s = $parts[2] ?? 0;

        return ($h * 3600) + ($m * 60) + $s;
    }


    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->companyRulesModel = new CompanyRulesModel();
        $this->leaveModel = new LeaveModel();
        $this->authService = new AuthService(service('request'));
        $this->pushNotificationService = new PushNotificationService();
        $this->userModel = new UserModel();
    }

    public function display()
    {
        return view('attendence/attendence');
    }

    public function getStatus()
    {
        // Authenticate user
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $date = date('Y-m-d');
        $userModel = new UserModel();
        $userData = $userModel->find($user->sub);
        if (!$userData) {
            return $this->respond(['status' => 'error', 'message' => 'User not found'], 404);
        }
        
        // Check if user has face photo for biometric attendance
        $userInfoModel = new \App\Models\UserInfoModel();
        $userInfo = $userInfoModel->where('user_id', $user->sub)->first();
        $hasFacePhoto = !empty($userInfo['face_photo']);
        
        // Check today's attendance record for the authenticated user
        $attendanceRecord = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $date)
            ->orderBy('id', 'DESC')
            ->first();
        $response = [
            'status' => 'success',
            'role'   => $userData['role'], // Include user role
            'has_face_photo' => $hasFacePhoto, // Include face photo status
        ];
        if (!$attendanceRecord) {
            $response['data'] = 'not_checked_in';
        } elseif ($attendanceRecord['check_in_time'] && !$attendanceRecord['check_out_time']) {
            $response['data'] = 'checked_in';
        } else {
            $response['data'] = 'checked_out';
        }

        return $this->respond($response);
    }

    public function checkIn()
    {
        date_default_timezone_set('Asia/Kolkata'); // Set server timezone

        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if (!in_array($user->role, ['hr', 'employee'])) {
            return $this->respond(['status' => 'error', 'message' => 'Access denied for this role'], 403);
        }

        // Always use server's current date and time (Asia/Kolkata timezone)
        // This ensures the date is always correct regardless of client timezone
        $date = date('Y-m-d'); // Today's date in server timezone
        $checkInTime = date('Y-m-d H:i:s'); // Current server time
        // Extract only the time part for storage (HH:MM:SS format)
        $timeOnly = date('H:i:s');

        // Check for yesterday's incomplete attendance (check-in without checkout)
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $yesterdayAttendance = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $yesterday)
            ->where('check_out_time', null)
            ->where('check_in_time IS NOT NULL')
            ->orderBy('id', 'DESC')
            ->first();

        // If found incomplete yesterday's attendance, update it with checkout time
        if ($yesterdayAttendance) {
            // Get company rules to determine standard checkout time
            $companyRule = $this->companyRulesModel->orderBy('id', 'DESC')->first();
            $fullDayHours = $companyRule['working_hours_per_day'] ?? 8;
            
            // Calculate standard checkout time based on check-in time + working hours
            $checkInParts = explode(':', $yesterdayAttendance['check_in_time']);
            $checkInHour = (int)$checkInParts[0];
            $checkInMinute = (int)$checkInParts[1];
            
            // Add working hours to check-in time to get checkout time
            $checkoutHour = $checkInHour + $fullDayHours;
            $checkoutMinute = $checkInMinute;
            
            // Handle hour overflow (e.g., if check-in at 10:00 AM + 8 hours = 6:00 PM)
            if ($checkoutHour >= 24) {
                $checkoutHour = $checkoutHour - 24;
            }
            
            $yesterdayCheckoutTime = sprintf('%02d:%02d:00', $checkoutHour, $checkoutMinute);
            
            // Calculate work hours for yesterday's record
            $calculation = $this->calculateWorkHours($yesterday, $yesterdayAttendance['meal_break'], $yesterdayAttendance['check_in_time'], $yesterdayCheckoutTime);
            
            $updateData = [
                'check_out_time' => $yesterdayCheckoutTime,
                'work_hours' => $calculation['work_hours'],
                'overtime' => $calculation['overtime'],
                'status' => $calculation['status']
            ];
            
            $this->attendanceModel->update($yesterdayAttendance['id'], $updateData);
            
            log_message('info', 'Auto-checkout for user ' . $user->sub . ' on ' . $yesterday . ' at ' . $yesterdayCheckoutTime . ' (Work hours: ' . $calculation['work_hours'] . ')');
            
            // Store the auto-checkout info to include in response
            $autoCheckoutInfo = [
                'date' => $yesterday,
                'checkout_time' => $yesterdayCheckoutTime,
                'work_hours' => $calculation['work_hours'],
                'status' => $calculation['status']
            ];
        } else {
            $autoCheckoutInfo = null;
        }

        // Get latest attendance for today
        $latestAttendance = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $date)
            ->orderBy('id', 'DESC')
            ->first();

        // If user is already checked in (has check-in but no check-out), update the check-in time
        if ($latestAttendance && !$latestAttendance['check_out_time']) {
            $updateData = [
                'check_in_time' => $timeOnly, // Store only time (HH:MM:SS)
            ];

            $this->attendanceModel->update($latestAttendance['id'], $updateData);

            $response = [
                'status' => 'success',
                'message' => 'Check-in time updated successfully',
                'data' => $updateData
            ];
            
            // Include auto-checkout info if applicable
            if ($autoCheckoutInfo) {
                $response['auto_checkout'] = $autoCheckoutInfo;
                $response['message'] .= '. Yesterday\'s attendance was automatically checked out at ' . $autoCheckoutInfo['checkout_time'] . ' (' . $autoCheckoutInfo['work_hours'] . ' hours)';
            }

            return $this->respond($response);
        }

        // If user has already checked out or no record exists, create a new check-in record
        // This allows checking in again after checkout
        $data = [
            'user_id' => $user->sub,
            'date' => $date, // Today's date from server
            'check_in_time' => $timeOnly, // Current time from server (HH:MM:SS)
            'status' => 'present'
        ];
        
        // Delete any leave record for today if exists
        $this->leaveModel
            ->where('user_id', $user->sub)
            ->where('start_date', $date)
            ->where('end_date', $date)
            ->delete();

        if ($this->attendanceModel->insert($data)) {
            // Check if attendance notifications are enabled
            $notificationSettingsModel = new NotificationSettingsModel();
            if ($notificationSettingsModel->isAttendanceNotificationsEnabled()) {
                // Send push notification to admins
                $employee = $this->userModel->find($user->sub);
                $employeeName = $employee ? $employee['username'] : 'Employee';
                
                log_message('info', '📝 Employee check-in: ' . $employeeName . ' at ' . $timeOnly);
                log_message('info', '📝 Attempting to send push notification to admins...');
                
                $pushResult = $this->pushNotificationService->notifyAdmins(
                    'Employee Check-In',
                    $employeeName . ' has checked in at ' . $timeOnly,
                    [
                        'type' => 'checkin',
                        'user_id' => $user->sub,
                        'username' => $employeeName,
                        'time' => $timeOnly,
                        'date' => $date,
                        'url' => base_url('/attendence')
                    ]
                );
                
                log_message('info', '📝 Push notification result: ' . json_encode($pushResult));
            } else {
                log_message('info', '📝 Attendance notifications are disabled - skipping push notification');
            }
            
            $response = [
                'status' => 'success',
                'message' => 'Checked in successfully'
            ];
            
            // Include auto-checkout info if applicable
            if ($autoCheckoutInfo) {
                $response['auto_checkout'] = $autoCheckoutInfo;
                $response['message'] .= '. Yesterday\'s attendance was automatically checked out at ' . $autoCheckoutInfo['checkout_time'] . ' (' . $autoCheckoutInfo['work_hours'] . ' hours)';
            }
            
            return $this->respond($response);
        }

        return $this->respond(['status' => 'error', 'message' => 'Check-in failed'], 500);
    }    


    /**
     * Calculate work hours from check-in and check-out times
     * @param string $date Date in Y-m-d format
     * @param string $checkInTime Time in H:i:s format
     * @param string $checkOutTime Time in H:i:s format
     * @return array ['work_hours' => formatted time, 'work_hours_seconds' => seconds, 'status' => status]
     */
   private function calculateWorkHours($date, $mealbreakTime, $checkInTime, $checkOutTime)
{
    if (empty($checkInTime) || empty($checkOutTime)) {
        return [
            'work_hours' => null,
            'work_hours_seconds' => 0,
            'overtime' => '00:00:00',
            'overtime_seconds' => 0,
            'status' => 'absent'
        ];
    }

    $checkInTimestamp  = strtotime($date . ' ' . $checkInTime);
    $checkOutTimestamp = strtotime($date . ' ' . $checkOutTime);

    if (!$checkInTimestamp || !$checkOutTimestamp || $checkOutTimestamp <= $checkInTimestamp) {
        log_message('debug', 'Invalid timestamps or checkout <= checkin');
        return [
            'work_hours' => '00:00:00',
            'work_hours_seconds' => 0,
            'overtime' => '00:00:00',
            'overtime_seconds' => 0,
            'status' => 'absent'
        ];
    }

    /* ---------------------------------------------------
    1. GROSS WORK DURATION
    --------------------------------------------------- */
    $grossWorkSeconds = $checkOutTimestamp - $checkInTimestamp;

    /* ---------------------------------------------------
    2. COMPANY RULES
    --------------------------------------------------- */
    $companyRule = $this->companyRulesModel->orderBy('id', 'DESC')->first();
    $isSaturdayHalfDay = $this->isSaturdayHalfDay($date, $companyRule);

    /* ---------------------------------------------------
    3. MEAL BREAK
    --------------------------------------------------- */
    $mealBreakSeconds = 0;

    if (!$isSaturdayHalfDay && !empty($mealbreakTime)) {
        $time = new \DateTime($mealbreakTime);
        $mealBreakSeconds =
            ($time->format('H') * 3600) +
            ($time->format('i') * 60) +
            $time->format('s');
    }

    /* ---------------------------------------------------
    4. NET WORKING HOURS
    --------------------------------------------------- */
    $workHoursInSeconds = max(0, $grossWorkSeconds - $mealBreakSeconds);

    /* ---------------------------------------------------
    5. COMPANY SETTINGS
    --------------------------------------------------- */
    $payrollType  = $companyRule['payroll_type'] ?? 'monthly';
    $graceMinutes = (int)($companyRule['grace_minutes'] ?? 10);
    $graceSeconds = $graceMinutes * 60;

    /* ---------------------------------------------------
    6. OVERTIME CALCULATION (UNCHANGED)
    --------------------------------------------------- */
    $fullDayHours = (float)($companyRule['working_hours_per_day'] ?? 8);
    $halfDayHours = (float)($companyRule['half_day_hours'] ?? 5);

    $fullDaySeconds = $fullDayHours * 3600;
    $halfDaySeconds = $halfDayHours * 3600;

    $overtimeSeconds = 0;

    if (!empty($companyRule) && (int)($companyRule['enable_overtime'] ?? 0) === 1) {
        $overtimeStartSeconds = $isSaturdayHalfDay
            ? $halfDaySeconds
            : ($fullDaySeconds + $mealBreakSeconds);

        if ($grossWorkSeconds > $overtimeStartSeconds) {
            $overtimeSeconds = $grossWorkSeconds - $overtimeStartSeconds;

            $minOvertimeSeconds =
                ((float)($companyRule['min_overtime_count_in_minutes'] ?? 0)) * 60;

            if ($overtimeSeconds < $minOvertimeSeconds) {
                $overtimeSeconds = 0;
            }
        }
    }

    /* ---------------------------------------------------
    7. ATTENDANCE STATUS (UPDATED - 4 HOUR RULE)
    --------------------------------------------------- */
    $status = 'absent';

    $effectiveSeconds = $workHoursInSeconds + $graceSeconds;

    $fullDayThreshold = 4 * 3600; // 4 hours
    $halfDayThreshold = 1 * 3600; // optional

    if ($payrollType === 'hourly') {
        $status = $workHoursInSeconds > 0 ? 'present' : 'absent';
    } else {
        if ($effectiveSeconds > $fullDayThreshold) {
            $status = 'present';
        } elseif ($effectiveSeconds >= $halfDayThreshold) {
            $status = 'half-day';
        } else {
            $status = 'absent';
        }
    }

    /* ---------------------------------------------------
    8. FORMAT OUTPUT
    --------------------------------------------------- */
    $formattedWorkHours = sprintf(
        '%02d:%02d:%02d',
        floor($workHoursInSeconds / 3600),
        floor(($workHoursInSeconds % 3600) / 60),
        $workHoursInSeconds % 60
    );

    $formattedOvertime = sprintf(
        '%02d:%02d:%02d',
        floor($overtimeSeconds / 3600),
        floor(($overtimeSeconds % 3600) / 60),
        $overtimeSeconds % 60
    );

    return [
        'work_hours' => $formattedWorkHours,
        'work_hours_seconds' => $workHoursInSeconds,
        'overtime' => $formattedOvertime,
        'overtime_seconds' => $overtimeSeconds,
        'status' => $status,
        'is_saturday_half_day' => $isSaturdayHalfDay
    ];
}

    /**
     * Check if a given date is a Saturday half-day based on company rules
     * @param string $date Date in Y-m-d format
     * @param array $companyRule Company rules array
     * @return bool True if it's a Saturday half-day
     */
    private function isSaturdayHalfDay($date, $companyRule)
    {
        // Check if it's Saturday first
        $dayOfWeek = date('N', strtotime($date));
        if ($dayOfWeek != 6) { // Not Saturday
            return false;
        }

        // Check if saturday_half_day_pattern is enabled
        if (empty($companyRule['saturday_half_day_pattern'])) {
            return false;
        }

        // Get the pattern (e.g., "1,2" or "1,3,5")
        $halfDayPattern = explode(',', $companyRule['saturday_half_day_pattern']);
        
        // Calculate which Saturday of the month this is
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $saturdayCount = 0;
        
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for ($day = 1; $day <= $totalDays; $day++) {
            $checkDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $checkTimestamp = strtotime($checkDate);
            
            if (date('N', $checkTimestamp) == 6) { // Is Saturday
                $saturdayCount++;
                if ($checkDate == $date) {
                    // Check if this Saturday number is in the half-day pattern
                    return in_array($saturdayCount, $halfDayPattern);
                }
            }
        }
        
        return false;
    }


    public function checkOut()
    {
        date_default_timezone_set('Asia/Kolkata');

        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if (!in_array($user->role, ['hr', 'employee'])) {
            return $this->respond(['status' => 'error', 'message' => 'Access denied for this role'], 403);
        }

        // Always use server's current date and time (Asia/Kolkata timezone)
        $date = date('Y-m-d'); // Today's date in server timezone
        $checkOutTimeOnly = date('H:i:s'); // Current server time (HH:MM:SS)

        // Fetch the latest attendance record without checkout
        $latestAttendance = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $date)
            ->where('check_out_time', null)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$latestAttendance) {
            return $this->respond(['status' => 'error', 'message' => 'No check-in record found for today'], 400);
        }

        // Calculate work hours using the helper method
        $calculation = $this->calculateWorkHours($date, $latestAttendance['meal_break'], $latestAttendance['check_in_time'], $checkOutTimeOnly);

        $data = [
            'check_out_time' => $checkOutTimeOnly, // Store only time (HH:MM:SS)
            'work_hours' => $calculation['work_hours'],
            'overtime' => $calculation['overtime'],
            'meal_break' => $latestAttendance['meal_break'],
            'status' => $calculation['status']
        ];

        if ($this->attendanceModel->update($latestAttendance['id'], $data)) {
            // Aggregate totals for the day and get the final status
            $syncResult = $this->syncDayAttendance($user->sub, $date);
            if ($syncResult) {
                $calculation['status'] = $syncResult['status'];
                $data['status'] = $syncResult['status'];
                $data['work_hours'] = $syncResult['work_hours'];
                $data['overtime'] = $syncResult['overtime'];
            }

            // Check if attendance notifications are enabled
            $notificationSettingsModel = new NotificationSettingsModel();
            if ($notificationSettingsModel->isAttendanceNotificationsEnabled()) {
                // Send push notification to admins
                $employee = $this->userModel->find($user->sub);
                $employeeName = $employee ? $employee['username'] : 'Employee';
                
                log_message('info', '📝 Employee check-out: ' . $employeeName . ' at ' . $checkOutTimeOnly);
                
                $this->pushNotificationService->notifyAdmins(
                    'Employee Check-Out',
                    $employeeName . ' has checked out at ' . $checkOutTimeOnly . ' (Status: ' . $calculation['status'] . ')',
                    [
                        'type' => 'checkout',
                        'user_id' => $user->sub,
                        'username' => $employeeName,
                        'time' => $checkOutTimeOnly,
                        'date' => $date,
                        'status' => $calculation['status'],
                        'url' => base_url('/attendence')
                    ]
                );
            } else {
                log_message('info', '📝 Attendance notifications are disabled - skipping push notification');
            }
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Checked out successfully (' . $calculation['status'] . ')',
                'data' => $data
            ]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Check-out failed'], 500);
    }

    public function getAttendanceData()
    {
        // Check Authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Assuming that HR has role "hr" and employees have role "employee"
        $userRole = $user->role; // "employee" or "hr"

        // If HR is logged in, they can see all employees' attendance
        if ($userRole === 'hr' || $userRole === 'admin') {
            $attendanceData = $this->attendanceModel->orderBy('date', 'DESC')->orderBy('id', 'DESC')->findAll(); // HR sees all attendance records
        } else {
            // If an employee is logged in, they can only see their own attendance records
            $attendanceData = $this->attendanceModel->where('user_id', $user->sub)->orderBy('date', 'DESC')->orderBy('id', 'DESC')->findAll();
        }
        $userModel = new UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();


        // Fetch the employee names from the associated User model
        // Also recalculate work hours for existing records to ensure accuracy
        foreach ($attendanceData as &$record) {
            // Assuming attendance has a user_id and you have a relation with User model
            $employee = $userModel->find($record['user_id']); // Fix the reference to user_id, not record['id']
            $record['employee_name'] = $employee ? $employee['username'] : 'Unknown'; // Add employee name to the record

            $userInfo = $userInfoModel->where('user_id', $record['user_id'])->first();
            $record['profile_image'] = $userInfo ? $userInfo['profile_image'] : null;

            // Recalculate work hours if both check-in and check-out times exist
            // This ensures old records with incorrect calculations are displayed correctly
            if (!empty($record['check_in_time']) && !empty($record['check_out_time'])) {
                $calculation = $this->calculateWorkHours($record['date'], $record['meal_break'], $record['check_in_time'], $record['check_out_time']);
                $record['work_hours'] = $calculation['work_hours'];
                // Optionally update status as well if needed
                // $record['status'] = $calculation['status'];
            }
        }
        // Return the attendance data
        return $this->respond(['status' => 'success', 'data' => $attendanceData]);
    }

    public function getLeaveDetails($userId, $dateStr = null)
    {
        // Check if the user is authorized
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Fetch leave details for the specific user
        $builder = $this->leaveModel->where('user_id', $userId);

        // If a date is provided, filter the leave records by the given date
        if ($dateStr) {
            $builder->where('start_date <=', $dateStr)
                ->where('end_date >=', $dateStr);
        }

        $leaveData = $builder->findAll();

        if (!$leaveData) {
            return $this->failNotFound('No leave records found for this employee');
        }

        // Return the leave data
        return $this->respond(['status' => 'success', 'data' => $leaveData]);
    }

    // public function getAttendance($month, $year)
    // {
    //     $userModel = new \App\Models\UserModel();
    //     $userInfoModel = new \App\Models\UserInfoModel();
    //     $attendanceModel = new AttendanceModel();
    //     $holidayCalendarModel = new HolidayCalendarModel();
    //     $companyRulesModel = new CompanyRulesModel();

    //     $authUser = $this->authService->user();
    //     if (!$authUser) {
    //         return $this->failUnauthorized('Unauthorized access');
    //     }

    //     // Get users based on role
    //     if ($authUser->role === 'admin' || $authUser->role === 'hr') {
    //         $users = $userModel->where('is_deleted', 0)->whereIn('role', ['employee', 'hr'])->findAll();
    //     } else {
    //         $users = $userModel->where('id', $authUser->sub)->findAll();
    //     }

    //     $userIds = array_column($users, 'id');
    //     $userInfoList = $userInfoModel->whereIn('user_id', $userIds)->findAll();

    //     $userInfoMap = [];
    //     foreach ($userInfoList as $info) {
    //         $userInfoMap[$info['user_id']] = $info;
    //     }

    //     // Get attendance records
    //     $attendanceData = $attendanceModel
    //         ->where('MONTH(date)', $month)
    //         ->where('YEAR(date)', $year)
    //         ->findAll();

    //     // Get holidays
    //     $holidays = $holidayCalendarModel
    //         ->where('MONTH(holiday_date)', $month)
    //         ->where('YEAR(holiday_date)', $year)
    //         ->orderBy('holiday_date', 'ASC')
    //         ->findAll();

    //     $companyRule = $companyRulesModel->first();
    //     if ($companyRule['saturday_off_enabled'] == 1) {
    //         if ($companyRule['saturday_off_type'] == 'all') {
    //             $saturdayOffIndexes = [1, 2, 3, 4, 5];
    //         }else if ($companyRule['saturday_off_type'] == 'alternate-even') {
    //             $saturdayOffIndexes = [2, 4];
    //         }else if ($companyRule['saturday_off_type'] == 'alternate-odd') {
    //             $saturdayOffIndexes = [1, 3, 5];
    //         }else if ($companyRule['saturday_off_type'] == 'custom') {
    //             $saturdayOffIndexes = explode(',', $companyRule['saturday_off_pattern'] );
    //         }else {
    //             $saturdayOffIndexes = [];
    //         }
    //     }else{
    //         // if ($companyRule['saturday_off_type'] == 'all') {
    //         //     $saturdayOffIndexes = [1, 2, 3, 4, 5];
    //         // }else if ($companyRule['saturday_off_type'] == 'alternate-even') {
    //         //     $saturdayOffIndexes = [2, 4];
    //         // }else if ($companyRule['saturday_off_type'] == 'alternate-odd') {
    //         //     $saturdayOffIndexes = [1, 3, 5];
    //         // }else if ($companyRule['saturday_off_type'] == 'custom') {
    //         //     $saturdayOffIndexes = explode(',', $companyRule['saturday_off_pattern'] );
    //         // }else {
    //             $saturdayOffIndexes = [];
    //         // }
    //     }

    //     // Calculate Saturday off dates
    //     $saturdayOffDates = [];
    //     $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    //     $saturdayCount = 0;

    //     for ($day = 1; $day <= $totalDays; $day++) {
    //         $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
    //         $timestamp = strtotime($date);
    //         if (date('N', $timestamp) == 6) { // Saturday
    //             $saturdayCount++;
    //             if (in_array($saturdayCount, $saturdayOffIndexes)) {
    //                 $saturdayOffDates[] = $date;
    //             }
    //         }
    //     }

    //     // Format users with attendance
    //     $attendanceWithUsers = [];
    //     foreach ($users as $user) {
    //         $userId = $user['id'];

    //         $userAttendance = array_filter($attendanceData, function ($attendance) use ($userId) {
    //             return $attendance['user_id'] == $userId;
    //         });

    //         // Group attendance by date and prioritize records with check_in_time but no check_out_time
    //         $attendanceByDate = [];
    //         foreach ($userAttendance as $record) {
    //             $date = $record['date'];
                
    //             // If no record exists for this date, add it
    //             if (!isset($attendanceByDate[$date])) {
    //                 $attendanceByDate[$date] = $record;
    //             } else {
    //                 // If current record has check_in_time but no check_out_time (currently working), prioritize it
    //                 $currentHasWorking = !empty($record['check_in_time']) && empty($record['check_out_time']);
    //                 $existingHasWorking = !empty($attendanceByDate[$date]['check_in_time']) && empty($attendanceByDate[$date]['check_out_time']);
                    
    //                 if ($currentHasWorking && !$existingHasWorking) {
    //                     // Current record is working, existing is not - use current
    //                     $attendanceByDate[$date] = $record;
    //                 } elseif (!$currentHasWorking && !$existingHasWorking) {
    //                     // Both are checked out - use the most recent one (higher ID)
    //                     if ($record['id'] > $attendanceByDate[$date]['id']) {
    //                         $attendanceByDate[$date] = $record;
    //                     }
    //                 } elseif ($currentHasWorking && $existingHasWorking) {
    //                     // Both are working - use the most recent one (higher ID)
    //                     if ($record['id'] > $attendanceByDate[$date]['id']) {
    //                         $attendanceByDate[$date] = $record;
    //                     }
    //                 }
    //             }
    //         }

    //         $formattedAttendance = [];
    //         foreach ($attendanceByDate as $record) {
    //             $formattedAttendance[] = [
    //                 'date' => $record['date'],
    //                 'check_in_time' => $record['check_in_time'],
    //                 'check_out_time' => $record['check_out_time'],
    //                 'status' => $record['status'],
    //                 'is_late' => $record['is_late'],
    //                 'late_minutes' => $record['late_minutes'],
    //                 'overtime' => $record['overtime'] ?? null,
    //             ];
    //         }

    //         $profileImage = $userInfoMap[$userId]['profile_image'] ?? null;

    //         $attendanceWithUsers[] = [
    //             'user_id' => $userId,
    //             'employee_name' => $user['username'],
    //             'profile_image' => $profileImage,
    //             'attendance' => $formattedAttendance,
    //         ];
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'data' => [
    //             'users' => $attendanceWithUsers,
    //             'holidays' => $holidays,
    //             'saturdayOffDates' => $saturdayOffDates
    //         ]
    //     ]);
    // }
    public function getAttendance($month, $year)
    {
        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();
        $attendanceModel = new AttendanceModel();
        $holidayCalendarModel = new HolidayCalendarModel();
        $companyRulesModel = new CompanyRulesModel();

        $authUser = $this->authService->user();
        if (!$authUser) {
            return $this->failUnauthorized('Unauthorized access');
        }

        // 🔹 Get users
        if ($authUser->role === 'admin' || $authUser->role === 'hr') {
            $users = $userModel
                ->where('is_deleted', 0)
                ->whereIn('role', ['employee', 'hr'])
                ->findAll();
        } else {
            $users = $userModel->where('id', $authUser->sub)->findAll();
        }

        $userIds = array_column($users, 'id');

        // 🔹 User info
        $userInfoList = $userInfoModel->whereIn('user_id', $userIds)->findAll();
        $userInfoMap = [];
        foreach ($userInfoList as $info) {
            $userInfoMap[$info['user_id']] = $info;
        }

        // 🔹 Attendance data
        $attendanceData = $attendanceModel
            ->where('MONTH(date)', $month)
            ->where('YEAR(date)', $year)
            ->findAll();

        // 🔹 Holidays
        $holidays = $holidayCalendarModel
            ->where('MONTH(holiday_date)', $month)
            ->where('YEAR(holiday_date)', $year)
            ->orderBy('holiday_date', 'ASC')
            ->findAll();

        $holidayDates = array_column($holidays, 'holiday_date');
        $holidayMap = array_flip($holidayDates);

        // 🔹 Company rules
        $companyRule = $companyRulesModel->first();
        $isIncludedHoliday = $companyRule['include_holidays_in_working_days'];

        if ($companyRule && $companyRule['saturday_off_enabled'] == 1) {
            switch ($companyRule['saturday_off_type']) {
                case 'all':
                    $saturdayOffIndexes = [1, 2, 3, 4, 5];
                    break;
                case 'alternate-even':
                    $saturdayOffIndexes = [2, 4];
                    break;
                case 'alternate-odd':
                    $saturdayOffIndexes = [1, 3, 5];
                    break;
                case 'custom':
                    $saturdayOffIndexes = explode(',', $companyRule['saturday_off_pattern']);
                    break;
                default:
                    $saturdayOffIndexes = [];
            }
        } else {
            $saturdayOffIndexes = [];
        }

        // 🔹 Calculate Saturday off dates
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $saturdayOffDates = [];
        $saturdayCount = 0;

        for ($day = 1; $day <= $totalDays; $day++) {
            $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            if (date('N', strtotime($date)) == 6) {
                $saturdayCount++;
                if (in_array($saturdayCount, $saturdayOffIndexes)) {
                    $saturdayOffDates[] = $date;
                }
            }
        }

        $saturdayOffMap = array_flip($saturdayOffDates);

        // 🔹 Final response
        $attendanceWithUsers = [];

        foreach ($users as $user) {
            $userId = $user['id'];

            // Filter user attendance
            $userAttendance = array_filter($attendanceData, function ($row) use ($userId) {
                return $row['user_id'] == $userId;
            });

            // Group by date (handle multiple punch-ins)
            $attendanceByDate = [];
            foreach ($userAttendance as $record) {
                $date = $record['date'];

                if (!isset($attendanceByDate[$date])) {
                    $attendanceByDate[$date] = $record;
                    $attendanceByDate[$date]['total_work_seconds'] = $this->timeToSeconds($record['work_hours'] ?? '00:00:00');
                    $attendanceByDate[$date]['total_overtime_seconds'] = $this->timeToSeconds($record['overtime'] ?? '00:00:00');
                } else {
                    // Accumulate hours
                    $attendanceByDate[$date]['total_work_seconds'] += $this->timeToSeconds($record['work_hours'] ?? '00:00:00');
                    $attendanceByDate[$date]['total_overtime_seconds'] += $this->timeToSeconds($record['overtime'] ?? '00:00:00');
                    
                    // Keep the earliest check_in_time
                    if (!empty($record['check_in_time'])) {
                        if (empty($attendanceByDate[$date]['check_in_time']) || $record['check_in_time'] < $attendanceByDate[$date]['check_in_time']) {
                            $attendanceByDate[$date]['check_in_time'] = $record['check_in_time'];
                            $attendanceByDate[$date]['is_late'] = $record['is_late'] ?? $attendanceByDate[$date]['is_late'];
                            $attendanceByDate[$date]['late_minutes'] = $record['late_minutes'] ?? $attendanceByDate[$date]['late_minutes'];
                        }
                    }

                    // Keep the latest check_out_time, or null if currently working
                    if (empty($record['check_out_time'])) {
                        $attendanceByDate[$date]['check_out_time'] = null;
                    } elseif ($attendanceByDate[$date]['check_out_time'] !== null) {
                        if (empty($attendanceByDate[$date]['check_out_time']) || $record['check_out_time'] > $attendanceByDate[$date]['check_out_time']) {
                            $attendanceByDate[$date]['check_out_time'] = $record['check_out_time'];
                        }
                    }

                    // Keep highest ID to maintain reference
                    if ($record['id'] > $attendanceByDate[$date]['id']) {
                        $attendanceByDate[$date]['id'] = $record['id'];
                    }
                }
            }

            // Recalculate status and formatted hours for aggregated records
            foreach ($attendanceByDate as $date => &$dayData) {
                if (isset($dayData['total_work_seconds'])) {
                    $totalSeconds = $dayData['total_work_seconds'];
                    
                    // Format the total work hours
                    $dayData['work_hours'] = sprintf('%02d:%02d:%02d',
                        floor($totalSeconds / 3600),
                        floor(($totalSeconds % 3600) / 60),
                        $totalSeconds % 60
                    );
                    
                    $dayData['overtime'] = sprintf('%02d:%02d:%02d',
                        floor($dayData['total_overtime_seconds'] / 3600),
                        floor(($dayData['total_overtime_seconds'] % 3600) / 60),
                        $dayData['total_overtime_seconds'] % 60
                    );
                    
                    // Determine cumulative status
                    $effectiveSeconds = $totalSeconds + (isset($companyRule['grace_minutes']) ? ((int)$companyRule['grace_minutes'] * 60) : 600);
                    $payrollType = $companyRule['payroll_type'] ?? 'monthly';
                    
                    if ($payrollType === 'hourly') {
                        $dayData['status'] = $totalSeconds > 0 ? 'present' : 'absent';
                    } else {
                        $fullDayThreshold = 4 * 3600; // 4 hours threshold
                        $halfDayThreshold = 1 * 3600; // 1 hour threshold
                        
                        if ($effectiveSeconds > $fullDayThreshold) {
                            $dayData['status'] = 'present';
                        } elseif ($effectiveSeconds >= $halfDayThreshold) {
                            $dayData['status'] = 'half-day';
                        } else {
                            $dayData['status'] = 'absent';
                        }
                    }
                }
            }

            // 🔹 Build full month attendance (THIS FIXES 26 JAN ISSUE)
            // $formattedAttendance = [];

            // for ($day = 1; $day <= $totalDays; $day++) {
            //     $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);

            //     // Holiday
            //     if (isset($holidayMap[$date])) {
            //         $formattedAttendance[] = [
            //             'date' => $date,
            //             'check_in_time' => null,
            //             'check_out_time' => null,
            //             'status' => 'holiday',
            //             'is_late' => null,
            //             'late_minutes' => null,
            //             'overtime' => null,
            //         ];
            //         continue;
            //     }

            //     // Saturday off
            //     if (isset($saturdayOffMap[$date])) {
            //         $formattedAttendance[] = [
            //             'date' => $date,
            //             'check_in_time' => null,
            //             'check_out_time' => null,
            //             'status' => 'week_off',
            //             'is_late' => null,
            //             'late_minutes' => null,
            //             'overtime' => null,
            //         ];
            //         continue;
            //     }

            //     // Attendance exists
            //     if (isset($attendanceByDate[$date])) {
            //         $record = $attendanceByDate[$date];
            //         $formattedAttendance[] = [
            //             'date' => $date,
            //             'check_in_time' => $record['check_in_time'],
            //             'check_out_time' => $record['check_out_time'],
            //             'status' => $record['status'],
            //             'is_late' => $record['is_late'],
            //             'late_minutes' => $record['late_minutes'],
            //             'overtime' => $record['overtime'] ?? null,
            //         ];
            //         continue;
            //     }

            //     // ABSENT
            //     $formattedAttendance[] = [
            //         'date' => $date,
            //         'check_in_time' => null,
            //         'check_out_time' => null,
            //         'status' => 'absent',
            //         'is_late' => null,
            //         'late_minutes' => null,
            //         'overtime' => null,
            //     ];
            // }
            // 🔹 Build full month attendance (THIS FIXES 26 JAN ISSUE)
            $formattedAttendance = [];

            for ($day = 1; $day <= $totalDays; $day++) {
                $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);

                // Holiday
                if (isset($holidayMap[$date])) {
                    $formattedAttendance[] = [
                        'date' => $date,
                        'check_in_time' => null,
                        'check_out_time' => null,
                        'status' => 'holiday',
                        'is_late' => null,
                        'late_minutes' => null,
                        'overtime' => null,
                    ];
                    continue;
                }

                // Sunday off (date('N') returns 7 for Sunday)
                if (date('N', strtotime($date)) == 7) {
                    $formattedAttendance[] = [
                        'date' => $date,
                        'check_in_time' => null,
                        'check_out_time' => null,
                        'status' => 'Week Off',
                        'is_late' => null,
                        'late_minutes' => null,
                        'overtime' => null,
                    ];
                    continue;
                }

                // Saturday off
                if (isset($saturdayOffMap[$date])) {
                    $formattedAttendance[] = [
                        'date' => $date,
                        'check_in_time' => null,
                        'check_out_time' => null,
                        'status' => 'Week Off',
                        'is_late' => null,
                        'late_minutes' => null,
                        'overtime' => null,
                    ];
                    continue;
                }

                // Attendance exists
                if (isset($attendanceByDate[$date])) {
                    $record = $attendanceByDate[$date];
                    $formattedAttendance[] = [
                        'date' => $date,
                        'check_in_time' => $record['check_in_time'],
                        'check_out_time' => $record['check_out_time'],
                        'status' => $record['status'],
                        'is_late' => $record['is_late'],
                        'late_minutes' => $record['late_minutes'],
                        'overtime' => $record['overtime'] ?? null,
                    ];
                    continue;
                }

                // ABSENT
                $formattedAttendance[] = [
                    'date' => $date,
                    'check_in_time' => null,
                    'check_out_time' => null,
                    'status' => 'absent',
                    'is_late' => null,
                    'late_minutes' => null,
                    'overtime' => null,
                ];
            }

            $attendanceWithUsers[] = [
                'user_id' => $userId,
                'employee_name' => $user['username'],
                'profile_image' => $userInfoMap[$userId]['profile_image'] ?? null,
                'attendance' => $formattedAttendance,
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'users' => $attendanceWithUsers,
                'isIncludedHoliday' => $isIncludedHoliday,
                'holidays' => $holidays,
                'saturdayOffDates' => $saturdayOffDates
            ]
        ]);
    }


    public function view()
    {
        $userModel = new UserModel();
        $attendanceModel = new AttendanceModel();
        $user = $this->authService->user(); // Get logged-in user
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }
        
        $role = $user->role; // User role
        $today = date('Y-m-d'); // Get today's date
        $currentYear = date('Y'); // Get current year
        $currentMonth = date('m'); // Get current month (numeric)
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        // Get Total Employees
        $totalEmployees = $userModel->where('role', 'employee')->countAllResults();
        $presentEmployees = $attendanceModel
            ->select('user_id')
            ->where('status', 'present')
            ->where('date', date('Y-m-d')) // Ensure it's for today
            ->distinct() // Count unique users
            ->countAllResults();

        $absentEmployees = $userModel
            ->where('role', 'employee')
            ->whereNotIn('id', function ($query) use ($today) {
                $query->select('user_id')
                    ->from('attendance')
                    ->where('date', $today);
            })
            ->countAllResults();
        $workingDays = $attendanceModel
            ->distinct()
            ->select('date')
            ->like('date', date('Y-m'))
            ->countAllResults();

        return view('attendence/view', [
            'role' => $role,
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'workingDays' => $workingDays,
            'totalDaysInMonth' => $totalDaysInMonth, // Total days in current month
        ]);
    }

    public function viewCalendar()
    {
        $userModel = new UserModel();
        $attendanceModel = new AttendanceModel();
        $user = $this->authService->user(); // Get logged-in user
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }
        
        $role = $user->role; // User role
        $today = date('Y-m-d'); // Get today's date
        $currentYear = date('Y'); // Get current year
        $currentMonth = date('m'); // Get current month (numeric)
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        // Get Total Employees
        $totalEmployees = $userModel->where('role', 'employee')->countAllResults();
        $presentEmployees = $attendanceModel
            ->select('user_id')
            ->where('status', 'present')
            ->where('date', date('Y-m-d')) // Ensure it's for today
            ->distinct() // Count unique users
            ->countAllResults();

        $absentEmployees = $userModel
            ->where('role', 'employee')
            ->whereNotIn('id', function ($query) use ($today) {
                $query->select('user_id')
                    ->from('attendance')
                    ->where('date', $today);
            })
            ->countAllResults();
        $workingDays = $attendanceModel
            ->distinct()
            ->select('date')
            ->like('date', date('Y-m'))
            ->countAllResults();

        return view('attendence/view_calendar', [
            'role' => $role,
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'workingDays' => $workingDays,
            'totalDaysInMonth' => $totalDaysInMonth, // Total days in current month
        ]);
    }

    public function getDashboardStats()
    {
        $userModel = new UserModel();
        $attendanceModel = new AttendanceModel();
        $user = $this->authService->user(); // Get logged-in user
        $role = $user->role;

        $today = date('Y-m-d');
        $currentYear = date('Y');
        $currentMonth = date('m');
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

        $totalEmployees = $userModel->where('role', 'employee')->countAllResults();

        $presentEmployees = $attendanceModel
            ->select('user_id')
            ->where('status', 'present')
            ->where('date', $today)
            ->distinct()
            ->countAllResults();

        $absentEmployees = $userModel
            ->where('role', 'employee')
            ->whereNotIn('id', function ($query) use ($today) {
                $query->select('user_id')
                    ->from('attendance')
                    ->where('date', $today);
            })
            ->countAllResults();

        $workingDays = $attendanceModel
            ->distinct()
            ->select('date')
            ->like('date', date('Y-m'))
            ->countAllResults();

        // Return as JSON
        return $this->response->setJSON([
            'role' => $role,
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'workingDays' => $workingDays,
            'totalDaysInMonth' => $totalDaysInMonth
        ]);
    }

    public function getFacePhoto()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $userInfoModel = new \App\Models\UserInfoModel();
        $userInfo = $userInfoModel->where('user_id', $user->sub)->first();

        if (!$userInfo || empty($userInfo['face_photo'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'No face photo registered. Please contact admin.',
                'face_photo' => null
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'face_photo' => base_url('upload/faces/' . $userInfo['face_photo'])
        ]);
    }

    public function faceCheckIn()
    {
        date_default_timezone_set('Asia/Kolkata');

        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if (!in_array($user->role, ['hr', 'employee'])) {
            return $this->respond(['status' => 'error', 'message' => 'Access denied for this role'], 403);
        }

        // Get the face image from request (base64)
        $json = $this->request->getJSON(true);
        $faceImage = $json['face_image'] ?? null;
        $checkInTimeInput = $json['check_in_time'] ?? date('Y-m-d H:i:s');
        $userLatitude = $json['latitude'] ?? null;
        $userLongitude = $json['longitude'] ?? null;
        $locationAccuracy = isset($json['location_accuracy']) ? (float)$json['location_accuracy'] : null;

        if (!$faceImage) {
            return $this->respond(['status' => 'error', 'message' => 'Face image is required'], 400);
        }

        // Check location if location settings are configured
        $locationSettingsModel = new LocationSettingsModel();
        $locationSettings = $locationSettingsModel->getSettings();
        
        if ($locationSettings && ($locationSettings['latitude'] != 0 || $locationSettings['longitude'] != 0)) {
            // Location verification is enabled
            if ($userLatitude === null || $userLongitude === null) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Location permission is required for check-in. Please enable location access.'
                ], 400);
            }

            // Ensure coordinates are floats
            $officeLat = (float)$locationSettings['latitude'];
            $officeLng = (float)$locationSettings['longitude'];
            $userLat = (float)$userLatitude;
            $userLng = (float)$userLongitude;

            // Calculate distance between user location and office location
            $distance = $this->calculateDistance(
                $officeLat,
                $officeLng,
                $userLat,
                $userLng
            );

            $radius = (float)$locationSettings['radius'];
            
            // Adjust radius tolerance based on location accuracy (for desktop browsers with poor GPS)
            // If accuracy is very poor (>1000m, typical for IP-based geolocation), add significant tolerance
            $effectiveRadius = $radius;
            if ($locationAccuracy !== null && $locationAccuracy > 1000) {
                $maxTotalRadius = 15000; // 15km max for desktop
                $tolerance = min($locationAccuracy * 1.5, $maxTotalRadius - $radius); // 1.5x accuracy for extra buffer
                $effectiveRadius = $radius + $tolerance;
                log_message('debug', 'Poor location accuracy detected (' . round($locationAccuracy, 2) . 'm). Adjusted radius tolerance: ' . round($effectiveRadius, 2) . 'm (original radius: ' . $radius . 'm, tolerance: ' . round($tolerance, 2) . 'm)');
            } else if ($locationAccuracy !== null && $locationAccuracy > 500) {
                // For moderate accuracy (500-1000m), add smaller tolerance
                $tolerance = min($locationAccuracy * 0.75, $radius * 1.5);
                $effectiveRadius = $radius + $tolerance;
                log_message('debug', 'Moderate location accuracy detected (' . round($locationAccuracy, 2) . 'm). Adjusted radius tolerance: ' . round($effectiveRadius, 2) . 'm');
            }
            
            // Debug info (can be removed later)
            log_message('debug', 'Location Check - Office: ' . $officeLat . ', ' . $officeLng . ' | User: ' . $userLat . ', ' . $userLng . ' | Distance: ' . $distance . 'm | Radius: ' . $radius . 'm | Effective Radius: ' . round($effectiveRadius, 2) . 'm | Accuracy: ' . ($locationAccuracy !== null ? round($locationAccuracy, 2) . 'm' : 'unknown'));
            
            // If radius is 0, check exact location (within 10 meters tolerance)
            if ($radius == 0) {
                // For exact location, also consider accuracy
                $tolerance = 10;
                if ($locationAccuracy !== null && $locationAccuracy > 100) {
                    $tolerance = min(50, $locationAccuracy * 0.3); // Max 50m tolerance for exact location
                }
                if ($distance > $tolerance) {
                    return $this->respond([
                        'status' => 'error',
                        'message' => 'You are not at the correct location. Please check in from the office location.',
                        'distance' => round($distance, 2)
                    ], 400);
                }
            } else {
                // Check if user is within the allowed radius (with tolerance for poor accuracy)
                if ($distance > $effectiveRadius) {
                    $message = 'You are outside the allowed check-in radius (' . $radius . ' meters). Current distance: ' . round($distance, 2) . ' meters.';
                    if ($locationAccuracy !== null && $locationAccuracy > 1000) {
                        $message .= ' Note: Your location accuracy is low (±' . round($locationAccuracy, 0) . 'm). On desktop, please ensure WiFi is enabled for better location accuracy, or contact admin for assistance.';
                    } else if ($locationAccuracy !== null && $locationAccuracy > 500) {
                        $message .= ' Note: Your location accuracy is moderate (±' . round($locationAccuracy, 0) . 'm). Please ensure location services are enabled.';
                    }
                    return $this->respond([
                        'status' => 'error',
                        'message' => $message,
                        'distance' => round($distance, 2),
                        'allowed_radius' => $radius,
                        'effective_radius' => round($effectiveRadius, 2),
                        'location_accuracy' => $locationAccuracy !== null ? round($locationAccuracy, 2) : null,
                        'debug' => [
                            'office_location' => ['lat' => $officeLat, 'lng' => $officeLng],
                            'user_location' => ['lat' => $userLat, 'lng' => $userLng]
                        ]
                    ], 400);
                }
            }
        }

        // Verify user has face photo registered
        $userInfoModel = new \App\Models\UserInfoModel();
        $userInfo = $userInfoModel->where('user_id', $user->sub)->first();

        if (!$userInfo || empty($userInfo['face_photo'])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'No face photo registered. Please contact admin to upload your face photo.'
            ], 400);
        }

        // Always use server's current date and time (Asia/Kolkata timezone)
        // This ensures the date is always correct regardless of client timezone
        $date = date('Y-m-d'); // Today's date in server timezone
        $timeOnly = date('H:i:s'); // Current server time (HH:MM:SS)

        // Get latest attendance for today
        $latestAttendance = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $date)
            ->orderBy('id', 'DESC')
            ->first();

        if ($latestAttendance && !$latestAttendance['check_out_time']) {
            
            $updateData = [
                'check_in_time' => $timeOnly, // Store only time (HH:MM:SS)
                'checkin_method' => 'face_recognition'
            ];

            $this->attendanceModel->update($latestAttendance['id'], $updateData);

            return $this->respond([
                'status' => 'success',
                'message' => 'Face verified! Check-in time updated successfully.',
                'data' => $updateData
            ]);
        }

        $companyRule = $this->companyRulesModel->orderBy('id', 'DESC')->first();
        // Defaults
        $break = '00:30:00';
        $isLate = 0;
        $lateMinutes = 0;
        // $halfDayHourTime = "02:00:00";
        if ($companyRule) {

            $break       = $companyRule['lunch_break'] ?? '00:30:00';
            $startTime   = $companyRule['start_time'];          // e.g. 10:00:00
            $gracePeriod = (int) $companyRule['grace_period'];  // minutes

            $checkInSeconds = $this->timeToSeconds($timeOnly);
            $startSeconds   = $this->timeToSeconds($startTime);
            $graceSeconds   = $gracePeriod * 60;

            if ($checkInSeconds > ($startSeconds + $graceSeconds)) {

                $isLate = 1;
                $lateSeconds = $checkInSeconds - ($startSeconds + $graceSeconds);
                $lateMinutes = ceil($lateSeconds / 60);

            }
        }
        
        // Create new attendance record
        $data = [
            'user_id' => $user->sub,
            'date' => $date, // Today's date from server
            'check_in_time' => $timeOnly, // Current time from server (HH:MM:SS)
            'meal_break' => $break,
            'status' => 'present',
            'checkin_method' => 'face_recognition',
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes
        ];

        // Delete any leave record for today if exists
        $this->leaveModel
            ->where('user_id', $user->sub)
            ->where('start_date', $date)
            ->where('end_date', $date)
            ->delete();

        if ($this->attendanceModel->insert($data)) {
            // Check if attendance notifications are enabled
            $notificationSettingsModel = new NotificationSettingsModel();
            if ($notificationSettingsModel->isAttendanceNotificationsEnabled()) {
                // Send push notification to admins
                $employee = $this->userModel->find($user->sub);
                $employeeName = $employee ? $employee['username'] : 'Employee';
                
                log_message('info', '📝 Employee face check-in: ' . $employeeName . ' at ' . $timeOnly);
                
                $this->pushNotificationService->notifyAdmins(
                    'Employee Check-In (Face Recognition)',
                    $employeeName . ' has checked in via face recognition at ' . $timeOnly,
                    [
                        'type' => 'checkin',
                        'user_id' => $user->sub,
                        'username' => $employeeName,
                        'time' => $timeOnly,
                        'date' => $date,
                        'method' => 'face_recognition',
                        'url' => base_url('/attendence')
                    ]
                );
            } else {
                log_message('info', '📝 Attendance notifications are disabled - skipping push notification');
            }
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Face verified! Checked in successfully via face recognition.'
            ]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Face check-in failed. Please try again.'], 500);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Delete today's attendance records for the authenticated user
     * Or delete all today's records if user is HR/Admin
     */
    public function deleteTodayAttendance()
    {
        date_default_timezone_set('Asia/Kolkata');
        
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $today = date('Y-m-d');
        
        // If user is HR or Admin, delete all today's attendance records
        if (in_array($user->role, ['hr', 'admin'])) {
            $deleted = $this->attendanceModel
                ->where('date', $today)
                ->delete();
            
            return $this->respond([
                'status' => 'success',
                'message' => "Deleted {$deleted} attendance record(s) for today ({$today})",
                'deleted_count' => $deleted
            ]);
        }
        
        // If user is employee, delete only their own today's attendance records
        $deleted = $this->attendanceModel
            ->where('user_id', $user->sub)
            ->where('date', $today)
            ->delete();
        
        return $this->respond([
            'status' => 'success',
            'message' => "Deleted {$deleted} attendance record(s) for today ({$today})",
            'deleted_count' => $deleted
        ]);
    }

    public function getDayAttendanceRecords()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['hr', 'admin'])) {
            return $this->failUnauthorized('Access denied');
        }

        $userId = $this->request->getGet('user_id');
        $date   = $this->request->getGet('date');

        if (!$userId || !$date) {
            return $this->fail('Invalid parameters');
        }

        $records = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('id', 'ASC')
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data'   => $records
        ]);
    }

    public function updateDayAttendanceRecords()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['hr', 'admin'])) {
            return $this->failUnauthorized('Access denied');
        }

        $payload = $this->request->getJSON(true);

        $userId  = $payload['user_id'] ?? null;
        $date    = $payload['date'] ?? null;
        $records = $payload['records'] ?? [];
        $manualStatus = $payload['status'] ?? null; // New: manual status override

        if (!$userId || !$date || empty($records)) {
            return $this->fail('Invalid data');
        }

        // Save records first without complex logic
        foreach ($records as $record) {
            $data = [
                'user_id' => $userId,
                'date' => $date,
                'check_in_time' => !empty($record['check_in_time']) ? $record['check_in_time'] : null,
                'check_out_time' => !empty($record['check_out_time']) ? $record['check_out_time'] : null,
            ];
            
            if (isset($record['id']) && $record['id'] !== 'new') {
                $this->attendanceModel->update($record['id'], $data);
            } else {
                if (!empty($data['check_in_time']) || !empty($data['check_out_time'])) {
                    $this->attendanceModel->insert($data);
                }
            }
        }
        
        // Remove conflicting leave
        $this->leaveModel
            ->where('user_id', $userId)
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->delete();

        $syncResult = $this->syncDayAttendance($userId, $date, $manualStatus);

        return $this->respond([
            'status' => 'success',
            'message' => 'Attendance updated successfully',
            'work_hours' => $syncResult ? $syncResult['work_hours'] : '00:00:00',
            'final_status' => $syncResult ? $syncResult['status'] : $manualStatus,
        ]);
    }

    public function bulkUpdateAttendanceRecords()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['hr', 'admin'])) {
            return $this->failUnauthorized('Access denied');
        }

        $payload = $this->request->getJSON(true);

        $userId   = $payload['user_id'] ?? null;
        $fromDate = $payload['from_date'] ?? null;
        $toDate   = $payload['to_date'] ?? null;
        $status   = $payload['status'] ?? null;

        $checkIn  = $payload['check_in_time'] ?? null;
        $checkOut = $payload['check_out_time'] ?? null;

        if (!$userId || !$fromDate || !$toDate || !$status) {
            return $this->fail('Invalid data');
        }

        // Company rules
        $companyRule = $this->companyRulesModel->orderBy('id', 'DESC')->first();
        $mealBreak   = $companyRule['lunch_break'] ?? '00:30:00';
        $startTime   = $companyRule['start_time'] ?? '09:00:00';
        $gracePeriod = (int)($companyRule['grace_period'] ?? 0);

        $start = new \DateTime($fromDate);
        $end   = new \DateTime($toDate);
        $end->modify('+1 day');

        foreach (new \DatePeriod($start, new \DateInterval('P1D'), $end) as $dt) {

            $date = $dt->format('Y-m-d');

            if (date('w', strtotime($date)) == 0) {
                continue;
            }
            
            $holidayCalendarModel = new HolidayCalendarModel();
            // Skip holidays
            if ($holidayCalendarModel->where('holiday_date', $date)->first()) {
                continue;
            }

            $workHours   = '00:00:00';
            $overtime    = '00:00:00';
            $isLate      = 0;
            $lateMinutes = 0;

            if ($checkIn) {
                $checkInSec = $this->timeToSeconds($checkIn);
                $startSec   = $this->timeToSeconds($startTime);
                $graceSec   = $gracePeriod * 60;

                if ($checkInSec > ($startSec + $graceSec)) {
                    $isLate = 1;
                    $lateMinutes = ceil(
                        ($checkInSec - ($startSec + $graceSec)) / 60
                    );
                }
            }

            if ($status !== 'absent' && $checkIn && $checkOut) {

                $calc = $this->calculateWorkHours(
                    $date,
                    $mealBreak,
                    $checkIn,
                    $checkOut
                );

                $workHours = $calc['work_hours'];
                $overtime  = $calc['overtime'];
            }

            $attendanceData = [
                'user_id'        => $userId,
                'date'           => $date,
                'check_in_time'  => $status === 'absent' ? null : $checkIn,
                'check_out_time' => $status === 'absent' ? null : $checkOut,
                'meal_break'     => $mealBreak,
                'work_hours'     => $status === 'absent' ? '00:00:00' : $workHours,
                'overtime'       => $status === 'absent' ? '00:00:00' : $overtime,
                'status'         => $status,
                'is_late'        => $isLate,
                'late_minutes'   => $lateMinutes
            ];

            $existing = $this->attendanceModel
                ->where('user_id', $userId)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $this->attendanceModel->update($existing['id'], $attendanceData);
            } else {
                $this->attendanceModel->insert($attendanceData);
            }

            // 🧹 Remove conflicting leave
            $this->leaveModel
                ->where('user_id', $userId)
                ->where('start_date <=', $date)
                ->where('end_date >=', $date)
                ->delete();
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Bulk attendance updated successfully'
        ]);
    }

    public function syncDayAttendance($userId, $date, $manualStatus = null)
    {
        $records = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($records)) return null;

        $companyRule = $this->companyRulesModel->orderBy('id', 'DESC')->first();
        $mealBreak   = $companyRule['lunch_break'] ?? '00:30:00';
        $startTime   = $companyRule['start_time'] ?? '09:00:00';
        $gracePeriod = (int)($companyRule['grace_period'] ?? 0);

        // Meal break setup
        $mb = new \DateTime($mealBreak);
        $mealSeconds = ($mb->format('H') * 3600) + ($mb->format('i') * 60) + $mb->format('s');
        $remainingMealBreak = $mealSeconds;

        $totalWorkSeconds = 0;
        $totalOvertimeSeconds = 0;
        $earliestCheckInStamp = null;
        $isLate = 0;
        $lateMinutes = 0;

        foreach ($records as &$record) {
            if (!empty($record['check_in_time'])) {
                $ciTs = strtotime($date . ' ' . $record['check_in_time']);
                if ($earliestCheckInStamp === null || $ciTs < $earliestCheckInStamp) {
                    $earliestCheckInStamp = $ciTs;
                }
            }

            $recordWorkSeconds = 0;
            if (!empty($record['check_in_time']) && !empty($record['check_out_time'])) {
                $in = strtotime($date . ' ' . $record['check_in_time']);
                $out = strtotime($date . ' ' . $record['check_out_time']);
                if ($out > $in) {
                    $duration = $out - $in;

                    // Deduct meal break sequentially
                    if ($remainingMealBreak > 0) {
                        if ($duration >= $remainingMealBreak) {
                            $duration -= $remainingMealBreak;
                            $remainingMealBreak = 0;
                        } else {
                            $remainingMealBreak -= $duration;
                            $duration = 0;
                        }
                    }
                    $recordWorkSeconds = $duration;
                    $totalWorkSeconds += $recordWorkSeconds;
                }
            }
            $record['calculated_work_seconds'] = $recordWorkSeconds;
        }

        // Overtime check on total
        $fullDayHours = (float)($companyRule['working_hours_per_day'] ?? 8);
        $halfDayHours = (float)($companyRule['half_day_hours'] ?? 5);
        $fullDaySeconds = $fullDayHours * 3600;

        $isSaturdayHalfDay = $this->isSaturdayHalfDay($date, $companyRule);
        $overtimeStartSeconds = $isSaturdayHalfDay ? ($halfDayHours * 3600) : $fullDaySeconds;

        if (!empty($companyRule) && (int)($companyRule['enable_overtime'] ?? 0) === 1) {
            if ($totalWorkSeconds > $overtimeStartSeconds) {
                $totalOvertimeSeconds = $totalWorkSeconds - $overtimeStartSeconds;
                $minOvertimeSeconds = ((float)($companyRule['min_overtime_count_in_minutes'] ?? 0)) * 60;
                if ($totalOvertimeSeconds < $minOvertimeSeconds) {
                    $totalOvertimeSeconds = 0;
                }
            }
        }

        // Late calc
        if ($earliestCheckInStamp) {
            $startSeconds = $this->timeToSeconds($startTime);
            $checkInSeconds = date('H', $earliestCheckInStamp) * 3600 + date('i', $earliestCheckInStamp) * 60 + date('s', $earliestCheckInStamp);
            $graceSeconds = $gracePeriod * 60;

            if ($checkInSeconds > ($startSeconds + $graceSeconds)) {
                $isLate = 1;
                $lateSeconds = $checkInSeconds - ($startSeconds + $graceSeconds);
                $lateMinutes = ceil($lateSeconds / 60);
            }
        }

        // Status calc
        $effectiveSeconds = $totalWorkSeconds + ($companyRule['grace_minutes'] ?? 10) * 60;
        $fullDayThreshold = 4 * 3600;
        $halfDayThreshold = 1 * 3600;
        $payrollType = $companyRule['payroll_type'] ?? 'monthly';

        if ($manualStatus) {
            $status = $manualStatus;
        } else {
            if ($payrollType === 'hourly') {
                $status = $totalWorkSeconds > 0 ? 'present' : 'absent';
            } else {
                if ($effectiveSeconds > $fullDayThreshold) {
                    $status = 'present';
                } elseif ($effectiveSeconds >= $halfDayThreshold) {
                    $status = 'half-day';
                } else {
                    $status = 'absent';
                }
            }
        }

        if ($status === 'absent') {
            $totalWorkSeconds = 0;
            // Also reset individual calculated variables
            foreach ($records as &$rec) {
                $rec['calculated_work_seconds'] = 0;
            }
            $totalOvertimeSeconds = 0;
        }

        // Update each record individually with its local work duration but GLOBAL status/late.
        // Overtime is assigned proportionally to the last record.
        $recCount = count($records);
        foreach ($records as $index => $rec) {
            $h = gmdate('H:i:s', $rec['calculated_work_seconds']);
            $ot = ($index === $recCount - 1) ? gmdate('H:i:s', $totalOvertimeSeconds) : '00:00:00';

            $this->attendanceModel->update($rec['id'], [
                'work_hours' => $h,
                'overtime' => $ot,
                'status' => $status,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes
            ]);
        }

        return [
            'status' => $status,
            'work_hours' => gmdate('H:i:s', $totalWorkSeconds),
            'overtime' => gmdate('H:i:s', $totalOvertimeSeconds)
        ];
    }
}
