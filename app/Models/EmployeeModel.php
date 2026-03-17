<?php
namespace App\Models;
use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = "empreport";
    protected $primaryKey = "id";
    protected $allowedFields = ["user_id", "department_id", "designation_id", "joining_date"];


}
