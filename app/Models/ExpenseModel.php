<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'category_id', 'amount', 'payment_method', 
        'paid_by', 'expense_date', 'description', 'attachment', 
        'status', 'created_by', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getExpensesWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' e');
        $builder->select('e.*, ec.name as category_name, u.firstname as paid_by_name, u2.firstname as created_by_name');
        $builder->join('expense_categories ec', 'ec.id = e.category_id', 'left');
        $builder->join('user_info u', 'u.user_id = e.paid_by', 'left');
        $builder->join('user_info u2', 'u2.user_id = e.created_by', 'left');

        if (!empty($filters['month'])) {
            $builder->where('MONTH(e.expense_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $builder->where('YEAR(e.expense_date)', $filters['year']);
        }
        if (!empty($filters['category_id'])) {
            $builder->where('e.category_id', $filters['category_id']);
        }
        if (!empty($filters['status'])) {
            $builder->where('e.status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $builder->where('e.created_by', $filters['user_id']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('e.title', $filters['search'])
                    ->orLike('u.firstname', $filters['search'])
                    ->groupEnd();
        }

        $builder->orderBy('e.expense_date', 'DESC');
        return $builder->get()->getResultArray();
    }
}
