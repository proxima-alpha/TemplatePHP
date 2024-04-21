<?php

namespace API;

use App\Helpers\QueryHelper;
use App\Helpers\Utils;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BaseModel;
use Models\ReplyModel;
use Models\TopicModel;

class TopicController extends CustomFileController
{
    protected TopicModel $topicModel;
    protected ReplyModel $replyModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->topicModel = model('Models\TopicModel');
        $this->customFileModel = model('Models\CustomFileModel');
        $this->replyModel = model('Models\ReplyModel');
    }

    /**
     * [get] /api/topic/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getTopic($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $result = $this->topicModel->find($id);
            $files = $this->customFileModel->get(['topic_id' => $id]);
            $result['files'] = $files;
            $response['success'] = true;
            $response['data'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/topic/create
     * @return ResponseInterface
     */
    public function createTopic(): ResponseInterface
    {
        $data = $this->request->getPost();
        if (!isset($data['files'])) {
            $data['files'] = [];
        }
        $validationRules = [
            'title' => [
                'label' => 'Title',
                'rules' => 'required|min_length[1]',
            ],
            'user_id' => [
                'label' => 'User',
                'rules' => 'required|min_length[1]',
            ],
        ];

        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                $this->db->transBegin();
                if (isset($data['id'])) unset($data['id']);
                $inserted_row_id = $this->topicModel->insert($data);
                if (!$inserted_row_id) {
                    $response['messages'] = $this->topicModel->errors();
                    throw new \Exception();
                }
                // image priority
                $queries = [];
                foreach ($data['files'] as $index => $file_id) {
                    // 이미지에 topic_id 할당하면서 priority 를 설정 해 준다
                    $queries[] = QueryHelper::getFileAllocation('topic_id', $inserted_row_id, $file_id, $data['identifier'], $index);
                }
                BaseModel::transaction($this->db, $queries);

                // create 일 때는 추가되었으나 사용하지 않는 파일에 대해서만 고려하면 된다
                $conditionQuery = "identifier = '" . $data['identifier'] . "'";
                $this->handleFileDelete($conditionQuery);
                $this->db->transCommit();
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
                $this->db->transRollback();
                if (!isset($response['message'])) {
                    $response['message'] = $e->getMessage();
                }
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/topic/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function updateTopic($id): ResponseInterface
    {
        $data = $this->request->getPost();
        if (!isset($data['files'])) {
            $data['files'] = [];
        }

        $validationRules = [
            'title' => [
                'label' => 'Title',
                'rules' => 'required|min_length[1]',
            ],
        ];

        try {
            $result = $this->topicModel->find($id);
            $this->checkSelfAccess($result['user_id'], true);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return $this->response->setJSON($response);
        }

        return $this->typicallyUpdate($this->topicModel, $id, $data, $validationRules, function ($model, $data) use ($id) {
            $queries = [];
            $selectorQuery = '';
            $prefix = '';
            foreach ($data['files'] as $index => $file_id) {
                // 이미지에 topic_id 할당하면서 priority 설정 해 준다
                $queries[] = QueryHelper::getFileIndexUpdate('topic_id', $id, $file_id, $data['identifier'], $index);
                $selectorQuery .= $prefix . $file_id;
                $prefix = ',';
            }
            BaseModel::transaction($this->db, $queries);

            $conditionQuery = '';
            if (sizeof($data['files']) > 0) {
                $conditionQuery = "topic_id = " . $id . " AND id NOT IN(" . $selectorQuery . ")" .
                    " OR identifier = '" . $data['identifier'] . "'";
            } else {
                // files 가 없는 경우 할당된 모든 이미지를 검색해 삭제해 주면 됨
                $conditionQuery = "topic_id = " . $id .
                    " OR identifier = '" . $data['identifier'] . "'";
            }
            $this->handleFileDelete($conditionQuery);
        });
    }

    /**
     * [delete] /api/topic/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteTopic($id): ResponseInterface
    {
        try {
            $result = $this->topicModel->find($id);
            $this->checkSelfAccess($result['user_id'], true);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return $this->response->setJSON($response);
        }

        return $this->typicallyUpdate($this->topicModel, $id, [
            'is_deleted' => 1
        ]);
    }

    /**
     * [get] /api/topic/{topic_id}/get/reply
     * @param $topic_id
     * @return ResponseInterface
     */
    public function getTopicReply($topic_id): ResponseInterface
    {
        $queryParams = $this->request->getGet();
        $page = $queryParams['page'];
        if ($queryParams['page'] != 'last') {
            $page = Utils::toInt($queryParams['page']);
        }

        try {
            $result = $this->replyModel->getPaginated([
                'per_page' => 5,
                'page' => $page,
            ], [
                'topic_id' => $topic_id,
                'is_deleted' => 0,
                'depth' => 0,
            ]);
            $response['success'] = true;
            $response['data'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/topic/{topic_id}/create/reply
     * @param $topic_id
     * @return ResponseInterface
     */
    public function createReply($topic_id): ResponseInterface
    {
        $data = $this->request->getPost();
        $data['topic_id'] = $topic_id;
        $validationRules = [
            'content' => [
                'label' => 'Content',
                'rules' => 'required|min_length[1]',
            ],
            'user_id' => [
                'label' => 'User',
                'rules' => 'required|min_length[1]',
            ],
        ];
        return $this->typicallyCreate($this->replyModel, $data, $validationRules);
    }

    /**
     * [post] /api/topic/reply/{reply_id}/create/nested-reply
     * @param $reply_id
     * @return ResponseInterface
     */
    public function createNestedReply($reply_id): ResponseInterface
    {
        $data = $this->request->getPost();
        $data['reply_id'] = $reply_id;

        try {
            $parent = $this->replyModel->find($reply_id);
            if (!$parent) throw new Exception('not exist');
            $data['depth'] = $parent['depth'] + 1;
            $data['topic_id'] = $parent['topic_id'];
        } catch (Exception $e) {
            //todo(log)
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return $this->response->setJSON($response);
        }

        $validationRules = [
            'content' => [
                'label' => 'Content',
                'rules' => 'required|min_length[1]',
            ],
            'user_id' => [
                'label' => 'User',
                'rules' => 'required|min_length[1]',
            ],
        ];
        return $this->typicallyCreate($this->replyModel, $data, $validationRules);
    }

    /**
     * [get] /api/topic/reply/get/{reply_id}
     * admin-reply
     * @param $reply_id
     * @return ResponseInterface
     */
    public function getReply($reply_id): ResponseInterface
    {
        return $this->typicallyGet($this->replyModel, $reply_id);
    }

    /**
     * [get] /api/topic/reply/{reply_id}/get/nested-reply
     * @param $reply_id
     * @return ResponseInterface
     */
    public function getNestedReply($reply_id): ResponseInterface
    {
        $queryParams = $this->request->getGet();
        $page = $queryParams['page'];
        if ($queryParams['page'] != 'last') {
            $page = Utils::toInt($queryParams['page']);
        }

        try {
            $result = $this->replyModel->getPaginated([
                'per_page' => 5,
                'page' => $page,
            ], [
                'reply_id' => $reply_id,
                'is_deleted' => 0,
            ]);
            $response['success'] = true;
            $response['data'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [delete] /api/topic/reply/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteReply($id): ResponseInterface
    {
        try {
            $result = $this->replyModel->find($id);
            $this->checkSelfAccess($result['user_id'], true);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return $this->response->setJSON($response);
        }
        return $this->typicallyUpdate($this->replyModel, $id, [
            'is_deleted' => 1
        ]);
    }
}
