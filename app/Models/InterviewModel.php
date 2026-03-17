<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewModel extends Model
{
    protected $table      = 'interviews';
    protected $primaryKey = 'id';
    protected $allowedFields = ['candidate_id', 'job_id', 'description', 'status', 'schedule_date','created_by'];

    protected $useTimestamps = true;
}
