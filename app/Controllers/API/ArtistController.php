<?php

namespace API;

use App\Helpers\QueryHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\ArtistModel;
use Models\BaseModel;
use Models\CodeArtistModel;

class ArtistController extends BaseApiController
{
    protected CodeArtistModel $codeArtistModel;
    protected ArtistModel $artistModel;

    public function __construct()
    {
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
        return $this->typicallyUpdate($this->artistModel, $id, $data);
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
