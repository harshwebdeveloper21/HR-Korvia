<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'task';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','task_title','department_id','description','document','task_status','assigned_date','due_date','created_by'];
    protected $useTimestamps = true;

  
}
