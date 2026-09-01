<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;

class AuthController extends ResourceController
{
    protected $authService;
    protected $userModel;

    public function __construct()
    {
        $this->authService = new AuthService(service("request"));
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if ($this->authService->check()) {
            return redirect()->to("/dashboard"); // Redirect to dashboard if already logged in
        }
        return view("dashboard/login"); // Render the login view
    }

    public function login()
    {
        $rules = [
            "email" => "required|valid_email",
            "password" => "required|min_length[6]",
        ];

        if (!$this->validate($rules)) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => "Validation failed",
                    "errors" => $this->validator->getErrors(),
                ],
                400,
            );
        }

        $email = $this->request->getVar("email");
        $password = $this->request->getVar("password");

        $user = $this->userModel->where("email", $email)->first();

        if (!$user || !password_verify($password, $user["password"])) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => "Invalid email or password",
                ],
                401,
            );
        }

        // Check if user is marked as deleted in users table
        if (!empty($user['is_deleted']) && $user['is_deleted'] == 1) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => "Your account has been removed. Please contact the administrator.",
                ],
                403
            );
        }

        // Check if user account status in user_info table
        $userInfoModel = new \App\Models\UserInfoModel();
        $userInfo = $userInfoModel->where('user_id', $user['id'])->first();

        if ($userInfo && !empty($userInfo['status'])) {
            $statusLower = strtolower(trim($userInfo['status']));
            if ($statusLower !== 'active') {
                $statusMsg = "Your account is marked as " . ucfirst($statusLower) . ". Login is not allowed.";
                if ($statusLower === 'resigned') {
                    $statusMsg = "Your account is marked as Resigned. Login is no longer allowed.";
                } elseif ($statusLower === 'fired') {
                    $statusMsg = "Your account has been terminated (Fired). Access denied.";
                } elseif ($statusLower === 'removed') {
                    $statusMsg = "Your account has been removed. Login is not allowed.";
                } elseif ($statusLower === 'inactive') {
                    $statusMsg = "Your account is currently Inactive. Please contact HR or Administrator.";
                }
                return $this->respond(
                    [
                        "status" => "error",
                        "message" => $statusMsg,
                    ],
                    403
                );
            }
        }

        // ✅ JWT token
        $jwtToken = $this->authService->attempt($email, $password);

        if (!$jwtToken) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => "Invalid credentials",
                ],
                401,
            );
        }

        // ✅ Remember Me
        if ($this->request->getPost("remember_me")) {
            $this->authService->rememberUser($user["id"]);
        }

        // Update status
        $this->userModel->update($user["id"], [
            "chat_status" => "online",
            "last_activity" => date("Y-m-d H:i:s"),
        ]);

        return $this->respond([
            "status" => "success",
            "token" => $jwtToken,
            "role" => $user["role"],
            "redirect" => "/dashboard",
        ]);
    }

    public function profile()
    {
        if (!$this->authService->check()) {
            return $this->failUnauthorized("Unauthorized: Token expired");
        }

        $user = $this->authService->user();
        return $this->respond($user);
    }

   
    public function logout()
    {
        $user = $this->authService->user();

        $userId = null;
        if (is_object($user)) {
            $userId = $user->sub ?? $user->id ?? null;
        } elseif (is_array($user)) {
            $userId = $user['sub'] ?? $user['id'] ?? null;
        }

        if ($userId) {
            $this->userModel->update($userId, [
                "chat_status" => "offline",
            ]);

            $pushSubscriptionModel = new \App\Models\PushSubscriptionModel();
            $pushSubscriptionModel->where("user_id", $userId)->delete();
        }

        $this->authService->logout();

        return redirect()
            ->to("/login")
            ->with("message", "You have been logged out.");
    }


    public function display()
    {
        return view("dashboard/change_password"); // Render the login view
    }
    public function changePassword()
    {
        // Check if user is authenticated
        $user = $this->authService->user();
        if (!$user) {
            return $this->failUnauthorized("Unauthorized access");
        }

        $currentPassword = $this->request->getVar("current_password");
        $newPassword = $this->request->getVar("new_password");

        // Validate current password
        $userId = null;
        if (is_object($user)) {
            $userId = $user->sub ?? $user->id ?? null;
        } elseif (is_array($user)) {
            $userId = $user['sub'] ?? $user['id'] ?? null;
        }
        
        $userRecord = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $userRecord["password"])) {
            return $this->failValidationErrors(
                "Current password is incorrect.",
            );
        }

        // Validate new password
        if (strlen($newPassword) < 6) {
            return $this->failValidationErrors(
                "New password must be at least 6 characters long.",
            );
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->update($userId, ["password" => $hashedPassword]);

        return $this->respond([
            "status" => "success",
            "message" => "Password changed successfully.",
        ]);
    }
    public function updateActivity()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(
                ["status" => "error", "message" => "Unauthorized"],
                401,
            );
        }

        $userId = null;
        if (is_object($user)) {
            $userId = $user->sub ?? $user->id ?? null;
        } elseif (is_array($user)) {
            $userId = $user['sub'] ?? $user['id'] ?? null;
        }

        if (!$userId) {
             return $this->respond(
                ["status" => "error", "message" => "Invalid user payload"],
                400,
            );
        }

        $this->userModel->update($userId, [
            "last_activity" => date("Y-m-d H:i:s"),
        ]);

        return $this->respond(["status" => "success"]);
    }
}
