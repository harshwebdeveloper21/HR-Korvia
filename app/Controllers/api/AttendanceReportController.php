<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\AttendanceModel;
use App\Models\UserInfoModel;
use App\Models\UserModel;

class AttendanceReportController extends Controller
{
    public function fetchEmployeesByDepartment()
    {
        $departmentId = $this->request->getPost('department_id');
        $userInfoModel = new \App\Models\UserInfoModel();
        
        $builder = $userInfoModel->select('user_id as id, firstname, lastname');

        if (!empty($departmentId) && $departmentId !== 'null') {
            $builder->where('department_id', $departmentId);
        }

        $employees = $builder->orderBy('firstname', 'ASC')->findAll();
        
        return $this->response->setJSON($employees);
    }

    public function create()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userInfoModel = new \App\Models\UserInfoModel();
        $employees = $userInfoModel->select('user_id as id, firstname, lastname')->orderBy('firstname', 'ASC')->findAll();
        return view('report/attendanceReport',['departments'=>$departments,'employees'=>$employees]);
    }
    public function fetchAttendanceReport()
    {
        $departmentId = $this->request->getVar('department_id');
        $employeeId   = $this->request->getVar('employee_id');
        $startDate    = $this->request->getVar('start_date');
        $endDate      = $this->request->getVar('end_date');
        $year         = $this->request->getVar('year');
        $month        = $this->request->getVar('month');

        // Debug: log what we receive
        log_message('debug', 'AttendanceReport Filters => department_id: ' . $departmentId . ', employee_id: ' . $employeeId . ', year: ' . $year . ', month: ' . $month);

        $attendanceModel = new AttendanceModel();
        $reportData = $attendanceModel->getAttendanceReport($departmentId, $employeeId, $startDate, $endDate, $year, $month);

        log_message('debug', 'AttendanceReport Result count: ' . count($reportData));

        // Process data for the chart
        $dailyUniqueUsers = [];
        foreach ($reportData as $data) {
            $date = $data['date'];
            $userId = $data['user_id'] ?? null;
            if (!isset($dailyUniqueUsers[$date])) {
                $dailyUniqueUsers[$date] = [];
            }
            if ($userId) {
                $dailyUniqueUsers[$date][$userId] = true;
            }
        }

        $formattedChartData = [];
        foreach ($dailyUniqueUsers as $date => $users) {
            $formattedChartData[] = [
                'date' => $date,
                'attendance_count' => count($users)
            ];
        }

        // Sort chart data by date
        usort($formattedChartData, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $this->response->setJSON([
            'status'    => 'success',
            'tableData' => $reportData,
            'chartData' => $formattedChartData,
            'empty'     => empty($reportData),
            'csrfHash'  => csrf_hash(),
            'debug'     => [
                'received_department_id' => $departmentId,
                'received_employee_id'   => $employeeId,
                'received_year'          => $year,
                'received_month'         => $month,
                'result_count'           => count($reportData)
            ]
        ]);
    }
    
}