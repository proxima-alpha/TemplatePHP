<?php

namespace API;

use App\Helpers\QueryHelper;
use App\Helpers\Utils;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\ArtistModel;
use Models\BaseModel;
use Models\CodeArtistModel;

class ArtistController extends CustomFileController
{
    protected CodeArtistModel $codeArtistModel;
    protected ArtistModel $artistModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->codeArtistModel = model('Models\CodeArtistModel');
        $this->artistModel = model('Models\ArtistModel');
    }

    /**
     * [get] /api/artist
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $queryParams = $this->request->getGet();
        $page = $queryParams['page'];
        if ($queryParams['page'] != 'last') {
            $page = Utils::toInt($queryParams['page']);
        }

        try {
            $result = $this->artistModel->getPaginated([
                'per_page' => 10,
                'page' => $page,
            ], [
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
     * [get] /api/artist/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function get($id): ResponseInterface
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
            'code_artist_id' => [
                'label' => 'Code Artist',
                'rules' => 'required',
            ],
            'name' => [
                'label' => 'Name',
                'rules' => 'required|min_length[1]',
            ],
            'introduction' => [
                'label' => 'Introduction',
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
                if (!isset($data['profile_id']) || sizeof($data['profile_id']) == 0) {
                    $data['profile_id'] = null;
                } else {
                    $data['profile_id'] = $data['profile_id'][0];
                }
                $inserted_row_id = $this->artistModel->insert($data);
                if (!$inserted_row_id) {
                    $this->db->transRollback();
                    $response['messages'] = $this->artistModel->errors();
                } else {
                    // image priority
                    $queries = [];
                    foreach ($data['previews'] as $index => $file_id) {
                        $queries[] = QueryHelper::getFileAllocation('artist_id', $inserted_row_id, $file_id, $data['identifier'], $index);
                    }
                    foreach ($data['profile_id'] as $index => $file_id) {
                        $queries[] = QueryHelper::getFileAllocation('artist_id', $inserted_row_id, $file_id, $data['identifier'], $index);
                    }
                    BaseModel::transaction($this->db, $queries);

                    // create 일 때는 추가되었으나 사용하지 않는 파일에 대해서만 고려하면 된다
                    $conditionQuery = "identifier = '" . $data['identifier'] . "'";
                    $this->handleFileDelete($conditionQuery);
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
    public function update($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        if (isset($data['id'])) unset($data['id']);
        if (!isset($data['profile_id']) || sizeof($data['profile_id']) == 0) {
            $data['profile_id'] = null;
        } else {
            $data['profile_id'] = $data['profile_id'][0];
        }
        if (!isset($data['previews'])) {
            $data['previews'] = [];
        }
        return $this->typicallyUpdate($this->artistModel, $id, $data, null, function ($model, $data) use ($id) {
            $queries = [];
            $selectorQuery = '';
            $prefix = '';
            foreach ($data['previews'] as $index => $file_id) {
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
            if (sizeof($data['previews']) > 0) {
                $conditionQuery = "(artist_id = " . $id . " AND target = 'artist_preview' AND id NOT IN(" . $selectorQuery . "))" .
                    " OR identifier = '" . $data['identifier'] . "'";
            } else {
                // files 가 없는 경우 할당된 모든 이미지를 검색해 삭제해 주면 됨
                $conditionQuery = "(artist_id = " . $id . " AND target = 'artist_preview')" .
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
    public function delete($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->artistModel, $id, $body);
    }
}
