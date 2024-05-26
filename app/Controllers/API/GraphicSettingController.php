<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\CodeProjectModel;
use Models\CustomFileModel;
use Models\ProjectModel;

class GraphicSettingController extends BaseApiController
{
    protected CustomFileModel $customFileModel;
    protected CodeProjectModel $codeProjectModel;
    protected ProjectModel $projectModel;

    public function __construct()
    {
        $this->customFileModel = model('Models\CustomFileModel');
        $this->codeProjectModel = model('Models\CodeProjectModel');
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
            $projects = $this->projectModel->get(['is_posted_popular' => 1, 'status' => 'open'], null, true);
            $postedProjects = $this->projectModel->get(['is_posted' => 1], null, true);
            $codes = $this->codeProjectModel->get();
            $projectParsed = [];
            foreach ($codes as $index => $code) {
                $projectParsed[$code['code']] = [];
            }
            foreach ($postedProjects as $index => $project) {
                $projectParsed[$project['code']][] = $project;
            }
            $data = array_merge($data, [
                'main' => $images,
                'relation' => $relations,
                'project' => $projects
            ]);
            $data = array_merge($data, $projectParsed);
            $response['success'] = true;
            $response['data'] = $data;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }
}
