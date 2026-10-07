<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InterviewAssessments extends BaseController
{
    public const COMPETENCIES = [
        'communication'   => ['Communication Skills', 'Clarity, articulation and listening'],
        'job_knowledge'   => ['Job Knowledge / Technical Skills', 'Role-specific competence and expertise'],
        'problem_solving' => ['Problem-Solving Ability', 'Analytical thinking and decision-making'],
        'attitude'        => ['Attitude & Confidence', 'Professionalism, composure and drive'],
        'experience'      => ['Relevant Experience', 'Suitability of past experience for the role'],
        'culture_fit'     => ['Cultural / Team Fit', 'Alignment with company values and team'],
    ];

    public const RECOMMENDATIONS = [
        'Strongly Recommend',
        'Recommend',
        'Recommend with Reservations',
        'Hold for Another Role',
        'Reject',
    ];

    public const RATING_LABELS = [1 => 'Poor', 2 => 'Below Average', 3 => 'Average', 4 => 'Good', 5 => 'Excellent'];

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('interview_assessments ia');
        $builder->select('ia.*, COALESCE(c.candidate_name, i.full_name) as candidate_name');
        $builder->join('interviews i', 'i.id = ia.interview_id', 'left');
        $builder->join('candidate c', 'c.id = i.candidate_id', 'left');
        $builder->orderBy('ia.id', 'DESC');
        $assessments = $builder->get()->getResultArray();

        foreach ($assessments as &$item) {
            $item['score'] = self::scoreFromRatings($item['ratings_data'] ?? null);
        }
        unset($item);

        return view('assessments/index', ['assessments' => $assessments]);
    }

    public function create()
    {
        $interviewId = (int) $this->request->getGet('interview_id');
        $from = $this->request->getGet('from') === 'interviews' ? 'interviews' : '';

        if ($interviewId) {
            $existing = (new \App\Models\InterviewAssessmentModel())->where('interview_id', $interviewId)->orderBy('id', 'DESC')->first();
            if ($existing) {
                return redirect()->to('/assessment/edit/' . $existing['id'] . ($from ? '?from=' . $from : ''));
            }
        }

        $data = $this->formData();
        $data['preselectInterview'] = $interviewId ?: null;
        $data['returnTo'] = $from;

        return view('assessments/form', $data);
    }

    public function store()
    {
        $this->ensureColumns();
        if ($error = $this->validatePayload()) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        $model = new \App\Models\InterviewAssessmentModel();
        $model->insert($this->payloadFromRequest());

        return $this->redirectAfterSave('Assessment submitted successfully!');
    }

    private function redirectAfterSave(string $message)
    {
        $target = $this->request->getPost('return_to') === 'interviews' ? '/addinterview' : '/assessment';
        return redirect()->to($target)->with('success', $message);
    }

    public function edit($id)
    {
        $model = new \App\Models\InterviewAssessmentModel();
        $assessment = $model->find($id);
        if (!$assessment) {
            return redirect()->to('/assessment')->with('error', 'Assessment not found.');
        }

        $data = $this->formData();
        $data['assessment'] = $assessment;
        $data['savedRatings'] = self::ratingsByKey($assessment['ratings_data'] ?? null);
        $data['savedFeedback'] = json_decode($assessment['feedback'] ?? '', true) ?: [];
        $data['returnTo'] = $this->request->getGet('from') === 'interviews' ? 'interviews' : '';

        return view('assessments/form', $data);
    }

    public function update($id)
    {
        $this->ensureColumns();
        $model = new \App\Models\InterviewAssessmentModel();
        if (!$model->find($id)) {
            return redirect()->to('/assessment')->with('error', 'Assessment not found.');
        }
        if ($error = $this->validatePayload()) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        $model->update($id, $this->payloadFromRequest());

        return $this->redirectAfterSave('Assessment updated successfully!');
    }

    public function delete($id)
    {
        $model = new \App\Models\InterviewAssessmentModel();
        $model->delete($id);
        return redirect()->to('/assessment')->with('success', 'Assessment deleted successfully!');
    }

    public function show($id)
    {
        $assessment = $this->loadAssessment($id);
        if (!$assessment) {
            return redirect()->to('/assessment')->with('error', 'Assessment not found.');
        }

        return view('assessments/view', [
            'assessment' => $assessment,
            'competencies' => self::COMPETENCIES,
            'ratingLabels' => self::RATING_LABELS,
        ]);
    }

    private function defaultSignature(): ?array
    {
        $db = \Config\Database::connect();
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

    public function pdf($id)
    {
        $assessment = $this->loadAssessment($id);
        if (!$assessment) {
            return $this->response->setStatusCode(404)->setBody('Assessment not found');
        }

        $company = (new \App\Models\CompanyLogoModel())->first();
        $logoSrc = '';
        $logoFile = $company['pdf_logo'] ?? ($company['logo_img'] ?? '');
        if ($logoFile && is_file(FCPATH . 'upload/' . $logoFile)) {
            $path = FCPATH . 'upload/' . $logoFile;
            $mime = mime_content_type($path) ?: 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }

        $html = view('assessments/print_form', [
            'assessment'      => $assessment,
            'competencies'    => self::COMPETENCIES,
            'ratingLabels'    => self::RATING_LABELS,
            'recommendations' => self::RECOMMENDATIONS,
            'company_name'    => $company['company_name'] ?? getCompanyName(),
            'company_email'   => $company['company_email'] ?? '',
            'logo_src'        => $logoSrc,
            'signature'       => $this->defaultSignature(),
        ]);

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeName = preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($assessment['candidate_name'] ?: 'candidate'));
        $filename = 'Candidate_Assessment_' . trim($safeName, '_') . '_' . $assessment['id'] . '.pdf';
        $disposition = $this->request->getGet('download') ? 'attachment' : 'inline';

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    private function loadAssessment($id): ?array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('interview_assessments ia');
        $builder->select('ia.*, COALESCE(i.full_name, c.candidate_name) as candidate_name, i.mobile_number as interview_mobile');
        $builder->join('interviews i', 'i.id = ia.interview_id', 'left');
        $builder->join('candidate c', 'c.id = i.candidate_id', 'left');
        $builder->where('ia.id', $id);
        $assessment = $builder->get()->getRowArray();
        if (!$assessment) {
            return null;
        }

        $assessment['ratings'] = self::ratingsByKey($assessment['ratings_data'] ?? null);
        $assessment['ratings_list'] = json_decode($assessment['ratings_data'] ?? '', true) ?: [];
        $assessment['feedback'] = json_decode($assessment['feedback'] ?? '', true) ?: [];
        $assessment['score'] = self::scoreFromRatings($assessment['ratings_data'] ?? null);
        $assessment['contact_number'] = ($assessment['contact_number'] ?? '') ?: ($assessment['interview_mobile'] ?? '');

        return $assessment;
    }

    private function formData(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('interviews i');
        $builder->select("i.id, i.mobile_number, i.interview_round, i.interview_date, i.schedule_date,
            COALESCE(NULLIF(i.full_name, ''), c.candidate_name) as candidate_name,
            COALESCE(NULLIF(i.position_applied_for, ''), j.job_title) as position_applied_for,
            COALESCE(d1.department_name, d2.department_name) as department_name,
            COALESCE(u.username, CONCAT(ui.firstname, ' ', ui.lastname)) as interviewer_name", false);
        $builder->join('candidate c', 'c.id = i.candidate_id', 'left');
        $builder->join('jobs j', 'j.id = i.job_id', 'left');
        $builder->join('department d1', 'd1.id = i.department_id', 'left');
        $builder->join('department d2', 'd2.id = j.department_id', 'left');
        $builder->join('users u', 'u.id = i.interviewer_id', 'left');
        $builder->join('user_info ui', 'ui.user_id = i.interviewer_id', 'left');
        $builder->orderBy('i.id', 'DESC');

        return [
            'interviews'      => $builder->get()->getResultArray(),
            'departments'     => $db->table('department')->get()->getResultArray(),
            'competencies'    => self::COMPETENCIES,
            'recommendations' => self::RECOMMENDATIONS,
            'ratingLabels'    => self::RATING_LABELS,
            'savedRatings'    => [],
            'savedFeedback'   => [],
            'preselectInterview' => null,
            'returnTo'        => '',
        ];
    }

    private function validatePayload(): ?string
    {
        if (!$this->request->getPost('interview_id')) {
            return 'Please select a candidate.';
        }
        if (!$this->request->getPost('interview_date')) {
            return 'Date of interview is required.';
        }
        foreach (array_keys(self::COMPETENCIES) as $key) {
            $rating = (int) $this->request->getPost('rating_' . $key);
            if ($rating < 1 || $rating > 5) {
                return 'Please rate the candidate on every competency.';
            }
        }
        if (!in_array($this->request->getPost('recommendation'), self::RECOMMENDATIONS, true)) {
            return 'Please select a final recommendation.';
        }
        return null;
    }

    private function payloadFromRequest(): array
    {
        $ratings = [];
        $total = 0;
        foreach (self::COMPETENCIES as $key => [$title, $desc]) {
            $rating = (int) $this->request->getPost('rating_' . $key);
            $total += $rating;
            $ratings[] = ['key' => $key, 'title' => $title, 'desc' => $desc, 'rating' => $rating];
        }

        return [
            'interview_id'      => $this->request->getPost('interview_id'),
            'job_title'         => $this->request->getPost('job_title'),
            'department'        => $this->request->getPost('department'),
            'interview_round'   => $this->request->getPost('interview_round'),
            'interviewer_name'  => $this->request->getPost('interviewer_name'),
            'interview_date'    => $this->request->getPost('interview_date') ?: null,
            'contact_number'    => $this->request->getPost('contact_number'),
            'reporting_manager' => $this->request->getPost('reporting_manager'),
            'ratings_data'      => json_encode($ratings),
            'overall_score'     => $total,
            'feedback'          => json_encode([
                'strengths'  => (string) $this->request->getPost('strengths'),
                'weaknesses' => (string) $this->request->getPost('weaknesses'),
            ]),
            'recommendation'    => $this->request->getPost('recommendation'),
        ];
    }

    public static function ratingsByKey($ratingsJson): array
    {
        $list = is_array($ratingsJson) ? $ratingsJson : (json_decode((string) $ratingsJson, true) ?: []);
        $byKey = [];
        foreach ($list as $row) {
            $key = $row['key'] ?? null;
            if (!$key) {
                foreach (self::COMPETENCIES as $k => [$title]) {
                    if (strcasecmp(trim($row['title'] ?? ''), $title) === 0) {
                        $key = $k;
                        break;
                    }
                }
            }
            if ($key) {
                $byKey[$key] = (int) ($row['rating'] ?? 0);
            }
        }
        return $byKey;
    }

    public static function scoreFromRatings($ratingsJson): array
    {
        $list = is_array($ratingsJson) ? $ratingsJson : (json_decode((string) $ratingsJson, true) ?: []);
        $total = 0;
        $count = 0;
        foreach ($list as $row) {
            $rating = (int) ($row['rating'] ?? 0);
            if ($rating > 0) {
                $total += $rating;
            }
            $count++;
        }
        return ['total' => $total, 'max' => $count * 5];
    }

    private function ensureColumns(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('contact_number', 'interview_assessments')) {
                $db->query("ALTER TABLE interview_assessments ADD COLUMN contact_number VARCHAR(50) NULL DEFAULT NULL AFTER interview_mode");
            }
            if (!$db->fieldExists('reporting_manager', 'interview_assessments')) {
                $db->query("ALTER TABLE interview_assessments ADD COLUMN reporting_manager VARCHAR(255) NULL DEFAULT NULL AFTER contact_number");
            }
        } catch (\Throwable $e) {
            log_message('error', 'Assessment column check failed: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('interview_assessments ia');
        $builder->select('ia.*, COALESCE(c.candidate_name, i.full_name) as candidate_name');
        $builder->join('interviews i', 'i.id = ia.interview_id', 'left');
        $builder->join('candidate c', 'c.id = i.candidate_id', 'left');
        $builder->orderBy('ia.id', 'DESC');
        $records = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Assessments');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Candidate Name',
            'C1' => 'Position Applied For',
            'D1' => 'Interview Round',
            'E1' => 'Score',
            'F1' => 'Recommendation',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => ltrim(getPrimaryColor(), '#')],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]
                ]
            ]);
            $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);
        }

        $rowNum = 2;
        foreach ($records as $index => $rec) {
            $score = self::scoreFromRatings($rec['ratings_data'] ?? null);
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $rec['candidate_name'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $rec['job_title'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, $rec['interview_round'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $score['max'] ? $score['total'] . ' / ' . $score['max'] : '-');
            $sheet->setCellValue('F' . $rowNum, $rec['recommendation'] ?? '-');

            $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]
                ]
            ]);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Assessments_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
