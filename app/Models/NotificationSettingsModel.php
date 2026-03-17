<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationSettingsModel extends Model
{
    protected $table = 'notification_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'attendance_notifications_enabled',
        'leave_notifications_enabled',
        'birthday_notifications_enabled',
        'checkout_reminder_enabled',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get the current notification settings
     * @return array|null
     */
    public function getSettings()
    {
        return $this->first();
    }

    /**
     * Check if attendance notifications are enabled
     * @return bool
     */
    public function isAttendanceNotificationsEnabled()
    {
        try {
            $settings = $this->getSettings();
            if (!$settings) {
                // If no settings exist, default to enabled
                return true;
            }
            return (bool)$settings['attendance_notifications_enabled'];
        } catch (\Exception $e) {
            // If table doesn't exist, default to enabled
            log_message('warning', 'NotificationSettingsModel: Error checking attendance notifications - ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Check if leave notifications are enabled
     * @return bool
     */
    public function isLeaveNotificationsEnabled()
    {
        try {
            $settings = $this->getSettings();
            if (!$settings) {
                // If no settings exist, default to enabled
                return true;
            }
            return (bool)($settings['leave_notifications_enabled'] ?? true);
        } catch (\Exception $e) {
            // If table doesn't exist or column doesn't exist, default to enabled
            log_message('warning', 'NotificationSettingsModel: Error checking leave notifications - ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Check if birthday notifications are enabled
     * @return bool
     */
    public function isBirthdayNotificationsEnabled()
    {
        try {
            $settings = $this->getSettings();
            if (!$settings) {
                // If no settings exist, default to enabled
                return true;
            }
            return (bool)($settings['birthday_notifications_enabled'] ?? true);
        } catch (\Exception $e) {
            // If table doesn't exist or column doesn't exist, default to enabled
            log_message('warning', 'NotificationSettingsModel: Error checking birthday notifications - ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Check if checkout reminder notifications are enabled
     * @return bool
     */
    public function isCheckoutReminderEnabled()
    {
        try {
            $settings = $this->getSettings();
            if (!$settings) {
                // If no settings exist, default to enabled
                return true;
            }
            return (bool)($settings['checkout_reminder_enabled'] ?? true);
        } catch (\Exception $e) {
            // If table doesn't exist or column doesn't exist, default to enabled
            log_message('warning', 'NotificationSettingsModel: Error checking checkout reminder notifications - ' . $e->getMessage());
            return true;
        }
    }
}

