<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskModel;
use App\Models\SubtaskModel;
use App\Models\UserModel;
use App\Models\CommentModel;
use App\Models\DepartmentModel;
use App\Services\AuthService;
use App\Libraries\EmailService;
helper('time');
class CommnetController extends ResourceController
{
    private $taskModel;
    private $subtaskModel;
    private $commentModel;
    private $userModel;
    private $authService;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->subtaskModel = new SubtaskModel();
        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel(); // Instantiate the UserModel
        $this->authService = new AuthService(service('request'));
    }

    // public function showTask($taskId)
    // {
    //     $commentModel = new CommentModel();
    //     $data['comments'] = $commentModel->getComments($taskId);
    //     $data['taskId'] = $taskId;

    //     return view('task/profile', $data);
    // }

    public function addComment()
    {
        if ($this->request->isAJAX()) {
            $taskId = $this->request->getVar('task_id');
            $comment = $this->request->getVar('comment');
            $userId = session()->get('user_id');

            $commentModel = new \App\Models\CommentModel();

            $data = [
                'task_id' => $taskId,
                'user_id' => $userId,
                'comment' => $comment
            ];

            $commentId = $commentModel->insert($data);

            if ($commentId) {
                // Fetch the newly inserted comment with user details
                $newComment = $commentModel
                    ->select('comments.*, user_info.firstname, user_info.profile_image')
                    ->join('user_info', 'user_info.user_id = comments.user_id')
                    ->where('comments.id', $commentId)
                    ->first();

                return $this->response->setJSON([
                    'success' => true,
                    'comment' => $newComment
                ]);
            } else {
                return $this->response->setJSON(['success' => false]);
            }
        }
    }
    public function getComments()
    {
        $taskId = $this->request->getGet('task_id');
        $commentModel = new \App\Models\CommentModel();
        $comments = $commentModel->getComments($taskId);

        return $this->response->setJSON([
            'success' => true,
            'comments' => $comments
        ]);
    }


}