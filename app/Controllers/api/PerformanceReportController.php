<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\PerformanceModel;

class PerformanceReportController extends Controller
{
    /**
     * Render the performance report page.
     */
    public function create()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userInfoModel = new \App\Models\UserInfoModel();
        $employees = $userInfoModel->select('user_id as id, firstname, lastname')->orderBy('firstname', 'ASC')->findAll();

        return view('report/performanceReport', [
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    /**
     * AJAX handler – fetch filtered performance data.
     * Accepts: department_id, employee_id, start_date, end_date, month, year
     */
    public function fetchtPerformanceReport()
    {
        return $this->fetchPerformanceReport();
    }

    public function fetchPerformanceReport()
    {
        if ($this->request->isAJAX()) {
            $departmentId = $this->request->getVar('department_id');
            $employeeId = $this->request->getVar('employee_id');
            $month = $this->request->getVar('month');   // 1-12 or empty
            $year = $this->request->getVar('year');    // YYYY or empty

            $rawDate = $this->request->getVar('start_date');
            $startDate = (!empty($rawDate) && strtotime($rawDate))
                ? date('Y-m-d', strtotime($rawDate))
                : null;

            $rawEndDate = $this->request->getVar('end_date');
            $endDate = (!empty($rawEndDate) && strtotime($rawEndDate))
                ? date('Y-m-d', strtotime($rawEndDate))
                : null;

            $performanceModel = new PerformanceModel();
            $reportData = $performanceModel->getperformanceReport(
                $departmentId,
                $employeeId,
                $startDate,
                $endDate,
                $month,
                $year
            );

            return $this->response->setJSON([
                'tableData' => !empty($reportData) ? $reportData : [],
                'csrfHash'  => csrf_hash(),
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
    }
}