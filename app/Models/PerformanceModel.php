<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceModel extends Model
{
    protected $table         = 'performance';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'user_id', 'review_date', 'reviewer_id', 'designation_id',
        'goals_achieved', 'team_work', 'management',
        'presentation_skill', 'behaviour', 'rating', 'notes',
    ];
    protected $useTimestamps = true;

    /**
     * Fetch performance data for dashboard widgets (current/last month).
     */
    public function fetchPerformanceData($monthType, $employeeId = null)
    {
        $builder = $this->builder();

        if ($employeeId) {
            $builder->where('user_id', $employeeId);
        }

        if ($monthType === 'this_month') {
            $builder->where('MONTH(review_date)', date('m'))
                    ->where('YEAR(review_date)',  date('Y'));
        } elseif ($monthType === 'last_month') {
            $lastMonth = date('m') == 1 ? 12 : date('m') - 1;
            $lastYear  = date('m') == 1 ? date('Y') - 1 : date('Y');
            $builder->where('MONTH(review_date)', $lastMonth)
                    ->where('YEAR(review_date)',  $lastYear);
        }

        return $builder->get()->getResult();
    }

    /**
     * Fetch performance report data with optional filters:
     *   - department_id
     *   - employee_id  (user_id)
     *   - start_date   (review_date >=)
     *   - month        (1-12)
     *   - year         (YYYY)
     */
    public function getperformanceReport(
        $departmentId = null,
        $employeeId   = null,
        $startDate    = null,
        $endDate      = null,
        $month        = null,
        $year         = null
    ) {
        $query = $this->select('
                user_info.firstname,
                user_info.lastname,
                department.department_name,
                performance.review_date,
                performance.rating
            ')
            ->join('user_info',   'user_info.user_id = performance.user_id',          'left')
            ->join('department',  'department.id = user_info.department_id',           'left')
            ->where("(LOWER(user_info.status) NOT IN ('inactive', 'resigned') OR user_info.status IS NULL)")
            ->where("(user_info.last_working_day IS NULL OR user_info.last_working_day >= CURDATE())");

        if (!empty($departmentId) && $departmentId !== 'null' && $departmentId !== 'undefined') {
            $query->where('department.id', (int)$departmentId);
        }

        if (!empty($employeeId) && $employeeId !== 'null' && $employeeId !== 'undefined') {
            $query->where('user_info.user_id', (int)$employeeId);
        }

        // From-date filter
        if (!empty($startDate)) {
            $query->where('performance.review_date >=', $startDate);
        }

        // To-date filter
        if (!empty($endDate)) {
            $query->where('performance.review_date <=', $endDate);
        }

        // Month filter
        if (!empty($month)) {
            $query->where('MONTH(performance.review_date)', (int)$month);
        }

        // Year filter
        if (!empty($year)) {
            $query->where('YEAR(performance.review_date)', (int)$year);
        }

        $query->orderBy('performance.review_date', 'DESC');

        $result = $query->get()->getResultArray();
        return !empty($result) ? $result : [];
    }
}
