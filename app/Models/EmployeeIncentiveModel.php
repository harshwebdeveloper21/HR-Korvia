<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeIncentiveModel extends Model
{
    protected $table            = 'employee_incentives';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'employee_id',
        'department_id',
        'incentive_type',
        'percentage',
        'base_salary',
        'incentive_amount',
        'month',
        'year',
        'remarks',
        'created_by',
    ];

    /**
     * Get total incentive amount for a given employee, month, and year.
     */
    public function getMonthlyTotalForEmployee(int $employeeId, int $month, int $year): float
    {
        $row = $this->selectSum('incentive_amount', 'total_incentive')
            ->where('employee_id', $employeeId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        return round((float) ($row['total_incentive'] ?? 0), 2);
    }

    /**
     * Get all incentive items for a given employee, month, and year.
     */
    public function getMonthlyItemsForEmployee(int $employeeId, int $month, int $year): array
    {
        return $this->where('employee_id', $employeeId)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
