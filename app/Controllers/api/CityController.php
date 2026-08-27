<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\UserInfoModel;
use App\Models\JobLocationAddressModel;
use App\Services\AuthService;
use CodeIgniter\Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CityController extends ResourceController
{
    private $cityModel;
    private $userInfoModel;
    private $jobLocationModel;
    private $authService;

    public function __construct()
    {
        $this->cityModel = new CityModel();
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
        'city_name' => 'required',
        'country_id' => 'required',
    ])) {
        return $this->respond([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $this->validator->getErrors()
        ], 400);
    }

    // ✅ Duplicate check (case-insensitive match)
    $existing = $this->cityModel
        ->where('LOWER(city_name)', strtolower(trim($data['city_name'])))
        ->where('country_id', $data['country_id'])
        ->first();

    if ($existing) {
        return $this->respond([
            'status' => 'error',
            'message' => 'City with the same name already exists in the selected country.'
        ], 409); // 409 Conflict
    }

    $data['created_by'] = $user->sub;

    if ($this->cityModel->insert($data)) {
        return $this->respond([
            'status' => 'success',
            'message' => 'City record added successfully'
        ], 201);
    }

    return $this->respond([
        'status' => 'error',
        'message' => 'Failed to add City record'
    ], 500);
}

    // Display All Cities
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }   

        $records = $this->cityModel->orderBy('created_at', 'DESC')->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }

    // Display Single City
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

        $record = $this->cityModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'city type not found'], 404);
    }


    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        // Ensure only Admin and HR can update the city
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        // Get the incoming data
        $data = $this->request->getPost(); // Retrieve the form data (city_name, country_id, etc.)

        // Validation: Ensure that city_name and country_id are provided
        if (!$this->validate([
            'city_name' => 'required',
            'country_id' => 'required',
        ])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Update the city data
        if ($this->cityModel->update($id, $data)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'City record updated successfully'
            ]);
        }

        // If update fails
        return $this->respond(['status' => 'error', 'message' => 'Failed to update City record'], 500);
    }


    public function creates()
    {
        // Fetch all countries for the dropdown
        $countryModel = new CountryModel();
        $countries = $countryModel->findAll(); // Get all countries from the database

        return view('city/city', ['countries' => $countries]);
    }

    public function display()
    {
        $cityModel = new CityModel();
        $cities = $cityModel->findAll();  // Fetch all cities

        // Fetch country names by ID for each city
        $countryModel = new CountryModel();
        $countries = $countryModel->findAll();

        // Pass cities and countries to the view
        return view('city/view', [
            'cities' => $cities,
            'countries' => $countries
        ]);
    }
    // Delete City
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        // Ensure only Admin and HR can delete the city
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
    
        // Find the city by ID
        $city = $this->cityModel->find($id);
        if (!$city) {
            return $this->respond(['status' => 'error', 'message' => 'City not found'], 404);
        }
    
        // Check if the city is linked to any employees
        $employeeCount = $this->userInfoModel->where('city_id', $id)->countAllResults();
    
        // Check if the city is linked to any job locations
        $jobCount = $this->jobLocationModel->where('city_id', $id)->countAllResults();
    
        // If the city is associated with employees or jobs, prevent deletion
        if ($employeeCount > 0 || $jobCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This city is associated with employees or jobs and cannot be deleted.'
            ], 400);
        }
    
        // Delete the city record
        if ($this->cityModel->delete($id)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'City record deleted successfully'
            ]);
        }
    
        // If deletion fails
        return $this->respond(['status' => 'error', 'message' => 'Failed to delete City record'], 500);
    }
    
    public function getAllCities()
    {
        $cityModel = new CityModel();
        $cities = $cityModel->findAll(); // Fetch all cities

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $cities
        ]);
    }
    public function addCity()
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    $cityName = trim($this->request->getPost('city_name'));
    $countryId = $this->request->getPost('country_id');

    if (!$this->validate([
        'city_name'   => 'required|string|min_length[2]',
        'country_id'  => 'required|is_natural_no_zero',
    ])) {
        return $this->response->setJSON([
            'status' => 'error',
            'errors' => $this->validator->getErrors()
        ]);
    }

    $cityModel = new CityModel();

    // 🔍 Check for duplicate city (case-insensitive)
    $existing = $cityModel->where('country_id', $countryId)
        ->where('LOWER(city_name)', strtolower($cityName))
        ->first();

    if ($existing) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'This city already exists for the selected country.'
        ]);
    }

    $data = [
        'city_name'   => $cityName,
        'country_id'  => $countryId,
        'created_by'  => $user->sub
    ];

    if ($cityModel->insert($data)) {
        return $this->response->setJSON([
            'status' => 'success',
            'city'   => [
                'id' => $cityModel->insertID(),
                'city_name' => $cityName
            ]
        ]);
    }

    return $this->response->setJSON([
        'status'  => 'error',
        'message' => 'Failed to add city. Please try again.'
    ]);
}

    /**
     * Export Cities to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized: Token missing or invalid']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->cityModel->builder();
        $builder->select('city.*, country.country_name, users.username as creator_name')
            ->join('country', 'country.id = city.country_id', 'left')
            ->join('users', 'users.id = city.created_by', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('city.city_name', $search)
                ->orLike('country.country_name', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('city.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cities');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'City Name',
            'C1' => 'Country Name',
            'D1' => 'Created By',
            'E1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['city_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['country_name'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, $item['creator_name'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Cities_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
