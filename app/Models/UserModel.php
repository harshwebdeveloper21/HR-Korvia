<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'role', 'created_at','is_read','is_deleted','chat_status','last_activity'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    public function getEmployeeReport($filters)
    {
        $builder = $this->db->table('users u')
            ->select('ui.firstname, ui.lastname, ui.joining_date,
                    d.department_name, des.designation_name, u.is_deleted, u.updated_at')
            ->join('user_info ui', 'ui.user_id = u.id')
            ->join('department d', 'd.id = ui.department_id', 'left')
            ->join('designation des', 'des.id = ui.designation_id', 'left')
            // ->where('u.is_deleted', 0)
            ->whereIn('u.role', ['employee', 'hr']);

        if (!empty($filters['department_id'])) {
            $builder->where('ui.department_id', $filters['department_id']);
        }

        if (!empty($filters['employee_id'])) {
            $builder->where('u.id', $filters['employee_id']);
        }

        if (!empty($filters['joining_from'])) {
            $builder->where('ui.joining_date >=', $filters['joining_from']);
        }

        if (!empty($filters['joining_to'])) {
            $builder->where('ui.joining_date <=', $filters['joining_to']);
        }

        if (!empty($filters['year'])) {
            $builder->where('YEAR(ui.joining_date)', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $builder->where('MONTH(ui.joining_date)', $filters['month']);
        }

        return $builder->orderBy('ui.joining_date', 'DESC')->get()->getResultArray();
    }

    public function getDepartmentWiseEmployeeCount()
    {
        $builder = $this->db->table('users u')
            ->select('d.id as department_id, d.department_name, COUNT(u.id) as employee_count')
            ->join('user_info ui', 'ui.user_id = u.id')
            ->join('department d', 'd.id = ui.department_id')
            ->where('u.role', 'employee')
            ->groupBy('d.id, d.department_name')
            ->orderBy('d.department_name');
        
        return $builder->get()->getResultArray();
    }
    
    public function getEmployeeSummary($filters)
    {
        $builder = $this->db->table('users u')
            ->select('COUNT(u.id) as total_employees')
            ->join('user_info ui', 'ui.user_id = u.id')
            ->where('u.role', 'employee');

        if (!empty($filters['department_id'])) {
            $builder->where('ui.department_id', $filters['department_id']);
        }

        if (!empty($filters['employee_id'])) {
            $builder->where('u.id', $filters['employee_id']);
        }

        if (!empty($filters['joining_from'])) {
            $builder->where('ui.joining_date >=', $filters['joining_from']);
        }

        if (!empty($filters['joining_to'])) {
            $builder->where('ui.joining_date <=', $filters['joining_to']);
        }

        if (!empty($filters['year'])) {
            $builder->where('YEAR(ui.joining_date)', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $builder->where('MONTH(ui.joining_date)', $filters['month']);
        }

        $totalResult = $builder->get()->getRowArray();

        // Department wise count
        $deptBuilder = $this->db->table('users u')
            ->select('d.department_name, COUNT(u.id) as total')
            ->join('user_info ui', 'ui.user_id = u.id')
            ->join('department d', 'd.id = ui.department_id')
            ->where('u.role', 'employee')
            ->groupBy('d.id, d.department_name')
            ->orderBy('d.department_name');

        if (!empty($filters['department_id'])) {
            $deptBuilder->where('ui.department_id', $filters['department_id']);
        }

        if (!empty($filters['employee_id'])) {
            $deptBuilder->where('u.id', $filters['employee_id']);
        }

        if (!empty($filters['joining_from'])) {
            $deptBuilder->where('ui.joining_date >=', $filters['joining_from']);
        }

        if (!empty($filters['joining_to'])) {
            $deptBuilder->where('ui.joining_date <=', $filters['joining_to']);
        }

        if (!empty($filters['year'])) {
            $deptBuilder->where('YEAR(ui.joining_date)', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $deptBuilder->where('MONTH(ui.joining_date)', $filters['month']);
        }

        $deptWise = $deptBuilder->get()->getResultArray();

        return [
            'total_employees' => $totalResult['total_employees'] ?? 0,
            'department_wise' => $deptWise
        ];
    }

    public function getUsersWithStatus()
    {
        return $this->select('id, name, status')->findAll();
    }
}
