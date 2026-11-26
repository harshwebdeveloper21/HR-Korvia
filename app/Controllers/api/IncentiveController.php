<?php

namespace App\Controllers\Api;

use App\Models\DepartmentModel;
use App\Models\EmployeeIncentiveModel;
use App\Models\PayrollModel;
use App\Models\UserInfoModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IncentiveController extends ResourceController
{
    private EmployeeIncentiveModel $incentiveModel;
    private AuthService $authService;

    private const ALLOWED_TYPES = [
        'Performance',
        'Sales Target',
        'Festival',
        'Attendance',
        'Other',
    ];

    public function __construct()
    {
        $this->incentiveModel = new EmployeeIncentiveModel();
        $this->authService    = new AuthService(service('request'));
    }

    /**
     * Ensure current web session user has payroll access (admin or hr).
     */
    private function checkPageAccess()
    {
        $role = strtolower((string) (session()->get('role') ?? ''));
        if (!in_array($role, ['admin', 'hr'], true)) {
            return redirect()->to('/')->with('error', 'Access denied. Only Admin and HR can access incentives.');
        }
        return null;
    }

    /**
     * Ensure current API user (JWT or session) has payroll access (admin or hr).
     */
    private function checkApiAccess()
    {
        $user = $this->authService->check();
        if (!$user) {
            return [null, $this->failUnauthorized('Unauthorized: Token missing or invalid')];
        }
        if (!in_array(strtolower((string) ($user->role ?? '')), ['admin', 'hr'], true)) {
            return [null, $this->failForbidden('Forbidden: Only Admin and HR can manage incentives.')];
        }
        return [$user, null];
    }

    /**
     * Render Add Incentive page: GET /addIncentive
     */
    public function addPage()
    {
        if ($redirect = $this->checkPageAccess()) {
            return $redirect;
        }

        $departmentModel = new DepartmentModel();
        $departments     = $departmentModel->orderBy('department_name', 'ASC')->findAll();

        return view('payroll/add_incentive', [
            'departments'    => $departments,
            'incentiveTypes' => self::ALLOWED_TYPES,
            'incentive'      => null,
        ]);
    }

    /**
     * Render Edit Incentive page: GET /editIncentive/(:num)
     */
    public function editPage($id = null)
    {
        if ($redirect = $this->checkPageAccess()) {
            return $redirect;
        }

        $incentive = $this->incentiveModel->find((int) $id);
        if (!$incentive) {
            return redirect()->to('/manageIncentive')->with('error', 'Incentive record not found.');
        }

        $departmentModel = new DepartmentModel();
        $departments     = $departmentModel->orderBy('department_name', 'ASC')->findAll();

        return view('payroll/add_incentive', [
            'departments'    => $departments,
            'incentiveTypes' => self::ALLOWED_TYPES,
            'incentive'      => $incentive,
        ]);
    }

    /**
     * Render Manage Incentive list page: GET /manageIncentive
     */
    public function managePage()
    {
        if ($redirect = $this->checkPageAccess()) {
            return $redirect;
        }

        $departmentModel = new DepartmentModel();
        $departments     = $departmentModel->orderBy('department_name', 'ASC')->findAll();

        return view('payroll/manage_incentive', [
            'departments' => $departments,
        ]);
    }

    /**
     * Resolve effective base salary for an employee from their payroll record first,
     * falling back to salary_increment_history / user_info.salary if no payroll record exists yet.
     */
    private function resolveBaseSalary(int $employeeId, int $month, int $year): float
    {
        $monthYear = sprintf('%04d-%02d', $year, $month);

        $payrollModel = new PayrollModel();
        $payrollForMonth = $payrollModel
            ->where('user_id', $employeeId)
            ->where('month_year', $monthYear)
            ->orderBy('id', 'DESC')
            ->first();

        if ($payrollForMonth && (float) ($payrollForMonth['salary_amount'] ?? 0) > 0) {
            return round((float) $payrollForMonth['salary_amount'], 2);
        }

        $latestPayroll = $payrollModel
            ->where('user_id', $employeeId)
            ->orderBy('month_year', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if ($latestPayroll && (float) ($latestPayroll['salary_amount'] ?? 0) > 0) {
            return round((float) $latestPayroll['salary_amount'], 2);
        }

        // Fallback to user_info + increment history if employee has no payroll record yet
        $userInfoModel = new UserInfoModel();
        $userInfo      = $userInfoModel->where('user_id', $employeeId)->first();
        $effective     = (float) ($userInfo['salary'] ?? 0);

        $payrollMonthEnd = date('Y-m-t', strtotime($monthYear . '-01'));
        $db              = \Config\Database::connect();
        $increments      = $db->table('salary_increment_history')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_from_date', 'DESC')
            ->get()
            ->getResultArray();

        if (!empty($increments)) {
            $found = null;
            $matchedLatest = false;
            foreach ($increments as $idx => $inc) {
                if (($inc['effective_from_date'] ?? '') <= $payrollMonthEnd) {
                    $found = (float) $inc['new_salary'];
                    $matchedLatest = ($idx === 0);
                    break;
                }
            }
            if ($found !== null) {
                if (!($matchedLatest && $effective > 0 && abs($effective - $found) > 0.001)) {
                    $effective = $found;
                }
            } else {
                $oldest    = end($increments);
                $effective = (float) ($oldest['previous_salary'] ?? $effective);
            }
        }

        return round($effective, 2);
    }

    /**
     * GET /api/incentives/employees?department_id=...
     * Returns active employees filtered by department (excluding super admin).
     */
    public function getEmployees()
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $departmentId = $this->request->getGet('department_id');

        $db      = \Config\Database::connect();
        $builder = $db->table('users')
            ->select('users.id, users.role, user_info.firstname, user_info.lastname, user_info.employee_id as emp_code, user_info.department_id, department.department_name')
            ->join('user_info', 'user_info.user_id = users.id', 'inner')
            ->join('department', 'department.id = user_info.department_id', 'left')
            ->where('users.is_deleted', 0)
            ->where('users.role !=', 'admin')
            ->where("(LOWER(user_info.status) NOT IN ('inactive', 'resigned', 'fired', 'removed') OR user_info.status IS NULL)");

        if (!empty($departmentId) && $departmentId !== 'all') {
            $builder->where('user_info.department_id', (int) $departmentId);
        }

        $rows = $builder->orderBy('user_info.firstname', 'ASC')->get()->getResultArray();

        $employees = array_map(static function ($row) {
            $fullName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
            $empCode  = !empty($row['emp_code']) ? $row['emp_code'] : ('K-' . str_pad((string) $row['id'], 4, '0', STR_PAD_LEFT));
            return [
                'id'              => (int) $row['id'],
                'name'            => $fullName,
                'emp_code'        => $empCode,
                'department_id'   => $row['department_id'] ? (int) $row['department_id'] : null,
                'department_name' => $row['department_name'] ?? 'N/A',
            ];
        }, $rows);

        return $this->respond([
            'status'    => true,
            'employees' => $employees,
        ]);
    }

    /**
     * GET /api/incentives/base-salary?employee_id=...&month=...&year=...
     */
    public function getBaseSalary()
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $employeeId = (int) $this->request->getGet('employee_id');
        $month      = (int) ($this->request->getGet('month') ?: date('n'));
        $year       = (int) ($this->request->getGet('year') ?: date('Y'));

        if ($employeeId <= 0) {
            return $this->failValidationErrors(['employee_id' => 'Valid employee ID is required.']);
        }

        $baseSalary = $this->resolveBaseSalary($employeeId, $month, $year);

        return $this->respond([
            'status'      => true,
            'base_salary' => $baseSalary,
        ]);
    }

    /**
     * Validate and compute incentive payload for create/update.
     */
    private function validateAndPreparePayload(array $input, ?int $excludeId = null): array
    {
        $employeeId    = (int) ($input['employee_id'] ?? 0);
        $month         = (int) ($input['month'] ?? 0);
        $year          = (int) ($input['year'] ?? 0);
        $incentiveType = trim((string) ($input['incentive_type'] ?? ''));
        $rawPercentage = $input['percentage'] ?? null;
        $remarks       = isset($input['remarks']) ? trim((string) $input['remarks']) : null;

        $errors = [];

        if ($employeeId <= 0) {
            $errors['employee_id'] = 'Employee is required.';
        }
        if ($month < 1 || $month > 12) {
            $errors['month'] = 'Valid Month is required.';
        }
        if ($year < 2000 || $year > 2100) {
            $errors['year'] = 'Valid Year is required.';
        }
        if (empty($incentiveType) || !in_array($incentiveType, self::ALLOWED_TYPES, true)) {
            $errors['incentive_type'] = 'Valid Incentive Type is required.';
        }
        if ($rawPercentage === null || $rawPercentage === '' || !is_numeric($rawPercentage)) {
            $errors['percentage'] = 'Incentive Percentage is required.';
        } else {
            $pct = round((float) $rawPercentage, 2);
            if ($pct <= 0 || $pct > 100) {
                $errors['percentage'] = 'Incentive Percentage must be greater than 0 and no more than 100.';
            }
        }

        if (!empty($errors)) {
            return [null, $errors];
        }

        // Verify employee exists
        $userInfoModel = new UserInfoModel();
        $userInfo      = $userInfoModel->where('user_id', $employeeId)->first();
        if (!$userInfo) {
            return [null, ['employee_id' => 'Selected employee does not exist.']];
        }

        // Prevent duplicate incentive of same type for same employee in same month/year
        $dupBuilder = $this->incentiveModel
            ->where('employee_id', $employeeId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('incentive_type', $incentiveType);

        if ($excludeId !== null) {
            $dupBuilder->where('id !=', $excludeId);
        }

        if ($dupBuilder->first()) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            return [null, [
                'duplicate' => "A '{$incentiveType}' incentive already exists for this employee in {$monthName} {$year}.",
            ]];
        }

        $departmentId    = !empty($input['department_id']) ? (int) $input['department_id'] : (!empty($userInfo['department_id']) ? (int) $userInfo['department_id'] : null);
        $percentage      = round((float) $rawPercentage, 2);
        $baseSalary      = $this->resolveBaseSalary($employeeId, $month, $year);
        $incentiveAmount = round(($baseSalary * $percentage) / 100, 2);

        return [[
            'employee_id'      => $employeeId,
            'department_id'    => $departmentId,
            'incentive_type'   => $incentiveType,
            'percentage'       => $percentage,
            'base_salary'      => $baseSalary,
            'incentive_amount' => $incentiveAmount,
            'month'            => $month,
            'year'             => $year,
            'remarks'          => $remarks,
        ], null];
    }

    /**
     * POST /api/incentives/create
     */
    public function create()
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $input = $this->request->getPost();
        if (empty($input)) {
            $json = $this->request->getJSON(true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        [$payload, $errors] = $this->validateAndPreparePayload($input);
        if ($errors !== null) {
            $firstMsg = reset($errors);
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => $firstMsg,
                'errors'  => $errors,
            ]);
        }

        $payload['created_by'] = (int) ($user->sub ?? 0);

        $id = $this->incentiveModel->insert($payload);
        if (!$id) {
            return $this->failServerError('Failed to save incentive.');
        }

        return $this->respondCreated([
            'status'  => true,
            'message' => 'Incentive added successfully.',
            'data'    => $this->incentiveModel->find($id),
        ]);
    }

    /**
     * POST /api/incentives/update/(:num)
     */
    public function update($id = null)
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $id       = (int) $id;
        $existing = $this->incentiveModel->find($id);
        if (!$existing) {
            return $this->failNotFound('Incentive record not found.');
        }

        $input = $this->request->getPost();
        if (empty($input)) {
            $json = $this->request->getJSON(true);
            if (is_array($json)) {
                $input = $json;
            }
        }

        [$payload, $errors] = $this->validateAndPreparePayload($input, $id);
        if ($errors !== null) {
            $firstMsg = reset($errors);
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => $firstMsg,
                'errors'  => $errors,
            ]);
        }

        if (!$this->incentiveModel->update($id, $payload)) {
            return $this->failServerError('Failed to update incentive.');
        }

        return $this->respond([
            'status'  => true,
            'message' => 'Incentive updated successfully.',
            'data'    => $this->incentiveModel->find($id),
        ]);
    }

    /**
     * DELETE /api/incentives/delete/(:num)
     */
    public function delete($id = null)
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $id       = (int) $id;
        $existing = $this->incentiveModel->find($id);
        if (!$existing) {
            return $this->failNotFound('Incentive record not found.');
        }

        $this->incentiveModel->delete($id);

        return $this->respond([
            'status'  => true,
            'message' => 'Incentive deleted successfully.',
        ]);
    }

    /**
     * Build filtered query for list and export.
     */
    private function buildFilteredListQuery()
    {
        $departmentId = $this->request->getGet('department_id');
        $employeeId   = $this->request->getGet('employee_id');
        $year         = $this->request->getGet('year');
        $month        = $this->request->getGet('month');

        $db      = \Config\Database::connect();
        $builder = $db->table('employee_incentives ei')
            ->select('ei.*, user_info.firstname, user_info.lastname, user_info.employee_id as emp_code, department.department_name')
            ->join('user_info', 'user_info.user_id = ei.employee_id', 'left')
            ->join('department', 'department.id = ei.department_id', 'left');

        if (!empty($departmentId) && $departmentId !== 'all') {
            $builder->where('ei.department_id', (int) $departmentId);
        }
        if (!empty($employeeId) && $employeeId !== 'all') {
            $builder->where('ei.employee_id', (int) $employeeId);
        }
        if (!empty($year) && $year !== 'all') {
            $builder->where('ei.year', (int) $year);
        }
        if (!empty($month) && $month !== 'all') {
            $builder->where('ei.month', (int) $month);
        }

        return $builder->orderBy('ei.year', 'DESC')
            ->orderBy('ei.month', 'DESC')
            ->orderBy('ei.id', 'DESC');
    }

    /**
     * GET /api/incentives/list
     */
    public function list()
    {
        [$user, $error] = $this->checkApiAccess();
        if ($error) {
            return $error;
        }

        $rows = $this->buildFilteredListQuery()->get()->getResultArray();

        $data = array_map(static function ($row) {
            $fullName  = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
            $empCode   = !empty($row['emp_code']) ? $row['emp_code'] : ('K-' . str_pad((string) $row['employee_id'], 4, '0', STR_PAD_LEFT));
            $monthNum  = (int) $row['month'];
            $monthName = date('F', mktime(0, 0, 0, $monthNum, 1));

            return [
                'id'               => (int) $row['id'],
                'employee_id'      => (int) $row['employee_id'],
                'emp_code'         => $empCode,
                'employee_name'    => $fullName ?: 'N/A',
                'department_id'    => $row['department_id'] ? (int) $row['department_id'] : null,
                'department_name'  => $row['department_name'] ?? 'N/A',
                'incentive_type'   => $row['incentive_type'],
                'percentage'       => (float) $row['percentage'],
                'base_salary'      => (float) $row['base_salary'],
                'incentive_amount' => (float) $row['incentive_amount'],
                'month'            => $monthNum,
                'year'             => (int) $row['year'],
                'month_year_label' => $monthName . ' ' . $row['year'],
                'remarks'          => $row['remarks'] ?? '',
            ];
        }, $rows);

        return $this->respond([
            'status' => true,
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/incentives/export
     */
    public function exportExcel()
    {
        // Support both JWT token and active web session for direct download links
        $sessionRole = strtolower((string) (session()->get('role') ?? ''));
        if (!in_array($sessionRole, ['admin', 'hr'], true)) {
            [$user, $error] = $this->checkApiAccess();
            if ($error) {
                return $error;
            }
        }

        $rows = $this->buildFilteredListQuery()->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Employee Incentives');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Emp ID',
            'C1' => 'Employee Name',
            'D1' => 'Department',
            'E1' => 'Month/Year',
            'F1' => 'Incentive Type',
            'G1' => 'Percentage (%)',
            'H1' => 'Base Salary (INR)',
            'I1' => 'Incentive Amount (INR)',
            'J1' => 'Remarks',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F4A33']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $rowNum = 2;
        $sno    = 1;
        foreach ($rows as $row) {
            $fullName  = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
            $empCode   = !empty($row['emp_code']) ? $row['emp_code'] : ('K-' . str_pad((string) $row['employee_id'], 4, '0', STR_PAD_LEFT));
            $monthName = date('F', mktime(0, 0, 0, (int) $row['month'], 1));

            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $empCode);
            $sheet->setCellValue('C' . $rowNum, $fullName ?: 'N/A');
            $sheet->setCellValue('D' . $rowNum, $row['department_name'] ?? 'N/A');
            $sheet->setCellValue('E' . $rowNum, $monthName . ' ' . $row['year']);
            $sheet->setCellValue('F' . $rowNum, $row['incentive_type']);
            $sheet->setCellValue('G' . $rowNum, (float) $row['percentage']);
            $sheet->setCellValue('H' . $rowNum, (float) $row['base_salary']);
            $sheet->setCellValue('I' . $rowNum, (float) $row['incentive_amount']);
            $sheet->setCellValue('J' . $rowNum, $row['remarks'] ?? '');

            $sheet->getStyle("G{$rowNum}:I{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
            $rowNum++;
        }

        if ($rowNum > 2) {
            $sheet->getStyle('A1:J' . ($rowNum - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D9D9D9'],
                    ],
                ],
            ]);
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'Employee_Incentives_' . date('Y_m_d_His') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($excelOutput);
    }
}
