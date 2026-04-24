<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\UserInfoModel;
use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\DesignationModel;
use App\Models\DepartmentModel;
use App\Models\NotificationModel;
use App\Services\AuthService;
use App\Libraries\EmailService;
use CodeIgniter\Config\Services;

class EmployeeController extends ResourceController
{
    protected $authService;
    // protected $email;
    protected $emailService;
    protected $cityModel;
    protected $countryModel;
    protected $stateModel;
    protected $departmentModel;
    protected $designationModel;
    protected $userModel;
    protected $userInfoModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->authService = Services::auth($this->request); // Inject the request object
        // $this->email = \Config\Services::email();
        $this->emailService = new EmailService(); // Initialize EmailService
        $this->cityModel = new CityModel();
        $this->countryModel = new CountryModel();
        $this->stateModel = new StateModel();
        $this->departmentModel = new DepartmentModel();
        $this->designationModel = new DesignationModel();
        $this->userModel = new UserModel();
        $this->userInfoModel = new UserInfoModel();
        $this->notificationModel = new NotificationModel();
    }

    public function creates($id = null)
    {
        // Fetch cities, countries, departments, and designations
        $cities = $this->cityModel->findAll();
        $countries = $this->countryModel->findAll();
        $state = $this->stateModel->findAll();
        $departments = $this->departmentModel->findAll();
        $designations = $this->designationModel->findAll();

        // If $id is provided, it's for an existing employee, fetch their data
        if ($id) {

            $user = $this->userModel->find($id);
            $userInfo = $this->userInfoModel->where('user_id', $id)->first();

            return view('employee/employee', [
                'cities' => $cities,
                'countries' => $countries,
                'state' => $state,
                'departments' => $departments,
                'designations' => $designations,
                'user' => $user,
                'userInfo' => $userInfo,
            ]);
        } else {
            return view('employee/employee', [
                'cities' => $cities,
                'countries' => $countries,
                'state' => $state,
                'departments' => $departments,
                'designations' => $designations,
            ]);
        }
    }

    public function get_user_details($id = null)
    {
        // Fetch supporting data
        $cities = $this->cityModel->findAll();
        $countries = $this->countryModel->findAll();
        $state = $this->stateModel->findAll();
        $departments = $this->departmentModel->findAll();
        $designations = $this->designationModel->findAll();

        // If ID is provided, return user-specific details
        if ($id !== null) {
            $user = $this->userModel->find($id);
            $userInfo = $this->userInfoModel->where('user_id', $id)->first();

            if (!$user) {
                return $this->respond([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            return $this->respond([
                'status' => true,
                'message' => 'User details fetched successfully.',
                'cities' => $cities,
                'countries' => $countries,
                'state' => $state,
                'departments' => $departments,
                'designations' => $designations,
                'user' => $user,
                'userInfo' => $userInfo,
            ]);
        }

        // If no ID is provided, return only master data
        return $this->respond([
            'status' => true,
            'message' => 'Master data fetched successfully.',
            'cities' => $cities,
            'countries' => $countries,
            'state' => $state,
            'departments' => $departments,
            'designations' => $designations,
        ]);
    }

    public function lastEmployeeId()
    {
        // Fetch the last inserted employee_id from the user_info table
        $userInfoModel = new \App\Models\UserInfoModel();
        $lastEmployee = $userInfoModel->orderBy('employee_id', 'DESC')->first();

        if ($lastEmployee) {
            return $this->respond([
                'status' => true,
                'employee_id' => $lastEmployee['employee_id']
            ]);
        } else {
            // If no employee exists, return a default starting ID (e.g., 1000)
            return $this->respond([
                'status' => true,
                'employee_id' => 1
            ]);
        }
    }

    public function display()
    {
        return view('employee/view');
    }

    public function liveRequest()
    {
        return view('employee/employee_live_request');
    }

    public function validateStep()
    {
        $validation = \Config\Services::validation();
        $step = $this->request->getPost('current_step');
        $userId = $this->request->getPost('user_id');
        if (!empty($userId)) {
            $passwordRules = 'permit_empty|min_length[6]';
            $passwordErrors = [
                'min_length' => 'Password must be at least 6 characters long.'
            ];
        } else {
            $passwordRules = 'required|min_length[6]';
            $passwordErrors = [
                'required' => 'Password is required.',
                'min_length' => 'Password must be at least 6 characters long.'
            ];
        }
        // Define validation rules with custom error messages
        $rules = [
            1 => [
                'firstname' => [
                    'rules' => 'required|min_length[3]',
                    'errors' => [
                        'required' => 'First name is required.',
                        'min_length' => 'First name must be at least 3 characters long.'
                    ]
                ],

                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email is required.',
                        'valid_email' => 'Please enter a valid email address.',
                        'is_unique' => 'This email is already registered.'
                    ]
                ],
                'password' => [
                    'rules' => $passwordRules,
                    'errors' => $passwordErrors
                ],
                'gender' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Gender is required.'
                    ]
                ],
            ],
            2 => [
                'address_1' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Address is required.']
                ],
                'city_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'City is required.']
                ],
                'country_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Country is required.']
                ],
                'state_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'State is required.']
                ],
                'postcode' => [
                    'rules' => 'required|max_length[8]',
                    'errors' => [
                        'required' => 'Postcode is required.',
                        'max_length' => 'Postcode cannot exceed 8 characters.'
                    ]
                ],
                'contact_number' => [
                    'rules' => 'required|exact_length[10]|numeric',
                    'errors' => [
                        'required' => 'Contact number is required.',
                        'exact_length' => 'Contact number must be exactly 10 digits.',
                        'numeric' => 'Contact number must contain only numbers.'
                    ]
                ],
            ],
            3 => [
                'employee_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Employee ID is required.']
                ],
                'designation_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Designation is required.']
                ],
                'department_id' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Department is required.']
                ],
                'joining_date' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Joining date is required.']
                ],
                'role' => [
                    'rules' => 'required',
                    'errors' => ['required' => 'Role is required.']
                ],

                'salary' => [
                    'rules' => 'required|decimal|greater_than[0]',
                    'errors' => [
                        'required' => 'Salary amount is required.',
                        'decimal' => 'Salary must be a valid decimal number.',
                        'greater_than' => 'Salary must be greater than zero.'
                    ]
                ]
            ]
        ];
        if (empty($userId)) {
            $rules[1]['email']['rules'] .= '|is_unique[users.email]';
        } else {
            // ✅ If editing, make sure it’s unique EXCEPT for current ID
            $rules[1]['email']['rules'] .= '|is_unique[users.email,id,' . $userId . ']';
        }
        // Validate step
        if (!$validation->setRules($rules[$step])->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        return $this->response->setJSON(['status' => true]);
    }

    public function create()
    {
        // Authorize before proceeding with the update
        $user = $this->authorize();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $data = $this->request->getPost();

        // Validate required fields
        if (!isset($data['gender'])) {
            return $this->failValidationErrors(['Gender is required']);
        }

        // Check if the email is already taken
        $existingUser = $this->userModel->where('email', $data['email'])->first();
        if ($existingUser) {
            return $this->failValidationErrors(['This email is already registered. Please use a different one.']);
        }

        // Hash password or generate
        $plainPassword = isset($data['password']) ? $data['password'] : bin2hex(random_bytes(4));
        $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = $this->userModel->insert([
            'email' => $data['email'],
            'username' => $data['firstname'] . ' ' . $data['lastname'],
            'password' => $passwordHash,
            'role' => $data['role'] ?? 'employee'
        ]);

        if (!$userId) {
            $db->transRollback();
            return $this->failServerError('Failed to create user.');
        }

        // Handle File Upload - Profile Image
        $profileImage = $this->request->getFile('profile_image');
        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $profileImageName = $profileImage->getRandomName();
            $profileImage->move(FCPATH . 'upload/', $profileImageName);
        }

        // Handle File Upload - Face Photo for biometric attendance
        $facePhoto = $this->request->getFile('face_photo');
        $facePhotoName = null;
        if ($facePhoto && $facePhoto->isValid() && !$facePhoto->hasMoved()) {
            // Create faces directory if not exists
            if (!is_dir(FCPATH . 'upload/faces/')) {
                mkdir(FCPATH . 'upload/faces/', 0755, true);
            }
            $facePhotoName = 'face_' . $facePhoto->getRandomName();
            $facePhoto->move(FCPATH . 'upload/faces/', $facePhotoName);
        }

        // Prepare user info data
        $userInfoData = [
            'user_id' => $userId,
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'] ?? '',
            'gender' => $data['gender'] ?? '',
            'date_of_birth' => $data['date_of_birth'] ?? '',
            'address_1' => $data['address_1'] ?? '',
            'address_2' => $data['address_2'] ?? '',
            'state_id' => $data['state_id'] ?? '',
            'postcode' => $data['postcode'] ?? '',
            'city_id' => $data['city_id'] ?? '',
            'country_id' => $data['country_id'] ?? '',
            'contact_number' => $data['contact_number'] ?? '',
            'employee_id' => isset($data['employee_id']) ? preg_replace('/[^0-9]/', '', $data['employee_id']) : '',
            'designation_id' => $data['designation_id'] ?? '',
            'department_id' => $data['department_id'] ?? '',
            'joining_date' => $data['joining_date'] ?? '',
            'working_location' => $data['working_location'] ?? '',
            'role' => $data['role'] ?? 'employee',
            'salary' => $data['salary'] ?? '',
        ];

        if (isset($profileImageName)) {
            $userInfoData['profile_image'] = $profileImageName;
        }

        if ($facePhotoName) {
            $userInfoData['face_photo'] = $facePhotoName;
        }

        $userInfo = $this->userInfoModel->insert($userInfoData);

        // ✅ Notify Admins or HRs
        $notificationModel = new \App\Models\NotificationModel();
        $userModel = new \App\Models\UserModel();

        $admins = $userModel->whereIn('role', ['admin', 'hr'])->findAll();
        foreach ($admins as $admin) {
            $notificationModel->insert([
                'sender_id' => $user->sub,
                'recipient_id' => $admin['id'],
                'data' => json_encode([
                    'username' => $data['firstname'] . ' ' . $data['lastname'],
                    'role' => $data['role'],
                    'type' => 'employee'
                ]),
                'is_read' => 0
            ]);
        }

        // 🎉 Birthday Notifications
        $today = date('m-d');
        $userInfoModel = new \App\Models\UserInfoModel();

        $birthdayUsers = $userInfoModel
            ->select('user_id, firstname, lastname')
            ->where("DATE_FORMAT(date_of_birth, '%m-%d')", $today)
            ->findAll();

        $adminHRs = $userModel->whereIn('role', ['admin', 'hr'])->findAll();

        foreach ($birthdayUsers as $birthdayUser) {
            $employeeId = $birthdayUser['user_id'];
            $fullName = $birthdayUser['firstname'] . ' ' . $birthdayUser['lastname'];

            $allRecipients = $adminHRs;
            $allRecipients[] = ['id' => $employeeId];

            $uniqueRecipients = [];
            foreach ($allRecipients as $recipient) {
                $uniqueRecipients[$recipient['id']] = $recipient;
            }

            foreach ($uniqueRecipients as $recipient) {
                $notificationModel->insert([
                    'sender_id' => $employeeId,
                    'recipient_id' => $recipient['id'],
                    'data' => json_encode([
                        'username' => $fullName,
                        'type' => 'birthday'
                    ]),
                    'is_read' => 0
                ]);
            }
        }

        if (!$userInfo) {
            $db->transRollback();
            return $this->failServerError('Failed to create user info.');
        }

        // Send login credentials
        $emailService = new EmailService();
        $emailResult = $emailService->sendEmail($userId, $plainPassword);

        if (!$emailResult['status']) {
            $db->transRollback();
            return $this->failServerError('User creation failed due to email error.');
        }

        // Initialize employee leaves if provided
        if (isset($data['remaining_paid_leave']) || isset($data['remaining_sick_leave'])) {
            $employeeLeaveModel = new \App\Models\EmployeeLeaveModel();
            $employeeLeaveModel->insert([
                'employee_id' => $userId,
                'paid_leave' => $data['remaining_paid_leave'] ?? 0,
                'casual_leave' => $data['remaining_sick_leave'] ?? 0
            ]);
        }

        $db->transComplete();

        return $this->respondCreated(['message' => 'Employee created successfully!']);
    }

    // Update Employee
    public function update($id = null)
    {
        // Authorize before proceeding with the update
        $user = $this->authorize();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        // Validate incoming data
        $data = $this->request->getPost();

        // Start with basic validation rules
        $rules = [
            'firstname' => 'required|min_length[3]',
        ];

        // Only add profile image validation if a new image is uploaded
        $profileImage = $this->request->getFile('profile_image');

        // Check for additional validation errors
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Check if the user exists

        $existingUser = $this->userModel->find($id);
        if (!$existingUser) {
            return $this->failNotFound('User not found');
        }

        // Check if a new password is provided
        $newPassword = $data['password'] ?? null;
        $passwordChanged = false;

        if (!empty($newPassword)) {
            if (!password_get_info($newPassword)['algo']) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $originalPassword = $newPassword; // Store the original password
            } else {
                $hashedPassword = $newPassword;
                $originalPassword = null; // No need to store, as it's already hashed
            }
            $passwordChanged = true;
        } else {
            $hashedPassword = $existingUser['password'];
            $originalPassword = null;
        }


        // Check if user_info exists before updating

        $userInfo = $this->userInfoModel->where('user_id', $id)->first();
        if (!$userInfo) {
            return $this->failNotFound('User info not found for update');
        }

        // Handle profile image update (only if a new image is uploaded)
        $profileImageName = $userInfo['profile_image']; // Keep the old image by default
        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            // Generate new image name
            $newImageName = $profileImage->getRandomName();

            // Move the uploaded image
            $profileImage->move(FCPATH . 'upload/', $newImageName);

            // Delete old image if it exists
            if (!empty($userInfo['profile_image']) && file_exists(FCPATH . 'upload/' . $userInfo['profile_image'])) {
                unlink(FCPATH . 'upload/' . $userInfo['profile_image']);
            }

            $profileImageName = $newImageName; // Set new image name
        }

        // Handle face photo update (for biometric attendance)
        $facePhoto = $this->request->getFile('face_photo');
        $facePhotoName = $userInfo['face_photo'] ?? null; // Keep the old face photo by default
        if ($facePhoto && $facePhoto->isValid() && !$facePhoto->hasMoved()) {
            $newFacePhotoName = 'face_' . $facePhoto->getRandomName();

            // Create faces directory if not exists
            if (!is_dir(FCPATH . 'upload/faces/')) {
                mkdir(FCPATH . 'upload/faces/', 0755, true);
            }

            $facePhoto->move(FCPATH . 'upload/faces/', $newFacePhotoName);

            // Delete old face photo if exists
            if (!empty($userInfo['face_photo']) && file_exists(FCPATH . 'upload/faces/' . $userInfo['face_photo'])) {
                unlink(FCPATH . 'upload/faces/' . $userInfo['face_photo']);
            }

            $facePhotoName = $newFacePhotoName;
        }

        // Use null coalescing operator (??) to prevent undefined key errors
        $designation_id = $data['designation_id'] ?? null;
        $department_id = $data['department_id'] ?? null;
        $employee_id = $data['employee_id'] ?? null;
        $role = $data['role'] ?? $existingUser['role']; // Keep old role if not provided

        // Update the users table
        $this->userModel->update($id, [
            'email' => $data['email'],
            'username' => ($data['firstname'] ?? '') . ' ' . ($data['lastname'] ?? ''),
            'role' => $role,
            'password' => $hashedPassword, // Update password only if provided
        ]);


        // Prepare gender field (or null if not set)
        $gender = isset($data['gender']) ? $data['gender'] : null;


        // Update the user_info table
        $this->userInfoModel->where('user_id', $id)->set([
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'] ?? '',
            'gender' => $gender,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address_1' => $data['address_1'] ?? '',
            'address_2' => $data['address_2'] ?? '',
            'state_id' => $data['state_id'] ?? '',
            'postcode' => $data['postcode'] ?? '',
            'city_id' => $data['city_id'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'contact_number' => $data['contact_number'] ?? '',
            'employee_id' => $employee_id,
            'designation_id' => $designation_id,
            'department_id' => $department_id,
            'joining_date' => $data['joining_date'] ?? null,
            'working_location' => $data['working_location'] ?? '',
            'role' => $role,
            'profile_image' => $profileImageName, // Update profile image only if changed
            'face_photo' => $facePhotoName, // Update face photo for biometric attendance
            'salary' => $data['salary'] ?? '',
        ])->update();

        // Update employee leaves if provided
        if (isset($data['remaining_paid_leave']) || isset($data['remaining_sick_leave'])) {
            $employeeLeaveModel = new \App\Models\EmployeeLeaveModel();
            $leaveData = [];
            if (isset($data['remaining_paid_leave'])) {
                $leaveData['paid_leave'] = $data['remaining_paid_leave'];
            }
            if (isset($data['remaining_sick_leave'])) {
                $leaveData['casual_leave'] = $data['remaining_sick_leave']; // Wait, the model uses 'casual_leave' for sick leave? Or sick_leave? Let me check the db column.
            }
            // wait, PayrollController maps remaining_casual_leaves to casual_leave. So sick leave is casual_leave. Let me just set both.
            
            $existingLeave = $employeeLeaveModel->where('employee_id', $id)->first();
            if ($existingLeave) {
                $employeeLeaveModel->update($existingLeave['id'], [
                    'paid_leave' => $data['remaining_paid_leave'] ?? $existingLeave['paid_leave'],
                    'casual_leave' => $data['remaining_sick_leave'] ?? $existingLeave['casual_leave'] // mapping sick leave to casual_leave based on existing code
                ]);
            } else {
                $employeeLeaveModel->insert([
                    'employee_id' => $id,
                    'paid_leave' => $data['remaining_paid_leave'] ?? 0,
                    'casual_leave' => $data['remaining_sick_leave'] ?? 0
                ]);
            }
        }

        $emailService = new EmailService();
        // Send email if password is changed
        if ($passwordChanged && $originalPassword) {
            $emailService->sendEmail($id, $originalPassword);
        }

        return $this->respond(['message' => 'Employee updated successfully!']);
    }

    public function index()
    {
        $user = $this->authService->check(); // Get logged-in user

        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        $role = $user->role;
        $departmentId = $this->request->getGet('department_id');

        // Build query with join
        $builder = $this->userModel
            ->select('users.*, user_info.firstname, user_info.lastname, user_info.profile_image, user_info.joining_date, user_info.id as user_info_id, department.department_name, department.id as department_id, employee_leaves.paid_leave, employee_leaves.casual_leave')
            ->join('user_info', 'user_info.user_id = users.id')
            ->join('department', 'department.id = user_info.department_id', 'left')
            ->join('employee_leaves', 'employee_leaves.employee_id = users.id', 'left');

        // Role-based filtering
        if ($role === 'admin') {
            $builder->whereIn('users.role', ['employee', 'hr']);
        } elseif ($role === 'hr') {
            $builder->where('users.role', 'employee');
        } else {
            return $this->failForbidden('You do not have permission to view employees');
        }

        // Department filtering
        if (!empty($departmentId)) {
            $builder->where('user_info.department_id', $departmentId);
        }

        $builder->where('users.is_deleted', 0);

        $builder->orderBy('users.id', 'DESC');


        $results = $builder->findAll();

        $employees = [];

        foreach ($results as $row) {
            $employees[] = [
                'user' => [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'password' => $row['password'],
                ],
                'user_info' => [
                    'id' => $row['user_info_id'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'joining_date' => $row['joining_date'],
                    'remaining_paid_leave' => (float) ($row['paid_leave'] ?? 0),
                    'remaining_sick_leave' => (float) ($row['casual_leave'] ?? 0),
                    'department_id' => $row['department_id'],
                    'department_name' => $row['department_name'],
                    'profile_image_url' => !empty($row['profile_image']) ? base_url('upload/' . $row['profile_image']) : base_url('public/upload/default-profile.jpg'),
                ]
            ];
        }

        return $this->respond([
            'status' => true,
            'employees' => $employees
        ]);
    }


    public function show($id = null)
    {


        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }
        // **Do NOT return hashed password in response**
        // unset($user['password']);
        $userInfo = $this->userInfoModel->where('user_id', $id)->first();

        // Append full image path if profile_image exists
        if ($userInfo && !empty($userInfo['profile_image'])) {
            $userInfo['profile_image'] = base_url('upload/' . $userInfo['profile_image']);
        } else {
            $userInfo['profile_image'] = base_url(env('ImagePath') . 'upload/default-profile.jpg'); // Set a default image
        }

        // Append full face photo path if exists
        if ($userInfo && !empty($userInfo['face_photo'])) {
            $userInfo['face_photo'] = base_url('upload/faces/' . $userInfo['face_photo']);
        } else {
            $userInfo['face_photo'] = null;
        }

        // Fetch leave balances for display (dynamic fetch)
        $employeeLeaveModel = new \App\Models\EmployeeLeaveModel();
        $leaveBalance = $employeeLeaveModel->where('employee_id', $id)->first();
        if ($leaveBalance) {
            $userInfo['remaining_paid_leave'] = $leaveBalance['paid_leave'] ?? 0;
            $userInfo['remaining_sick_leave'] = $leaveBalance['casual_leave'] ?? 0;
        } else {
            $userInfo['remaining_paid_leave'] = 0; // Default values
            $userInfo['remaining_sick_leave'] = 0;
        }

        return $this->respond([
            'user' => $user,
            'user_info' => $userInfo
        ]);
    }

    // Add JWT authorization to protected routes
    private function authorize()
    {
        $user = $this->authService->check();
        if (!$user) {
            log_message('error', 'User is not authorized. JWT token missing or invalid.');
            return false; // Return false if user is not authorized
        }
        return $user;
    }

    public function delete($id = null)
    {
        // Authenticate user
        $user = $this->authorize();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized access');
        }

        // Only Admin or HR can delete employees
        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: Only Admin or HR can delete employee records');
        }

        $db = \Config\Database::connect();

        // Verify employee exists and is not already deleted
        $existingUser = $db->table('users')->where('id', $id)->where('is_deleted', 0)->get()->getRowArray();
        if (!$existingUser) {
            return $this->failNotFound('Employee not found');
        }

        // ── Start a transaction so everything succeeds or nothing changes ──
        $db->transStart();

        // 1. Attendance records
        $db->table('attendance')->where('user_id', $id)->delete();

        // 2. Leave records
        $db->table('leaves')->where('user_id', $id)->delete();

        // 3. Payroll records
        $db->table('payroll')->where('user_id', $id)->delete();

        // 4. Performance records
        $db->table('performance')->where('user_id', $id)->delete();

        // 5. Tasks assigned to or created by this employee
        $db->table('task')->where('user_id', $id)->delete();

        // 6. Sub-tasks
        $db->table('subtasks')->where('user_id', $id)->delete();

        // 7. Training records
        $db->table('training')->where('user_id', $id)->delete();

        // 8. Comments
        $db->table('comments')->where('user_id', $id)->delete();

        // 9. Bank / account details
        $db->table('account_detail')->where('user_id', $id)->delete();

        // 10. Notifications (both sent and received)
        $db->table('notifications')->where('sender_id', $id)->orWhere('recipient_id', $id)->delete();

        // 11. Employee of the Month records
        $db->table('employee_of_month_certificates')->where('user_id', $id)->delete();

        // 12. Push notification subscriptions
        $db->table('push_subscriptions')->where('user_id', $id)->delete();

        // 13. Remember tokens (sessions)
        $db->table('remember_tokens')->where('user_id', $id)->delete();

        // 14. Employee reports
        $db->table('empreport')->where('user_id', $id)->delete();

        // 15. User info (profile)
        $db->table('user_info')->where('user_id', $id)->delete();

        // 16. Finally — delete the user account itself
        $db->table('users')->where('id', $id)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->failServerError('Failed to delete employee. Transaction rolled back.');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Employee and all related data have been permanently deleted.'
        ]);
    }

    public function profile($id)
    {
        $user = $this->authService->user();
        $userId = $user->sub;
        // Pass employee ID to the view
        return view('employee/profile', ['id' => $id, 'employeeId' => $userId,]);
    }


    /**
     * profileview($id)
     *
     * Route: /employee/profile/view/{users.id}
     *
     * Receives the users.id (user_id), finds the matching
     * user_info row, and renders the profile view using user_info.id.
     */
    public function profileview($id)
    {
        // Look up user_info by users.id (user_id column)
        $userInfo = $this->userInfoModel
            ->select('id')
            ->where('user_id', $id)
            ->first();

        if (!$userInfo) {
            return $this->failNotFound('Employee profile not found for user ID: ' . $id);
        }

        $userInfoId   = $userInfo['id'];                   // user_info.id — what the profile view needs
        $loggedInUser = $this->authService->user();
        $employeeId   = $loggedInUser ? $loggedInUser->sub : null;

        return view('employee/profile', [
            'id'         => $userInfoId,  // user_info.id
            'employeeId' => $employeeId,  // logged-in user's own users.id
        ]);
    }

    public function details($id)
    {
        // Fetch user info with joins
        $user = $this->userInfoModel
            ->select('
            users.email, users.username, users.id AS user_id,
            user_info.id, user_info.profile_image, user_info.firstname, user_info.lastname,
            user_info.date_of_birth, user_info.gender, user_info.contact_number,
            user_info.address_1, user_info.address_2, user_info.city_id, user_info.state_id,
            user_info.country_id, user_info.designation_id, user_info.department_id,
            user_info.postcode, user_info.employee_id, user_info.joining_date,
            user_info.working_location, user_info.role, user_info.salary,
            account_detail.acc_number, account_detail.bank_name, account_detail.ifsc_code,
            account_detail.acc_in_name, account_detail.branch_name, account_detail.branch_code
        ')
            ->join('users', 'users.id = user_info.user_id', 'left')
            ->join('account_detail', 'account_detail.user_id = user_info.user_id', 'left')
            ->where('user_info.id', $id)
            ->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Load related names from reference tables
        $user['city_name'] = $this->cityModel->find($user['city_id'])['city_name'] ?? 'N/A';
        $user['state_name'] = $this->stateModel->find($user['state_id'])['state_name'] ?? 'N/A';
        $user['country_name'] = $this->countryModel->find($user['country_id'])['country_name'] ?? 'N/A';
        $user['designation_name'] = $this->designationModel->find($user['designation_id'])['designation_name'] ?? 'N/A';
        $user['department_name'] = $this->departmentModel->find($user['department_id'])['department_name'] ?? 'N/A';

        $city = $this->cityModel->findAll();
        $state = $this->stateModel->findAll();
        $country = $this->countryModel->findAll();
        $designation = $this->designationModel->findAll();
        $department = $this->departmentModel->findAll();
        // Handle default profile image
        $user['profile_image'] = !empty($user['profile_image'])
            ? base_url('upload/' . $user['profile_image'])
            : base_url(env('ImagePath') . 'upload/default-profile.jpg');

        return $this->response->setJSON([
            'success' => true,
            'data' => $user,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'designation' => $designation,
            'department' => $department,
        ]);
    }

    public function change_password_user()
    {
        $request = service('request');
        $input = json_decode($request->getBody(), true);

        $userId = $input['user_id'] ?? null;
        $newPassword = $input['password'] ?? null;
        $confirmPassword = $input['confirm_password'] ?? null;

        if (!$userId || !$newPassword || !$confirmPassword) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User ID, password, and confirm password are required.'
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password and Confirm Password do not match.'
            ]);
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        $userModel->update($userId, [
            'password' => $hashedPassword
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }

    public function change_image()
    {
        $id = $this->request->getPost('id');
        $profileImage = $this->request->getFile('profile_image');
        $userInfo = $this->userInfoModel->where('user_id', $id)->first();
        if (!$userInfo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        $profileImageName = $userInfo['profile_image'];

        if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
            $newImageName = $profileImage->getRandomName();
            $profileImage->move(FCPATH . 'upload/', $newImageName);

            if (!empty($profileImageName) && file_exists(FCPATH . 'upload/' . $profileImageName)) {
                unlink(FCPATH . 'upload/' . $profileImageName);
            }

            $profileImageName = $newImageName;
        }

        $this->userInfoModel->where('user_id', $id)->set([
            'profile_image' => $profileImageName,
        ])->update();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Profile image updated successfully.'
        ]);
    }
    public function save_overview()
    {
        $input = $this->request->getPost();

        // Validation rules
        $rules = [
            'id' => 'required|is_natural_no_zero',
            'firstname' => 'required|min_length[2]',
            'lastname' => 'permit_empty|min_length[2]',
            'email' => 'required|valid_email',
            'gender' => 'required|in_list[male,female,other]',
            'dob' => 'required|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userId = $input['id'];
        $email = $input['email'];
        $firstname = $input['firstname'];
        $lastname = $input['lastname'];
        $gender = $input['gender'];
        $dob = $input['dob'];

        // Load models
        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();

        // Begin DB transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Update `users` table
        $userModel->update($userId, [
            'email' => $email,
            'username' => $firstname,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update `user_info` table
        $userInfoModel->where('user_id', $userId)->set([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'gender' => $gender,
            'date_of_birth' => $dob,
            'updated_at' => date('Y-m-d H:i:s')
        ])->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    }
    public function get_user_address_data($userId)
    {

        $userInfoModel = new \App\Models\UserInfoModel();
        $cityModel = new \App\Models\CityModel();
        $stateModel = new \App\Models\StateModel();
        $countryModel = new \App\Models\CountryModel();
        $designationModel = new \App\Models\DesignationModel();
        $departmentModel = new \App\Models\DepartmentModel();

        $user = $userInfoModel->where('user_id', $userId)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'user' => $user,
            'city' => $cityModel->findAll(),
            'state' => $stateModel->findAll(),
            'country' => $countryModel->findAll(),
            'designation' => $designationModel->findAll(),
            'department' => $departmentModel->findAll()
        ]);
    }
    public function update_user_address()
    {
        $data = $this->request->getPost();

        $rules = [
            'user_id' => 'required|integer',
            'address_1' => 'required|min_length[3]',
            'address_2' => 'permit_empty',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'postcode' => 'required',
            'contact_number' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userId = $data['user_id'];

        $userInfoModel = new \App\Models\UserInfoModel();
        $user = $userInfoModel->where('user_id', $userId)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Update address fields
        $userInfoModel->where('user_id', $userId)->set([
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'country_id' => $data['country_id'],
            'state_id' => $data['state_id'],
            'city_id' => $data['city_id'],
            'postcode' => $data['postcode'],
            'contact_number' => $data['contact_number'],
            'updated_at' => date('Y-m-d H:i:s')
        ])->update();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Address updated successfully'
        ]);
    }
    public function get_user_bank_data($userId)
    {
        $model = new \App\Models\AccountDetailModel();
        $bank = $model->select('user_id, bank_name, acc_number, acc_in_name, branch_name, branch_code')
            ->where('user_id', $userId)
            ->first();

        // Even if not found, return blank structure so frontend modal can open
        if (!$bank) {
            $bank = [
                'user_id' => $userId,
                'bank_name' => '',
                'acc_number' => '',
                'acc_in_name' => '',
                'branch_name' => '',
                'branch_code' => ''
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $bank
        ]);
    }


    public function update_user_bank_data()
    {
        $data = $this->request->getPost();

        $rules = [
            'user_id' => [
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'User ID is required.',
                    'integer' => 'Invalid User ID.',
                ],
            ],
            'bank_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'The Bank Name field is required.',
                ],
            ],
            'acc_number' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'The Account Number field is required.',
                    'numeric' => 'The Account Number must contain only digits.',
                ],
            ],
            'acc_in_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'The Account Holder Name field is required.',
                ],
            ],
            'branch_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'The Branch Name field is required.',
                ],
            ],
            'branch_code' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'The Branch Code field is required.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $model = new \App\Models\AccountDetailModel();

        // Check if bank data already exists
        $existing = $model->where('user_id', $data['user_id'])->first();

        $bankData = [
            'user_id' => $data['user_id'],
            'bank_name' => $data['bank_name'],
            'acc_number' => $data['acc_number'],
            'acc_in_name' => $data['acc_in_name'],
            'branch_name' => $data['branch_name'],
            'branch_code' => $data['branch_code'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Update existing record
            $model->where('user_id', $data['user_id'])->update(null, $bankData);
            $message = 'Bank info updated successfully';
        } else {
            // Insert new record
            $bankData['created_at'] = date('Y-m-d H:i:s');
            $model->insert($bankData);
            $message = 'Bank info inserted successfully';
        }

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    public function update_user_job_data()
    {
        $data = $this->request->getPost();

        $rules = [
            'user_id' => 'required|integer',
            'employee_id' => 'required',
            'department_id' => 'required|integer',
            'designation_id' => 'required|integer',
            'joining_date' => 'required|valid_date',
            'working_location' => 'required',
            'postcode' => 'required',
            'salary' => 'required|numeric',
            'role' => 'required|in_list[admin,employee,hr]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        // ✅ Remove 'EMP#' if exists, and keep only the number
        $data['employee_id'] = str_replace('EMP#', '', $data['employee_id']);

        $model = new \App\Models\UserInfoModel();

        $existing = $model->where('user_id', $data['user_id'])->first();
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $model->where('user_id', $data['user_id'])->set([
            'employee_id' => $data['employee_id'],
            'department_id' => $data['department_id'],
            'designation_id' => $data['designation_id'],
            'joining_date' => $data['joining_date'],
            'working_location' => $data['working_location'],
            'postcode' => $data['postcode'],
            'salary' => $data['salary'],
            'role' => $data['role'],
            'updated_at' => date('Y-m-d H:i:s')
        ])->update();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Job details updated successfully'
        ]);
    }
}
