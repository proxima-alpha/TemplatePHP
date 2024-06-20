<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\QuestionBoardModel;
use Models\QuestionModel;

class QuestionController extends BaseAdminController
{
    protected QuestionBoardModel $boardModel;
    protected QuestionModel $questionModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->boardModel = model('Models\QuestionBoardModel');
        $this->questionModel = model('Models\QuestionModel');
    }

    /**
     * /admin/question-board/{code}/{page}
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
            $result = $this->questionModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'question_board_id' => $board['id'],
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/question-board/' . $code,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/question/table',
                ],
                'js' => [
                    '/admin/question',
                ],
            ])
            . view('/admin/question/table', $data)
            . parent::loadFooter();
    }
}
