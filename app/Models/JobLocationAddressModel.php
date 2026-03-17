<?php
namespace App\Models;
use CodeIgniter\Model;

class JobLocationAddressModel extends Model {
    protected $table = 'job_location_addresses';
    protected $primaryKey = 'address_id';
    protected $allowedFields = ['locations_id', 'address', 'city_id', 'state_id', 'country_id', 'postal_code', 'created_at', 'updated_at'];
}

?>