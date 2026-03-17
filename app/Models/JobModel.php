<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    // The table associated with the model
    protected $table = 'jobs';

    // The primary key of the table
    protected $primaryKey = 'id';

    // Fields that can be mass-assigned
    protected $allowedFields = [
        'job_title',
        'description',
        'department_id',
        'addresses_id',
        'status',
        'locations_id',
        'age',
        'gender',
        'job_type',
        'experience',
        'salary_range',
        'post_date',
        'close_date',
        'created_at',
        'updated_at',
        'created_by',
    ];

    // Enable automatic handling of timestamps
    protected $useTimestamps = true;

   
}
