<?php

namespace App\Models;

use CodeIgniter\Model;

class OfferLetterTemplateModel extends Model
{
    protected $table = 'offer_letter_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'template_header', 'template_footer', 'template_img', 'content', 'content_page2', 'content_pages', 'created_by'];
    protected $useTimestamps = true;
}
