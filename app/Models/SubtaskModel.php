<?php

namespace App\Models;

use CodeIgniter\Model;

class SubtaskModel extends Model
{
    protected $table = 'subtasks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['task_id','user_id', 'subtask_title', 'subtask_status',
    'subtask_assigned_date', 'subtask_due_date', 'created_by','files','description'];
    protected $useTimestamps = true;
}
