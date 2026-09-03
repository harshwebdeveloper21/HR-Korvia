<?php

namespace App\Models;

use CodeIgniter\Model;

class ExprienceLetterModel extends Model
{
        protected $table = 'exprience_letter_templetes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'template_img', 'template_header', 'template_footer', 'content', 'created_by'];
    protected $useTimestamps = true;
}

?>
