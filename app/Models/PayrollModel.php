<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table = 'payroll';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'leave_type', 'remaining_paid_leaves', 'month_year', 'total_leaves', 'total_half_day', 'total_paid_leaves', 'used_paid_leaves', 'used_sick_leaves', 'remaining_sick_leaves', 'salary_amount', 'acc_number', 'bank_name', 'ifsc_code', 'acc_in_name',
        'branch_name', 'branch_code', 'tax_deduction', 'salary_deduction', 'bonuses', 'net_salary', 'payment_date', 'payment_status', 'created_by', 'worked_hours', 'overtime_pay', 'total_overtime_hours', 'adjustment_amount', 'adjustment_remark'
    ];
    protected $useTimestamps = true;

    public function getPayrollReport($departmentId = null, $employeeId = null, $startDate = null, $endDate = null, $year = null, $month = null)
    {
        $query = $this->select('user_info.firstname, user_info.lastname, department.department_name, payroll.salary_amount, payroll.net_salary, payroll.bonuses, payroll.tax_deduction, payroll.salary_deduction, payroll.payment_date, payroll.month_year')
        ->join('user_info', 'user_info.user_id = payroll.user_id', 'left')
        ->join('department', 'department.id = user_info.department_id', 'left')
        ->where("(LOWER(user_info.status) NOT IN ('inactive', 'resigned') OR user_info.status IS NULL)")
        ->where("(user_info.last_working_day IS NULL OR user_info.last_working_day >= CURDATE())");

        if (!empty($departmentId) && $departmentId !== 'null' && $departmentId !== 'undefined') {
            $query->where('department.id', (int)$departmentId);
        }

        if (!empty($employeeId) && $employeeId !== 'null' && $employeeId !== 'undefined') {
            $query->where('user_info.user_id', (int)$employeeId);
        }

        if (!empty($startDate)) {
            $query->where('payroll.payment_date >=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('payroll.payment_date <=', $endDate);
        }

        if (!empty($year)) {
            $query->groupStart()
                  ->where('YEAR(payroll.payment_date)', (int)$year)
                  ->orLike('payroll.month_year', (string)$year, 'after')
                  ->groupEnd();
        }

        if (!empty($month)) {
            $mPadded = sprintf('%02d', (int)$month);
            $query->groupStart()
                  ->where('MONTH(payroll.payment_date)', (int)$month)
                  ->orLike('payroll.month_year', '-' . $mPadded, 'before')
                  ->groupEnd();
        }

        return $query->orderBy('payroll.payment_date', 'DESC')->get()->getResultArray();
    }
}
