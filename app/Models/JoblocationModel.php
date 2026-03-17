<?php

namespace App\Models;

use CodeIgniter\Model;

class JoblocationModel extends Model
{
    protected $table = 'job_location';
    protected $primaryKey = 'location_id';
    protected $allowedFields = [
        'job_location', 'created_at','updated_at',
    ];
    protected $useTimestamps = true;
}
