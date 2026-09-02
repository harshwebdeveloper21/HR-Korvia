<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\HolidayCalendarModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HolidaysController extends BaseController
{
    protected $holidayCalendarModel;

    public function __construct()
    {
        $this->holidayCalendarModel = new HolidayCalendarModel();
    }

    /**
     * Accurate Festival Dates by Year (2024 - 2030) - Excluding Fixed National Holidays & New Year
     */
    public static function getStandardHolidaysByYear($year)
    {
        $calendars = [
            2024 => [
                ['title' => 'Makar Sankranti', 'date' => '2024-01-14', 'description' => 'Happy Makar Sankranti / Uttarayan'],
                ['title' => 'Maha Shivratri', 'date' => '2024-03-08', 'description' => 'Happy Maha Shivratri'],
                ['title' => 'Dhuleti', 'date' => '2024-03-25', 'description' => 'Happy Holi / Dhuleti - Festival of Colors'],
                ['title' => 'Raksha Bandhan', 'date' => '2024-08-19', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2024-08-26', 'description' => 'Happy Krishna Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2024-09-17', 'description' => 'Happy Ganesh Chaturthi / Visarjan'],
                ['title' => 'Dussehra', 'date' => '2024-10-12', 'description' => 'Happy Dussehra / Vijayadashami'],
                ['title' => 'Diwali - Deepawali', 'date' => '2024-10-31', 'description' => 'Happy Diwali - Festival of Lights'],
                ['title' => 'Bhai Duj', 'date' => '2024-11-03', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2024-12-25', 'description' => 'Merry Christmas'],
            ],
            2025 => [
                ['title' => 'Makar Sankranti', 'date' => '2025-01-14', 'description' => 'Happy Makar Sankranti / Uttarayan'],
                ['title' => 'Maha Shivratri', 'date' => '2025-02-26', 'description' => 'Happy Maha Shivratri'],
                ['title' => 'Dhuleti', 'date' => '2025-03-14', 'description' => 'Happy Holi / Dhuleti - Festival of Colors'],
                ['title' => 'Raksha Bandhan', 'date' => '2025-08-09', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2025-08-16', 'description' => 'Happy Krishna Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2025-09-06', 'description' => 'Happy Ganesh Chaturthi / Visarjan'],
                ['title' => 'Dussehra', 'date' => '2025-10-02', 'description' => 'Happy Dussehra / Vijayadashami'],
                ['title' => 'Diwali - Deepawali', 'date' => '2025-10-20', 'description' => 'Happy Diwali - Festival of Lights'],
                ['title' => 'Bhai Duj', 'date' => '2025-10-23', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2025-12-25', 'description' => 'Merry Christmas'],
            ],
            2026 => [
                ['title' => 'Makar Sankranti', 'date' => '2026-01-14', 'description' => 'Happy Makar Sankranti'],
                ['title' => 'Dhuleti', 'date' => '2026-03-04', 'description' => 'Happy Dhuleti'],
                ['title' => 'Raksha Bandhan', 'date' => '2026-08-28', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2026-09-04', 'description' => 'Happy Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2026-09-25', 'description' => 'Happy Ganesh Visarjan'],
                ['title' => 'Diwali - Deepawali', 'date' => '2026-11-08', 'description' => 'Happy Diwali'],
                ['title' => 'Bhai Duj', 'date' => '2026-11-11', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2026-12-25', 'description' => 'Merry Christmas'],
            ],
            2027 => [
                ['title' => 'Makar Sankranti', 'date' => '2027-01-14', 'description' => 'Happy Makar Sankranti'],
                ['title' => 'Dhuleti', 'date' => '2027-03-23', 'description' => 'Happy Dhuleti'],
                ['title' => 'Raksha Bandhan', 'date' => '2027-08-17', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2027-08-25', 'description' => 'Happy Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2027-09-15', 'description' => 'Happy Ganesh Visarjan'],
                ['title' => 'Diwali - Deepawali', 'date' => '2027-10-29', 'description' => 'Happy Diwali'],
                ['title' => 'Bhai Duj', 'date' => '2027-11-01', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2027-12-25', 'description' => 'Merry Christmas'],
            ],
            2028 => [
                ['title' => 'Makar Sankranti', 'date' => '2028-01-14', 'description' => 'Happy Makar Sankranti'],
                ['title' => 'Dhuleti', 'date' => '2028-03-11', 'description' => 'Happy Dhuleti'],
                ['title' => 'Raksha Bandhan', 'date' => '2028-08-05', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2028-08-13', 'description' => 'Happy Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2028-09-03', 'description' => 'Happy Ganesh Visarjan'],
                ['title' => 'Diwali - Deepawali', 'date' => '2028-10-17', 'description' => 'Happy Diwali'],
                ['title' => 'Bhai Duj', 'date' => '2028-10-20', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2028-12-25', 'description' => 'Merry Christmas'],
            ],
            2029 => [
                ['title' => 'Makar Sankranti', 'date' => '2029-01-14', 'description' => 'Happy Makar Sankranti'],
                ['title' => 'Dhuleti', 'date' => '2029-03-01', 'description' => 'Happy Dhuleti'],
                ['title' => 'Raksha Bandhan', 'date' => '2029-08-24', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2029-09-01', 'description' => 'Happy Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2029-09-22', 'description' => 'Happy Ganesh Visarjan'],
                ['title' => 'Diwali - Deepawali', 'date' => '2029-11-05', 'description' => 'Happy Diwali'],
                ['title' => 'Bhai Duj', 'date' => '2029-11-08', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2029-12-25', 'description' => 'Merry Christmas'],
            ],
            2030 => [
                ['title' => 'Makar Sankranti', 'date' => '2030-01-14', 'description' => 'Happy Makar Sankranti'],
                ['title' => 'Dhuleti', 'date' => '2030-03-20', 'description' => 'Happy Dhuleti'],
                ['title' => 'Raksha Bandhan', 'date' => '2030-08-13', 'description' => 'Happy Raksha Bandhan'],
                ['title' => 'Janmashtami', 'date' => '2030-08-21', 'description' => 'Happy Janmashtami'],
                ['title' => 'Ganesh Visarjan', 'date' => '2030-09-11', 'description' => 'Happy Ganesh Visarjan'],
                ['title' => 'Diwali - Deepawali', 'date' => '2030-10-26', 'description' => 'Happy Diwali'],
                ['title' => 'Bhai Duj', 'date' => '2030-10-29', 'description' => 'Happy Bhai Duj'],
                ['title' => 'Christmas', 'date' => '2030-12-25', 'description' => 'Merry Christmas'],
            ],
        ];

        return $calendars[(int)$year] ?? [];
    }

    public function company_holidays()
    {
        return view('holidays/show');
    }

    public function display_holidays()
    {
        return view('holidays/holidays');
    }
    public function add_holidays()
    {
        return view('holidays/holidays_add');
    }
    public function edit()
    {
        return view('holidays/edit_holiday');
    }


    public function get_holidays()
    {
        $year = $this->request->getGet('year');

        $builder = $this->holidayCalendarModel->orderBy('holiday_date', 'ASC');

        if (!empty($year) && $year !== 'all') {
            $builder->where('YEAR(holiday_date)', (int)$year);
        }

        $holidays = $builder->findAll();

        // Fetch distinct years from database
        $db = \Config\Database::connect();
        $yearsQuery = $db->query("SELECT DISTINCT YEAR(holiday_date) as yr FROM holiday_calendar ORDER BY yr ASC")->getResultArray();
        $dbYears = array_filter(array_column($yearsQuery, 'yr'));

        // Supported years list
        $allYears = array_unique(array_merge([2024, 2025, 2026, 2027, 2028, 2029, 2030], $dbYears));
        sort($allYears);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $holidays,
            'years' => array_values($allYears),
            'selected_year' => $year ?: date('Y')
        ]);
    }

    /**
     * Auto-generate / seed holidays for a year, next 5 years, or full calendar
     */
    public function autoGenerate()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $targetYear = $input['year'] ?? date('Y');
        $mode = $input['mode'] ?? 'single'; // 'single', 'next_5_years', 'all'

        $yearsToProcess = [];
        $currentYr = (int)date('Y');

        if ($mode === 'next_5_years') {
            for ($y = $currentYr; $y <= $currentYr + 5; $y++) {
                $yearsToProcess[] = $y;
            }
        } elseif ($mode === 'all' || $targetYear === 'all') {
            $yearsToProcess = [2024, 2025, 2026, 2027, 2028, 2029, 2030];
        } else {
            $yearsToProcess = [(int)$targetYear];
        }

        $insertedCount = 0;
        $skippedCount = 0;

        $ignoredKeywords = ['republic', 'independence', 'gandhi', 'new year'];

        foreach ($yearsToProcess as $yr) {
            $standardList = self::getStandardHolidaysByYear($yr);
            foreach ($standardList as $holiday) {
                // Ignore explicitly requested holidays
                $lowerTitle = strtolower($holiday['title']);
                $shouldIgnore = false;
                foreach ($ignoredKeywords as $kw) {
                    if (strpos($lowerTitle, $kw) !== false) {
                        $shouldIgnore = true;
                        break;
                    }
                }
                if ($shouldIgnore) {
                    continue;
                }

                // Check if holiday already exists on this date
                $existing = $this->holidayCalendarModel
                    ->where('holiday_date', $holiday['date'])
                    ->first();

                if (!$existing) {
                    $this->holidayCalendarModel->insert([
                        'title'        => $holiday['title'],
                        'holiday_date' => $holiday['date'],
                        'description'  => $holiday['description'],
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
                    $insertedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        $yearsStr = implode(', ', $yearsToProcess);
        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => "Processed year(s) [{$yearsStr}]: {$insertedCount} holidays created" . ($skippedCount > 0 ? " ({$skippedCount} already existed)." : "."),
            'inserted' => $insertedCount,
            'skipped'  => $skippedCount,
            'years'    => $yearsToProcess
        ]);
    }

    /**
     * Export Holidays to Excel
     */
    public function export()
    {
        $year = $this->request->getGet('year');
        $builder = $this->holidayCalendarModel->orderBy('holiday_date', 'ASC');

        if (!empty($year) && $year !== 'all') {
            $builder->where('YEAR(holiday_date)', (int)$year);
            $filename = "Holidays_{$year}_" . date('Ymd_His') . ".xlsx";
            $sheetTitle = "Holidays {$year}";
        } else {
            $filename = "Holidays_All_" . date('Ymd_His') . ".xlsx";
            $sheetTitle = "All Holidays";
        }

        $holidays = $builder->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);

        // Header definitions
        $headers = ['No.', 'Holiday Title', 'Holiday Date', 'Day', 'Description'];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . '1', $header);
        }

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        foreach ($holidays as $i => $item) {
            $timestamp = strtotime($item['holiday_date']);
            $formattedDate = date('d M Y', $timestamp);
            $dayName = date('l', $timestamp);

            $sheet->setCellValue('A' . $rowNum, $i + 1);
            $sheet->setCellValue('B' . $rowNum, $item['title']);
            $sheet->setCellValue('C' . $rowNum, $formattedDate);
            $sheet->setCellValue('D' . $rowNum, $dayName);
            $sheet->setCellValue('E' . $rowNum, $item['description'] ?? '');

            // Alternate row fill
            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:E{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
        }

        // Auto column widths
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function store()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['title']) || empty($data['holiday_date'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Title and holiday date are required.'
            ], 400);
        }

        // Check if holiday with same date already exists
        $existing = $this->holidayCalendarModel
            ->where('holiday_date', $data['holiday_date'])
            ->first();

        if ($existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'A holiday already exists on this date.'
            ], 409); // 409 Conflict
        }

        $insertData = [
            'title'         => $data['title'],
            'holiday_date'  => $data['holiday_date'],
            'description'   => $data['description'] ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        if ($this->holidayCalendarModel->insert($insertData)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Holiday added successfully.'
            ], 200);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to add holiday.'
            ], 500);
        }
    }
    public function update()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['title']) || empty($data['holiday_date']) || empty($data['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Title, holiday date, and ID are required.'
            ], 400);
        }

        // Check if holiday exists by ID
        $existing = $this->holidayCalendarModel->find($data['id']);
        if (!$existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Holiday not found.'
            ], 404);
        }

        // Check if another holiday exists on the same date (exclude current ID)
        $duplicate = $this->holidayCalendarModel
            ->where('holiday_date', $data['holiday_date'])
            ->where('id !=', $data['id'])
            ->first();

        if ($duplicate) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'A holiday already exists on this date.'
            ], 409);
        }

        $updateData = [
            'title'         => $data['title'],
            'holiday_date'  => $data['holiday_date'],
            'description'   => $data['description'] ?? null,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        if ($this->holidayCalendarModel->update($data['id'], $updateData)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Holiday updated successfully.'
            ], 200);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to update holiday.'
            ], 500);
        }
    }


    public function delete($id = null)
    {
        // Load the model
        $holidayModel = new \App\Models\HolidayCalendarModel();

        // Check if the holiday exists
        $holiday = $holidayModel->find($id);

        if (!$holiday) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Holiday not found.'
            ], 404);
        }

        // Perform delete
        if ($holidayModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Holiday deleted successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete holiday.'
            ], 500);
        }
    }
    public function edit_holiday($id = null)
    {
        $holidayModel = new \App\Models\HolidayCalendarModel();
        $holiday = $holidayModel->find($id);

        if (!$holiday) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Holiday not found.'
            ], 404);
        }
        return $this->response->setJSON([
            'status' => 'success',
            'holiday' => $holiday,
        ]);
    }
}
