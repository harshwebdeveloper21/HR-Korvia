<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\AttendanceModel;

class AttendanceReportController extends Controller
{
    public function create()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userModel = new \App\Models\UserModel();
        // Fetch employees with role 'employee'
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();
        return view('report/attendanceReport',['departments'=>$departments,'employees'=>$employees]);
    }
    public function fetchAttendanceReport()
    {
        if ($this->request->isAJAX()) {
            $departmentId = $this->request->getPost('department_id');
            $employeeId = $this->request->getPost('employee_id');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
            $year = $this->request->getPost('year');
            $month = $this->request->getPost('month');
    
            $attendanceModel = new AttendanceModel();
            $reportData = $attendanceModel->getAttendanceReport($departmentId, $employeeId, $startDate, $endDate, $year, $month);
    
            // Process data for the chart
            $chartData = [];
            foreach ($reportData as $data) {
                $date = $data['date'];
                if (!isset($chartData[$date])) {
                    $chartData[$date] = 0;
                }
                $chartData[$date]++;
            }
    
            $formattedData = [];
            foreach ($chartData as $date => $count) {
                $formattedData[] = [
                    'date' => $date,
                    'attendance_count' => $count
                ];
            }
    
            return $this->response->setJSON([
                'tableData' => $reportData,
                'chartData' => $formattedData
            ]);
        }
    }
    
}