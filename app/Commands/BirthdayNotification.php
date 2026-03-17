<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\UserInfoModel;
use App\Models\NotificationSettingsModel;
use App\Services\PushNotificationService;

class BirthdayNotification extends BaseCommand
{
    protected $group       = 'HR';
    protected $name        = 'notify:birthday';
    protected $description = 'Notify all users about today employee birthdays';

    public function run(array $params)
    {
        date_default_timezone_set('Asia/Kolkata');

        // Check if birthday notifications are enabled
        try {
            $notificationSettingsModel = new NotificationSettingsModel();
            if (!$notificationSettingsModel->isBirthdayNotificationsEnabled()) {
                CLI::write('Birthday notifications are disabled. Skipping...', 'yellow');
                return;
            }
        } catch (\Exception $e) {
            // If table doesn't exist, default to enabled
            log_message('warning', 'Notification settings table not found, sending birthday notifications by default');
        }

        $today = date('m-d'); // match only month-day
        $todayFull = date('Y-m-d');

        $userInfoModel = new UserInfoModel();
        $pushService = new PushNotificationService();
        $userModel = new UserModel();

        // Fetch employees whose birthday is today (using date_of_birth field)
        $employees = $userInfoModel
            ->select('user_info.id, user_info.user_id, user_info.firstname, user_info.lastname, user_info.date_of_birth')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->where("DATE_FORMAT(user_info.date_of_birth, '%m-%d')", $today)
            ->whereIn('users.role', ['employee', 'hr']) // Only employees and HR, not admins
            ->where('user_info.date_of_birth IS NOT NULL')
            ->where('user_info.date_of_birth !=', '0000-00-00')
            ->findAll();

        if (empty($employees)) {
            CLI::write('No birthdays today 🎂', 'yellow');
            return;
        }

        $notificationCount = 0;
        foreach ($employees as $employee) {
            $fullName = trim($employee['firstname'] . ' ' . $employee['lastname']);
            if (empty($fullName)) {
                $fullName = 'Employee #' . $employee['user_id'];
            }

            $message = '🎉 Today is ' . $fullName . '\'s birthday!';

            $result = $pushService->notifyAllUsers(
                'Employee Birthday',
                $message,
                [
                    'type'     => 'birthday',
                    'user_id'  => $employee['user_id'],
                    'username' => $fullName,
                    'date'     => $todayFull,
                    'url'      => base_url('/employee/profile/' . $employee['user_id'])
                ]
            );

            if ($result['success'] > 0) {
                CLI::write('✅ Notified all users for: ' . $fullName . ' (' . $result['success'] . ' notification(s) sent)', 'green');
                $notificationCount++;
            } else {
                CLI::write('⚠️ Failed to notify for: ' . $fullName, 'yellow');
            }
        }

        if ($notificationCount > 0) {
            CLI::write("Birthday notifications sent successfully ✅ ({$notificationCount} employee(s))", 'blue');
        } else {
            CLI::write('No notifications were sent (no user subscriptions found)', 'yellow');
        }
    }
}
