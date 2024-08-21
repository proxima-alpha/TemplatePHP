<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Models\CodeProjectModel;
use Models\CustomFileModel;
use Models\ProjectModel;
use Models\SettingModel;

class SettingController extends BaseApiController
{
    protected SettingModel $settingModel;
    protected CustomFileModel $customFileModel;
    protected CodeProjectModel $codeProjectModel;
    protected ProjectModel $projectModel;

    public function __construct()
    {
        $this->settingModel = model('Models\SettingModel');
        $this->customFileModel = model('Models\CustomFileModel');
        $this->codeProjectModel = model('Models\CodeProjectModel');
        $this->projectModel = model('Models\ProjectModel');
    }

    /**
     * [get] /api/setting/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getSetting($id): ResponseInterface
    {
        return $this->typicallyFind($this->settingModel, $id);
    }

    /**
     * [post] /api/setting/create
     * @return ResponseInterface
     */
    public function createSetting(): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $validationRules = [
            'code' => [
                'label' => 'Code',
                'rules' => 'required|min_length[1]|regex_match[^[^0-9][a-zA-Z0-9_\-]+$]',
                'errors' => [
                    'regex_match' => '{field} have to start with character'
                ],
            ],
            'name' => [
                'label' => 'Name',
                'rules' => 'required|min_length[1]',
            ],
        ];
        return $this->typicallyCreate($this->settingModel, $data, $validationRules);
    }

    /**
     * [post] /api/setting/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function updateSetting($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        return $this->typicallyUpdate($this->settingModel, $id, $data);
    }

    /**
     * [post] /api/setting/update
     * @return ResponseInterface
     */
    public function updateWithCode(): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $validationRules = [
            'code' => [
                'label' => 'Code',
                'rules' => 'required',
            ],
        ];
        $code = $this->settingModel->getLatest(['code' => $data['code']]);
        return $this->typicallyUpdate($this->settingModel, $code['id'], $data, $validationRules);
    }

    /**
     * [delete] /api/setting/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteSetting($id): ResponseInterface
    {
        $this->checkAdmin();
        return $this->typicallyDelete($this->settingModel, $id);
    }


    /**
     * [get] /api/setting/graphic-setting
     * @return ResponseInterface
     */
    public function getGraphicSettings(): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $data = [];
            // priority 때문에 따로조회
            $main_images = $this->customFileModel->get(['target' => 'main']);
            $mobile_main_images = $this->customFileModel->get(['target' => 'main_mobile']);
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
                'main' => $main_images,
                'main_mobile' => $mobile_main_images,
                'relation' => $relations,
                'project_popular' => $projects
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

    /**
     * [get] /api/setting/guide
     * @return ResponseInterface
     */
    public function getGuide(): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $data = [];
            $guide_ko = $this->settingModel->findByCode(['guide-how-to-use_ko']);
            $guide_en = $this->settingModel->findByCode(['guide-how-to-use_en']);
            $guide_jp = $this->settingModel->findByCode(['guide-how-to-use_jp']);
            $data = array_merge($data, [
                'how_to_use' => [
                    'ko' => $guide_ko['value'] ?? '',
                    'en' => $guide_en['value'] ?? '',
                    'jp' => $guide_jp['value'] ?? '',
                ]
            ]);
            $response['success'] = true;
            $response['data'] = $data;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/setting/guide
     * @return ResponseInterface
     */
    public function setGuide(): ResponseInterface
    {
        $data = $this->request->getPost();
        $response = [
            'success' => false,
        ];

        try {
            if (isset($data['how_to_use'])) {
                if (isset($data['how_to_use']['ko'])) {
                    $this->settingModel->put(['code' => 'guide-how-to-use_ko'], ['value' => $data['how_to_use']['ko']]);
                }
                if (isset($data['how_to_use']['en'])) {
                    $this->settingModel->put(['code' => 'guide-how-to-use_en'], ['value' => $data['how_to_use']['en']]);
                }
                if (isset($data['how_to_use']['jp'])) {
                    $this->settingModel->put(['code' => 'guide-how-to-use_jp'], ['value' => $data['how_to_use']['jp']]);
                }
            }
            $response['success'] = true;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }
}
