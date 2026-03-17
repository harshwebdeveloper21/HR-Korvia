<?php

namespace App\Controllers;

use App\Models\UserInfoModel;
use CodeIgniter\Controller;

class UserInfoController extends Controller
{
    public function index()
    {
        return view('multi_step_form');
    }

    public function save()
    {
        $model = new UserInfoModel();
        
        // Validation rules
        $validationRules = [
            'firstname' => 'required|min_length[3]',
            'lastname' => 'required|min_length[3]',
            // 'email' => 'required|valid_email',
            'email' => 'required|valid_email|is_unique',
            'contact_number' => 'required',
            'address_1' => 'required',
            'address_2' => 'required',
            'employee_id' => 'required',
            'designation_id' => 'required',
            'department_id' => 'required'
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON(['error' => $this->validator->getErrors()]);
        }

        // Get form data
        $data = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number'),
            'address_1' => $this->request->getPost('address_1'),
            'address_2' => $this->request->getPost('address_2'),
            'employee_id' => $this->request->getPost('employee_id'),
            'designation_id' => $this->request->getPost('designation_id'),
            'department_id' => $this->request->getPost('department_id'),
            // Add more fields as needed
        ];

        // Save to the database
        if ($model->save($data)) {
            return $this->response->setJSON(['success' => 'Data saved successfully']);
        } else {
            return $this->response->setJSON(['error' => 'Failed to save data']);
        }
    }
}
