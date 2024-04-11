<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\ArtistModel;
use Models\CodeArtistModel;
use Models\CustomFileModel;
use Models\ProjectModel;

class GraphicSettingController extends BaseApiController
{
    protected CustomFileModel $customFileModel;
    protected ArtistModel $artistModel;
    protected CodeArtistModel $codeArtistModel;
    protected ProjectModel $projectModel;

    public function __construct()
    {
        $this->customFileModel = model('Models\CustomFileModel');
        $this->artistModel = model('Models\ArtistModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
        $this->projectModel = model('Models\ProjectModel');
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
            $projects = $this->projectModel->get(['is_posted' => 1], null, true);
            $artists = $this->artistModel->get(['is_posted' => 1], null, true);
            $codes = $this->codeArtistModel->get();
            $artist_parsed = [];
            foreach ($codes as $index => $code) {
                $artist_parsed[$code['code']] = [];
            }
            foreach ($artists as $index => $artist) {
                $artist_parsed[$artist['code']][] = $artist;
            }
            $data = array_merge($data, [
                'main' => $images,
                'relation' => $relations,
                'project' => $projects
            ]);
            $data = array_merge($data, $artist_parsed);
            $response['success'] = true;
            $response['data'] = $data;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }
}
