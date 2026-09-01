<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterEmployeeIdToVarchar extends Migration
{
    public function up()
    {
        // Change employee_id from int(20) to varchar(50) to support string IDs like EMP-078
        $this->db->query("ALTER TABLE user_info MODIFY COLUMN employee_id VARCHAR(50) NULL DEFAULT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE user_info MODIFY COLUMN employee_id INT(20) NULL DEFAULT NULL");
    }
}
