<?php

namespace App\Controllers;

use App\Services\AuthService;

class IncrementLetterController extends BaseController
{
    private const TEMPLATE_KEY = 'increment_letter';

    private const RICH_FIELDS = ['opening_paragraph', 'terms_increment', 'terms_promotion', 'closing_paragraph'];

    public const PLACEHOLDERS = [
        'employee_name'     => 'Employee full name',
        'employee_code'     => 'Employee ID',
        'company_name'      => 'Company name (letterhead)',
        'change_text'       => '"designation", "compensation" or both',
        'letter_date'       => 'Letter date',
        'effective_date'    => 'Effective date',
        'prev_designation'  => 'Current designation',
        'new_designation'   => 'New designation',
        'prev_department'   => 'Current department',
        'new_department'    => 'New department',
        'prev_annual_ctc'   => 'Current annual CTC',
        'new_annual_ctc'    => 'New annual CTC',
        'prev_monthly'      => 'Current monthly salary',
        'new_monthly'       => 'New monthly salary',
        'increment_amount'  => 'Monthly increment amount',
        'reporting_manager' => 'Reporting manager',
    ];

    public const TEMPLATE_GROUPS = [
        'Letterhead & Footer' => [
            'company_name'    => ['Company Name', 'KORVIA RETAIL PRIVATE LIMITED'],
            'company_tagline' => ['Tagline / Address Line', 'KORVIA SMART • Surat, Gujarat'],
            'footer_website'  => ['Footer Website', 'www.korviasmart.com'],
            'ref_prefix'      => ['Reference Number Prefix', 'KRPL/HR/INC/'],
        ],
        'Title & Subject' => [
            'title_increment'   => ['Title (Increment only)', 'INCREMENT LETTER'],
            'subject_increment' => ['Subject (Increment only)', 'Revision in Compensation'],
            'title_promotion'   => ['Title (Promotion only)', 'PROMOTION LETTER'],
            'subject_promotion' => ['Subject (Promotion only)', 'Revision in Designation'],
            'title_both'        => ['Title (Increment + Promotion)', 'INCREMENT / PROMOTION LETTER'],
            'subject_both'      => ['Subject (Increment + Promotion)', 'Revision in Compensation / Designation'],
        ],
        'Letter Body' => [
            'opening_paragraph' => ['Opening Paragraph', '<p>Dear {employee_name}, We are pleased to inform you that, in recognition of your performance and contribution to Korvia Retail Private Limited, the Company has decided to revise your {change_text} as detailed below, with effect from <strong>{effective_date}</strong>.</p>'],
            'revision_heading'  => ['Revision Table Heading', 'REVISION DETAILS'],
            'terms_increment'   => ['Terms Paragraph (when salary is increased)', '<p>A revised Annexure A reflecting your updated monthly salary structure (gross monthly {new_monthly}) will be issued along with this letter. All other terms and conditions of your employment, as set out in your original Letter of Appointment, remain unchanged unless otherwise communicated to you in writing.</p>'],
            'terms_promotion'   => ['Terms Paragraph (promotion only, no salary change)', '<p>All other terms and conditions of your employment, as set out in your original Letter of Appointment, remain unchanged unless otherwise communicated to you in writing.</p>'],
            'closing_paragraph' => ['Closing Paragraph', '<p>We appreciate your continued commitment and look forward to your ongoing contribution to the Korvia Smart team.</p>'],
        ],
        'Revision Table Labels' => [
            'label_current_designation' => ['Current Designation', 'Current Designation'],
            'label_new_designation'     => ['New Designation', 'New Designation'],
            'label_current_department'  => ['Current Department', 'Current Department'],
            'label_new_department'      => ['New Department', 'New Department (if changed)'],
            'label_current_ctc'         => ['Current Annual CTC', 'Current Annual CTC'],
            'label_new_ctc'             => ['New Annual CTC', 'New Annual CTC'],
            'label_effective_date'      => ['Effective Date', 'Effective Date'],
            'label_reporting_manager'   => ['Reporting Manager', 'Reporting Manager'],
        ],
        'Signatures' => [
            'signing_for'        => ['Signing On Behalf Of', 'Korvia Retail Private Limited'],
            'employee_ack_label' => ['Employee Sign Label', 'Employee Acknowledgement'],
            'employee_ack_sub'   => ['Employee Sign Sub-label', 'Signature & Date'],
        ],
    ];

    public function template()
    {
        $authUser = $this->authorizeTemplateEditor();
        if (!is_object($authUser)) {
            return $authUser;
        }

        $db = \Config\Database::connect();
        return view('increment_letter/template', [
            'groups'       => self::TEMPLATE_GROUPS,
            'richFields'   => self::RICH_FIELDS,
            'placeholders' => self::PLACEHOLDERS,
            'values'       => $this->templateSettings($db),
            'tableReady'   => $db->tableExists('letter_template_settings'),
        ]);
    }

    public function saveTemplate()
    {
        $authUser = $this->authorizeTemplateEditor();
        if (!is_object($authUser)) {
            return $authUser;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('letter_template_settings')) {
            return redirect()->to('/increment-letter-template')->with('error', 'Template table is missing. Run db/server_update_letter_template_settings.sql first.');
        }

        $table = $db->table('letter_template_settings');
        $existing = $table->where('template_key', self::TEMPLATE_KEY)->get()->getRowArray();

        if ($this->request->getPost('reset')) {
            if ($existing) {
                $db->table('letter_template_settings')->where('id', $existing['id'])->delete();
            }
            return redirect()->to('/increment-letter-template')->with('success', 'Template reset to the default wording.');
        }

        $values = $this->templateFromInput((array) $this->request->getPost('tpl'));
        $defaults = $this->templateDefaults();
        $overrides = array_filter($values, static fn($v, $k) => $v !== $defaults[$k], ARRAY_FILTER_USE_BOTH);

        $row = [
            'content'    => json_encode($overrides, JSON_UNESCAPED_UNICODE),
            'updated_by' => (int) ($authUser->sub ?? $authUser->id ?? 0) ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($existing) {
            $db->table('letter_template_settings')->where('id', $existing['id'])->update($row);
        } else {
            $db->table('letter_template_settings')->insert($row + [
                'template_key' => self::TEMPLATE_KEY,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/increment-letter-template')->with('success', 'Increment / Promotion letter template saved.');
    }

    public function previewTemplate()
    {
        $authUser = $this->authorizeTemplateEditor();
        if (!is_object($authUser)) {
            return $authUser;
        }

        $tpl = $this->templateFromInput((array) $this->request->getPost('tpl'));
        $type = (string) $this->request->getPost('preview_type');
        $isPromotion = in_array($type, ['promotion', 'both'], true);
        $isIncrement = $type !== 'promotion';
        $db = \Config\Database::connect();

        $data = [
            'employeeName'     => 'Rahul Sharma',
            'employeeCode'     => 'EMP0042',
            'refNumber'        => $tpl['ref_prefix'] . '0042',
            'letterDate'       => date('d/m/Y'),
            'effectiveDate'    => date('01/m/Y'),
            'prevDesignation'  => 'Sales Executive',
            'newDesignation'   => $isPromotion ? 'Senior Sales Executive' : 'Sales Executive',
            'prevDepartment'   => 'Sales',
            'newDepartment'    => 'No change',
            'prevAnnualCtc'    => $this->money(25000 * 12),
            'newAnnualCtc'     => $this->money(($isIncrement ? 30000 : 25000) * 12),
            'prevMonthly'      => $this->money(25000),
            'newMonthly'       => $this->money($isIncrement ? 30000 : 25000),
            'incrementAmount'  => $this->money($isIncrement ? 5000 : 0),
            'reportingManager' => 'Priya Mehta',
            'isIncrement'      => $isIncrement,
            'isPromotion'      => $isPromotion,
        ];

        return $this->renderPdf($db, $tpl, $data, 'Template_Preview')
            ->setHeader('X-CSRF-HASH', csrf_hash());
    }

    public function pdf($historyId)
    {
        $authUser = $this->authorize();
        if (!is_object($authUser)) {
            return $authUser;
        }

        $db = \Config\Database::connect();
        $record = $db->table('salary_increment_history')->where('id', $historyId)->get()->getRowArray();
        if (!$record) {
            return $this->response->setStatusCode(404)->setBody('Increment record not found');
        }

        return $this->render($db, $record, $authUser);
    }

    public function latest($userId)
    {
        $authUser = $this->authorize();
        if (!is_object($authUser)) {
            return $authUser;
        }

        $db = \Config\Database::connect();
        $record = $db->table('salary_increment_history')->where('employee_id', $userId)
            ->orderBy('effective_from_date', 'DESC')->orderBy('id', 'DESC')
            ->get()->getRowArray();
        if (!$record) {
            return $this->response->setStatusCode(404)->setContentType('text/html')->setBody(
                '<div style="font-family:sans-serif;padding:40px;text-align:center;color:#374151">'
                . '<h3>No increment or promotion found</h3><p>Add an increment or promotion for this employee first, then print the letter.</p>'
                . '<a href="/empview">Back to Employees</a></div>'
            );
        }

        return $this->render($db, $record, $authUser);
    }

    /** @return object|\CodeIgniter\HTTP\ResponseInterface */
    private function authorize()
    {
        $authUser = (new AuthService($this->request))->check();
        if (!$authUser) {
            return redirect()->to('/login');
        }
        if (!in_array($authUser->role, ['admin', 'hr', 'branch_admin'], true)) {
            return redirect()->to('/dashboard')->with('error', 'You do not have access to increment letters.');
        }
        return $authUser;
    }

    /** @return object|\CodeIgniter\HTTP\ResponseInterface */
    private function authorizeTemplateEditor()
    {
        $authUser = (new AuthService($this->request))->check();
        if (!$authUser) {
            return redirect()->to('/login');
        }
        if (!in_array($authUser->role, ['admin', 'hr'], true)) {
            return redirect()->to('/dashboard')->with('error', 'Only Admin and HR can edit letter templates.');
        }
        return $authUser;
    }

    private function templateDefaults(): array
    {
        $defaults = [];
        foreach (self::TEMPLATE_GROUPS as $fields) {
            foreach ($fields as $key => [, $default]) {
                $defaults[$key] = $default;
            }
        }
        return $defaults;
    }

    private function templateSettings($db): array
    {
        $defaults = $this->templateDefaults();
        if (!$db->tableExists('letter_template_settings')) {
            return $defaults;
        }
        $row = $db->table('letter_template_settings')->where('template_key', self::TEMPLATE_KEY)->get()->getRowArray();
        $saved = $row ? json_decode((string) $row['content'], true) : null;
        if (!is_array($saved)) {
            return $defaults;
        }
        return array_merge($defaults, array_intersect_key(array_map('strval', $saved), $defaults));
    }

    /** Blank fields fall back to the default wording so the letter never has empty headings. */
    private function templateFromInput(array $input): array
    {
        $values = [];
        foreach ($this->templateDefaults() as $key => $default) {
            $value = trim((string) ($input[$key] ?? ''));
            if (in_array($key, self::RICH_FIELDS, true)) {
                $value = $this->sanitizeRichText($value);
                $isBlank = trim(html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8'), " \t\n\r\0\x0B\xC2\xA0") === '';
            } else {
                $value = strip_tags($value);
                $isBlank = $value === '';
            }
            $values[$key] = $isBlank ? $default : $value;
        }
        return $values;
    }

    private function sanitizeRichText(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><span><ul><ol><li><div><h4><h5><h6><sup><sub>');
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        return preg_replace('/javascript\s*:/i', '', $html);
    }

    private function fillPlaceholders(string $value, array $vars, bool $isHtml): string
    {
        $map = [];
        foreach ($vars as $key => $v) {
            $map['{' . $key . '}'] = esc((string) $v);
        }
        return strtr($isHtml ? $value : esc($value), $map);
    }

    private function render($db, array $record, object $authUser)
    {
        $userId = (int) $record['employee_id'];
        $emp = $db->table('users u')
            ->select('u.id, u.branch_id, u.role, ui.firstname, ui.lastname, ui.employee_id, ui.designation_id, ui.department_id, ds.designation_name, d.department_name, d.manager_id')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->join('designation ds', 'ds.id = ui.designation_id', 'left')
            ->join('department d', 'd.id = ui.department_id', 'left')
            ->where('u.id', $userId)
            ->get()->getRowArray();
        if (!$emp) {
            return $this->response->setStatusCode(404)->setBody('Employee not found');
        }
        if ($authUser->role === 'branch_admin') {
            $myBranch = (new AuthService($this->request))->getBranchId();
            if ($myBranch && (int) $emp['branch_id'] !== (int) $myBranch) {
                return redirect()->to('/empview')->with('error', 'This employee belongs to another branch.');
            }
        }

        $designationName = function ($id) use ($db) {
            if (empty($id)) {
                return '';
            }
            $row = $db->table('designation')->select('designation_name')->where('id', $id)->get()->getRowArray();
            return $row['designation_name'] ?? '';
        };
        $departmentName = function ($id) use ($db) {
            if (empty($id)) {
                return '';
            }
            $row = $db->table('department')->select('department_name')->where('id', $id)->get()->getRowArray();
            return $row['department_name'] ?? '';
        };

        $prevDesignation = $designationName($record['previous_designation_id'] ?? null) ?: ($emp['designation_name'] ?? '');
        $newDesignation  = $designationName($record['new_designation_id'] ?? null) ?: $prevDesignation;
        $prevDepartment  = $departmentName($record['previous_department_id'] ?? null) ?: ($emp['department_name'] ?? '');
        $newDepartment   = $departmentName($record['new_department_id'] ?? null) ?: $prevDepartment;

        $designationChanged = $newDesignation !== '' && $newDesignation !== $prevDesignation;
        $departmentChanged  = $newDepartment !== '' && $newDepartment !== $prevDepartment;
        $isPromotion = $designationChanged || $departmentChanged;
        $isIncrement = (float) $record['increment_amount'] > 0;

        $reportingManager = trim((string) ($record['reporting_manager'] ?? ''));
        if ($reportingManager === '') {
            $managerId = null;
            $deptId = (int) ($record['new_department_id'] ?? 0) ?: (int) ($emp['department_id'] ?? 0);
            $dept = $deptId ? $db->table('department')->select('manager_id')->where('id', $deptId)->get()->getRowArray() : null;
            if (!empty($dept['manager_id']) && (int) $dept['manager_id'] !== $userId) {
                $managerId = (int) $dept['manager_id'];
            } elseif (!empty($emp['branch_id'])) {
                $ba = $db->table('users')->select('id')->where('role', 'branch_admin')->where('branch_id', $emp['branch_id'])
                    ->where('is_deleted', 0)->where('id !=', $userId)->get()->getRowArray();
                $managerId = $ba['id'] ?? null;
            }
            if ($managerId) {
                $m = $db->table('users u')->select('u.username, ui.firstname, ui.lastname')
                    ->join('user_info ui', 'ui.user_id = u.id', 'left')->where('u.id', $managerId)->get()->getRowArray();
                $reportingManager = $m ? ucwords(trim(preg_replace('/\s+/', ' ', ($m['firstname'] ?? '') . ' ' . ($m['lastname'] ?? ''))) ?: (string) $m['username']) : '';
            }
        }

        $fmtDate = static fn($d) => (!empty($d) && $d !== '0000-00-00') ? date('d/m/Y', strtotime($d)) : '';
        $issued = $record['created_at'] ?? date('Y-m-d');

        $name = ucwords(trim(preg_replace('/\s+/', ' ', ($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? ''))));
        $tpl = $this->templateSettings($db);

        return $this->renderPdf($db, $tpl, [
            'employeeName'     => $name,
            'employeeCode'     => (string) ($emp['employee_id'] ?? ''),
            'refNumber'        => $tpl['ref_prefix'] . sprintf('%04d', (int) $record['id']),
            'letterDate'       => $fmtDate($issued),
            'effectiveDate'    => $fmtDate($record['effective_from_date'] ?? ''),
            'prevDesignation'  => $prevDesignation,
            'newDesignation'   => $newDesignation,
            'prevDepartment'   => $prevDepartment,
            'newDepartment'    => $departmentChanged ? $newDepartment : 'No change',
            'prevAnnualCtc'    => $this->money((float) $record['previous_salary'] * 12),
            'newAnnualCtc'     => $this->money((float) $record['new_salary'] * 12),
            'prevMonthly'      => $this->money((float) $record['previous_salary']),
            'newMonthly'       => $this->money((float) $record['new_salary']),
            'incrementAmount'  => $this->money((float) $record['increment_amount']),
            'reportingManager' => $reportingManager,
            'isIncrement'      => $isIncrement,
            'isPromotion'      => $isPromotion,
        ], $name ?: 'employee');
    }

    private function renderPdf($db, array $tpl, array $data, string $fileSuffix)
    {
        $isIncrement = $data['isIncrement'];
        $isPromotion = $data['isPromotion'];
        $variant = $isPromotion && $isIncrement ? 'both' : ($isPromotion ? 'promotion' : 'increment');

        $changes = array_filter([$isPromotion ? 'designation' : '', $isIncrement ? 'compensation' : '']);
        $newDepartmentName = $data['newDepartment'] === 'No change' ? $data['prevDepartment'] : $data['newDepartment'];
        $vars = [
            'employee_name'     => $data['employeeName'],
            'employee_code'     => $data['employeeCode'],
            'company_name'      => $tpl['company_name'],
            'change_text'       => implode(' and ', $changes) ?: 'designation and/or compensation',
            'letter_date'       => $data['letterDate'],
            'effective_date'    => $data['effectiveDate'],
            'prev_designation'  => $data['prevDesignation'],
            'new_designation'   => $data['newDesignation'],
            'prev_department'   => $data['prevDepartment'],
            'new_department'    => $newDepartmentName,
            'prev_annual_ctc'   => $data['prevAnnualCtc'],
            'new_annual_ctc'    => $data['newAnnualCtc'],
            'prev_monthly'      => $data['prevMonthly'],
            'new_monthly'       => $data['newMonthly'],
            'increment_amount'  => $data['incrementAmount'],
            'reporting_manager' => $data['reportingManager'],
        ];

        $text = [];
        foreach ($tpl as $key => $value) {
            $text[$key] = $this->fillPlaceholders($value, $vars, in_array($key, self::RICH_FIELDS, true));
        }

        $title = $text['title_' . $variant];
        $html = view('increment_letter/print', $data + [
            'title'        => $title,
            'subject'      => $text['subject_' . $variant],
            'text'         => $text,
            'employeeLine' => trim(implode(' / ', array_filter([$data['employeeCode'], $newDepartmentName]))),
            'signature'    => $this->defaultSignature($db),
            'company'      => (new \App\Models\CompanyLogoModel())->first() ?: [],
        ]);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeTitle = trim(preg_replace('/[^A-Za-z0-9]+/', '_', ucwords(strtolower($title))), '_') ?: 'Letter';
        $safeName = trim(preg_replace('/[^A-Za-z0-9]+/', '_', $fileSuffix), '_');
        $filename = $safeTitle . '_' . $safeName . '.pdf';
        $disposition = $this->request->getGet('download') ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    private function money(float $amount): string
    {
        if ($amount <= 0) {
            return '-';
        }
        $decimals = fmod($amount, 1.0) > 0 ? '.' . substr(number_format($amount, 2, '.', ''), -2) : '';
        $whole = (string) (int) floor($amount);
        if (strlen($whole) > 3) {
            $whole = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($whole, 0, -3)) . ',' . substr($whole, -3);
        }
        return '₹ ' . $whole . $decimals;
    }

    private function defaultSignature($db): ?array
    {
        if (!$db->tableExists('digital_signatures')) {
            return null;
        }
        $sig = $db->table('digital_signatures')->where('is_default', 1)->get()->getRowArray()
            ?: $db->table('digital_signatures')->orderBy('id', 'DESC')->get()->getRowArray();
        if (!$sig) {
            return null;
        }
        $toDataUri = static function (?string $relPath): string {
            if (empty($relPath) || !is_file(FCPATH . $relPath)) {
                return '';
            }
            $path = FCPATH . $relPath;
            $mime = @mime_content_type($path) ?: 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        };
        return [
            'name'          => $sig['signee_name'] ?? '',
            'designation'   => $sig['designation'] ?? '',
            'signature_src' => $toDataUri($sig['signature_path'] ?? null),
            'stamp_src'     => $toDataUri($sig['stamp_path'] ?? null),
        ];
    }
}
