<?php

namespace App\Models;

use CodeIgniter\Model;

class HolidayCalendarModel extends Model
{
    protected $table            = 'holiday_calendar';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'title',
        'holiday_date',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true; // Enables auto setting created_at and updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Optionally:
    protected $returnType    = 'array';
}
