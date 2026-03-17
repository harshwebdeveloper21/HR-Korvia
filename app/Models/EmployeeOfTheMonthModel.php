<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeOfTheMonthModel extends Model
{
    protected $table = 'emp_of_month';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'emp_image', 'content', 'created_by'];
    protected $useTimestamps = true;
}
