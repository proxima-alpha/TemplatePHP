<?php

namespace API;

use App\Helpers\QueryHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BaseModel;
use Models\ProjectModel;
use Models\RewardModel;

class ProjectController extends CustomFileController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->projectModel = model('Models\ProjectModel');
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
            'artist_id' => [
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


                if (isset($data['project_image_id'])) {
                    if (sizeof($data['project_image_id']) > 0) {
                        $data['project_image_id'] = $data['project_image_id'][0];
                    } else {
                        $data['project_image_id'] = null;
                    }
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
                    $queries[] = QueryHelper::getGroupCreate($data['artist_id'], $inserted_row_id);
                    if (isset($data['project_image_id'])) {
                        $queries[] = "UPDATE custom_file SET identifier = NULL WHERE id = '" . $data['project_image_id'] . "'";
                    }
                    BaseModel::transaction($this->db, $queries);
                    $this->db->transCommit();
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
    public function updateArtist($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        if (!isset($data['profile_id']) || sizeof($data['profile_id']) == 0) {
            $data['profile_id'] = null;
        } else {
            $data['profile_id'] = $data['profile_id'][0];
        }
        return $this->typicallyUpdate($this->artistModel, $id, $data, null, function ($model, $data) use ($id) {
            $queries = [];
            $selectorQuery = '';
            $prefix = '';
            foreach ($data['files'] as $index => $file_id) {
                // 이미지에 artist_id 할당하면서 priority 설정 해 준다
                $queries[] = QueryHelper::getFileIndexUpdate('artist_id', $id, $file_id, $data['identifier'], $index);
                $selectorQuery .= $prefix . $file_id;
                $prefix = ',';
            }
            if (isset($data['profile_id'])) {
                $file_id = $data['profile_id'];
                // 이미지에 artist_id 할당하면서 priority 설정 해 준다
                $queries[] = QueryHelper::getFileIndexUpdate('artist_id', $id, $file_id, $data['identifier'], 0);
                $selectorQuery .= $prefix . $file_id;
                $prefix = ',';
            }
            BaseModel::transaction($this->db, $queries);

            $conditionQuery = "";
            if (sizeof($data['files']) > 0) {
                $conditionQuery = "(artist_id = " . $id . " AND target = 'artist_preview' AND id NOT IN(" . $selectorQuery . "))" .
                    " OR identifier = '" . $data['identifier'] . "'";
            } else {
                // files 가 없는 경우 할당된 모든 이미지를 검색해 삭제해 주면 됨
                $conditionQuery = "(artist_id = " . $id . "AND target = 'artist_preview')" .
                    " OR identifier = '" . $data['identifier'] . "'";
            }
            if (isset($data['profile_id'])) {
                $conditionQuery .= " OR (artist_id = " . $id . " AND target = 'artist_profile' AND id NOT IN(" . $selectorQuery . "))";
            } else {
                $conditionQuery .= " OR (artist_id = " . $id . " AND target = 'artist_profile')";
            }
            $this->handleFileDelete($conditionQuery);
        });
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
