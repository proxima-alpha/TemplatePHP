<?php

namespace Views;

use App\Helpers\Utils;
use Exception;
use Models\BoardModel;
use Models\CustomFileModel;
use Models\ReplyModel;
use Models\TopicModel;

class BoardController extends BaseClientController
{
    protected BoardModel $boardModel;
    protected TopicModel $topicModel;
    protected CustomFileModel $customFileModel;
    protected ReplyModel $replyModel;

    public function __construct()
    {
        parent::__construct();
        $this->boardModel = model('Models\BoardModel');
        $this->topicModel = model('Models\TopicModel');
        $this->customFileModel = model('Models\CustomFileModel');
        $this->replyModel = model('Models\ReplyModel');
    }

    /**
     * /board/{code}/{index}
     * @param $code
     * @param $page
     * @return string
     */
    public function getBoard($code, $page = 1): string
    {
        $page = Utils::toInt($page);
        $data = $this->getViewData();
        try {
            $board = $this->boardModel->findByCode($code);
            $data['board'] = $board;
            if ($data['board']['is_public'] != 1 && !$data['is_login']) {
                return view('/redirect', [
                    'path' => '/login'
                ]);
            }
            $searchCondition = [
                'board_id' => $board['id'],
                'is_deleted' => 0,
            ];
            if ($board['is_public'] != 1 && !$data['is_admin']) {
                $searchCondition['user_id'] = $data['user_id'];
            }
            $result = $this->topicModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], $searchCondition);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/board/' . $code,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        $css = [];
        $js = ['/library/slick/slick.min.js'];
        $view = '';
        if ($board['type'] == 'grid') {
            $css = [
                '/common/grid',
                '/client/board/grid',
            ];
            $js = array_merge($js, [
                '/common/topic_view',
                '/module/slick_custom',
            ]);
            $view = '/client/board/grid';
        } else if ($board['type'] == 'table') {
            $css = [
                '/common/table',
                '/client/topic/table',
            ];
            $view = '/client/topic/table';
        }
        return parent::loadHeader([
                'css' => $css,
                'js' => $js,
            ])
            . view($view, $data)
            . parent::loadFooter();
    }

    /**
     * /topic/{id}
     * @param $id
     * @return string
     */
    public function getTopic($id = 1): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getTopicData($id));
            if ($data['board']['is_public'] != 1) {
                if (!$data['is_login']) {
                    return view('/redirect', [
                        'path' => '/login'
                    ]);
                }
                if (!$data['is_admin'] && $data['data']['user_id'] != $data['user_id']) {
                    throw new Exception('forbidden');
                }
            }
            $result = $this->replyModel->getPaginated([
                'per_page' => 5,
                'page' => 1,
            ], [
                'topic_id' => $id,
                'is_deleted' => 0,
                'depth' => 0,
            ]);
            $data['reply'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/client/topic/common',
                    '/common/uploader_slider_box',
                    '/client/topic/view',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/common/delete',
                    '/common/topic',
                    '/common/topic_view',
                ],
            ])
            . view('/client/topic/view', $data)
            . parent::loadFooter();
    }

    /**
     * /topic/{id}/edit
     * @param $id
     * @return string
     */
    public function editTopic($id = 1): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getTopicData($id));
            if ($data['board']['is_public'] != 1) {
                if (!$data['is_login']) {
                    return view('/redirect', [
                        'path' => '/login'
                    ]);
                }
                if (!$data['is_admin'] && $data['data']['user_id'] != $data['user_id']) {
                    throw new Exception('forbidden');
                }
            }
            $data = array_merge($data, [
                'type' => 'edit'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/client/topic/common',
                    '/client/topic/input',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/topic',
                ],
            ])
            . view('/client/topic/input', $data)
            . parent::loadFooter();
    }

    /**
     * /board/{code}/topic/create
     * @param $code
     * @return string
     */
    public function createTopic($code): string
    {
        $data = $this->getViewData();
        try {
            $board = $this->boardModel->findByCode($code);
            $data['board'] = $board;
            if ($data['board']['is_public'] != 1 && !$data['is_login']) {
                return view('/redirect', [
                    'path' => '/login'
                ]);
            }
            $data = array_merge($data, [
                'type' => 'create'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/client/topic/common',
                    '/client/topic/input',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/topic',
                ],
            ])
            . view('/client/topic/input', $data)
            . parent::loadFooter();
    }

    /**
     * topic 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getTopicData($id): array
    {
        $result = [];
        $topics = $this->topicModel->get(['id' => $id, 'is_deleted' => 0]);
        if (sizeof($topics) != 1) throw new Exception('deleted');
        $topic = $topics[0];
        $board = $this->boardModel->find($topic['board_id']);
        $files = $this->customFileModel->get(['topic_id' => $id]);
        $topic['files'] = $files;
        $result['data'] = $topic;
        $result['board'] = $board;
        return $result;
    }
}
