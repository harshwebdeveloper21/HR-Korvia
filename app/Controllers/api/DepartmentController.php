<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DepartmentModel;
use App\Models\UserInfoModel;
use App\Models\OnboardingModel;
use App\Models\TaskModel;
use App\Models\TrainingModel;
use App\Models\JobModel;
use App\Models\PerformanceModel;
use App\Services\AuthService;
use CodeIgniter\Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DepartmentController extends ResourceController
{
    protected $authService;
    private $departmentModel;
    private $userInfoModel;
    private $jobModel;
    private $taskPerformanceModel;
    private $trainingModel;
    private $taskModel;
    private $onboardingPerformanceModel;

    public function __construct()
    {
        // Inject the AuthService
        $this->authService = Services::auth($this->request);
        // Initialize the DepartmentModel
        $this->departmentModel = new DepartmentModel();
        $this->taskPerformanceModel = new PerformanceModel();
        $this->jobModel = new JobModel();
        $this->trainingModel = new TrainingModel();
        $this->taskModel = new TaskModel();
        $this->onboardingPerformanceModel = new OnboardingModel();
        $this->userInfoModel = new UserInfoModel();
    }

    // Create Department
    public function create()
{
    $user = $this->authorize(['admin', 'hr']);
    if (!$user) {
        return $this->failUnauthorized('Unauthorized access');
    }

    $data = $this->request->getPost();

    // Validation
    if (!$this->validate([
        'department_name' => 'required|min_length[3]',
    ])) {
        return $this->failValidationErrors($this->validator->getErrors());
    }

    // ✅ Normalize department name
    $departmentName = ucwords(strtolower(trim($data['department_name'])));

    // ✅ Check if department already exists (case-insensitive)
    $existing = $this->departmentModel
        ->where('LOWER(department_name)', strtolower($departmentName))
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Department already exists.'
        ], 409); // 409 Conflict
    }

    // Insert the department
    $departmentId = $this->departmentModel->insert([
        'department_name' => $departmentName,
    ]);

    return $this->respondCreated([
        'message' => 'Department created successfully!',
        'id' => $departmentId,
    ]);
}

    // Update Department
    public function update($id = null)
    {
        $user = $this->authorize(['admin', 'hr']);
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $data = $this->request->getPost();

        // Validate incoming data
        if (!$this->validate([
            'department_name' => 'required|min_length[3]',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Update the department
        $updated = $this->departmentModel->update($id, [
            'department_name' => $data['department_name'],
        ]);

        if (!$updated) {
            return $this->failServerError('Failed to update department');
        }

        return $this->respond([
            'message' => 'Department updated successfully!',
        ]);
    }



    // Display All Departments
    public function index()
    {
        $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $departments = $this->departmentModel->orderBy('created_at', 'DESC')->findAll();

        return $this->respond([
            'departments' => $departments,
        ]);
    }


    // Display Single Department
    public function getById($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Only Admin and HR can access leave records
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $record = $this->departmentModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'department not found'], 404);
    }


    // // Update Department
    // public function update($id = null)
    // {
    //     $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
    //     if (!$user) {
    //         return $this->failUnauthorized('Unauthorized access');
    //     }

    //     $data = $this->request->getPost();

    //     // Validate incoming data
    //     if (!$this->validate([
    //         'department_name' => 'required|min_length[3]',
    //     ])) {
    //         return $this->failValidationErrors($this->validator->getErrors());
    //     }

    //     // Update the department
    //     $updated = $this->departmentModel->update($id, [
    //         'department_name' => $data['department_name'],
    //     ]);

    //     if (!$updated) {
    //         return $this->failServerError('Failed to update department');
    //     }

    //     return $this->respond([
    //         'message' => 'Department updated successfully!',
    //     ]);
    // }

    // Delete Department
    public function delete($id = null)
{
    $user = $this->authorize(['admin', 'hr']); // Only allow admin and HR roles
    if (!$user) {
        return $this->failUnauthorized('Unauthorized access');
    }

    // Check if the department exists
    $department = $this->departmentModel->find($id);
    if (!$department) {
        return $this->failNotFound('Department not found');
    }

    // Check if the department is used in other tables
    $employeeCount = $this->userInfoModel->where('department_id', $id)->countAllResults();
    $jobCount = $this->jobModel->where('department_id', $id)->countAllResults();
    // $taskPerformanceCount = $this->taskPerformanceModel->where('department_id', $id)->countAllResults();
    $onboardingPerformanceCount = $this->onboardingPerformanceModel->where('department_id', $id)->countAllResults();
    $trainingCount = $this->trainingModel->where('department_id', $id)->countAllResults();
    $taskCount = $this->taskModel->where('department_id', $id)->countAllResults();

    // If the department is associated with any of these, prevent deletion
    if ($employeeCount > 0 || $jobCount > 0|| 
        $onboardingPerformanceCount > 0 || $trainingCount > 0 || $taskCount > 0) {
        return $this->respond([
            'status' => 'error',
            'message' => 'This department is associated with employees, jobs, or other tasks and cannot be deleted.'
        ], 400);
    }

    // Proceed with deletion if no dependencies exist
    if (!$this->departmentModel->delete($id)) {
        return $this->failServerError('Failed to delete department');
    }

    return $this->respond([
        'status' => 'success',
        'message' => 'Department deleted successfully!'
    ]);
}


    // Add JWT authorization to protected routes with role checking
    private function authorize($roles = [])
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, $roles)) {
            log_message('error', 'Unauthorized access: User does not have the required role');
            return false;
        }
        return $user;
    }

    public function creates()
    {
        return view('department/department');
    }

    public function display()
    {
        return view('department/view');
    }
    public function getAllDepartement()
    {
        $countryModel = new DepartmentModel();
        $departments  = $countryModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $departments 
        ]);
    }
   public function addDepartment()
{
    $departmentModel = new \App\Models\DepartmentModel();
    $departmentName = trim($this->request->getPost('department_name'));

    if (empty($departmentName)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Department Name is required.'
        ]);
    }

    // Case-insensitive duplicate check
    $existing = $departmentModel
        ->where('LOWER(department_name)', strtolower($departmentName))
        ->first();

    if ($existing) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'This department already exists.'
        ]);
    }

    // Insert new department
    $data = ['department_name' => $departmentName];
    $departmentId = $departmentModel->insert($data);

    if ($departmentId) {
        return $this->response->setJSON([
            'success' => true,
            'department' => [
                'id' => $departmentId,
                'department_name' => $departmentName
            ]
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add department.'
        ]);
    }
}

 

    // In DepartmentController.php

    public function getDepartments()
    {
       $model = new DepartmentModel();
            $departments = $model->findAll();

            return $this->response->setJSON([
                'status' => true,
                'departments' => $departments,
            ]);
    }

    /**
     * Export Departments to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authorize(['admin', 'hr', 'employee']);
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized access']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->departmentModel->builder();
        $builder->select('department.*');

        if (!empty($search)) {
            $builder->like('department.department_name', $search);
        }

        $records = $builder->orderBy('department.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Departments');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Department Name',
            'C1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['department_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Departments_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
