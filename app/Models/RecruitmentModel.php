<?php

namespace App\Models;

use CodeIgniter\Model;

class RecruitmentModel extends Model
{
    protected $table = 'recruitments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['position', 'candidate_name', 'interview_date', 'status'];
}
