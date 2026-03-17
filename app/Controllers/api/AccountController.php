<?php
namespace App\Controllers\Api;

use App\Models\AccountDetailModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use App\Models\UserModel;
// use App\Models\UserModel; 

class AccountController extends ResourceController
{
    private $accountModel;
    private $authService;

    public function __construct()
    {
        $this->accountModel = new AccountDetailModel();
        $this->authService = new AuthService(service('request'));
    }

    public function index()
    {
        $userModel = new \App\Models\UserModel();
        // Get the role of the logged-in user
        $role = session()->get('role');  // Assuming the user's role is stored in the session

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // If the user is an admin, fetch both HR and employees for the dropdown
        if ($role === 'admin') {
            $reviewers = $userModel->whereIn('role', ['hr', 'employee'])->findAll();
        } elseif ($role === 'hr') {
            // If the user is HR, only fetch employees for the dropdown
            $reviewers = $employees;
        } else {
            // If neither admin nor HR, you may want to handle or return an empty array
            $reviewers = [];
        }
        return view('account_detail/add_account'  ,['employees' => $reviewers]);
    }
    public function display()
    {
        return view('account_detail/view');
    }
    public function EditPage($id)
    {
        $userModel = new \App\Models\UserModel();
        // Get the role of the logged-in user
        $role = session()->get('role');  // Assuming the user's role is stored in the session

        // Fetch employees with role 'employee'
        $employees = $userModel->where('role', 'employee')->findAll();

        // If the user is an admin, fetch both HR and employees for the dropdown
        if ($role === 'admin') {
            $reviewers = $userModel->whereIn('role', ['hr', 'employee'])->findAll();
        } elseif ($role === 'hr') {
            // If the user is HR, only fetch employees for the dropdown
            $reviewers = $employees;
        } else {
            // If neither admin nor HR, you may want to handle or return an empty array
            $reviewers = [];
        }
        return view('account_detail/edit',['employees' => $reviewers,'id'=>$id]);
    }
 
    public function fetch()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
        $this->accountModel->select('account_detail.*, users.username')
            ->join('users', 'users.id = account_detail.user_id');

        // Role-based filtering
        if ($user->role === 'admin') {
            // Admin can see all records (no filter)
            $accounts = $this->accountModel->findAll();
        } elseif ($user->role === 'hr') {
            // HR can only see employee records (exclude admin & HR)
            $accounts = $this->accountModel->where('users.role', 'employee')->findAll();
        } elseif ($user->role === 'employee') {
            // Employee can only see their own records
            $accounts = $this->accountModel->where('payroll.user_id', $user->sub)->findAll();
        } else {
            return $this->failForbidden('Forbidden: Unauthorized role');
        }

        return $this->respond(['status' => 'success', 'data' => $accounts]);
    }
    public function store()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }
    
        $accountModel = new \App\Models\AccountDetailModel();
        $data = $this->request->getPost();
        $data['created_by'] = $user->sub;
    
        $validation = \Config\Services::validation();
    
        $validationRules = [
            'user_id' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Employee is required.',
                    'integer' => 'Invalid employee ID.'
                ]
            ],
             'acc_number' => [
                'rules' => 'required|is_unique[account_detail.acc_number]',
                'errors' => [
                    'required' => 'Account number is required.',
                    'is_unique' => 'This account number already exists.'
                ]
            ],
            'bank_name' => [
                'rules' => 'required',
                'errors' => ['required' => 'Bank name is required.']
            ],
            'ifsc_code' => [
                'rules' => 'required',
                'errors' => ['required' => 'IFSC code is required.']
            ],
            'acc_in_name' => [
                'rules' => 'required',
                'errors' => ['required' => 'Account holder name is required.']
            ],
            'branch_name' => [
                'rules' => 'required',
                'errors' => ['required' => 'Branch name is required.']
            ],
            'branch_code' => [
                'rules' => 'required',
                'errors' => ['required' => 'Branch code is required.']
            ],
            'created_by' => [
                'rules' => 'required',
                'errors' => ['required' => 'Creator information is missing.']
            ]
        ];
    
        if (!$validation->setRules($validationRules)->run($data)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['errors' => $validation->getErrors()]);
        }
    
        // ✅ Check if account already exists for user_id
        $existing = $accountModel->where('user_id', $data['user_id'])->first();
        if ($existing) {
            return $this->response->setStatusCode(409)->setJSON([
                'status' => 'error',
                'message' => 'Account details for this employee already exist.'
            ]);
        }
    
        $accountModel->save($data);
    
        return $this->response->setJSON(['status' => 'success']);
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

        $record = $this->accountModel
            ->select('account_detail.*,users.username')
            ->join('users', 'users.id = account_detail.user_id', 'left')
            ->where('account_detail.id', $id)
            ->first();
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'job type not found'], 404);
    }
    public function getAccountDetail($id)
    {
        $model = new \App\Models\AccountDetailModel();
        $data = $model->find($id);
    
        if ($data) {
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Record not found.']);
        }
    }
    public function updatedata($id)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }
    
        // Role-based access control
        if ($user->role !== 'admin' && $user->role !== 'hr') {
            return $this->failForbidden('Forbidden: You do not have permission to update account details');
        }
    
        $data = [
            'user_id'      => $this->request->getVar('user_id'),
            'acc_in_name'  => $this->request->getVar('acc_in_name'),
            'acc_number'   => $this->request->getVar('acc_number'),
            'bank_name'    => $this->request->getVar('bank_name'),
            'ifsc_code'    => $this->request->getVar('ifsc_code'),
            'branch_name'  => $this->request->getVar('branch_name'),
            'branch_code'  => $this->request->getVar('branch_code'),
        ];
    
        $validation = \Config\Services::validation();
        
        $validationRules = [
            'user_id' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Employee is required.',
                    'integer' => 'Invalid employee ID.'
                ]
            ],
            'acc_number' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Account number is required.',
                    'is_unique' => 'This account number already exists.'
                ]
            ],
            'bank_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Bank name is required.'
                ]
            ],
            'ifsc_code' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'IFSC code is required.'
                ]
            ],
            'acc_in_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Account holder name is required.'
                ]
            ],
            'branch_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Branch name is required.'
                ]
            ],
            'branch_code' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Branch code is required.'
                ]
            ],
          
        ];
        // ✅ Pass your custom $data into run()
        if (!$validation->setRules($validationRules)->run($data)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['errors' => $validation->getErrors()]);
        }
    
        // Update
        if ($this->accountModel->update($id, $data)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Account detail updated successfully'
            ]);
        }
    
        return $this->failServerError('Failed to update Account Detail');
    }
    

public function delete($id = null)
{
    $user = $this->authService->check();
    if (!$user) {
        return $this->failUnauthorized('Unauthorized: Token missing or invalid');
    }

    if ($user->role !== 'admin') {
        return $this->failForbidden('Forbidden: Only Admin can delete payroll records');
    }

    if ($this->accountModel->delete($id)) {
        return $this->respond(['status' => 'success', 'message' => 'Account Detail deleted successfully']);
    }

    return $this->respond(['status' => 'error', 'message' => 'Failed to delete payroll record'], 500);
}
}
