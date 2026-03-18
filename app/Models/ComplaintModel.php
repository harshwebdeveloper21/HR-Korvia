<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplaintModel extends Model
{
    protected $table            = 'complaints';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'name',
        'email',
        'mobile',
        'type',
        'subject',
        'message',
        'file',
        'status',
        'admin_remark'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get complaints with filtering
     */
    public function getFilteredComplaints($type = null, $status = null, $date_from = null, $date_to = null)
    {
        $builder = $this->builder();
        
        if (!empty($type)) {
            $builder->where('type', $type);
        }
        
        if (!empty($status)) {
            $builder->where('status', $status);
        }
        
        if (!empty($date_from)) {
            $builder->where('DATE(created_at) >=', $date_from);
        }
        
        if (!empty($date_to)) {
            $builder->where('DATE(created_at) <=', $date_to);
        }
        
        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }
}
