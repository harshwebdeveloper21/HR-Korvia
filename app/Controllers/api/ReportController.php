<?php

namespace App\Controllers\Api;

use App\Models\UserInfoModel;
use App\Models\LeaveModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;

class ReportController extends ResourceController
{
    protected $authService;
    protected $userInfoModel;
    protected $leaveModel;

    public function __construct()
    {
        $this->userInfoModel = new UserInfoModel();
        $this->leaveModel = new LeaveModel();
        $this->authService = new AuthService(service('request')); // Inject AuthService
    }

    // Employee Report API
    public function empReport()
    {
        // Check Authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        // Retrieve filters from request
        $filters = $this->request->getGet();
        $query = $this->userInfoModel;

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }
        if (!empty($filters['employee_id'])) {
            $query->where('user_id', $filters['employee_id']);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->where('joining_date >=', $filters['start_date'])
                ->where('joining_date <=', $filters['end_date']);
        }

        $employees = $query->findAll();

        // Prepare chart data (example: department-wise employee count)
        $departmentCount = [];
        foreach ($employees as $emp) {
            if (isset($departmentCount[$emp['department']])) {
                $departmentCount[$emp['department']]++;
            } else {
                $departmentCount[$emp['department']] = 1;
            }
        }

        // Prepare chart data
        $chartData = [
            'labels' => array_keys($departmentCount), // Department names
            'data' => array_values($departmentCount),  // Count of employees in each department
        ];

        return $this->respond([
            'status' => 'success',
            'data' => $employees,
            'chartData' => $chartData, // Include chart data
        ]);
    }
    // Leave Report API
    public function leaveReport()
    {
        // Check Authorization
        $userId = $this->authService->check();
        if (!$userId) {
            return $this->failUnauthorized('Invalid or missing token');
        }

        // Retrieve filters from request
        $filters = $this->request->getGet();
        $query = $this->leaveModel;

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->where('start_date >=', $filters['start_date'])
                  ->where('end_date <=', $filters['end_date']);
        }

        $leaves = $query->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $leaves,
        ]);
    }
}
