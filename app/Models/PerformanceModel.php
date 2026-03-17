<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceModel extends Model
{
    protected $table = 'performance';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'review_date', 'reviewer_id', 'designation_id', 'goals_achieved', 'team_work', 'management', 'presentation_skill', 'behaviour', 'rating', 'notes'];
    protected $useTimestamps = true;

    public function fetchPerformanceData($monthType, $employeeId = null)
    {
        // Fetch performance data for the current or last month
        $builder = $this->builder();

        // Filter by employee ID if provided (for employee's own data)
        if ($employeeId) {
            $builder->where('user_id', $employeeId);
        }

        // Assuming `review_date` is a DateTime field and storing in `Y-m` format for this month and last month
        if ($monthType === 'this_month') {
            $builder->where('MONTH(review_date)', date('m'))
                ->where('YEAR(review_date)', date('Y'));
        } elseif ($monthType === 'last_month') {
            $builder->where('MONTH(review_date)', date('m') - 1)
                ->where('YEAR(review_date)', date('Y'));
        }

        return $builder->get()->getResult();
    }

    public function getperformanceReport($departmentId = null, $employeeId = null, $startDate = null)
    {
        $query = $this->select('
                user_info.firstname, 
                department.department_name, 
                performance.review_date, 
                performance.rating
            ')
            ->join('user_info', 'user_info.user_id = performance.user_id', 'left')
            ->join('department', 'department.id = user_info.department_id', 'left');

        if ($departmentId) {
            $query->where('department.id', $departmentId);
        }

        if ($employeeId) {
            $query->where('user_info.user_id', $employeeId);
        }

        if ($startDate) {
            $query->where('performance.review_date >=', $startDate);
        }

        $result = $query->get()->getResultArray();

        return !empty($result) ? $result : [];
    }
}
