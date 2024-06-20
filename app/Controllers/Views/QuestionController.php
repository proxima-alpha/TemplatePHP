<?php

namespace Views;

use App\Helpers\Utils;
use Exception;
use Models\QuestionBoardModel;
use Models\QuestionModel;

class QuestionController extends BaseClientController
{
    protected QuestionBoardModel $boardModel;
    protected QuestionModel $questionModel;

    public function __construct()
    {
        parent::__construct();
        $this->boardModel = model('Models\QuestionBoardModel');
        $this->questionModel = model('Models\QuestionModel');
    }

    /**
     * /question-board/{code}/{page}
     * @param $code
     * @param $page
     * @return string
     */
    function getBoard($code, $page = 1): string
    {
        $page = Utils::toInt($page);
        $data = $this->getViewData();
        try {
            $board = $this->boardModel->findByCode($code);
            $data['board'] = $board;
            if($data['board']['is_public'] != 1 && !$data['is_login']) {
                return view('/redirect', [
                    'path' => '/login'
                ]);
            }
            $searchCondition = [
                'question_board_id' => $board['id']
            ];
            if ($board['is_public'] != 1) {
                $searchCondition['questioner_id'] = $data['user_id'];
            }
            $result = $this->questionModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], $searchCondition);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/question-board/' . $code,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/client/question/table',
                ],
                'js' => [
                    '/client/question',
                ],
            ])
            . view('/client/question/table', $data)
            . parent::loadFooter();
    }

    /**
     * /question/{id}
     * @param $id
     * @return string
     */
    function get($id): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getReservationData($id));
            if($data['board']['is_public'] != 1) {
                if(!$data['is_login']) {
                    return view('/redirect', [
                        'path' => '/login'
                    ]);
                }
                if(!$data['is_admin'] && $data['data']['user_id'] != $data['user_id']) {
                    throw new Exception('forbidden');
                }
            }
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/client/question/view',
                ],
                'js'=> [
                    '/module/calendar',
                    '/module/time_selector',
                    '/common/reservation',
                ],
            ])
            . view('/client/question/view', $data)
            . parent::loadFooter();
    }

    /**
     * @throws Exception
     */
    private function getReservationData($id): array
    {
        $result = [];
        $questions = $this->questionModel->get(['id' => $id]);
        if (sizeof($questions) != 1) throw new Exception('deleted');
        $question = $questions[0];
        $board = $this->boardModel->find($question['question_board_id']);
        $result['data'] = $question;
        $result['board'] = $board;
        return $result;
    }
}
