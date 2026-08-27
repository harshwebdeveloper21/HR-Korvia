<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\PayrollModel;

class PayrollReportController extends Controller
{
    public function create()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userModel = new \App\Models\UserModel();
        // Fetch employees with role 'employee'
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();

        return view('report/payrollReport',['departments'=>$departments,'employees'=>$employees]);
    }
public function fetchPayrollReport()
    {
        if ($this->request->isAJAX()) {
            $departmentId = $this->request->getVar('department_id');
            $employeeId   = $this->request->getVar('employee_id');
            $startDate    = $this->request->getVar('start_date');
            $endDate      = $this->request->getVar('end_date');
            $year         = $this->request->getVar('year');
            $month        = $this->request->getVar('month');

            $payrollModel = new PayrollModel();
            $reportData = $payrollModel->getPayrollReport($departmentId, $employeeId, $startDate, $endDate, $year, $month);

            return $this->response->setJSON([
                'tableData' => $reportData,
                'csrfHash'  => csrf_hash()
            ]);
        }
    }

  
    }

