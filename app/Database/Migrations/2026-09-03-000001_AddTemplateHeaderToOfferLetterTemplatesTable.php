<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTemplateHeaderToOfferLetterTemplatesTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('template_header', 'offer_letter_templates')) {
            $this->forge->addColumn('offer_letter_templates', [
                'template_header' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                    'after'      => 'title',
                ],
            ]);
        }
        if (!$this->db->fieldExists('template_footer', 'offer_letter_templates')) {
            $this->forge->addColumn('offer_letter_templates', [
                'template_footer' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                    'after'      => 'template_header',
                ],
            ]);
        }
        if (!$this->db->fieldExists('content_page2', 'offer_letter_templates')) {
            $this->forge->addColumn('offer_letter_templates', [
                'content_page2' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'content',
                ],
            ]);
        }
        if (!$this->db->fieldExists('content_pages', 'offer_letter_templates')) {
            $this->forge->addColumn('offer_letter_templates', [
                'content_pages' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                    'after' => 'content_page2',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('template_header', 'offer_letter_templates')) {
            $this->forge->dropColumn('offer_letter_templates', 'template_header');
        }
        if ($this->db->fieldExists('template_footer', 'offer_letter_templates')) {
            $this->forge->dropColumn('offer_letter_templates', 'template_footer');
        }
        if ($this->db->fieldExists('content_page2', 'offer_letter_templates')) {
            $this->forge->dropColumn('offer_letter_templates', 'content_page2');
        }
        if ($this->db->fieldExists('content_pages', 'offer_letter_templates')) {
            $this->forge->dropColumn('offer_letter_templates', 'content_pages');
        }
    }
}
