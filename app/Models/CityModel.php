<?php

namespace App\Models;

use CodeIgniter\Model;

class CityModel extends Model
{
    protected $table = 'city';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'city_name','created_by','country_id', 'created_at','updated_at'
    ];
    protected $useTimestamps = true;
}
