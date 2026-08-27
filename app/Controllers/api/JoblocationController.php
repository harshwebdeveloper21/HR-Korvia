<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\JoblocationModel;
use App\Services\AuthService;
use CodeIgniter\Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JoblocationController extends ResourceController
{
    protected $authService;
    private $joblocationModel;

    public function __construct()
    {
        // Inject the AuthService
        $this->authService = Services::auth($this->request);
        // Initialize the DepartmentModel
        $this->joblocationModel = new JoblocationModel();
    }

    // Create Department
    public function create()
    {
        $user = $this->authorize(['admin', 'hr']);
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $data = $this->request->getPost();

        // Validate incoming data
        if (!$this->validate([
            'job_location' => 'required|min_length[3]',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check if job location already exists
        $locationModel = new \App\Models\JoblocationModel();
        $existingLocation = $locationModel->where('job_location', $data['job_location'])->first();

        if ($existingLocation) {
            return $this->fail('Job location already exists.', 409); // 409 Conflict
        }

        // Insert the new location
        $locationId = $locationModel->insert([
            'job_location' => $data['job_location'],
        ]);

        return $this->respondCreated([
            'message' => 'Job location created successfully!',
            'location_id' => $locationId,
        ]);
    }


    // Update Department
    public function update($id = null)
    {
        $user = $this->authorize(['admin', 'hr']);
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        // Fetch existing job location record
        $existingLocation = $this->joblocationModel->find($id);
        if (!$existingLocation) {
            return $this->failNotFound('Location not found');
        }

        $data = $this->request->getPost();

        // Validation rules
        if (!$this->validate([
            'job_location' => 'required|min_length[3]', // Require input & min 3 characters
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check if the same location name already exists in another record
        $duplicateLocation = $this->joblocationModel
            ->where('job_location', $data['job_location'])
            ->where('location_id !=', $id) // Exclude current record
            ->first();

        if ($duplicateLocation) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'This location name already exists! Please use a different name.'
            ], 400);
        }

        // Check if the new location is the same as the existing one
        if ($data['job_location'] === $existingLocation['job_location']) {
            return $this->respond([
                'status'  => 'info',
                'message' => 'No changes were made! The location is already up to date.'
            ]);
        }

        // Update only if the location is different
        $updateData = [
            'job_location' => $data['job_location'],
        ];

        if ($this->joblocationModel->update($id, $updateData)) {
            return $this->respond([
                'status'  => 'success',
                'message' => 'Location updated successfully!',
            ]);
        }

        return $this->failServerError('Failed to update location');
    }



    // Display All Departments
    public function index()
    {
        $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $locations = $this->joblocationModel->orderBy('created_at', 'DESC')->findAll();

        return $this->respond([
            'locations' => $locations,
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

        $record = $this->joblocationModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'location not found'], 404);
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
    // public function delete($id = null)
    // {
    //     $user = $this->authorize(['admin', 'hr']); // Only allow admin and hr roles
    //     if (!$user) {
    //         return $this->failUnauthorized('Unauthorized access');
    //     }

    //     $location = $this->joblocationModel->find($id);

    //     if (!$location) {
    //         return $this->failNotFound('location not found');
    //     }

    //     $deleted = $this->joblocationModel->delete($id);

    //     if (!$deleted) {
    //         return $this->failServerError('Failed to delete location');
    //     }

    //     return $this->respond([
    //         'message' => 'location deleted successfully!',
    //     ]);
    // }
    public function delete($id = null)
    {
        $user = $this->authorize(['admin', 'hr']); // Only allow admin and HR roles
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        // Check if the location exists
        $locationModel = new \App\Models\JoblocationModel();
        $location = $locationModel->find($id);

        if (!$location) {
            return $this->failNotFound('Location not found');
        }

        // Check if this location is assigned to any job
        $jobModel = new \App\Models\JobModel();
        $jobsUsingLocation = $jobModel->where('locations_id', $id)->countAllResults();

        if ($jobsUsingLocation > 0) {
            return $this->fail('Cannot delete location. It is assigned to existing jobs.');
        }

        // Proceed with deletion if no jobs are using this location
        if (!$locationModel->delete($id)) {
            return $this->failServerError('Failed to delete location');
        }

        return $this->respond([
            'message' => 'Location deleted successfully!',
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
        return view('joblocation/addlocation');
    }

    public function display()
    {
        return view('joblocation/view');
    }

    /**
     * Export Job Locations to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authorize(['admin', 'hr', 'employee']);
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized access']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->joblocationModel->builder();
        $builder->select('job_location.*');

        if (!empty($search)) {
            $builder->like('job_location.job_location', $search);
        }

        $records = $builder->orderBy('job_location.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Job Locations');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Job Location',
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
            $sheet->setCellValue('B' . $rowNum, $item['job_location'] ?? '-');
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

        $filename = 'Job_Locations_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
