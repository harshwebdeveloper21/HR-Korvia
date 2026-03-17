<?php

namespace App\Models;

use CodeIgniter\Model;

class CandidateModel extends Model
{
    protected $table = 'candidate';
    protected $primaryKey = 'id';
    protected $allowedFields = ['candidate_name', 'email', 'job_id', 'job_date',
     'phone_number', 'status','resume','notes','created_at','updated_at','created_by'];
     
    protected $useTimestamps = true;
}
