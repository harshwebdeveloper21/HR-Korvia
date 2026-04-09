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
    /**
     * API: Get all employee leave balances.
     */
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized();
        }

        $monthYear = $this->request->getGet('month') ?: date('Y-m');

        $data = $this->employeeLeaveModel->getAllWithEmployeeInfo();

        $db = \Config\Database::connect();

        // Calculate remaining leaves for each record
        foreach ($data as &$row) {
            // Fetch TOTAL used paid leaves from ALL Payroll records so it accumulates correctly (March + April, etc.)
            $payrollRecord = $db->table('payroll')
                ->selectSum('used_paid_leaves')
                ->where('user_id', $row['employee_id'])
                ->get()
                ->getRowArray();

            $paidUsed = $payrollRecord ? (float)($payrollRecord['used_paid_leaves'] ?? 0) : 0;
            
            // Casual leaves: auto-count total from leaves table
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
            'data' => $data,
            'month' => $monthYear
        ]);
    }

    /**
     * API: Get detailed approved leave records for an employee in a specific month
     */
    public function getLeaveDetails()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized();
        }

        $employeeId = $this->request->getGet('employee_id');

        if (!$employeeId) {
            return $this->fail('Employee ID is required');
        }

        $db = \Config\Database::connect();
        
        // 1. Fetch the target override used_paid_leaves from Payroll (same as the main table)
        $payrollRecord = $db->table('payroll')
            ->selectSum('used_paid_leaves')
            ->where('user_id', $employeeId)
            ->get()
            ->getRowArray();
        $targetPaid = $payrollRecord ? (float)($payrollRecord['used_paid_leaves'] ?? 0) : 0;
        
        // Casual leaves still auto-count from leaves table
        $targetCasual = $this->employeeLeaveModel->getUsedLeavesByType($employeeId, 'Casual Leave');

        // 2. Fetch actual approved chronological records from leaves table
        $builder = $db->table('leaves l')
            ->select('l.start_date, l.end_date, l.no_of_day, lt.leave_type')
            ->join('leave_type lt', 'lt.id = l.leave_id', 'left')
            ->where('l.user_id', $employeeId)
            ->where('l.status', 'approved')
            ->orderBy('l.start_date', 'ASC');

        $rawRecords = $builder->get()->getResultArray();

        // 3. Process records chronologically and beautifully truncate them at the exact Target thresholds!
        $finalRecords = [];
        $accumulatedPaid = 0;
        $accumulatedCasual = 0;

        foreach ($rawRecords as $rec) {
            $days = (float)$rec['no_of_day'];
            $isPaid = (stripos($rec['leave_type'], 'Paid') !== false);
            
            if ($isPaid) {
                if ($accumulatedPaid < $targetPaid) {
                    $available = $targetPaid - $accumulatedPaid;
                    if ($days > $available) {
                        $rec['no_of_day'] = $available; // Truncate extra days from this record to exactly hit the target
                        $days = $available;
                    }
                    $accumulatedPaid += $days;
                    $finalRecords[] = $rec;
                }
            } else {
                if ($accumulatedCasual < $targetCasual) {
                    $available = $targetCasual - $accumulatedCasual;
                    if ($days > $available) {
                        $rec['no_of_day'] = $available;
                        $days = $available;
                    }
                    $accumulatedCasual += $days;
                    $finalRecords[] = $rec;
                }
            }
        }

        $totalCount = $accumulatedPaid + $accumulatedCasual;

        $employee = $this->userModel->find($employeeId);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'employee_name' => $employee['username'] ?? 'Unknown',
                'month' => 'All-Time',
                'total_count' => $totalCount,
                'records' => $finalRecords
            ]
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
            'employee_id' => 'required|is_not_unique[users.id]',
            'paid_leave' => 'required|decimal',
            'casual_leave' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'employee_id' => $this->request->getVar('employee_id'),
            'paid_leave' => $this->request->getVar('paid_leave'),
            'casual_leave' => $this->request->getVar('casual_leave'),
            'created_by' => $user->sub,
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
            'status' => 'success',
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
            'data' => $record
        ]);
    }
}
