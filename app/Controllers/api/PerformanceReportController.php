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

        $userModel = new \App\Models\UserModel();
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();

        return view('report/performanceReport', [
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    /**
     * AJAX handler – fetch filtered performance data.
     * Accepts: department_id, employee_id, start_date, month, year
     */
    public function fetchtPerformanceReport()
    {
        if ($this->request->isAJAX()) {
            $departmentId = $this->request->getPost('department_id');
            $employeeId = $this->request->getPost('employee_id');
            $month = $this->request->getPost('month');   // 1-12 or empty
            $year = $this->request->getPost('year');    // YYYY or empty

            // start_date: only parse if a non-empty value was posted
            $rawDate = $this->request->getPost('start_date');
            $startDate = (!empty($rawDate) && strtotime($rawDate))
                ? date('Y-m-d', strtotime($rawDate))
                : null;

            $performanceModel = new PerformanceModel();
            $reportData = $performanceModel->getperformanceReport(
                $departmentId,
                $employeeId,
                $startDate,
                $month,
                $year
            );

            return $this->response->setJSON([
                'tableData' => !empty($reportData) ? $reportData : [],
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
    }
}