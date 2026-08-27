<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CountryModel;
use App\Models\UserInfoModel;
use App\Models\JobLocationAddressModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CountryController extends ResourceController
{
    private $countryModel;
    private $userInfoModel;
    private $jobLocationModel;
    private $authService;

    public function __construct()
    {
        $this->countryModel = new CountryModel();
        $this->userInfoModel = new UserInfoModel();
        $this->jobLocationModel = new JobLocationAddressModel();
        $this->authService = new AuthService(service('request'));
    }

    // Create Department
    public function create()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    if (!in_array($user->role, ['admin', 'hr'])) {
        return $this->failForbidden('Forbidden: You do not have access to this resource');
    }

    $data = $this->request->getPost();

    if (!$this->validate([
        'country_name' => 'required|string',
    ])) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $this->validator->getErrors()
        ], 400);
    }

    // ✅ Normalize input for comparison and save
    $data['country_name'] = ucwords(strtolower(trim($data['country_name'])));

    // ✅ Check for duplicate (case-insensitive)
    $existing = $this->countryModel
        ->where('LOWER(country_name)', strtolower($data['country_name']))
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Country already exists.'
        ], 409); // 409 Conflict
    }

    $data['created_by'] = $user->sub;

    if ($this->countryModel->insert($data)) {
        return $this->respond([
            'status' => 'success',
            'message' => 'Country record added successfully'
        ], 201);
    }

    return $this->respond([
        'status' => 'error',
        'message' => 'Failed to add Country record'
    ], 500);
}

    // Display All Departments
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $records = $this->countryModel->orderBy('created_at', 'DESC')->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }
   
    // Update Department
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $data = $this->request->getPost();

        if ($this->countryModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Country record updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update Country record'], 500);
    }
 
    // Delete Payroll Record (Admin Only)
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
    
        // Check if the country is linked to employees or jobs
        $employeeCount = $this->userInfoModel->where('country_id', $id)->countAllResults();
        $jobCount = $this->jobLocationModel->where('country_id', $id)->countAllResults();
    
        if ($employeeCount > 0 || $jobCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This country is associated with employees or jobs and cannot be deleted.'
            ], 400);
        }
    
        // Proceed with deletion if no dependencies
        if ($this->countryModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'Country record deleted successfully']);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'Failed to delete Country record'], 500);
    }
    

    // Add JWT authorization to protected routes with role checking
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
    
        $record = $this->countryModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }
    
        return $this->respond(['status' => 'error', 'message' => 'Leave type not found'], 404);
    }

    public function creates()
    {
        return view('country/country');
    }

    public function display()
    {
        return view('country/view');
    }
    public function getAllCountry()
    {
        $countryModel = new CountryModel();
        $countries  = $countryModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $countries 
        ]);
    }
   public function addCountry()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    $countryName = trim($this->request->getPost('country_name'));

    if (!$this->validate([
        'country_name' => 'required|string|min_length[2]',
    ])) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->validator->getErrors()
        ]);
    }

    $countryModel = new CountryModel();

    // 🔍 Check for existing country (case-insensitive)
    $existing = $countryModel->where('LOWER(country_name)', strtolower($countryName))->first();
    if ($existing) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'This country already exists.'
        ]);
    }

    $data = [
        'country_name' => $countryName,
        'created_by' => $user->sub
    ];

    if ($countryModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'country' => [
                'id' => $countryModel->insertID(),
                'country_name' => $countryName
            ]
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to add country. Please try again.'
        ]);
    }
}

    /**
     * Export Countries to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized: Token missing or invalid']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->countryModel->builder();
        $builder->select('country.*, users.username as creator_name')
            ->join('users', 'users.id = country.created_by', 'left');

        if (!empty($search)) {
            $builder->like('country.country_name', $search);
        }

        $records = $builder->orderBy('country.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Countries');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Country Name',
            'C1' => 'Created By',
            'D1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['country_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['creator_name'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Countries_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
