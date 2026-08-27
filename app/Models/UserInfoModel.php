<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInfoModel extends Model
{
    protected $table = 'user_info';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'firstname', 'lastname','email', 'gender', 'date_of_birth', 'address_1', 'address_2', 'state_id', 
        'postcode', 'city_id', 'country_id', 'contact_number', 
        'employee_id', 'designation_id', 'department_id', 'joining_date', 'working_location', 
        'role','salary','job_id','status','status_reason','last_working_day','resume','profile_image','face_photo',
        'last_increment_date', 'last_increment_amount'
    ];

    protected $useTimestamps = true;    
}
