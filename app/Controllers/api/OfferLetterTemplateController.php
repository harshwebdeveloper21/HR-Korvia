<?php
namespace App\Controllers\Api;
use App\Models\OfferLetterTemplateModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OfferLetterTemplateController extends ResourceController
{
    protected $templateModel;
    private $authService;

    public function __construct()
    {
        $this->templateModel = new OfferLetterTemplateModel();
        $this->authService = new AuthService(service('request'));

        helper(['form']);
    }

    public function index()
    {
        return view('offer_templates/create');
    }

    public function saveTemplate()
    {
        // Validate user authorization
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required|min_length[10]',
            'template_img' => 'uploaded[template_img]|is_image[template_img]|max_size[template_img,2048]|mime_in[template_img,image/jpg,image/jpeg,image/png,image/webp]'
        ];

        $messages = [
            'title' => [
                'required' => 'Title is required.',
                'min_length' => 'Title must be at least 3 characters.',
            ],
            'content' => [
                'required' => 'Content is required.',
                'min_length' => 'Content must be at least 10 characters long.',
            ],
            'template_img' => [
                'uploaded' => 'Upload a template image.',
                'is_image' => 'Only valid image formats are allowed.',
                'max_size' => 'The image size must not exceed 2MB.',
                'mime_in' => 'Only JPG, JPEG, PNG, and WEBP formats are allowed.',
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ])->setStatusCode(422);
        }

        $model = new \App\Models\OfferLetterTemplateModel();
        $id = $this->request->getPost('id');

        // Prepare data including created_by
        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'created_by' => $user->sub,
        ];

        // Handle image upload
        $file = $this->request->getFile('template_img');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'upload/', $newName);
            $data['template_img'] = $newName;
        }

        if ($id) {
            $model->update($id, $data);
            return $this->response->setJSON(['status' => 'updated']);
        } else {
            $model->insert($data);
            return $this->response->setJSON(['status' => 'created']);
        }
    }

    public function view()
    {

        return view('offer_templates/view');
    }
    public function listTemplates()
    {
        $templates = $this->templateModel->orderBy('created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $templates
        ]);
    }
    public function delete($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid template ID.'
            ])->setStatusCode(400);
        }

        $template = $this->templateModel->find($id);

        if (!$template) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Template not found.'
            ])->setStatusCode(404);
        }

        $this->templateModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Template deleted successfully.'
        ]);
    }
    public function EditPage($id)
    {
        return view('offer_templates/edit_page', ['id' => $id]);
    }

    public function getTemplate($id)
    {
        $model = new \App\Models\OfferLetterTemplateModel();
        $template = $model->find($id);

        if ($template) {
            return $this->response->setJSON(['status' => 'success', 'data' => $template]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found']);
        }
    }
    public function updateTemplate($id)
    {
        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required|min_length[10]',
        ];

        // Only validate image if a new one is uploaded
        $file = $this->request->getFile('template_img');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['template_img'] = 'is_image[template_img]|max_size[template_img,2048]|mime_in[template_img,image/jpg,image/jpeg,image/png,image/webp]';
        }

        $messages = [
            'title' => [
                'required' => 'Title is required.',
                'min_length' => 'Title must be at least 3 characters.',
            ],
            'content' => [
                'required' => 'Content is required.',
                'min_length' => 'Content must be at least 10 characters long.',
            ],
            'template_img' => [
                'is_image' => 'Only valid image formats are allowed.',
                'max_size' => 'The image size must not exceed 2MB.',
                'mime_in' => 'Only JPG, JPEG, PNG, and WEBP formats are allowed.',
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ])->setStatusCode(422);
        }

        $model = new \App\Models\OfferLetterTemplateModel();
        $template = $model->find($id);

        if (!$template) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Template not found']);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ];

        // Only move and save image if uploaded
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'upload/', $newName);
            $data['template_img'] = $newName;
        }

        $model->update($id, $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Template updated successfully']);
    }

    public function templateView($id)
    {
        $model = new \App\Models\OfferLetterTemplateModel();
        $template = $model->find($id);

        if (!$template) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Template not found.'
            ]);
        }

        // Fix image paths in content (convert relative to absolute)
        $template['content'] = str_replace(
            ['../upload/', 'src="upload/'],
            [base_url('upload/') . '/', 'src="' . base_url('upload/') . '/'],
            $template['content']
        );

        return view('offer_templates/template_view', ['templates' => $template]);
    }
    function parseTemplate($templateContent, $data)
    {
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }
        return $templateContent;
    }

    public function generateOfferLetter($candidateId, $templateId)
{
    $templateModel = new \App\Models\OfferLetterTemplateModel();
    $candidateModel = new \App\Models\CandidateModel();
    $jobModel = new \App\Models\JobModel();
    $departmentModel = new \App\Models\DepartmentModel();
    $companyModel = new \App\Models\CompanyLogoModel();
    $userModel = new \App\Models\UserModel();
    $userInfoModel = new \App\Models\UserInfoModel();
    $onboardingModel = new \App\Models\OnboardingModel();

    $template = $templateModel->find($templateId);
    $candidate = $candidateModel->find($candidateId);
    $company = $companyModel->first();
    $job = $jobModel->find($candidate['job_id']);
    $department = $departmentModel->find($job['department_id']);
    $creator = $userModel->find($template['created_by']);

    $userInfo = $userInfoModel
        ->where('user_id', $candidate['id'])
        ->first();

    $onboarding = $onboardingModel->where('candidate_id', $candidateId)->first();
    $docuSubmitted = $onboarding['docu_submitted'] ?? '';

    $joiningDate = $userInfo['joining_date'] ?? '';
    $salary = $userInfo['salary'] ?? '';

    // ✅ Safely prepare logo
    $logoImgTag = '<!-- Logo not supported or not found -->';
    if (!empty($company['logo_img'])) {
        $logoPath = FCPATH . 'upload/' . $company['logo_img'];
        if (file_exists($logoPath)) {
            $mimeType = mime_content_type($logoPath);
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            if (in_array($mimeType, $allowedTypes)) {
                $base64 = base64_encode(file_get_contents($logoPath));
                $logoImgTag = '<img src="data:' . $mimeType . ';base64,' . $base64 . '" height="80" class="offer-letter-logo">';
            }
        }
    }

    $data = [
        'logo_img' => $logoImgTag,
        'company_name' => $company['company_name'] ?? '',
        'company_address' => $company['company_address'] ?? '',
        'company_phone' => $company['company_phone'] ?? '',
        'company_email' => $company['company_email'] ?? '',
        'today_date' => date('F j, Y'),
        'candidate_name' => $candidate['candidate_name'] ?? '',
        'job_title' => $job['job_title'] ?? '',
        'start_date' => !empty($job['post_date']) ? date('F j, Y', strtotime($job['post_date'])) : '',
        'department_name' => $department['department_name'] ?? '',
        'created_by' => $creator['username'] ?? '',
        'creator_email' => $creator['email'] ?? '',
        'creator_designation' => $creator['designation'] ?? '',
        'joining_date' => !empty($joiningDate) ? date('F j, Y', strtotime($joiningDate)) : '',
        'salary' => $salary,
        'docu_submitted' => !empty($docuSubmitted) ? $docuSubmitted : 'No documents submitted yet.',
    ];

    $parsedContent = $this->parseTemplate($template['content'], $data);
    // print_r($parsedContent);die;
    $finalHtml = view('offer_templates/offer_letter_preview', array_merge($data, ['content' => $parsedContent]));

    // Generate PDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($finalHtml);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $this->response
        ->setContentType('application/pdf')
        ->setHeader('Content-Disposition', 'inline; filename="Offer Letter.pdf"')
        ->setBody($dompdf->output());
}
public function getOfferTemplate($id)
{
    $model = new \App\Models\OfferLetterTemplateModel();
    $template = $model->find($id);

    if (!$template) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Template not found.'
        ])->setStatusCode(404);
    }

    return $this->response->setJSON([
        'status' => true,
        'data' => [
            'title' => $template['title'],
            'content' => $template['content'],
            'template_img' => base_url('upload/' . $template['template_img']),
        ]
    ]);
}

    /**
     * Export Offer Letter Templates to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->user();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->templateModel->builder();
        $builder->select('offer_letter_templates.*, user_info.firstname, user_info.lastname')
            ->join('user_info', 'user_info.user_id = offer_letter_templates.created_by', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('offer_letter_templates.title', $search)
                ->orLike('offer_letter_templates.content', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('offer_letter_templates.id', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Offer Templates');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Template ID',
            'C1' => 'Template Title',
            'D1' => 'Content Preview',
            'E1' => 'Created By',
            'F1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $creator = trim(($item['firstname'] ?? '') . ' ' . ($item['lastname'] ?? '')) ?: 'Admin';
            $preview = strip_tags($item['content'] ?? '');
            if (mb_strlen($preview) > 150) {
                $preview = mb_substr($preview, 0, 147) . '...';
            }

            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, 'TMPL-' . sprintf('%03d', $item['id']));
            $sheet->setCellValue('C' . $rowNum, $item['title'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, $preview);
            $sheet->setCellValue('E' . $rowNum, $creator);
            $sheet->setCellValue('F' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');

            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Offer_Templates_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
