<?php

namespace App\Models;

use CodeIgniter\Model;

class SmtpModel extends Model
{
    protected $table = 'smtp_settings';
    protected $primaryKey = 'smtp_id';
    protected $allowedFields = [
        'smtp_protocol',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_email',
        'smtp_from_name',
        'sent_mail_enable'
    ];

    public function getSettings()
    {
        return $this->first(); // Fetch the first SMTP record
    }
}
