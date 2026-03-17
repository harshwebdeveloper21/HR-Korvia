<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainingModel extends Model
{
    protected $table = 'training';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'training_title', 'user_id', 'description','department_id', 'start_date', 'end_date', 'location', 'created_at','updated_at'
    ];
    protected $useTimestamps = true;
}
