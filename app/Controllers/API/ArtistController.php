<?php

namespace API;

use App\Helpers\QueryHelper;
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
    public function createArtist(): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        if (!isset($data['files'])) {
            $data['files'] = [];
        }
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
                $inserted_row_id = $this->artistModel->insert($data);
                if (!$inserted_row_id) {
                    $response['messages'] = $this->artistModel->errors();
                } else {
                    // image priority
                    $queries = [];
                    foreach ($data['files'] as $index => $file_id) {
                        $queries[] = QueryHelper::getFileAllocation($file_id, $data['identifier'], 'artist_id', $inserted_row_id, $index);
                    }
                    foreach ($data['profile_id'] as $index => $file_id) {
                        $queries[] = QueryHelper::getFileAllocation($file_id, $data['identifier'], 'artist_id', $inserted_row_id, $index);
                    }
                    BaseModel::transaction($this->db, $queries);

                    // create 일 때는 추가되었으나 사용하지 않는 파일에 대해서만 고려하면 된다
                    $conditionQuery = "identifier = '" . $data['identifier'] . "'";
                    $this->handleFileDelete($conditionQuery);
                    $response['success'] = true;
                }
            } catch (Exception $e) {
                //todo(log)
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
                $queries[] = QueryHelper::getFileIndexUpdate('artist_id', $id, $file_id, $data['identifier'], $index);
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
