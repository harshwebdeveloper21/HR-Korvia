<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveTypeModel extends Model
{
    protected $table = 'leave_type';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'leave_type','number_of_leaves', 'allow_half_day', 'requires_approval', 'created_by',
    ];
    protected $useTimestamps = true;
}
