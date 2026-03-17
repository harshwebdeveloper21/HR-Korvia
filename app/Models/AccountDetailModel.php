<?php

namespace App\Models;
use CodeIgniter\Model;

class AccountDetailModel extends Model
{
    protected $table = 'account_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = [
       'user_id', 'acc_number', 'bank_name', 'ifsc_code', 'acc_in_name',
        'branch_name', 'branch_code', 'created_by', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    public function getAccountsWithUsername()
    {
    return $this->select('account_detail.*, users.username')
                    ->join('users', 'users.id = account_detail.user_id')
                    ->findAll();
    }
}
