<?php

namespace API;

use App\Helpers\QueryHelper;
use App\Helpers\Utils;
use CodeIgniter\HTTP\ResponseInterface;
use Crisu83\ShortId\ShortId;
use Exception;
use Models\ArtistGroupModel;
use Models\BaseModel;
use Models\ProjectModel;
use Models\RewardModel;

class ProjectController extends CustomFileController
{
    protected ProjectModel $projectModel;
    protected ArtistGroupModel $artistGroupModel;
    protected RewardModel $rewardModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->projectModel = model('Models\ProjectModel');
        $this->artistGroupModel = model('Models\ArtistGroupModel');
        $this->rewardModel = model('Models\RewardModel');
    }

    /**
     * [get] /api/project
     * @param $target
     * @return ResponseInterface
     */
    public function index($target): ResponseInterface
    {
        $queryParams = $this->request->getGet();
        $page = $queryParams['page'];
        if ($queryParams['page'] != 'last') {
            $page = Utils::toInt($queryParams['page']);
        }
        try {
            $condition = [
                'is_deleted' => 0,
                'status' => 'open',
            ];
            if (isset($target) && $target != 'all') {
                $condition = array_merge(['code_project.code' => $target]);
            }

            $result = $this->projectModel->getPaginated([
                'per_page' => 10,
                'page' => $page,
            ], $condition);
            $response['success'] = true;
            $response['data'] = $result;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [get] /api/project/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function get($id): ResponseInterface
    {
        return $this->typicallyFind($this->projectModel, $id);
    }


    /**
     * [get] /api/project/reward/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getReward($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $result = $this->rewardModel->get(['project_id' => $id, 'is_deleted' => 0]);
            if (!$result) throw new Exception('not exist');
            $response['success'] = true;
            $response['data'] = [
                'array' => $result
            ];
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [get] /api/project/artist/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getArtist($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $result = $this->artistGroupModel->getArtists($id);
            if (!$result) throw new Exception('not exist');
            $response['success'] = true;
            $response['data'] = [
                'array' => $result
            ];
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/project/create
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $validationRules = [
            'artists' => [
                'label' => 'Artist',
                'rules' => 'required',
            ],
            'rewards' => [
                'label' => 'Reward',
                'rules' => 'required',
            ],
            'title' => [
                'label' => 'Title',
                'rules' => 'required|min_length[1]',
            ],
            'content' => [
                'label' => 'Content',
                'rules' => 'required|min_length[1]',
            ],
            'title_en' => [
                'label' => 'Title',
                'rules' => 'required|min_length[1]',
            ],
            'content_en' => [
                'label' => 'Content',
                'rules' => 'required|min_length[1]',
            ],
            'start_date' => [
                'label' => 'Start Date',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                // 날짜 체크
                if (isset($data['end_date'])) {
                    $startTimeRaw = strtotime($data['start_date']);
                    $endTimeRaw = strtotime($data['end_date']);
                    $endDateString = date("Y-m-d", $endTimeRaw);
                    $data['end_date'] = $endDateString . " 23:59:59";
                    $endTimeRaw = strtotime($data['end_date']);
                    if ($startTimeRaw > $endTimeRaw)
                        throw new Exception('End Date should be later than Start Date.');
                }

                if (isset($data['id'])) unset($data['id']);
                if (isset($data['project_image_id'])) {
                    if (sizeof($data['project_image_id']) > 0) {
                        $data['project_image_id'] = $data['project_image_id'][0];
                    } else {
                        $data['project_image_id'] = null;
                    }
                }
                if (isset($data['artists'])) {
                    $data['artists'] = array_unique($data['artists']);
                }

                $this->db->transBegin();
                $inserted_row_id = $this->projectModel->insert($data);
                if (!$inserted_row_id) {
                    $response['messages'] = $this->projectModel->errors();
                    throw new \Exception();
                }
                foreach ($data['rewards'] as $reward) {
                    $reward['project_id'] = $inserted_row_id;
                    $inserted_id = false;
                    if (isset($reward['id'])) {
                        $inserted_id = $this->rewardModel->update($reward['id'], $reward);
                    } else {
                        $inserted_id = $this->rewardModel->insert($reward);
                    }
                    if (!$inserted_id) {
                        $response['messages'] = $this->rewardModel->errors();
                        throw new \Exception();
                    }
                }
                $queries = [];
                $queries[] = QueryHelper::getGroupCreate($data['artists'], $inserted_row_id);
                if (isset($data['project_image_id'])) {
                    $queries[] = "UPDATE custom_file SET identifier = NULL WHERE id = '" . $data['project_image_id'] . "';";
                }
                BaseModel::transaction($this->db, $queries);
                $this->db->transCommit();
                $conditionQuery = "identifier = '" . $data['identifier'] . "'";
                $this->handleFileDelete($conditionQuery);
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
     * [post] /api/project/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];
        $this->checkAdmin();
        $data = $this->request->getPost();
        if (isset($data['id'])) unset($data['id']);
        if (isset($data['project_image_id'])) {
            if (sizeof($data['project_image_id']) > 0) {
                $data['project_image_id'] = $data['project_image_id'][0];
            } else {
                $data['project_image_id'] = null;
            }
        }
        if (isset($data['artists'])) {
            $data['artists'] = array_unique($data['artists']);
        }

        $previousData = $this->projectModel->getLatest(['id' => $id]);
        if (strlen($id) == 0) {
            $response['message'] = "field 'id' should not be empty.";
        } else {
            try {
                // 날짜 체크
                $endDateString = $data['end_date'] ?? $previousData['end_date'];
                $startDateString = $data['start_date'] ?? $previousData['start_date'];
                $startTimeRaw = strtotime($startDateString);
                $endTimeRaw = strtotime($endDateString);
                if ($startTimeRaw > $endTimeRaw)
                    throw new Exception('End Date should be later than Start Date.');

                $this->db->transBegin();
                $inserted_id = $this->projectModel->update($id, $data);
                if (!$inserted_id) {
                    $response['messages'] = $this->projectModel->errors();
                    throw new \Exception();
                }
                $queries = [];
                // reward 제거
                $rewards = $this->rewardModel->get(['project_id' => $id, 'is_deleted' => 0]);
                $selectorQuery = '';
                $prefix = '';
                foreach ($rewards as $reward) {
                    $hasReward = false;
                    foreach ($data['rewards'] as $newReward) {
                        if (isset($newReward['id']) && $reward['id'] == $newReward['id']) {
                            $hasReward = true;
                            break;
                        }
                    }
                    if (!$hasReward) {
                        $selectorQuery .= $prefix . $reward['id'];
                        $prefix = ',';
                    }
                }
                if ($selectorQuery != '') {
                    $queries[] = "UPDATE reward SET is_deleted= 0 WHERE id NOT IN(" . $selectorQuery . ")";
                }
                foreach ($data['rewards'] as $index => $newReward) {
                    $newReward['project_id'] = $id;
                    $newReward['priority'] = $index + 1;
                    $inserted_id = false;
                    if (isset($newReward['id'])) {
                        $inserted_id = $this->rewardModel->update($newReward['id'], $newReward);
                    } else {
                        $inserted_id = $this->rewardModel->insert($newReward);
                    }
                    if (!$inserted_id) {
                        $response['messages'] = $this->rewardModel->errors();
                        throw new \Exception();
                    }
                }

                $queries[] = "DELETE FROM artist_group WHERE project_id = '" . $id . "';";
                $queries[] = QueryHelper::getGroupCreate($data['artists'], $id);
//
                if (isset($data['project_image_id'])) {
                    $queries[] = "UPDATE custom_file SET identifier = NULL WHERE id = '" . $data['project_image_id'] . "';";
                }
                BaseModel::transaction($this->db, $queries);
                $conditionQuery = "identifier = '" . $data['identifier'] . "'";
                if (isset($previousData['project_image_id']) && (
                        !isset($data['project_image_id']) || $previousData['project_image_id'] != $data['project_image_id'])) {
                    $conditionQuery .= " OR id = " . $previousData['project_image_id'];
                }
                $this->handleFileDelete($conditionQuery);
                $this->db->transCommit();
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
//                ServerLogger::log($e);
                $this->db->transRollback();
                if (!isset($response['message'])) {
                    $response['message'] = $e->getMessage();
                }
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/project/regenerate-hash/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function regenerateHash($id): ResponseInterface
    {
        $this->checkAdmin();
        $shortid = ShortId::create();
        $shortid->setLength(10);
        $body = [
            'access_hash' => $shortid->generate(),
        ];
        return $this->typicallyUpdate($this->projectModel, $id, $body);
    }

    /**
     * [delete] /api/project/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteArtist($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->projectModel, $id, $body);
    }

    /**
     * /api/project/post
     * @return ResponseInterface
     */
    public function post(): ResponseInterface
    {
        $data = $this->request->getPost();
        if (!isset($data['projects'])) {
            $data['projects'] = [];
        }
        $response = [
            'success' => false,
        ];

        try {
            $queries = [];
            $selectorQuery = '';
            $prefix = '';
            foreach ($data['projects'] as $index => $project_id) {
                // priority 를 설정 해 준다
                $query = "UPDATE project
                        SET project.is_posted_popular = 1, project.priority = " . ($index + 1) . "
                        WHERE project.id = " . $project_id;
                $queries[] = $query;
                $selectorQuery .= $prefix . $project_id;
                $prefix = ',';
            }
            $conditionQuery = '';
            if ($selectorQuery != '') {
                $conditionQuery = " WHERE project.id NOT IN(" . $selectorQuery . ")";
            }
            $queries[] = "UPDATE project
                        SET project.is_posted_popular = 0 " . $conditionQuery;

            BaseModel::transaction($this->db, $queries);
            $response['success'] = true;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * /api/project/post/{code}
     * @param $code
     * @return ResponseInterface
     */
    public function postByCode($code): ResponseInterface
    {
        $data = $this->request->getPost();
        if (!isset($data['projects'])) {
            $data['projects'] = [];
        }
        $response = [
            'success' => false,
        ];

        try {
            $queries = [];
            $selectorQuery = '';
            $prefix = '';
            foreach ($data['projects'] as $index => $project_id) {
                // priority 를 설정 해 준다
                $query = "UPDATE project
                        LEFT JOIN code_project ON code_project.id = project.code_project_id
                        SET project.is_posted = 1, project.priority = " . ($index + 1) . "
                        WHERE code_project.code = '" . $code . "' AND project.id = " . $project_id;
                $queries[] = $query;
                $selectorQuery .= $prefix . $project_id;
                $prefix = ',';
            }
            $conditionQuery = '';
            if ($selectorQuery != '') {
                $conditionQuery = " AND project.id NOT IN(" . $selectorQuery . ")";
            }
            $queries[] = "UPDATE project
                        LEFT JOIN code_project ON code_project.id = project.code_project_id
                        SET project.is_posted = 0" .
                " WHERE code_project.code = '" . $code . "'" . $conditionQuery;

            BaseModel::transaction($this->db, $queries);
            $response['success'] = true;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }
}
