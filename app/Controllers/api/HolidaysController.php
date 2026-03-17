<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\HolidayCalendarModel;

class HolidaysController extends BaseController
{
    protected $holidayCalendarModel;

    public function __construct()
    {
        $this->holidayCalendarModel = new HolidayCalendarModel();
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
        $holidays = $this->holidayCalendarModel
            ->orderBy('holiday_date', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $holidays
        ]);
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
