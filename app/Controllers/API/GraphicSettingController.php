<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\ArtistModel;
use Models\CustomFileModel;

class GraphicSettingController extends BaseApiController
{
    protected CustomFileModel $customFileModel;
    protected ArtistModel $artistModel;

    public function __construct()
    {
        $this->customFileModel = model('Models\CustomFileModel');
        $this->artistModel = model('Models\ArtistModel');
    }

    /**
     * [get] /api/graphic-setting/get/all
     * @return ResponseInterface
     */
    public function getGraphicSettings(): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $queryResult = $this->customFileModel->getGraphicSettings();

            foreach ($queryResult as $row) {
                $target = $row['target'];
                switch ($target) {
                    case 'main':
                        if (!isset($data['main_video'])) $data['main_video'] = [];
                        $data['main_video'][] = $row;
                        break;
                    case 'logo':
                        if (!isset($data['logo'])) $data['logo'] = [];
                        $data['logo'][] = $row;
                        break;
                    case 'footer_logo':
                        if (!isset($data['footer_logo'])) $data['footer_logo'] = [];
                        $data['footer_logo'][] = $row;
                        break;
                    case 'favicon':
                        if (!isset($data['favicon'])) $data['favicon'] = [];
                        $data['favicon'][] = $row;
                        break;
                    case 'open_graph':
                        if (!isset($data['open_graph'])) $data['open_graph'] = [];
                        $data['open_graph'][] = $row;
                        break;
                }
            }

            if (!isset($data)) $data = [];

            // priority 때문에 따로조회
            $images = $this->customFileModel->get(['target' => 'main']);
            $relations = $this->customFileModel->get(['target' => 'relation']);
            $artists = $this->artistModel->get(['is_posted' => 1]);
            $data = array_merge($data, [
                'main' => $images,
                'relation' => $relations,
                'artist' => $artists
            ]);
            $response['success'] = true;
            $response['data'] = $data;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }
}
