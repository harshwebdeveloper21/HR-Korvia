<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LeaveTypeModel;
use App\Models\LeaveModel;
use App\Services\AuthService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeaveTypeController extends ResourceController
{
    private $leaveTypeModel;
    private $authService;

    public function __construct()
    {
        $this->leaveTypeModel = new LeaveTypeModel();
        $this->authService = new AuthService(service('request'));
    }

    public function creates()
    {
        return view('leave_type/leave_type');
    }

    public function display()
    {
        return view('leave_type/view');
    }

    // Display All Leave Types
    public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $records = $this->leaveTypeModel->orderBy('created_at', 'DESC')->findAll();
        return $this->respond(['status' => 'success', 'data' => $records]);
    }

    // Display Single Leave Type
    public function getById($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        $record = $this->leaveTypeModel->find($id);
        if ($record) {
            return $this->respond(['status' => 'success', 'data' => $record]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Leave type not found'], 404);
    }

    // Create Leave Type
    public function create()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $data = $this->request->getPost();

        if (!$this->validate([
            'leave_type' => 'required',
            'number_of_leaves' => 'required|numeric',
        ])) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        $data['created_by'] = $user->sub;

        if ($this->leaveTypeModel->insert($data)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Leave type added successfully'
            ], 201);
        }

        return $this->respond([
            'status' => 'error',
            'message' => 'Failed to add Leave type'
        ], 500);
    }

    // Update Leave Type
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        $data = $this->request->getPost();

        if ($this->leaveTypeModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Leave type updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update Leave type'], 500);
    }

    // Delete Leave Type
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized('Unauthorized: Token missing or invalid');
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->failForbidden('Forbidden: You do not have access to this resource');
        }

        if ($this->leaveTypeModel->delete($id)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Leave type deleted successfully'
            ]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete Leave type'], 500);
    }

    /**
     * Export Leave Types to styled Excel (.xlsx)
     */
    public function exportExcel()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized: Token missing or invalid']);
        }

        $search = $this->request->getGet('search');

        $builder = $this->leaveTypeModel->builder();
        $builder->select('leave_type.*');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('leave_type.leave_type', $search)
                ->orLike('leave_type.number_of_leaves', $search)
                ->groupEnd();
        }

        $records = $builder->orderBy('leave_type.created_at', 'DESC')->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leave Types');

        $headers = [
            'A1' => 'S.No',
            'B1' => 'Leave Type',
            'C1' => 'Number of Leaves',
            'D1' => 'Allow Half Day',
            'E1' => 'Created Date'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E66136']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowNum = 2;
        $sno = 1;
        foreach ($records as $item) {
            $halfDay = !empty($item['allow_half_day']) && $item['allow_half_day'] == 1 ? 'Yes' : 'No';

            $sheet->setCellValue('A' . $rowNum, $sno++);
            $sheet->setCellValue('B' . $rowNum, $item['leave_type'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $item['number_of_leaves'] ?? '0');
            $sheet->setCellValue('D' . $rowNum, $halfDay);
            $sheet->setCellValue('E' . $rowNum, !empty($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : '-');
            $rowNum++;
        }

        $lastRow = $rowNum > 2 ? $rowNum - 1 : 2;
        $borderStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
        ];
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray($borderStyle);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $filename = 'Leave_Types_' . date('Y_m_d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
