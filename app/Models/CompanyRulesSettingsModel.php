<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyRulesSettingsModel extends Model
{
    protected $table            = 'company_rule_setting';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'key_name',
        'value',
    ];

    protected $returnType       = 'array';
}
