<?php

namespace App\Controllers\api;

use App\Controllers\BaseController;
use App\Models\EmployeeLeaveModel;
use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\API\ResponseTrait;

class EmployeeLeaveController extends BaseController
{
    use ResponseTrait;

    protected $employeeLeaveModel;
    protected $userModel;
    protected $authService;

    public function __construct()
    {
        $this->employeeLeaveModel = new EmployeeLeaveModel();
        $this->userModel = new UserModel();
        $this->authService = new AuthService(service('request'));
    }

    /**
     * Display the Manage Leaves page. (Admin & HR only)
     */
    public function index()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return redirect()->to('/login');
        }

        // Fetch all active employees for the dropdown
        $employees = $this->userModel->where('is_deleted', 0)
                                      ->whereIn('role', ['employee', 'hr'])
                                      ->findAll();

        return view('leave/manage_leaves', [
            'employees' => $employees,
            'role'      => $user->role
        ]);
    }

    /**
     * API: Get all employee leave balances.
     */
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized();
        }

        $data = $this->employeeLeaveModel->getAllWithEmployeeInfo();
        
        // Calculate remaining leaves for each record
        foreach ($data as &$row) {
            $paidUsed = $this->employeeLeaveModel->getUsedLeavesByType($row['employee_id'], 'Paid Leave');
            $casualUsed = $this->employeeLeaveModel->getUsedLeavesByType($row['employee_id'], 'Casual Leave');
            
            $row['paid_used'] = $paidUsed;
            $row['casual_used'] = $casualUsed;
            $row['paid_remaining'] = $row['paid_leave'] - $paidUsed;
            $row['casual_remaining'] = $row['casual_leave'] - $casualUsed;
            $row['total_leaves'] = $row['paid_leave'] + $row['casual_leave'];
            $row['total_used'] = $paidUsed + $casualUsed;
            $row['total_remaining'] = $row['paid_remaining'] + $row['casual_remaining'];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * API: Add or update leave balance for an employee.
     */
    public function store()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized();
        }

        $rules = [
            'employee_id'  => 'required|is_not_unique[users.id]',
            'paid_leave'   => 'required|decimal',
            'casual_leave' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'employee_id'  => $this->request->getVar('employee_id'),
            'paid_leave'   => $this->request->getVar('paid_leave'),
            'casual_leave' => $this->request->getVar('casual_leave'),
            'created_by'   => $user->sub,
        ];

        // Check if record exists
        $existing = $this->employeeLeaveModel->where('employee_id', $data['employee_id'])->first();

        if ($existing) {
            $this->employeeLeaveModel->update($existing['id'], $data);
            $message = 'Leave balance updated successfully.';
        } else {
            $this->employeeLeaveModel->insert($data);
            $message = 'Leave balance added successfully.';
        }

        return $this->respond([
            'status'  => 'success',
            'message' => $message
        ]);
    }

    /**
     * API: Get balance for a specific employee.
     */
    public function getByEmployee($id)
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized();
        }

        $record = $this->employeeLeaveModel->where('employee_id', $id)->first();
        if (!$record) {
            return $this->failNotFound('No leave balance found for this employee.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $record
        ]);
    }
}
