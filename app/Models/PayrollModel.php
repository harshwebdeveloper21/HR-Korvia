<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table = 'payroll';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'leave_type', 'remaining_paid_leaves', 'month_year', 'total_leaves', 'total_half_day', 'total_paid_leaves', 'used_paid_leaves', 'used_sick_leaves', 'remaining_sick_leaves', 'salary_amount', 'acc_number', 'bank_name', 'ifsc_code', 'acc_in_name',
        'branch_name', 'branch_code', 'tax_deduction', 'salary_deduction', 'bonuses', 'net_salary', 'payment_date', 'payment_status', 'created_by', 'worked_hours', 'overtime_pay', 'total_overtime_hours'
    ];
    protected $useTimestamps = true;

    public function getPayrollReport($departmentId = null, $employeeId = null, $startDate = null, $endDate = null, $year = null, $month = null)
    {
        $payrollModel = new PayrollModel();
        $query = $payrollModel ->select('user_info.firstname, department.department_name, payroll.salary_amount, payroll.net_salary,payroll.bonuses,payroll.tax_deduction, payroll.payment_date')
        ->join('user_info', 'user_info.user_id = payroll.user_id','left')
        ->join('department', 'department.id = user_info.department_id','left');       

        if ($departmentId) {
            $query->where('department.id', $departmentId);
        }

        if ($employeeId) {
            $query->where('user_info.user_id', $employeeId);
        }

        if ($startDate) {
            $query->where('payroll.payment_date >=', $startDate);
        }

        if ($endDate) {
            $query->where('payroll.payment_date <=', $endDate);
        }

        if ($year) {
            $query->where('YEAR(payroll.payment_date)', $year);
        }

        if ($month) {
            $query->where('MONTH(payroll.payment_date)', $month);
        }

        return $query->orderBy('payroll.payment_date', 'DESC')->get()->getResultArray();
    }
}
