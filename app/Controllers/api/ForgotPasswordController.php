<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Models\CompanyLogoModel;
use App\Libraries\EmailService;
use CodeIgniter\RESTful\ResourceController;

class ForgotPasswordController extends ResourceController
{
    private $userModel;
    private $passwordResetModel;

    public function __construct()
    {

        // helper(['url', 'form']);

        // // Exclude resetPassword from authentication
        // if (!in_array(service('router')->methodName(), ['resetPassword'])) {
        //     $this->middleware('auth');
        // }

        $this->userModel = new UserModel();
        $this->passwordResetModel = new PasswordResetModel();
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid email address']);
        }

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email not found']);
        }

        $token = bin2hex(random_bytes(32));
        $this->passwordResetModel->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $resetLink = base_url("/reset_password?token=$token");

        try {
            $emailService = new EmailService(); // use dynamic SMTP config

            // Load company details
            $companyModel = new \App\Models\CompanyLogoModel();
            $company = $companyModel->first();

            // Fallbacks in case fields are null
            $companyName = $company['company_name'] ?? 'Your Company';
            $companyAddress = $company['company_address'] ?? 'Company Address';
            $companyPhone = $company['company_phone'] ?? '';
            $companyEmail = $company['company_email'] ?? '';

            $subject = 'Password Reset Request';

            $message = "
            <div style='max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; font-family: Arial, sans-serif; background-color: #ffffff;'>
                <div style='text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee;'>
                    <h2 style='margin: 0; color: #E66136;'>$companyName</h2>
                    <p style='margin: 5px 0; color: #666;'>Password Reset Notification</p>
                </div>
        
                <div style='padding: 20px 0;'>
                    <p style='font-size: 16px; color: #333;'>Dear <strong>{$user['username']}</strong>,</p>
                    <p style='font-size: 15px; color: #333;'>We received a request to reset your password. Click the button below to reset it:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . $resetLink . "' style='background-color: #E66136; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                    </div>
                    <p style='font-size: 14px; color: #555;'>If you didn’t request a password reset, please ignore this email. Your account will remain secure.</p>
                </div>
        
                <div style='border-top: 1px solid #eee; padding-top: 15px; text-align: center; font-size: 13px; color: #999;'>
                    <p style='margin: 0;'>© " . date('Y') . " $companyName. All rights reserved.</p>
                    <p style='margin: 0;'>$companyAddress</p>";

            if ($companyPhone) {
                $message .= "<p style='margin: 0;'>Phone: $companyPhone</p>";
            }
            if ($companyEmail) {
                $message .= "<p style='margin: 0;'>Email: $companyEmail</p>";
            }

            $message .= "
                </div>
            </div>";

            $emailService->sendForgotEmail($email, $subject, $message);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Reset link sent to your email address.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Reset Password
    public function resetPassword()
    {
        $token = $this->request->getPost('token');
        $newPassword = $this->request->getVar('new_password');
        $confirmPassword = $this->request->getVar('confirm_password');

        // Validate input
        if (!$token || !$newPassword || !$confirmPassword) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'All fields are required']);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Passwords do not match']);
        }

        // Find token in database
        $resetRecord = $this->passwordResetModel->where('token', $token)->first();
        if (!$resetRecord) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid or expired token']);
        }

        // Find user and update password
        $user = $this->userModel->where('email', $resetRecord['email'])->first();
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
        }

        // Update password
        $this->userModel->update($user['id'], ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]);

        // Delete token
        $this->passwordResetModel->where('email', $resetRecord['email'])->delete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Password reset successfully']);
    }
    public function index()
    {
        return view('dashboard/forgot_password'); // Render the login view
    }

    public function display()
    {
        return view('dashboard/reset_password'); // Render the login view
    }
}
