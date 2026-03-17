<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'task_id', 'comment', 'created_at'];
    protected $useTimestamps = false;
    public function getComments($taskId)
    {
        return $this->select('comments.*, user_info.firstname, user_info.profile_image')
                    ->join('user_info', 'user_info.user_id = comments.user_id')
                    ->where('comments.task_id', $taskId)
                    ->orderBy('comments.created_at', 'DESC')
                    ->findAll();
    }

    public function addComment($data)
    {
        return $this->save($data);
    }
}
