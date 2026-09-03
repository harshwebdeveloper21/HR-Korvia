<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTemplateHeaderFooterToExprienceLetterTemplates extends Migration
{
    public function up()
    {
        $fields = [
            'template_header' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'template_img'
            ],
            'template_footer' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'template_header'
            ],
        ];

        if (!$this->db->fieldExists('template_header', 'exprience_letter_templetes')) {
            $this->forge->addColumn('exprience_letter_templetes', [
                'template_header' => $fields['template_header']
            ]);
        }

        if (!$this->db->fieldExists('template_footer', 'exprience_letter_templetes')) {
            $this->forge->addColumn('exprience_letter_templetes', [
                'template_footer' => $fields['template_footer']
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('template_header', 'exprience_letter_templetes')) {
            $this->forge->dropColumn('exprience_letter_templetes', 'template_header');
        }
        if ($this->db->fieldExists('template_footer', 'exprience_letter_templetes')) {
            $this->forge->dropColumn('exprience_letter_templetes', 'template_footer');
        }
    }
}
