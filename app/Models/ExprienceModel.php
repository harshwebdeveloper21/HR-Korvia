<?php
namespace App\Models;
use CodeIgniter\Model;

class ExprienceModel extends Model
{
    protected $table = 'exprience';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'from_date', 'to_date', 'template_id','generated_by'];
}
?>