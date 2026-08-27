<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyLogoModel extends Model
{
    protected $table = 'company_logo';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'logo_img',
        'favicon_icon',
        'company_name',
        'company_address',      // Added
        'company_phone',        // Added
        'company_email',        // Added
        'created_by'
    ];
}
?>
