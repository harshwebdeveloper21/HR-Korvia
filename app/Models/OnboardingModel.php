<?php

namespace App\Models;

use CodeIgniter\Model;

class OnboardingModel extends Model
{
    protected $table = 'onboarding';
    protected $primaryKey = 'id';
    protected $allowedFields = ['candidate_id', 'department_id', 'job_id', 'start_date', 'onboarding_status',
    'docu_submitted','offer_later_id', 'created_at', 'created_by'];
    protected $useTimestamps = true;

    public function getRecruitmentProgress($userRole, $userId)
    {
        $query = $this->select('candidate.firstname, candidate.lastname, candidate.email, candidate.phone, department.department_name, jobs.job_title')
            ->join('candidate', 'candidate.id = onboarding.candidate_id', 'left')
            ->join('department', 'department.id = onboarding.department_id', 'left')
            ->join('jobs', 'jobs.id = onboarding.job_id', 'left')
            ->orderBy('onboarding.created_at', 'DESC');

        if ($userRole !== 'admin' && $userRole !== 'hr') {
            $query->where('candidate.user_id', $userId);
        }

        return $query->findAll();
    }
}
