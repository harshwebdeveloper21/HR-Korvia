<?php

namespace App\Controllers\Api;
use App\Models\EmployeeOfTheMonthModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;

class EmployeeOfTheMonthController extends ResourceController
{
    protected $employeeOfTheMonthModel;
    private $authService;

    public function __construct()
    {
        $this->employeeOfTheMonthModel = new EmployeeOfTheMonthModel();
        $this->authService = new AuthService(service('request'));

        helper(['form']);
    }

    public function index()
    {
        return view('empofmonth/create');
    }

    public function SaveEmpMonth()
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
            'emp_image' => 'uploaded[emp_image]|is_image[emp_image]|max_size[emp_image,2048]|mime_in[emp_image,image/jpg,image/jpeg,image/png,image/webp]'
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
            'emp_image' => [
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

        $model = new \App\Models\EmployeeOfTheMonthModel();
        $id = $this->request->getPost('id');

        // Prepare data including created_by
        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'created_by' => $user->sub,
        ];

        // Handle image upload
        $file = $this->request->getFile('emp_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'upload/', $newName);
            $data['emp_image'] = $newName;
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

        return view('empofmonth/view');
    }
    public function listTemplates()
    {
        $templates = $this->employeeOfTheMonthModel->orderBy('created_at', 'DESC')->findAll();

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

        $template = $this->employeeOfTheMonthModel->find($id);

        if (!$template) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Template not found.'
            ])->setStatusCode(404);
        }

        $this->employeeOfTheMonthModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Template deleted successfully.'
        ]);
    }
    public function EditPage($id)
    {
        return view('empofmonth/edit_page', ['id' => $id]);
    }

    public function getTemplate($id)
    {
        $model = new \App\Models\EmployeeOfTheMonthModel();
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
        $file = $this->request->getFile('emp_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['emp_image'] = 'is_image[emp_image]|max_size[emp_image,2048]|mime_in[emp_image,image/jpg,image/jpeg,image/png,image/webp]';
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
            'emp_image' => [
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

        $model = new \App\Models\EmployeeOfTheMonthModel();
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
            $data['emp_image'] = $newName;
        }

        $model->update($id, $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Template updated successfully']);
    }

    public function templateView($id)
    {
        $model = new \App\Models\EmployeeOfTheMonthModel();
        $template = $model->find($id);

        if (!$template) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Template not found.'
            ]);
        }

        $template['content'] = str_replace(
            ['../upload/', 'src="upload/'],
            [base_url('upload/') . '/', 'src="' . base_url('upload/') . '/'],
            $template['content']
        );

        return view('empofmonth/empof_monthview', ['templates' => $template]);
    }

    function parseTemplate($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', htmlspecialchars($value), $template);
        }
        return $template;
    }
}
