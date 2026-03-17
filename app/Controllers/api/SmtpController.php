<?php

namespace App\Controllers\Api;

use App\Models\SmtpModel;
use CodeIgniter\RESTful\ResourceController;

class SmtpController extends ResourceController
{
    protected $modelName = 'App\Models\SmtpModel';
    protected $format    = 'json';
    // Define smtpModel as an instance of the SmtpModel
    protected $smtpModel;
    public function __construct()
    {
        // Initialize the smtpModel
        $this->smtpModel = new SmtpModel();
    }

    public function updateSmtpSettings()
    {
        // Define custom error messages
        $validationMessages = [
            'smtp_host' => [
                'required' => 'SMTP host is required.',
            ],
            'smtp_port' => [
                'required' => 'SMTP port is required.',
                'numeric'  => 'SMTP port must be a number.',
            ],
            'smtp_username' => [
                'required' => 'SMTP username is required.',
            ],
            'smtp_password' => [
                'required' => 'SMTP password is required.',
            ],
            'smtp_encryption' => [
                'required' => 'SMTP encryption is required.',
            ],
            'smtp_from_email' => [
                'required'    => 'From Email is required.',
                'valid_email' => 'Please provide a valid email address for the From Email.',
            ],
            'smtp_from_name' => [
                'required' => 'From Name is required.',
            ]
        ];

        // Simple validation for required fields with custom error messages
        if (!$this->validate([
            'smtp_host'       => 'required',
            'smtp_port'       => 'required|numeric',
            'smtp_username'   => 'required',
            'smtp_password'   => 'required',
            'smtp_encryption' => 'required',
            'smtp_from_email' => 'required|valid_email',
            'smtp_from_name'  => 'required',
        ], $validationMessages)) {
            // Return custom validation errors
            return $this->respond([
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // If validation passes, continue with the rest of the code
        $data = [
            'sent_mail_enable'=> $this->request->getPost('sent_mail_enable') ? 1 : 0,
            'smtp_protocol'   => $this->request->getPost('smtp_protocol'),
            'smtp_host'       => $this->request->getPost('smtp_host'),
            'smtp_port'       => $this->request->getPost('smtp_port'),
            'smtp_username'   => $this->request->getPost('smtp_username'),
            'smtp_password'   => $this->request->getPost('smtp_password'),
            'smtp_encryption' => $this->request->getPost('smtp_encryption'),
            'smtp_from_email' => $this->request->getPost('smtp_from_email'),
            'smtp_from_name'  => $this->request->getPost('smtp_from_name'),
        ];

        if ($this->model->update(1, $data)) {
            return $this->respond(['message' => 'SMTP settings updated successfully']);
        } else {
            return $this->respond(['message' => 'Failed to update SMTP settings'], 400);
        }
    }


    public function display()
    {
        return view('mail/welcome_mail'); // Render the login view
    }
    public function view()
    {
        $smtpSettings = $this->model->first(); // Get the first record from the database
        return view('mail/smtp_email', ['smtpSettings' => $smtpSettings]); // Render the login view
    }
    public function getSmtpSettings()
    {
        // Retrieve the SMTP settings from the database
        $smtpSettings = $this->smtpModel->find(1); // Assuming you only have one record or it's stored under id 1

        if ($smtpSettings) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $smtpSettings
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'SMTP settings not found'
            ]);
        }
    }
}
