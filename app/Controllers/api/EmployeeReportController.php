<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\UserModel;
use App\Services\AuthService;


class EmployeeReportController extends ResourceController
{
    protected $authService;
    protected $userModel;

    public function __construct()
    {
        $this->authService = new AuthService(service('request'));
        $this->userModel = new UserModel();
    }
    public function create()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userModel = new \App\Models\UserModel();
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();

        $userinfoModel = new \App\Models\UserInfoModel();
        $users = $userinfoModel->where('role', 'employee')->findAll();

        return view('report/empReport', ['departments' => $departments, 'employees' => $employees, 'users' => $users]);
    }

    public function fetchEmployeeReport()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $filters = [
            'department_id' => $this->request->getPost('department_id'),
            'employee_id'   => $this->request->getPost('employee_id'),
            'status'        => $this->request->getPost('status') ?? 'active',
            'joining_from'  => $this->request->getPost('joining_from'),
            'joining_to'    => $this->request->getPost('joining_to'),
            'year'          => $this->request->getPost('year'),
            'month'         => $this->request->getPost('month'),
        ];

        $employeeModel = new UserModel();
        $report = $employeeModel->getEmployeeReport($filters);
        $summary = $employeeModel->getEmployeeSummary($filters);

        return $this->response->setJSON([
            'tableData' => $report,
            'chartData' => $this->prepareChartData($report),
            'summary'   => $summary
        ]);
    }

    private function prepareChartData($report)
    {
        $yearlyData = [];

        foreach ($report as $emp) {
            $year = date('Y', strtotime($emp['joining_date']));
            $yearlyData[$year] = ($yearlyData[$year] ?? 0) + 1;
        }

        return [
            'labels' => array_keys($yearlyData),
            'datasets' => [[
                'label' => 'Employees Joined',
                'data' => array_values($yearlyData),
            ]]
        ];
    }

    public function fetchEmployeesByDepartment()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $departmentId = $this->request->getPost('department_id');

        $userModel = new UserModel();

        $builder = $userModel
            ->select('users.id, user_info.firstname, user_info.lastname')
            ->join('user_info', 'user_info.user_id = users.id', 'left')
            ->whereIn('users.role', ['hr', 'employee']);

        if (!empty($departmentId)) {
            $builder->where('user_info.department_id', $departmentId);
        }

        $employees = $builder->orderBy('user_info.firstname', 'ASC')->findAll();

        return $this->response->setJSON([
            'employees' => $employees,
            'csrfHash'  => csrf_hash()
        ]);
    }

}
