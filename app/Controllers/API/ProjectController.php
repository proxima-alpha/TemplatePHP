<?php

namespace API;

use App\Helpers\QueryHelper;
use CodeIgniter\HTTP\ResponseInterface;
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
     * [get] /api/artist/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getArtist($id): ResponseInterface
    {
        return $this->typicallyFind($this->artistModel, $id);
    }

    /**
     * [post] /api/artist/create
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
                    $this->db->transRollback();
                    $response['messages'] = $this->projectModel->errors();
                } else {
                    foreach ($data['rewards'] as $reward) {
                        $reward['project_id'] = $inserted_row_id;
                        if (isset($reward['id'])) {
                            $this->rewardModel->update($reward['id'], $reward);
                        } else {
                            $this->rewardModel->insert($reward);
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
                }
            } catch (Exception $e) {
                //todo(log)
                $this->db->transRollback();
                $response['message'] = $e->getMessage();
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/artist/update/{id}
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
                $this->db->transBegin();
                $this->projectModel->update($id, $data);
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
                    if (isset($newReward['id'])) {
                        $this->rewardModel->update($newReward['id'], $newReward);
                    } else {
                        $this->rewardModel->insert($newReward);
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
                $response['message'] = $e->getMessage();
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [delete] /api/artist/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteArtist($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->artistModel, $id, $body);
    }
}
