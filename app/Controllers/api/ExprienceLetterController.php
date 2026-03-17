<?php

namespace App\Controllers\Api;

use App\Models\ExprienceLetterModel;
use App\Services\AuthService;
use App\Models\UserInfoModel;
use App\Models\ExperienceModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\CompanyLogoModel;
use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExprienceLetterController extends ResourceController
{
    protected $templateModel;
    protected $userinfoModel;
    private $authService;

    public function __construct()
    {
        $this->templateModel = new ExprienceLetterModel();
        $this->userinfoModel = new UserInfoModel();
        $this->authService = new AuthService(service('request'));

        helper(['form']);
    }

    public function index()
    {
        return view('exprience_templetes/create');
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

        $model = new \App\Models\ExprienceLetterModel();
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
            $file->move(FCPATH . 'upload/templates/', $newName);
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

        return view('exprience_templetes/view');
    }
    public function display()
    {

        return view('exprience_templetes/view_letter');
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
        return view('exprience_templetes/edit_page', ['id' => $id]);
    }

    public function getTemplate($id)
    {
        $model = new \App\Models\ExprienceLetterModel();
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

        $model = new \App\Models\ExprienceLetterModel();
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
            $file->move(FCPATH . 'upload/templates/', $newName);
            $data['template_img'] = $newName;
        }

        $model->update($id, $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Template updated successfully']);
    }
    public function templateView($id)
    {
        $model = new \App\Models\ExprienceLetterModel();
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

        return view('exprience_templetes/template_view', ['templates' => $template]);
    }
    public function addemployeePage()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        $userModel = new \App\Models\UserModel();
        $employee = $userModel->whereIn('role', ['hr', 'employee'])->findAll();

        $exprienceModel = new \App\Models\ExprienceLetterModel();
        $template = $exprienceModel->findAll();
        return view('exprience_templetes/add_exp_emp', ['employee' => $employee, 'template' => $template]);
    }

    public function generateExperienceLetter($insert = true)
    {

        $employeeId = $this->request->getPost('employee_id');
        $templateId = $this->request->getPost('template_id');
        $fromDate = $this->request->getPost('from_date');
        $toDate = $this->request->getPost('to_date');
        $loggedInUserId = session()->get('user_id'); // 👈 adjust if your session key is different

        $userModel = new \App\Models\UserInfoModel();
        $templateModel = new \App\Models\ExprienceLetterModel();
        $companyModel = new \App\Models\CompanyLogoModel();
        $designationModel = new \App\Models\DesignationModel();
        $departmentModel = new \App\Models\DepartmentModel();
        $generatedLetterModel = new \App\Models\ExprienceModel(); // 👈 load new model
        // ✅ INSERT into database
        $existing = $generatedLetterModel
            ->where('employee_id', $employeeId)
            ->first();

        if ($existing && $insert) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Experience letter has already been generated for this employee for the selected period.'
            ])->setStatusCode(400); // 👈 This triggers `error:` block
        }


        // ✅ Insert only if not duplicate
        if ($insert && !$existing) {
            $generatedLetterModel->insert([
                'employee_id' => $employeeId,
                'template_id' => $templateId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'generated_by' => $loggedInUserId
            ]);
        }

        $employee = $userModel->where('user_id', $employeeId)->first();

        $template = $templateModel->find($templateId);
        $company = $companyModel->first();
        $todate = $generatedLetterModel
            ->where('employee_id', $employeeId)
            ->where('template_id', $templateId)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$employee || !$template || !$company) {
            return redirect()->back()->with('error', 'Invalid employee, template, or company data.');
        }
        $companyLogoBase64 = '';
        if ($company && !empty($company['logo_img'])) {
            $companyLogoPath = FCPATH . 'upload/' . $company['logo_img'];
            
            if (file_exists($companyLogoPath)) {
                $type = pathinfo($companyLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($companyLogoPath);
                $companyLogoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        $designation = $designationModel->find($employee['designation_id']);
        $department = $departmentModel->find($employee['department_id']);
        $designationName = $designation['designation_name'] ?? 'N/A';
        $departmentName = $department['department_name'] ?? 'N/A';
        $creator = $userModel->find($template['created_by']);
        $placeholders = [
            '{{employee_name}}'   => $employee['firstname'] . ' ' . $employee['lastname'], // 👈 updated
            '{{designation}}'     => $designationName,
            '{{department}}'      => $departmentName,
            '{{address_1}}' => $employee['address_1'],
            '{{joining_date}}'    => date('d M Y', strtotime($employee['joining_date'])),
            '{{current_date}}'    => date('F d, Y'),
            '{{department}}'      => $employee['department'] ?? '',
            '{{created_by}}' => $creator['firstname'],
            '{{role}}' => $creator['role'],
            '{{company_name}}'    => $company['company_name'],
            '{{leaving_date}}' => date('d M Y', strtotime($todate['to_date'])),

            '{{company_address}}' => $company['company_address'],
            '{{company_email}}'   => $company['company_email'],
            '{{company_phone}}'   => $company['company_phone'],
            '{{companyLogoBase64}}' => $companyLogoBase64,
            'company_name'    => $company['company_name'],
        ];

        $parsedContent = strtr($template['content'], $placeholders);

        $html = view('exprience_templetes/experience_letter_preview', [
            'content' => $parsedContent,
            'companyLogoBase64' => $companyLogoBase64,
            'company_name'    => $company['company_name'],
            'company_address' => $company['company_address'],
            'company_email' => $company['company_email'],
            'company_phone' => $company['company_phone'],
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $employeeName = str_replace(' ', '_', $employee['firstname'] . '_' . $employee['lastname']);
        $currentDateTime = date('Ymd_His'); // e.g., 20250415_142305
        $filename = "experience_letter_{$employeeName}_{$currentDateTime}.pdf";
        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output())
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function getJoiningDate($id)
    {
        $userModel = new \App\Models\UserInfoModel();
        $employee = $userModel->find($id);

        if ($employee) {
            return $this->response->setJSON(['joining_date' => $employee['joining_date']]);
        } else {
            return $this->response->setJSON(['joining_date' => null]);
        }
    }

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
        $generatedLetterModel = new \App\Models\ExprienceModel(); // 👈 load new model
        $jobs = $generatedLetterModel->select('exprience.id,user_info.firstname,exprience.from_date,exprience_letter_templetes.title,exprience.to_date')
            ->join('user_info', 'user_info.user_id = exprience.employee_id')
            ->join('exprience_letter_templetes', 'exprience_letter_templetes.id = exprience.template_id')
            ->orderBy('exprience.created_at', 'DESC')
            ->findAll();

        return $this->respond(['status' => 'success', 'data' => $jobs]);
    }
    public function downloadExperienceLetter($generatedLetterId)
    {
        $generatedModel = new \App\Models\ExprienceModel();
        $data = $generatedModel->find($generatedLetterId);



        if (!$data) {
            return redirect()->back()->with('error', 'Letter not found.');
        }

        // Reuse your existing logic
        $this->request->setGlobal('post', [
            'employee_id' => $data['employee_id'],
            'template_id' => $data['template_id'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date']
        ]);

        return $this->generateExperienceLetter(false); // 👈 calls the original method
    }
    public function deleteletter($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if ($user->role !== 'admin') {
            return $this->failForbidden('Forbidden: Only Admin can delete job records');
        }

        // Check if there are candidates applied for this job
        $generatedModel = new \App\Models\ExprienceModel();
        // Proceed with job deletion if no candidates are linked
        if ($generatedModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'exprience letter deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete exprience record'], 500);
    }
    public function getTemplateById($id)
{
    $model = new \App\Models\ExprienceLetterModel();
    $template = $model->find($id);

    if (!$template) {
        return $this->response->setJSON(['status' => false, 'message' => 'Template not found'])->setStatusCode(404);
    }

    return $this->response->setJSON([
        'status' => true,
        'data' => [
            'title' => $template['title'],
            'content' => $template['content'],
            'template_img' => base_url('upload/templates/' . $template['template_img']),
        ]
    ]);
}
}
