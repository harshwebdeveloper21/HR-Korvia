<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseCategoryModel extends Model
{
    protected $table = 'expense_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'created_at'];
    protected $useTimestamps = false;
}
