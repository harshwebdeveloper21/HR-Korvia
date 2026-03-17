<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignationModel extends Model
{
    protected $table = 'designation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'designation_name','department_id', 'created_at',
    ];
    protected $useTimestamps = true;
    
    public function getDesignationsWithDepartment()
    {
        return $this->select('designation.*, department.department_name')
                    ->join('department', 'department.id = designation.department_id')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
