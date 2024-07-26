<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\QuestionBoardModel;
use Models\QuestionModel;
use Models\ReservationDateFragmentModel;

class QuestionController extends EmailController
{
    protected QuestionBoardModel $boardModel;
    protected QuestionModel $questionModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->boardModel = model('Models\QuestionBoardModel');
        $this->questionModel = model('Models\QuestionModel');
    }

    /**
     * [get] /api/question-board/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getBoard($id): ResponseInterface
    {
        return $this->typicallyFind($this->boardModel, $id);
    }

    /**
     * [post] /api/question-board/create
     * @return ResponseInterface
     */
    public function createBoard(): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $validationRules = [
            'code' => [
                'label' => 'Code',
                'rules' => 'required|min_length[1]|regex_match[^[^0-9][a-zA-Z0-9_\-]+$]',
                'errors' => [
                    'regex_match' => '{field} have to start with character'
                ],
            ],
            'alias' => [
                'label' => 'Alias',
                'rules' => 'required|min_length[1]',
            ],
        ];
        return $this->typicallyCreate($this->boardModel, $data, $validationRules);
    }

    /**
     * [post] /api/question-board/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function updateBoard($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        return $this->typicallyUpdate($this->boardModel, $id, $data);
    }

    /**
     * [delete] /api/question-board/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteBoard($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->boardModel, $id, $body);
    }

    /**
     * [get] /api/question/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getReservation($id): ResponseInterface
    {
        return $this->typicallyGet($this->questionModel, $id);
    }

    /**
     * [post] /api/question/request
     * @return ResponseInterface
     */
    public function requestReservation(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'question_comment' => [
                'label' => 'Question Comment',
                'rules' => 'required|min_length[1]',
            ],
            'question_board_code' => [
                'label' => 'Question Board Code',
                'rules' => 'required',
            ],
        ];

        if (isset($data['user_id'])) {
            $data['questioner_id'] = $data['user_id'];
        }

        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            $board = $this->boardModel->findByCode($data['question_board_code']);
            $data['question_board_id'] = $board['id'];
            try {
                $result = $this->questionModel->insert($data);
                if (!$result) {
                    $response['messages'] = $this->questionModel->errors();
                } else {
                    $response['success'] = true;
                }

                $adminUsers = $this->userModel->get(['type' => 'admin', 'is_deleted' => 0]);
                $questions = $this->questionModel->get(['id' => $result]);
                if (sizeof($questions) > 0 && sizeof($adminUsers) > 0) {
                    $question = $questions[0];
                    $adminUser = $adminUsers[0];
                    $memberUsers = $this->userModel->get(['type' => 'member', 'is_deleted' => 0]);
                    $copies = [];
                    foreach ($memberUsers as $memberUser) {
                        $copies[] = $memberUser['email'];
                    }
                    $this->sendReservationMail(array_merge([
                        'email' => $adminUser['email'],
                        'copies' => $copies,
                        'title' => lang('Service.email_question_new'),
                    ], $question));
                }
            } catch (Exception $e) {
                //todo(log)
                $response['message'] = $e->getMessage();
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/question/accept/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function acceptReservation($id): ResponseInterface
    {
        $this->checkAdmin();
        $response = [
            'success' => false,
        ];
        $data = $this->request->getPost();

        $data['respondent_id'] = $this->session->user_id;
        $data['status'] = 'accepted';

        if (strlen($id) == 0) {
            $response['message'] = "field 'id' should not be empty.";
        } else {
            try {
                $this->questionModel->update($id, $data);

                $questions = $this->questionModel->get(['id' => $id]);
                if (sizeof($questions) > 0) {
                    $question = $questions[0];
                    if (isset($question['questioner_is_notification']) && $question['questioner_is_notification'] == 1 &&
                        isset($question['questioner_email']) && strlen($question['questioner_email']) > 0) {
                        $this->sendReservationMail(array_merge([
                            'email' => $question['questioner_email'],
                            'title' => lang('Service.email_question_proceed'),
                        ], $question));
                    }
                }
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }
}
