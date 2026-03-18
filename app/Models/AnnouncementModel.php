<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table            = 'announcements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 
        'description', 
        'type', 
        'start_date', 
        'end_date', 
        'target_audience', 
        'target_roles', 
        'target_users', 
        'attachment', 
        'status', 
        'created_by', 
        'is_deleted'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active announcements for a specific user based on roles and target audience
     */
    public function getActiveAnnouncements($userId, $userRole, $limit = null)
    {
        $today = date('Y-m-d');
        
        $builder = $this->where('status', 'Active')
                        ->where('start_date <=', $today)
                        ->where('end_date >=', $today)
                        ->where('is_deleted', 0);

        // Filter by target audience
        $builder->groupStart()
                ->where('target_audience', 'All Users')
                ->orGroupStart()
                    ->where('target_audience', 'Specific Role')
                    ->where("FIND_IN_SET('$userRole', target_roles) >", 0)
                ->groupEnd()
                ->orGroupStart()
                    ->where('target_audience', 'Specific Users')
                    ->where("FIND_IN_SET('$userId', target_users) >", 0)
                ->groupEnd()
            ->groupEnd();

        $builder->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Mark an announcement as read for a user
     */
    public function markAsRead($announcementId, $userId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('announcement_reads');
        
        // Check if already exists
        $exists = $builder->where('announcement_id', $announcementId)
                          ->where('user_id', $userId)
                          ->countAllResults();

        if ($exists == 0) {
            return $builder->insert([
                'announcement_id' => $announcementId,
                'user_id'         => $userId,
                'read_at'         => date('Y-m-d H:i:s')
            ]);
        }
        
        return true;
    }

    /**
     * Get read status for announcements for a user
     */
    public function getReadStatus($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('announcement_reads')
                  ->where('user_id', $userId)
                  ->get()
                  ->getResultArray();
    }
}
