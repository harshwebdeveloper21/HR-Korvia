<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table = 'chat';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'receiver_id', 'message', 'files', 'sent_at'];

    public function saveMessage($sender_id, $receiver_id, $message, $files = null)
    {
        $data = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'files' => $files,
            'sent_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    public function getMessages($sender_id, $receiver_id)
    {
        return $this->builder()
            ->groupStart()
                ->where('sender_id', $sender_id)
                ->where('receiver_id', $receiver_id)
            ->groupEnd()
            ->orGroupStart()
                ->where('sender_id', $receiver_id)
                ->where('receiver_id', $sender_id)
            ->groupEnd()
            ->orderBy('sent_at', 'ASC')
            ->get()
            ->getResultArray();
    }
}
