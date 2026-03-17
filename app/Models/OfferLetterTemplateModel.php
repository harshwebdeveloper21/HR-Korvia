<?php

namespace App\Models;

use CodeIgniter\Model;

class OfferLetterTemplateModel extends Model
{
    protected $table = 'offer_letter_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'template_img', 'content', 'created_by'];
    protected $useTimestamps = true;
}
