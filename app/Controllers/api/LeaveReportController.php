<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\LeaveTypeModel;

use App\Models\LeaveModel;

class LeaveReportController extends Controller
{
    public function create()
    {
        $userModel = new \App\Models\UserModel();
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();

        $leaveTypeModel = new \App\Models\LeaveTypeModel();
        $leaveTypes = $leaveTypeModel->findAll();

        $departmentModel = new \App\Models\DepartmentModel();
        $departments = $departmentModel->findAll();

        return view('report/leaveReport', [
            'employees'  => $employees,
            'leaveTypes' => $leaveTypes,
            'departments' => $departments
        ]);
    }
public function fetchLeaveReport()
    {
        $leaveModel = new LeaveModel();
        
        $employee_id = $this->request->getPost('employee_id');
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $year = $this->request->getPost('year');
        $month = $this->request->getPost('month');
        $leave_type = $this->normalizeLeaveTypeFilter($this->request->getPost('leave_type'));
        $status = $this->request->getPost('status');

        // Fetch filtered report data
        $report = $leaveModel->getEmployeeReport($employee_id, $start_date, $end_date, $year, $month, $leave_type, $status);

        // Fetch dynamic chart data
        $chartQuery = $leaveModel->select('leave_type.leave_type, COUNT(leaves.id) AS total')
            ->join('leave_type', 'leave_type.id = leaves.leave_id', 'left')
            ->groupBy('leave_type.leave_type');

        // Apply filters for the chart
        if ($employee_id) {
            $chartQuery->where('leaves.user_id', $employee_id);
        }
        if ($start_date) {
            $chartQuery->where('leaves.start_date >=', $start_date);
        }
        if ($end_date) {
            $chartQuery->where('leaves.end_date <=', $end_date);
        }
        if ($year) {
            $chartQuery->where('YEAR(leaves.start_date)', $year);
        }
        if ($month) {
            $chartQuery->where('MONTH(leaves.start_date)', $month);
        }
        if ($leave_type) {
            $chartQuery->where('leaves.leave_id', $leave_type);
        }
        if ($status) {
            $chartQuery->where('leaves.status', $status);
        }

        $chartDataResults = $chartQuery->findAll();

        $labels = array_column($chartDataResults, 'leave_type');
        $data = array_column($chartDataResults, 'total');

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Number of Leaves',
                    'data' => $data,
                    'backgroundColor' => ['#17a2b8', '#ff6347', '#28a745', '#d3c75e']
                ],
            ]
        ];

        return $this->response->setJSON([
            'tableData' => $report,
            'chartData' => $chartData
        ]);
    }

    private function normalizeLeaveTypeFilter($leaveType)
    {
        if ($leaveType === null) {
            return null;
        }

        $leaveType = trim((string) $leaveType);
        if ($leaveType === '' || strtolower($leaveType) === 'all' || strtolower($leaveType) === 'all types') {
            return null;
        }

        if (ctype_digit($leaveType)) {
            return (int) $leaveType;
        }

        $leaveTypeModel = new LeaveTypeModel();
        $matchedLeaveType = $leaveTypeModel
            ->select('id')
            ->where('LOWER(leave_type)', strtolower($leaveType))
            ->first();

        return $matchedLeaveType['id'] ?? null;
    }
}
