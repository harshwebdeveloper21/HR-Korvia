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
        $attendanceModel = new AttendanceModel();
        $query = $attendanceModel ->select('user_info.firstname, department.department_name, attendance.date, attendance.status')
        ->join('user_info', 'user_info.user_id = attendance.user_id','left')
        ->join('department', 'department.id = user_info.department_id','left');

            if ($departmentId) {
                $query->where('department.id', $departmentId);
            }
    
            if ($employeeId) {
                $query->where('user_info.user_id', $employeeId);
            }
    
            if ($startDate) {
                $query->where('attendance.date >=', $startDate);
            }

            if ($endDate) {
                $query->where('attendance.date <=', $endDate);
            }

            if ($year) {
                $query->where('YEAR(attendance.date)', $year);
            }

            if ($month) {
                $query->where('MONTH(attendance.date)', $month);
            }
    
            return $query->orderBy('attendance.date', 'DESC')->get()->getResultArray();
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
