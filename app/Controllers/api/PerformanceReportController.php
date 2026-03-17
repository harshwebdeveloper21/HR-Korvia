<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\PerformanceModel;

class PerformanceReportController extends Controller
{
    public function create()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $userModel = new \App\Models\UserModel();
        // Fetch employees with role 'employee'
        $employees = $userModel->whereIn('role', ['hr', 'employee'])->findAll();
        return view('report/performanceReport',['departments'=>$departments,'employees'=>$employees]);
    }
    // public function fetchtPerformanceReport()
    // {
    //     if ($this->request->isAJAX()) {
    //         $departmentId = $this->request->getPost('department_id');
    //         $employeeId = $this->request->getPost('employee_id');
    //         $startDate = $this->request->getPost('start_date');
   
    //         $performanceModel = new PerformanceModel();

    //         // $reportData = $performanceModel->getperformanceReport($departmentId, $employeeId, $startDate);
    //         // print_r($reportData);
    //         // die;

    //         $reportData = $performanceModel->where('user_id' , $employeeId)
    //                                         ->where('review_date',$startDate)
    //                                         ->where('user_id',$departmentId)
    //                                        ->findAll();

    //         print_r($reportData);
    //         die;
              
    //         return $this->response->setJSON([
    //             'tableData' => $reportData
    //         ]);
    //     }
    // }
    public function fetchtPerformanceReport()
    {
        if ($this->request->isAJAX()) {
            $departmentId = $this->request->getPost('department_id');
            $employeeId = $this->request->getPost('employee_id');
            $startDate = date('Y-m-d', strtotime($this->request->getPost('start_date')));

            $performanceModel = new PerformanceModel();
            $reportData = $performanceModel->getperformanceReport($departmentId, $employeeId, $startDate);

            // Ensure an empty array is returned if no records are found
            return $this->response->setJSON([
                'tableData' => !empty($reportData) ? $reportData : []
            ]);
        }
    }
}