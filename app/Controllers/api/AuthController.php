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

        // ✅ Remember Me (SEPARATE TOKEN)
        if ($this->request->getPost("remember_me")) {
            helper("text");

            $rememberToken = random_string("crypto", 64);
            $rememberTokenHash = hash("sha256", $rememberToken);

            db_connect()
                ->table("remember_tokens")
                ->insert([
                    "user_id" => $user["id"],
                    "token_hash" => $rememberTokenHash,
                    "expires_at" => date("Y-m-d H:i:s", strtotime("+30 days")),
                ]);

            // Set secure flag based on current connection (false for localhost/HTTP, true for HTTPS)
            $secure = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

            setcookie(
                "remember_me_token",
                $rememberToken,
                time() + 86400 * 30,
                "/",
                "",
                $secure,
                true,
            );
        }

        // Update status
        $this->userModel->update($user["id"], [
            "chat_status" => "online",
            "last_activity" => date("Y-m-d H:i:s"),
        ]);

        return $this->respond([
            "status" => "success",
            "token" => $jwtToken, // ✅ REAL JWT
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
        $request = service("request");
        $session = session();
        $db = db_connect();

        $user = $this->authService->user();

        if ($user) {
            $this->userModel->update($user->sub, [
                "chat_status" => "offline",
            ]);

            $pushSubscriptionModel = new \App\Models\PushSubscriptionModel();
            $pushSubscriptionModel->where("user_id", $user->sub)->delete();
        }

        $rememberToken = $request->getCookie("remember_me_token");

        if ($rememberToken) {
            $deleted = $db
                ->table("remember_tokens")
                ->where("token_hash", hash("sha256", $rememberToken))
                ->delete();

            log_message("debug", "Remember-me token delete count: " . $deleted);

            setcookie(
                "remember_me_token",
                "",
                time() - 3600,
                "/",
                "",
                is_https(), // match secure flag
                true,
            );
        } else {
            log_message("debug", "No remember-me cookie found on logout");
        }

        $this->authService->logout();
        $session->destroy();

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
        $userRecord = $this->userModel->find($user->sub);
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
        $this->userModel->update($user->sub, ["password" => $hashedPassword]);

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

        $userId = $user->sub; // Or use session
        $this->userModel->update($userId, [
            "last_activity" => date("Y-m-d H:i:s"),
        ]);

        return $this->respond(["status" => "success"]);
    }
}
