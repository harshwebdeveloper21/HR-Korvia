<?php

namespace App\Controllers;

use App\Models\RecruitmentModel;
use CodeIgniter\RESTful\ResourceController;

class RecruitmentController extends ResourceController
{
    public function create()
    {
        $model = new RecruitmentModel();
        $data = $this->request->getPost();
        
        if ($model->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Candidate added']);
        }
        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Candidate not added']);
    }
}
