<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'sender_id',
        'recipient_id',
        'data',
        'is_read',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Comment this if your CI version doesn't support it
    // protected $casts = [
    //     'data' => 'json',
    //     'is_read' => 'boolean',
    // ];
}
