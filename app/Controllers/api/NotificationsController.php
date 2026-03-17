<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;
use App\Models\NotificationModel;
use App\Services\AuthService;

class NotificationsController extends ResourceController
{
    protected $authService;
    protected $notificationModel;

    public function __construct()
    {   
        $this->authService = new AuthService(service('request'));
        $this->notificationModel = new NotificationModel();
    }
    public function clearAll()
    {
        $user = $this->authService->check(); // Ensure token-based
        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }
 
        // Extract user ID from JWT
      
 
         $userId = $user->sub;
 
        $notificationModel = new NotificationModel();
        $notificationModel->where('recipient_id', $userId)
                          ->set(['is_read' => 1])
                          ->update();
 
        return $this->respond(['status' => 'success', 'message' => 'All notifications marked as read.']);
    }

    public function getNotifications()
    {
        $user = $this->authService->check(); // Ensure token-based
        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        $userId = $user->sub;

        $notificationModel = new NotificationModel();

        $notifications = $notificationModel
            // ->where('recipient_id', $userId)
            ->where('recipient_id', $user->sub) // ✅ Show only this user's notifications
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $count = $notificationModel
            ->where('recipient_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'count' => $count,
            'notifications' => $notifications,
        ]);
    }
    public function markAsRead($id)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        // Make sure only the recipient can mark it read
        $notification = $this->notificationModel->find($id);
        if (!$notification || $notification['recipient_id'] != $user->sub) {
            return $this->failNotFound('Notification not found');
        }

        $this->notificationModel->update($id, ['is_read' => 1]);

        return $this->respond(['status' => 'success', 'message' => 'Notification marked as read']);
    }
    public function display()
    {
        return view('notifications/view');
    }
    public function getNotificationsAll()
    {
        $user = $this->authService->check(); // Ensure token-based
        if (!$user) {
            return $this->failUnauthorized('Unauthorized');
        }

        $userId = $user->sub;

        $notificationModel = new NotificationModel();

        $notifications = $notificationModel
            // ->where('recipient_id', $userId)
            ->where('recipient_id', $user->sub) // ✅ Show only this user's notifications
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $count = $notificationModel
            ->where('recipient_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'count' => $count,
            'notifications' => $notifications,
        ]);
    }
}
