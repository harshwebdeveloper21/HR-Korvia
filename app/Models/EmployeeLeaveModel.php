<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeLeaveModel extends Model
{
    protected $table           = 'employee_leaves';
    protected $primaryKey      = 'id';
    protected $useTimestamps   = true;
    protected $createdField    = 'created_at';
    protected $updatedField    = 'updated_at';

    protected $allowedFields = [
        'employee_id',
        'paid_leave',
        'casual_leave',
        'created_by',
    ];

    /**
     * Get all employee leave balances joined with user info.
     */
    public function getAllWithEmployeeInfo(): array
    {
        return $this->db->table('employee_leaves el')
            ->select('el.*, u.username, u.email, u.role')
            ->join('users u', 'u.id = el.employee_id', 'left')
            ->where('u.is_deleted', 0)
            ->orderBy('el.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get leave balance for a specific employee.
     */
    public function getByEmployeeId(int $employeeId): ?array
    {
        return $this->where('employee_id', $employeeId)->first();
    }

    /**
     * Calculate used leaves for an employee in a given month/year from the leaves table.
     */
    public function getUsedLeavesByType(int $employeeId, string $leaveTypeName, ?string $monthYear = null): float
    {
        $db = \Config\Database::connect();
        $builder = $db->table('leaves l')
            ->select('SUM(CAST(l.no_of_day AS DECIMAL(10,2))) as total_used')
            ->join('leave_type lt', 'lt.id = l.leave_id', 'left')
            ->where('l.user_id', $employeeId)
            ->where('l.status', 'approved')
            ->where('LOWER(lt.leave_type)', strtolower($leaveTypeName));

        if ($monthYear) {
            $builder->where("DATE_FORMAT(l.start_date, '%Y-%m')", $monthYear);
        }

        $result = $builder->get()->getRowArray();
        return (float) ($result['total_used'] ?? 0);
    }
}
