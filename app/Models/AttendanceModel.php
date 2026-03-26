<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'date', 'check_in_time', 'check_out_time', 'meal_break', 'work_hours', 'overtime','created_at','updated_at','status','checkin_method', 'is_late', 'late_minutes'
    ];
    protected $useTimestamps = true;

    public function getAttendanceReport($departmentId = null, $employeeId = null, $startDate = null, $endDate = null, $year = null, $month = null)
    {
        $builder = $this->db->table('attendance');
        $builder->select('attendance.user_id, user_info.firstname, user_info.lastname, department.department_name, attendance.date, attendance.status')
               ->join('user_info', 'user_info.user_id = attendance.user_id', 'left')
               ->join('department', 'department.id = user_info.department_id', 'left');

        if (!empty($employeeId) && $employeeId !== 'null' && $employeeId !== 'undefined' && $employeeId !== '') {
            $builder->where('attendance.user_id', (int)$employeeId);
        }

        if (!empty($departmentId) && $departmentId !== 'null' && $departmentId !== 'undefined' && $departmentId !== '') {
            $builder->where('user_info.department_id', (int)$departmentId);
        }

        if (!empty($startDate)) {
            $builder->where('attendance.date >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('attendance.date <=', $endDate);
        }

        if (!empty($year)) {
            $builder->where('YEAR(attendance.date)', $year);
        }

        if (!empty($month)) {
            $builder->where('MONTH(attendance.date)', $month);
        }

        return $builder->orderBy('attendance.date', 'DESC')->get()->getResultArray();
    }
        public function getHoursByStatus()
        {
            $builder = $this->db->table($this->table);
            $builder->select('status, SUM(work_hours) as total_hours');
            $builder->groupBy('status');
            $query = $builder->get();
            return $query->getResultArray();
        }
}
