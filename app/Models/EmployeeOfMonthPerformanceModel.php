<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeOfMonthPerformanceModel extends Model
{
    protected $table = 'employee_of_month_certificates'; // Your table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'template_id',
        'month_year',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true; // Enables auto handling of created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
