<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFacePhotoToUserInfo extends Migration
{
    public function up()
    {
        // Guard: only add column if it doesn't already exist
        $fields = $this->db->getFieldNames('user_info');
        if (!in_array('face_photo', $fields)) {
            $this->forge->addColumn('user_info', [
                'face_photo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'profile_image',
                ]
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('user_info', 'face_photo');
    }
}

