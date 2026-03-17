<?php

namespace App\Controllers\Api;

use App\Models\NotificationSettingsModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use CodeIgniter\Config\Services;

class NotificationSettingsController extends ResourceController
{
    protected $notificationSettingsModel;
    protected $authService;

    public function __construct()
    {
        $this->notificationSettingsModel = new NotificationSettingsModel();
        $this->authService = new AuthService(service('request'));
    }

    /**
     * Get current notification settings
     */
    public function getSettings()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        try {
            $settings = $this->notificationSettingsModel->getSettings();
            
            if (!$settings) {
                // Return default enabled if no settings exist
                return $this->respond([
                    'status' => 'success',
                    'data' => [
                        'attendance_notifications_enabled' => true,
                        'leave_notifications_enabled' => true,
                        'birthday_notifications_enabled' => true
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // If table doesn't exist, return default enabled
            log_message('error', 'Notification settings table not found: ' . $e->getMessage());
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'attendance_notifications_enabled' => true,
                    'leave_notifications_enabled' => true
                ],
                'message' => 'Please run the SQL file: db/notification_settings.sql to create the table'
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'id' => $settings['id'],
                'attendance_notifications_enabled' => (bool)$settings['attendance_notifications_enabled'],
                'leave_notifications_enabled' => (bool)($settings['leave_notifications_enabled'] ?? true),
                'birthday_notifications_enabled' => (bool)($settings['birthday_notifications_enabled'] ?? true)
            ]
        ]);
    }

    /**
     * Update notification settings (admin only)
     */
    public function updateSettings()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Only admin can update notification settings'], 403);
        }

        $json = $this->request->getJSON(true);
        $attendanceEnabled = $json['attendance_notifications_enabled'] ?? null;
        $leaveEnabled = $json['leave_notifications_enabled'] ?? null;
        $birthdayEnabled = $json['birthday_notifications_enabled'] ?? null;

        // Validation - at least one field should be provided
        if ($attendanceEnabled === null && $leaveEnabled === null && $birthdayEnabled === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'At least one notification setting is required'
            ], 400);
        }

        // Get existing settings or create new
        $existing = $this->notificationSettingsModel->getSettings();
        
        $data = [];
        
        // Only update fields that are provided
        if ($attendanceEnabled !== null) {
            $attendanceValue = $attendanceEnabled === true || $attendanceEnabled === 'true' || $attendanceEnabled === 1 || $attendanceEnabled === '1' ? 1 : 0;
            $data['attendance_notifications_enabled'] = $attendanceValue;
        }
        
        if ($leaveEnabled !== null) {
            $leaveValue = $leaveEnabled === true || $leaveEnabled === 'true' || $leaveEnabled === 1 || $leaveEnabled === '1' ? 1 : 0;
            $data['leave_notifications_enabled'] = $leaveValue;
        }
        
        if ($birthdayEnabled !== null) {
            $birthdayValue = $birthdayEnabled === true || $birthdayEnabled === 'true' || $birthdayEnabled === 1 || $birthdayEnabled === '1' ? 1 : 0;
            $data['birthday_notifications_enabled'] = $birthdayValue;
        }

        if ($existing) {
            $updated = $this->notificationSettingsModel->update($existing['id'], $data);
            if (!$updated) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Failed to update notification settings',
                    'errors' => $this->notificationSettingsModel->errors()
                ], 500);
            }
        } else {
            $insertId = $this->notificationSettingsModel->insert($data);
            if (!$insertId) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Failed to save notification settings',
                    'errors' => $this->notificationSettingsModel->errors()
                ], 500);
            }
        }

        // Verify the saved data
        $savedSettings = $this->notificationSettingsModel->getSettings();

        $attendanceStatus = isset($data['attendance_notifications_enabled']) ? ($data['attendance_notifications_enabled'] ? 'ENABLED' : 'DISABLED') : 'UNCHANGED';
        $leaveStatus = isset($data['leave_notifications_enabled']) ? ($data['leave_notifications_enabled'] ? 'ENABLED' : 'DISABLED') : 'UNCHANGED';
        $birthdayStatus = isset($data['birthday_notifications_enabled']) ? ($data['birthday_notifications_enabled'] ? 'ENABLED' : 'DISABLED') : 'UNCHANGED';
        
        log_message('info', 'Notification settings updated - Attendance: ' . $attendanceStatus . ', Leave: ' . $leaveStatus . ', Birthday: ' . $birthdayStatus);

        return $this->respond([
            'status' => 'success',
            'message' => 'Notification settings updated successfully',
            'data' => [
                'attendance_notifications_enabled' => (bool)$savedSettings['attendance_notifications_enabled'],
                'leave_notifications_enabled' => (bool)($savedSettings['leave_notifications_enabled'] ?? true),
                'birthday_notifications_enabled' => (bool)($savedSettings['birthday_notifications_enabled'] ?? true)
            ]
        ]);
    }

    /**
     * View page for notification settings (admin only)
     */
    public function view()
    {
        $user = $this->authService->user();
        if (!$user || $user->role !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        return view('settings/notification_settings', [
            'role' => $user->role
        ]);
    }
}

