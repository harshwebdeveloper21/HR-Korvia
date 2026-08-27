<?php
namespace App\Controllers\Api;

use App\Models\JoblocationModel;
use App\Models\StateModel;
use App\Models\JobLocationAddressModel;
use CodeIgniter\Controller;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JobaddressController extends ResourceController {
 
    private $joblocationModel;
    private $jobLocationAddressModel;
    private $authService;

    public function __construct()
    {
       
        $this->joblocationModel = new JoblocationModel();
        $this->jobLocationAddressModel = new JobLocationAddressModel();
        $this->authService = new AuthService(service('request'));
    }
    public function display()
    {

        return view('offficeaddress/view');
    }
   public function creates()
    {

        $joblocationModel = new JoblocationModel();
        $cityModel = new \App\Models\CityModel();
        $countryModel = new \App\Models\CountryModel();
        $stateModel = new \App\Models\StateModel();
        $locations = $joblocationModel->findAll();
        $cities = $cityModel->findAll();
         $states = $stateModel->findAll();
        $countries = $countryModel->findAll();
        return view('offficeaddress/add_address', ['locations' => $locations,  'cities' => $cities,
        'countries' => $countries,'states'=> $states]);
        // return view('offficeaddress/add_address');
    }
    public function store()
    {
         // Authenticate the user
         $user = $this->authService->check();
         if (!$user) {
             return $this->failUnauthorized('Unauthorized: Token missing or invalid');
         }
        $validation = \Config\Services::validation();
        
        // Validation Rules
        $validationRules = [
            'locations_id' => 'required|integer',
            'address'     => 'required|min_length[5]|max_length[500]',
            'city_id'        => 'required|integer',
            'state_id'       => 'required|integer',
            'country_id'     => 'required|integer',
            'postal_code' => 'required|numeric|exact_length[6]'
        ];

        // Custom Validation Messages
        $validationMessages = [
            'locations_id' => [
                'required' => 'location field is required.',
                'integer'  => 'location ID must be a valid number.'
            ],
            'address' => [
                'required'   => 'address field is required.',
                'min_length' => 'address must be at least 5 characters long.',
                'max_length' => 'address must not exceed 500 characters.'
            ],
            'city_id' => [
                'required'   => 'city field is required.',
                'integer'  => 'city ID must be a valid number.'
               
            ],
            'state_id' => [
                'required'   => 'state field is required.',
              
                'integer' => 'country ID must be a valid number',
               
            ],
            'country_id' => [
                'required'   => 'country field is required.',
                'integer'  => 'country ID must be a valid number.'
                
            ],
            'postal_code' => [
                'required'    => 'postal code field is required.',
                'numeric'     => 'postal code must contain only numbers.',
                'exact_length'=> 'postal code must be exactly 6 digits.'
            ]
        ];

        // Run validation
        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Save data if validation passes
        $model = new JobLocationAddressModel();
        $model->save($this->request->getPost());

        return $this->respond([
            'status' => 'success',
            'message' => 'Address added successfully'
        ]);
    }
    // public function getAll()
    // {
    //     // Check if the user is authorized
    //     $user = $this->authService->check();
    //     if (!$user) {
    //         return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    //     }
    
    //     // Role-based access control
    //     if (!in_array($user->role, ['admin', 'hr'])) {
    //         return $this->failForbidden('Forbidden: You do not have access to this resource');
    //     }
    
    //     $model = new JobLocationAddressModel();
    
    //     // Correct Query
    //     $jobs = $model->select('
    //       jl.location_id, 
    //      jla.address_id, 
    //             jl.job_location, 
    //             jla.address, 
    //             jla.city
    //         ')
    //         ->from('job_location jl') // Define alias for job_location table
    //         ->join('job_location_addresses jla', 'jl.location_id = jla.locations_id', 'left') // Proper join
    //         ->orderBy('jla.created_at', 'DESC')
    //         ->findAll();
    
    //         if (empty($jobs)) {
    //             return $this->respond([
    //                 'status' => 'error',
    //                 'message' => 'No job locations found.',
                   
    //             ], 404);
    //         }
    
    //     return $this->respond([
    //         'status' => 'success',
    //         'locations' => $jobs
    //     ]);
    // }
     public function getAll()
    {
        // Check if the user is authorized
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
        $model = new JobLocationAddressModel();
        $jobs = $model->select('job_location_addresses.address_id,job_location.job_location,job_location_addresses.address,city.city_name')
            ->join('job_location', 'job_location_addresses.locations_id = job_location.location_id')
            ->join('city', 'job_location_addresses.city_id = city.id')
            ->orderBy('job_location_addresses.created_at', 'DESC')
            ->findAll();

        return $this->respond(['status' => 'success', 'data' => $jobs]);
    }
    // public function delete($id = null)
    // {
    //     // Check if the user is authorized
    //     $user = $this->authService->check();
    //     if (!$user) {
    //         return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    //     }
    
    //     // Only admin can delete
    //     if ($user->role !== 'admin') {
    //         return $this->failForbidden('Forbidden: Only Admin can delete address records');
    //     }
    
    //     // Ensure $id is provided
    //     if (!$id) {
    //         return $this->fail('Invalid request: Missing address ID', 400);
    //     }
    
    //     $model = new JobLocationAddressModel();
    
    //     // Check if the record exists
    //     $record = $model->find($id);
    //     if (!$record) {
    //         return $this->failNotFound('Job address not found');
    //     }
    
    //     // Delete the record
    //     if ($model->delete($id)) {
    //         return $this->respond(['status' => 'success', 'message' => 'Job address deleted successfully']);
    //     }
    
    //     return $this->failServerError('Failed to delete job address');
    // }
    
    public function delete($id = null)
    {
        // Authenticate the user
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        // Only admin can delete
        if ($user->role !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admin can delete job addresses');
        }
    
        // Ensure $id is provided
        if (!$id) {
            return $this->fail('Invalid request: Missing address ID', 400);
        }
    
        $db = \Config\Database::connect();
        $model = new JobLocationAddressModel();
    
        // Check if the record exists
        $record = $model->find($id);
        if (!$record) {
            return $this->failNotFound('Job address not found');
        }
    
        // Check if the job address is being used in the jobs table
        $jobCount = $db->table('jobs')->where('addresses_id', $id)->countAllResults();
    
        if ($jobCount > 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'This Job Address is associated with job records and cannot be deleted.'
            ], 400);
        }
    
        // Proceed with deletion
        if ($model->delete($id)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Job address deleted successfully'
            ]);
        }
    
        return $this->failServerError('Failed to delete job address');
    }
    
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

        $record = $this->jobLocationAddressModel
            ->select('job_location_addresses.*,job_location.job_location,city.city_name,country.country_name,states.state_name')
            ->join('job_location', 'job_location.location_id = job_location_addresses.locations_id', 'left')
            ->join('city', 'job_location_addresses.city_id = city.id')
            ->join('country', 'job_location_addresses.country_id = country.id')
              ->join('states', 'job_location_addresses.state_id = states.id')
            ->where('job_location_addresses.address_id', $id)
            ->first();
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Leave type not found'], 404);
    }
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

        // Add gender validation
        $validationRules = [
            'locations_id' => 'required|integer',
            'address'     => 'required|min_length[5]|max_length[500]',
            'city_id'        => 'required',
            'state_id'       =>'required',
            'country_id'     => 'required',
            'postal_code' => 'required|numeric|exact_length[6]'
        ];

        // Custom Validation Messages
        $validationMessages = [
            'locations_id' => [
                'required' => 'location field is required.',
                'integer'  => 'location ID must be a valid number.'
            ],
            'address' => [
                'required'   => 'address field is required.',
                'min_length' => 'address must be at least 5 characters long.',
                'max_length' => 'address must not exceed 500 characters.'
            ],
            'city_id' => [
                'required'   => 'city field is required.',
              
            ],
            'state_id' => [
                'required'   => 'state field is required.',
               
            ],
            'country_id' => [
                'required'   => 'country field is required.',
                
            ],
            'postal_code' => [
                'required'    => 'postal code field is required.',
                'numeric'     => 'postal code must contain only numbers.',
                'exact_length'=> 'postal code must be exactly 6 digits.'
            ]
        ];

        // Run validation
        if (!$this->validate($validationRules, $validationMessages)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }
 
        // Ensure gender is included in update data
        $updateData = [
            'locations_id'    => $data['locations_id'],
            'address' => $data['address'],
            'city_id' => $data['city_id'],
            'state_id'     => $data['state_id'],
            'country_id'   => $data['country_id'],
            'postal_code'   => $data['postal_code'],
        

          
        ];

        if ($this->jobLocationAddressModel->update($id, $updateData)) {
            return $this->respond(['status' => 'success', 'message' => 'Job updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update job'], 500);
    }

    /**
     * Export Job Addresses to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized: Token missing or invalid']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->jobLocationAddressModel->builder();
        $builder->select('job_location_addresses.*, job_location.job_location, city.city_name, states.state_name, country.country_name')
            ->join('job_location', 'job_location.location_id = job_location_addresses.locations_id', 'left')
            ->join('city', 'city.id = job_location_addresses.city_id', 'left')
            ->join('states', 'states.id = job_location_addresses.state_id', 'left')
            ->join('country', 'country.id = job_location_addresses.country_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('job_location.job_location', $search)
                ->orLike('job_location_addresses.address', $search)
                ->orLike('city.city_name', $search)
                ->orLike('states.state_name', $search)
                ->orLike('country.country_name', $search)
                ->orLike('job_location_addresses.postal_code', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('job_location_addresses.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Job Addresses');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Job Location',
            'C1' => 'Address',
            'D1' => 'City',
            'E1' => 'State',
            'F1' => 'Country',
            'G1' => 'Postal Code',
            'H1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['job_location'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['address'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, $item['city_name'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $item['state_name'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $item['country_name'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, $item['postal_code'] ?? '-');
            $sheet->setCellValue('H' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Job_Addresses_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

?>