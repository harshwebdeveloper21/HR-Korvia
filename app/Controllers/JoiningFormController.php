<?php

namespace App\Controllers;

use App\Services\AuthService;

class JoiningFormController extends BaseController
{
    private const EMPLOYMENT_TYPES = [
        'full'       => 'Full Time',
        'full_time'  => 'Full Time',
        'part'       => 'Part Time',
        'part_time'  => 'Part Time',
        'contract'   => 'Contract',
        'intern'     => 'Internship',
        'internship' => 'Internship',
        'temporary'  => 'Temporary',
    ];

    private const PERSONAL_EMAIL_DOMAINS = ['gmail.com', 'yahoo.com', 'yahoo.in', 'outlook.com', 'hotmail.com', 'rediffmail.com', 'icloud.com', 'live.com'];

    /** Editable fields, grouped by form section: key => label. */
    public const SECTIONS = [
        'Employment Details' => [
            'employee_name'     => 'Employee Name',
            'employee_id'       => 'Employee ID',
            'designation'       => 'Designation',
            'department'        => 'Department',
            'reporting_manager' => 'Reporting Manager',
            'work_location'     => 'Work Location',
            'joining_date'      => 'Date of Joining',
            'employment_type'   => 'Employment Type',
        ],
        'Personal Details' => [
            'father_spouse_name' => "Father's / Spouse's Name",
            'date_of_birth'      => 'Date of Birth',
            'gender'             => 'Gender',
            'blood_group'        => 'Blood Group',
            'mobile'             => 'Mobile Number',
            'personal_email'     => 'Personal Email',
            'aadhaar_number'     => 'Aadhaar Number',
            'pan_number'         => 'PAN Number',
            'qualification'      => 'Highest Qualification',
            'previous_employer'  => 'Previous Employer (if any)',
            'present_address'    => 'Present Address',
            'permanent_address'  => 'Permanent Address',
        ],
        'Emergency Contact' => [
            'emergency_name'         => 'Contact Name',
            'emergency_relationship' => 'Relationship',
            'emergency_number'       => 'Contact Number',
            'emergency_alt_number'   => 'Alternate Number',
        ],
        'Bank Details (for Salary Processing)' => [
            'bank_name'      => 'Bank Name',
            'bank_branch'    => 'Branch',
            'account_number' => 'Account Number',
            'ifsc'           => 'IFSC Code',
        ],
    ];

    private const TABLE = 'employee_joining_forms';

    public function pdf($userId)
    {
        $ctx = $this->authorize($userId);
        if (!is_array($ctx)) {
            return $ctx;
        }
        $data = $this->buildData($ctx['db'], $ctx['emp']);

        $html = view('joining_form/print', $data);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeName = trim(preg_replace('/[^A-Za-z0-9]+/', '_', $data['fields']['employee_name'] ?: 'employee'), '_');
        $filename = 'New_Joinee_Form_' . $safeName . '.pdf';
        $disposition = $this->request->getGet('download') ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function edit($userId)
    {
        $ctx = $this->authorize($userId);
        if (!is_array($ctx)) {
            return $ctx;
        }
        $data = $this->buildData($ctx['db'], $ctx['emp']);
        $data['userId']      = (int) $userId;
        $data['sections']    = self::SECTIONS;
        $data['tableReady']  = $ctx['db']->tableExists(self::TABLE);

        return view('joining_form/edit', $data);
    }

    public function save($userId)
    {
        $ctx = $this->authorize($userId);
        if (!is_array($ctx)) {
            return $ctx;
        }
        $db = $ctx['db'];
        if (!$db->tableExists(self::TABLE)) {
            return redirect()->back()->with('error', 'Joining form table is missing. Please run db/server_update_joining_form.sql first.');
        }

        $fields = [];
        foreach (self::SECTIONS as $group) {
            foreach (array_keys($group) as $key) {
                $fields[$key] = trim((string) $this->request->getPost($key));
            }
        }
        $fields['aadhaar_number'] = preg_replace('/[^0-9 ]/', '', $fields['aadhaar_number']);
        $fields['pan_number']     = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $fields['pan_number']));
        $fields['ifsc']           = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $fields['ifsc']));

        $errors = [];
        if ($fields['pan_number'] !== '' && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $fields['pan_number'])) {
            $errors[] = 'PAN Number must be in the format ABCDE1234F.';
        }
        if ($fields['aadhaar_number'] !== '' && strlen(str_replace(' ', '', $fields['aadhaar_number'])) !== 12) {
            $errors[] = 'Aadhaar Number must have 12 digits.';
        }
        if ($fields['ifsc'] !== '' && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $fields['ifsc'])) {
            $errors[] = 'IFSC Code must be in the format ABCD0123456.';
        }
        if ($errors) {
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }

        $checks = array_values(array_filter((array) $this->request->getPost('checks'), 'is_string'));

        $auto = $this->buildData($db, $ctx['emp'])['autoFields'];
        $overrides = [];
        foreach ($fields as $key => $value) {
            if ($value !== (string) ($auto[$key] ?? '')) {
                $overrides[$key] = $value;
            }
        }

        $authUser = $ctx['auth'];
        $row = [
            'form_data'  => json_encode($overrides, JSON_UNESCAPED_UNICODE),
            'checklist'  => json_encode($checks),
            'updated_by' => (int) ($authUser->sub ?? $authUser->id ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $existing = $db->table(self::TABLE)->where('user_id', $userId)->get()->getRowArray();
        if ($existing) {
            $db->table(self::TABLE)->where('id', $existing['id'])->update($row);
        } else {
            $row['user_id']    = (int) $userId;
            $row['created_at'] = $row['updated_at'];
            $db->table(self::TABLE)->insert($row);
        }

        $this->syncBankDetails($db, (int) $userId, $fields, $row['updated_by']);

        $redirect = redirect()->to('/employee/joining-form/' . (int) $userId . '/edit')->with('success', 'Joining form saved.');
        return $this->request->getPost('then_print') ? redirect()->to('/employee/joining-form/' . (int) $userId) : $redirect;
    }

    private function syncBankDetails($db, int $userId, array $f, int $by): void
    {
        if (!$db->tableExists('account_detail') || ($f['bank_name'] === '' && $f['account_number'] === '' && $f['ifsc'] === '')) {
            return;
        }
        $data = [
            'bank_name'   => $f['bank_name'],
            'branch_name' => $f['bank_branch'],
            'acc_number'  => $f['account_number'],
            'ifsc_code'   => $f['ifsc'],
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $acc = $db->table('account_detail')->where('user_id', $userId)->orderBy('id', 'DESC')->get()->getRowArray();
        if ($acc) {
            $db->table('account_detail')->where('id', $acc['id'])->update($data);
        } else {
            $db->table('account_detail')->insert($data + [
                'user_id'     => $userId,
                'acc_in_name' => $f['employee_name'],
                'branch_code' => '',
                'created_by'  => $by,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /** @return array{db: mixed, emp: array, auth: object}|\CodeIgniter\HTTP\ResponseInterface */
    private function authorize($userId)
    {
        $authService = new AuthService($this->request);
        $authUser = $authService->check();
        if (!$authUser) {
            return redirect()->to('/login');
        }
        if (!in_array($authUser->role, ['admin', 'hr', 'branch_admin'], true)) {
            return redirect()->to('/dashboard')->with('error', 'You do not have access to joining forms.');
        }

        $db = \Config\Database::connect();
        $emp = $db->table('users u')
            ->select('u.id, u.email, u.role, u.branch_id, u.department_id AS user_department_id,
                      ui.firstname, ui.lastname, ui.employee_id, ui.gender, ui.date_of_birth, ui.contact_number,
                      ui.address_1, ui.address_2, ui.state, ui.postcode, ui.joining_date, ui.working_location,
                      ui.department_id, ui.job_id, ui.profile_image, ui.face_photo,
                      ds.designation_name, d.department_name, d.manager_id, b.name AS branch_name, b.city AS branch_city,
                      j.job_type')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->join('designation ds', 'ds.id = ui.designation_id', 'left')
            ->join('department d', 'd.id = COALESCE(ui.department_id, u.department_id)', 'left')
            ->join('branches b', 'b.id = u.branch_id', 'left')
            ->join('jobs j', 'j.id = ui.job_id', 'left')
            ->where('u.id', $userId)
            ->get()->getRowArray();

        if (!$emp) {
            return $this->response->setStatusCode(404)->setBody('Employee not found');
        }
        if ($authUser->role === 'branch_admin') {
            $myBranch = $authService->getBranchId();
            if ($myBranch && (int) $emp['branch_id'] !== (int) $myBranch) {
                return redirect()->to('/empview')->with('error', 'This employee belongs to another branch.');
            }
        }

        return ['db' => $db, 'emp' => $emp, 'auth' => $authUser];
    }

    private function buildData($db, array $emp): array
    {
        $email = (string) $emp['email'];

        $candidateIds = array_map('intval', array_column(
            $db->table('candidate')->select('id')->where('email', $email)->get()->getResultArray(),
            'id'
        ));

        $interviewQuery = $db->table('interviews')->groupStart()->where('email', $email);
        if ($candidateIds) {
            $interviewQuery->orWhereIn('candidate_id', $candidateIds);
        }
        $interview = $interviewQuery->groupEnd()->orderBy('convert_to_employee', 'DESC')->orderBy('id', 'DESC')->get()->getRowArray();

        $reportingManager = '';
        if ($interview && $db->fieldExists('reporting_manager', 'interview_assessments')) {
            $row = $db->table('interview_assessments')->select('reporting_manager')
                ->where('interview_id', $interview['id'])
                ->where('reporting_manager IS NOT NULL')->where('reporting_manager !=', '')
                ->orderBy('id', 'DESC')->get()->getRowArray();
            $reportingManager = $row['reporting_manager'] ?? '';
        }
        if ($reportingManager === '') {
            $managerId = null;
            if ($emp['role'] === 'employee' && !empty($emp['manager_id']) && (int) $emp['manager_id'] !== (int) $emp['id']) {
                $managerId = (int) $emp['manager_id'];
            } elseif (in_array($emp['role'], ['employee', 'department_manager'], true) && !empty($emp['branch_id'])) {
                $ba = $db->table('users')->select('id')->where('role', 'branch_admin')->where('branch_id', $emp['branch_id'])
                    ->where('is_deleted', 0)->get()->getRowArray();
                $managerId = $ba['id'] ?? null;
            }
            if ($managerId) {
                $reportingManager = $this->personName($db, (int) $managerId);
            }
        }

        $employmentType = '';
        $rawType = str_replace(['-', ' '], '_', strtolower(trim((string) ($interview['job_type'] ?? '') ?: (string) ($emp['job_type'] ?? ''))));
        if ($rawType !== '') {
            $employmentType = self::EMPLOYMENT_TYPES[$rawType] ?? ucwords(str_replace('_', ' ', $rawType));
        }

        $qualification = trim(implode(' - ', array_filter([
            $interview['highest_qualification'] ?? '',
            $interview['degree_course'] ?? '',
        ])));

        $workLocation = $emp['branch_name']
            ? trim($emp['branch_name'] . ($emp['branch_city'] ? ', ' . $emp['branch_city'] : ''))
            : (string) ($emp['working_location'] ?? '');

        $presentAddress = trim(implode(', ', array_filter([
            $emp['address_1'] ?? '',
            $emp['address_2'] ?? '',
            $emp['state'] ?? '',
            $emp['postcode'] ?? '',
        ])));
        if ($presentAddress === '' && $interview) {
            $presentAddress = trim(implode(', ', array_filter([
                $interview['current_address'] ?? '', $interview['city'] ?? '', $interview['state'] ?? '', $interview['pincode'] ?? '',
            ])));
        }

        $bank = $db->tableExists('account_detail')
            ? ($db->table('account_detail')->where('user_id', $emp['id'])->orderBy('id', 'DESC')->get()->getRowArray() ?: [])
            : [];

        $fmtDate = static fn($d) => (!empty($d) && $d !== '0000-00-00') ? date('d/m/Y', strtotime($d)) : '';

        $auto = [
            'employee_name'     => ucwords(trim(preg_replace('/\s+/', ' ', ($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? '')))),
            'employee_id'       => $emp['employee_id'] ?? '',
            'designation'       => $emp['designation_name'] ?? '',
            'department'        => $emp['department_name'] ?? '',
            'reporting_manager' => $reportingManager,
            'work_location'     => $workLocation,
            'joining_date'      => $fmtDate($emp['joining_date'] ?? ''),
            'employment_type'   => $employmentType,
            'date_of_birth'     => $fmtDate($emp['date_of_birth'] ?? ($interview['date_of_birth'] ?? '')),
            'gender'            => ucfirst((string) ($emp['gender'] ?: ($interview['gender'] ?? ''))),
            'mobile'            => $emp['contact_number'] ?: ($interview['mobile_number'] ?? ''),
            'personal_email'    => $email,
            'qualification'     => $qualification,
            'previous_employer' => $interview['previous_company'] ?? '',
            'present_address'   => $presentAddress,
            'bank_name'         => $bank['bank_name'] ?? '',
            'bank_branch'       => $bank['branch_name'] ?? '',
            'account_number'    => $bank['acc_number'] ?? '',
            'ifsc'              => $bank['ifsc_code'] ?? '',
        ];

        $saved = [];
        $manualChecks = [];
        $savedAt = null;
        if ($db->tableExists(self::TABLE)) {
            $row = $db->table(self::TABLE)->where('user_id', $emp['id'])->get()->getRowArray();
            if ($row) {
                $saved = json_decode((string) $row['form_data'], true) ?: [];
                $manualChecks = json_decode((string) $row['checklist'], true) ?: [];
                $savedAt = $row['updated_at'];
            }
        }

        $fields = [];
        foreach (self::SECTIONS as $group) {
            foreach (array_keys($group) as $key) {
                $savedVal = trim((string) ($saved[$key] ?? ''));
                $fields[$key] = $savedVal !== '' ? $savedVal : (string) ($auto[$key] ?? '');
            }
        }

        [$documentChecklist, $extraDocuments] = $this->documentChecklist($db, $candidateIds, $emp);
        [$onboardingChecklist, $assets] = $this->onboardingChecklist($db, $emp);

        foreach ([&$documentChecklist, &$onboardingChecklist] as &$list) {
            foreach ($list as &$item) {
                $item['auto'] = $item['done'];
                $item['done'] = $item['done'] || in_array($item['key'], $manualChecks, true);
            }
            unset($item);
        }
        unset($list);

        return [
            'fields'              => $fields,
            'autoFields'          => $auto,
            'documentChecklist'   => $documentChecklist,
            'extraDocuments'      => $extraDocuments,
            'onboardingChecklist' => $onboardingChecklist,
            'assets'              => $assets,
            'company'             => (new \App\Models\CompanyLogoModel())->first() ?: [],
            'savedAt'             => $savedAt,
        ];
    }

    private function personName($db, int $userId): string
    {
        $row = $db->table('users u')->select('u.username, ui.firstname, ui.lastname')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->where('u.id', $userId)->get()->getRowArray();
        if (!$row) {
            return '';
        }
        $name = trim(preg_replace('/\s+/', ' ', ($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')));
        return ucwords($name !== '' ? $name : (string) $row['username']);
    }

    /** @return array{0: array<int, array{key: string, label: string, done: bool}>, 1: string[]} */
    private function documentChecklist($db, array $candidateIds, array $emp): array
    {
        $docs = [];
        if ($candidateIds && $db->tableExists('candidate_documents')) {
            $hasTitle = $db->fieldExists('doc_title', 'candidate_documents');
            $docs = $db->table('candidate_documents')
                ->select('doc_key, file_name' . ($hasTitle ? ', doc_title' : ''))
                ->whereIn('candidate_id', $candidateIds)
                ->get()->getResultArray();
        }

        $keys = [];
        $titled = [];
        foreach ($docs as $d) {
            $keys[$d['doc_key']] = true;
            $titled[$d['doc_key']] = strtolower(trim(($d['doc_title'] ?? '') . ' ' . ($d['file_name'] ?? '')));
        }
        $used = [];
        $hasKey = function (array $want) use ($keys, &$used) {
            foreach ($want as $k) {
                if (isset($keys[$k])) {
                    $used[$k] = true;
                    return true;
                }
            }
            return false;
        };
        $titleMatch = function (string $pattern) use ($titled, &$used) {
            foreach ($titled as $k => $text) {
                if (strpos($k, 'other_doc') === 0 && preg_match($pattern, $text)) {
                    $used[$k] = true;
                    return true;
                }
            }
            return false;
        };

        $hasPhoto = $hasKey(['other_doc_2']) || $titleMatch('/photo|passport size/')
            || (!empty($emp['profile_image']) && $emp['profile_image'] !== '1789966027_54c5a38ccda20f7c2bac.jpg');

        $list = [
            ['key' => 'doc_photo',       'label' => 'Photograph (2 copies)',            'done' => $hasPhoto],
            ['key' => 'doc_aadhaar',     'label' => 'Aadhaar Card copy',                'done' => $hasKey(['id_proof']) || $titleMatch('/aadha+r/')],
            ['key' => 'doc_pan',         'label' => 'PAN Card copy',                    'done' => $titleMatch('/\bpan\b/')],
            ['key' => 'doc_address',     'label' => 'Address Proof',                    'done' => $titleMatch('/address|electricity|light bill|rent|voter|driving|passport(?! size)/')],
            ['key' => 'doc_education',   'label' => 'Educational Certificates',         'done' => $hasKey(['edu_cert']) || $titleMatch('/degree|certificate|marksheet|education/')],
            ['key' => 'doc_relieving',   'label' => 'Previous Relieving Letter',        'done' => $hasKey(['relieving_letter', 'experience_letter']) || $titleMatch('/reliev|experience/')],
            ['key' => 'doc_salary',      'label' => 'Last Salary Slip',                 'done' => $hasKey(['salary_1', 'salary_2', 'salary_3']) || $titleMatch('/salary|pay ?slip/')],
            ['key' => 'doc_bank',        'label' => 'Bank Passbook / Cancelled Cheque', 'done' => $titleMatch('/passbook|cheque|check|bank/')],
            ['key' => 'doc_appointment', 'label' => 'Signed Appointment Letter',        'done' => $titleMatch('/appointment/')],
            ['key' => 'doc_annexure',    'label' => 'Signed Annexure A',                'done' => $titleMatch('/annexure/')],
        ];

        $extra = [];
        foreach ($docs as $d) {
            if (empty($used[$d['doc_key']]) && strpos($d['doc_key'], 'other_doc') === 0 && $d['doc_key'] !== 'other_doc') {
                $extra[] = trim((string) ($d['doc_title'] ?? '')) ?: (string) $d['file_name'];
            }
        }

        return [$list, $extra];
    }

    /** @return array{0: array<int, array{key: string, label: string, done: bool}>, 1: array<int, array>} */
    private function onboardingChecklist($db, array $emp): array
    {
        $assets = [];
        if ($db->tableExists('gadget_issuances')) {
            $assets = $db->table('gadget_issuances')
                ->where('user_id', $emp['id'])
                ->orderBy('issuance_date', 'ASC')
                ->get()->getResultArray();
        }
        $activeAssets = array_values(array_filter($assets, fn($a) => strtolower((string) $a['status']) !== 'returned' && empty($a['return_date'])));

        $assetMatch = function (string $pattern) use ($activeAssets) {
            foreach ($activeAssets as $a) {
                if (preg_match($pattern, strtolower($a['gadget_name'] . ' ' . $a['gadget_type']))) {
                    return true;
                }
            }
            return false;
        };

        $domain = strtolower(substr(strrchr((string) $emp['email'], '@') ?: '', 1));
        $companyDomain = '';
        $company = $db->table('company_logo')->select('company_email')->get()->getRowArray();
        if (!empty($company['company_email'])) {
            $companyDomain = strtolower(substr(strrchr($company['company_email'], '@') ?: '', 1));
        }
        $hasCompanyEmail = $domain !== '' && !in_array($domain, self::PERSONAL_EMAIL_DOMAINS, true)
            && ($companyDomain === '' || $domain === $companyDomain);

        $list = [
            ['key' => 'ob_id_card',   'label' => 'Employee ID Card issued',                  'done' => $assetMatch('/id ?card|identity/')],
            ['key' => 'ob_email',     'label' => 'Company Email ID created',                 'done' => $hasCompanyEmail],
            ['key' => 'ob_access',    'label' => 'Store/Office access briefing done',        'done' => false],
            ['key' => 'ob_induction', 'label' => 'Induction & policy briefing done',         'done' => false],
            ['key' => 'ob_uniform',   'label' => 'Uniform / ID badge issued (if applicable)', 'done' => $assetMatch('/uniform|badge/')],
            ['key' => 'ob_attendance', 'label' => 'Attendance system enrolment done',        'done' => !empty($emp['face_photo'])],
        ];

        return [$list, $assets];
    }
}
