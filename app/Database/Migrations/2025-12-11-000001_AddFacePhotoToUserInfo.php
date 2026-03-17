<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFacePhotoToUserInfo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user_info', [
            'face_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'profile_image'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user_info', 'face_photo');
    }
}

