<?php

namespace App\Controllers;

use App\Libraries\EmailService;
use CodeIgniter\RESTful\ResourceController;

class SMTPEmailController extends ResourceController
{
    public function sendTestEmail()
    {
        $emailService = new EmailService();

        $to = $this->request->getPost('to');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        $response = $emailService->sendEmail($to, $subject, $message);
        return $this->respond($response);
    }
}
