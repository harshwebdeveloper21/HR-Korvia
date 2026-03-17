<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\CompanyRulesModel;
use App\Models\NotificationSettingsModel;
use App\Services\PushNotificationService;

class CheckoutReminder extends BaseCommand
{
    protected $group       = 'HR';
    protected $name        = 'notify:checkout-reminder';
    protected $description = 'Remind employees to checkout 10 minutes before office end time';

    public function run(array $params)
    {
        date_default_timezone_set('Asia/Kolkata');

        // Check if checkout reminder notifications are enabled
        try {
            $notificationSettingsModel = new NotificationSettingsModel();
            if (!$notificationSettingsModel->isCheckoutReminderEnabled()) {
                CLI::write('Checkout reminder notifications are disabled. Skipping...', 'yellow');
                return;
            }
        } catch (\Exception $e) {
            // If table doesn't exist, default to enabled
            log_message('warning', 'Notification settings table not found, sending checkout reminders by default');
        }

        // Get company rules to find office end time
        $companyRulesModel = new CompanyRulesModel();
        $rules = $companyRulesModel->orderBy('id', 'DESC')->first();

        if (!$rules || empty($rules['end_time'])) {
            CLI::write('Office end time not configured. Skipping checkout reminders...', 'yellow');
            return;
        }

        $endTime = $rules['end_time']; // Format: HH:MM:SS or HH:MM
        $today = date('Y-m-d');
        
        // Parse end time
        $endTimeParts = explode(':', $endTime);
        $endHour = (int)$endTimeParts[0];
        $endMinute = (int)($endTimeParts[1] ?? 0);
        
        // Calculate 10 minutes before end time
        $reminderTime = $endMinute - 10;
        $reminderHour = $endHour;
        
        if ($reminderTime < 0) {
            $reminderTime += 60;
            $reminderHour -= 1;
        }
        
        // Get current time
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        
        // Check if current time is exactly 10 minutes before end time (with 1 minute tolerance)
        $timeDifference = ($endHour * 60 + $endMinute) - ($currentHour * 60 + $currentMinute);
        
        if ($timeDifference < 9 || $timeDifference > 11) {
            // Not the right time (should be 10 minutes before, allow 9-11 minute range)
            CLI::write("Current time: {$currentHour}:{$currentMinute}, End time: {$endHour}:{$endMinute}, Difference: {$timeDifference} minutes. Not sending reminders yet.", 'yellow');
            return;
        }

        CLI::write("⏰ It's 10 minutes before office end time ({$endHour}:{$endMinute}). Sending checkout reminders...", 'blue');

        // Find all employees who are checked in but not checked out today
        $attendanceModel = new AttendanceModel();
        $userModel = new UserModel();
        $pushService = new PushNotificationService();

        $checkedInEmployees = $attendanceModel
            ->select('attendance.user_id, attendance.check_in_time, users.username, users.id as user_id')
            ->join('users', 'users.id = attendance.user_id', 'left')
            ->where('attendance.date', $today)
            ->where('attendance.check_in_time IS NOT NULL')
            ->where('attendance.check_in_time !=', '')
            ->where('(attendance.check_out_time IS NULL OR attendance.check_out_time = "")')
            ->whereIn('users.role', ['employee', 'hr']) // Only employees and HR
            ->findAll();

        if (empty($checkedInEmployees)) {
            CLI::write('No employees need checkout reminders (all have checked out or not checked in today)', 'yellow');
            return;
        }

        $notificationCount = 0;
        $failedCount = 0;

        foreach ($checkedInEmployees as $employee) {
            $employeeName = $employee['username'] ?? 'Employee';
            $userId = $employee['user_id'];
            
            $message = "⏰ Don't forget to checkout! Office time ends in 10 minutes ({$endHour}:{$endMinute})";

            $result = $pushService->notifyUser(
                $userId,
                'Checkout Reminder',
                $message,
                [
                    'type' => 'checkout_reminder',
                    'user_id' => $userId,
                    'username' => $employeeName,
                    'end_time' => $endTime,
                    'date' => $today,
                    'url' => base_url('/attendence')
                ]
            );

            if ($result['success'] > 0) {
                CLI::write("✅ Sent checkout reminder to: {$employeeName}", 'green');
                $notificationCount++;
            } else {
                CLI::write("⚠️ Failed to send reminder to: {$employeeName} (no subscription found)", 'yellow');
                $failedCount++;
            }
        }

        if ($notificationCount > 0) {
            CLI::write("Checkout reminders sent successfully ✅ ({$notificationCount} employee(s))", 'green');
        } else {
            CLI::write("No reminders sent ({$failedCount} employee(s) without subscriptions)", 'yellow');
        }
    }
}









