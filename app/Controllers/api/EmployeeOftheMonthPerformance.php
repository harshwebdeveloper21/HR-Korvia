<?php

namespace App\Controllers\Api;

use App\Models\EmployeeOfTheMonthModel;
use App\Models\EmployeeOfMonthPerformanceModel;
use App\Models\UserModel;
use App\Models\PerformanceModel;
use App\Models\CompanyLogoModel;
use App\Models\DesignationModel;
use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use Dompdf\Options;

class EmployeeOftheMonthPerformance extends ResourceController
{
    protected $employeeOfTheMonthModel;
    protected $userModel;
    protected $performanceModel;
    protected $companyLogoModel;
    protected $designationModel;

    public function __construct()
    {
        $this->employeeOfTheMonthModel = new EmployeeOfTheMonthModel();
        $this->userModel = new UserModel();
        $this->performanceModel = new PerformanceModel();
        $this->companyLogoModel = new CompanyLogoModel();
        $this->designationModel = new DesignationModel(); // <-- Add this

        helper(['form']);
    }

    public function view()
    {
        // Get all active employees with their highest performance rating
        $topEmployees = $this->userModel
            ->select('users.id, users.username, MAX(performance.rating) as rating')
            ->join('performance', 'users.id = performance.user_id', 'left')
            ->where('users.is_deleted', 0)
            ->whereIn('users.role', ['employee', 'hr'])
            ->groupBy('users.id')
            ->orderBy('rating', 'DESC')
            ->findAll();

        // Get all templates
        $letterTemplates = $this->employeeOfTheMonthModel->findAll();

        return view('empofmonth/empMonthPerformace/select', [
            'topEmployees' => $topEmployees,
            'letterTemplates' => $letterTemplates
        ]);
    }

    // Function to parse template data and replace placeholders with actual data
    public function parseTemplate($templateContent, $data)
    {
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }
        return $templateContent;
    }

    public function generatePerformancePdf($employeeId, $templateId, $monthYear = null)
    {
        $employee = $this->userModel->find($employeeId);

        // Filter performance for selected month if provided
        $performanceQuery = $this->performanceModel->where('user_id', $employeeId);
        if ($monthYear) {
            $performanceQuery->where('DATE_FORMAT(review_date, "%Y-%m")', $monthYear);
        }
        $performance = $performanceQuery->orderBy('review_date', 'DESC')->first();

        if (!$performance) {
            return $this->respond(['status' => false, 'message' => 'Performance data not found for the selected month.'], 400);
        }

        $designationName = '';
        if (!empty($performance['designation_id'])) {
            $designation = $this->designationModel->find($performance['designation_id']);
            $designationName = $designation['designation_name'] ?? '';
        }

        $template = $this->employeeOfTheMonthModel->find($templateId);
        $creator = null;
        if ($template && !empty($template['created_by'])) {
            $creator = $this->userModel->find($template['created_by']);
        }

        $company = $this->companyLogoModel->first();

        // ✅ Fix for mime_content_type error
        $logoImgTag = '<!-- Logo not found -->';
        $logoPath = FCPATH . 'upload/' . $company['logo_img'];
        if (file_exists($logoPath)) {
            $logoMimeType = mime_content_type($logoPath);
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (in_array($logoMimeType, $allowedTypes)) {
                $logoBase64 = base64_encode(file_get_contents($logoPath));
                $logoImgTag = '<img src="data:' . $logoMimeType . ';base64,' . $logoBase64 . '" height="80" class="offer-letter-logo">';
            }
        }

        // Prepare dynamic data
        $data = [
            'today_date' => date('F j, Y'),
            'today_month' => date('M Y'),
            'logo_img' => $logoImgTag,
            'designation_name' => $designationName,
            'username' => $employee['username'],
            'goals_achieved' => $performance['goals_achieved'],
            'team_work' => $performance['team_work'],
            'management' => $performance['management'],
            'presentation_skill' => $performance['presentation_skill'],
            'behaviour' => $performance['behaviour'],
            'rating' => $performance['rating'] ?? '',
            'created_by' => $creator['username'] ?? 'Admin',
            'company_name' => $company['company_name'] ?? '',
            'company_address' => $company['company_address'] ?? '',
            'company_phone' => $company['company_phone'] ?? '',
            'company_email' => $company['company_email'] ?? '',
        ];

        $templateContent = $template['content'] ?? '';
        $finalContent = $this->parseTemplate($templateContent, $data);

        $finalHtml = view('empofmonth/empMonthPerformace/empof_month_preview', [
            'content' => $finalContent,
            'logo_img' => $data['logo_img'],
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'company_phone' => $data['company_phone'],
            'company_email' => $data['company_email'],
        ]);

        // Initialize and configure Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($finalHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Employee_of_the_Month.pdf';
        $disposition = $this->request->getGet('download') ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setHeader('Content-Type', 'application/pdf; charset=utf-8')
            ->setBody($dompdf->output());
    }


    public function generateAndSaveCertificate()
    {
        $employeeId = $this->request->getPost('employee_id');
        $templateId = $this->request->getPost('template_id');
        $monthYear  = $this->request->getPost('month_year');

        // Validate future date
        $currentDate = date('Y-m');
        if ($monthYear > $currentDate) {
            return $this->respond([
                'status' => false,
                'message' => 'The selected month/year cannot be in the future.'
            ], 400);
        }

        // Set a default template if none is selected
        if (!$templateId) {
            $defaultTemplate = $this->employeeOfTheMonthModel->first();
            if ($defaultTemplate) {
                $templateId = $defaultTemplate['id'];
            }
        }

        // Validate required fields
        if (!$employeeId) {
            return $this->respond([
                'status' => false,
                'message' => 'Please select an Employee.'
            ], 400);
        }

        // Set default month to current month if not provided
        if (!$monthYear) {
            $monthYear = date('Y-m');
        }

        $certModel = new EmployeeOfMonthPerformanceModel();

        // Check if certificate already exists for that user and month
        $existingCertificate = $certModel
            ->where('user_id', $employeeId)
            ->where('month_year', $monthYear)
            ->first();

        if ($existingCertificate) {
            // Update the existing certificate
            $certModel->update($existingCertificate['id'], [
                'template_id' => $templateId,
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new certificate record
            $certModel->save([
                'user_id'     => $employeeId,
                'template_id' => $templateId,
                'month_year'  => $monthYear,
                'created_by'  => session()->get('user_id'),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Generate and return PDF
        return $this->generatePerformancePdf($employeeId, $templateId, $monthYear);
    }


    public function AllEmpOfMonth()
    {

        return view('empofmonth/empMonthPerformace/all_empof_month');
    }
    public function getAllPerformances()
    {
        $model = new EmployeeOfMonthPerformanceModel();

        $data = $model
            ->select('employee_of_month_certificates.*, users.username as user_name,user_info.profile_image,templates.title as template_title')
            ->join('users', 'users.id = employee_of_month_certificates.user_id')
            ->join('user_info', 'user_info.user_id = employee_of_month_certificates.user_id', 'left')
            ->join('emp_of_month templates', 'templates.id = employee_of_month_certificates.template_id', 'left')
            ->orderBy('employee_of_month_certificates.created_at', 'DESC')
            ->findAll();

        return $this->respond($data);
    }
    public function deletePerformance($id)
    {
        $model = new EmployeeOfMonthPerformanceModel();
        $performance = $model->find($id);

        if (!$performance) {
            return $this->failNotFound('Record not found');
        }

        $model->delete($id);
        return $this->respondDeleted(['message' => 'Deleted successfully']);
    }
      public function getEmpMonthTemplate($id)
{
    $model = new \App\Models\EmployeeOfTheMonthModel();
    $template = $model->find($id);

    if (!$template) {
        return $this->response->setJSON(['status' => false, 'message' => 'Template not found'])->setStatusCode(404);
    }

    return $this->response->setJSON([
        'status' => true,
        'data' => [
            'title' => $template['title'],
            'content' => $template['content'],
            'emp_image' => base_url('upload/' . $template['emp_image']),
        ]
    ]);
}
}
