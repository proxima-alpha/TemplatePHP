<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\CodeProjectModel;
use Models\CodeRewardRequestModel;
use Models\SettingModel;

class CodeController extends BaseApiController
{
    protected CodeProjectModel $codeProjectModel;
    protected CodeRewardRequestModel $codeRewardRequestModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->codeProjectModel = model('Models\CodeProjectModel');
        $this->codeRewardRequestModel = model('Models\CodeRewardRequestModel');
        $this->settingModel = model('Models\SettingModel');
    }

    /**
     * [get] /api/code/project/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getCodeProject($id): ResponseInterface
    {
        return $this->typicallyFind($this->codeProjectModel, $id);
    }

    /**
     * [post] /api/code/project/create
     * @return ResponseInterface
     */
    public function createCodeProject(): ResponseInterface
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
        return $this->typicallyCreate($this->codeProjectModel, $data, $validationRules, function ($model, $data) {
            $this->settingModel->insert([
                "code" => "main-show-" . $data['code'],
                "type" => "tinyint",
                "value" => "1",
                "is_editable" => "0",
                "name" => "메인 활성화",
            ]);
        });
    }

    /**
     * [post] /api/code/project/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function updateCodeProject($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $previousCode = $this->codeProjectModel->getLatest(['id' => $id]);
        return $this->typicallyUpdate($this->codeProjectModel, $id, $data, null, function ($model, $data) use ($previousCode) {
            if (isset($data['code']) && $previousCode['code'] != $data['code']) {
                $settings = $this->settingModel->get([
                    "code" => "main-show-" . $previousCode['code']
                ]);
                if (sizeof($settings) > 0) {
                    $this->settingModel->update($settings[0]['id'], [
                        'code' => "main-show-" . $data['code']
                    ]);
                }
            }
        });
    }

    /**
     * [delete] /api/code/project/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteCodeProject($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        $previousCode = $this->codeProjectModel->getLatest(['id' => $id]);
        return $this->typicallyUpdate($this->codeProjectModel, $id, $body, null, function ($model, $data) use ($previousCode) {
            if (isset($data['code']) && $previousCode['code'] != $data['code']) {
                $settings = $this->settingModel->get([
                    "code" => "main-show-" . $previousCode['code']
                ]);
                if (sizeof($settings) > 0) {
                    $this->settingModel->delete($settings[0]['id']);
                }
            }
        });
    }

    /**
     * [get] /api/code/project/exchange-priority/{from}/{to}
     * @param $from
     * @param $to
     * @return ResponseInterface
     */
    public function exchangeCodeProjectPriority($from, $to): ResponseInterface
    {
        $this->checkAdmin();
        $response = [
            'success' => false,
        ];

        try {
            $this->codeProjectModel->exchangePriority($from, $to);
            $response['success'] = true;
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [get] /api/code/reward-request/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function getCodeRewardRequest($id): ResponseInterface
    {
        return $this->typicallyFind($this->codeRewardRequestModel, $id);
    }

    /**
     * [post] /api/code/reward-request/create
     * @return ResponseInterface
     */
    public function createCodeRewardRequest(): ResponseInterface
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
        return $this->typicallyCreate($this->codeRewardRequestModel, $data, $validationRules);
    }

    /**
     * [post] /api/code/reward-request/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function updateCodeRewardRequest($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        return $this->typicallyUpdate($this->codeRewardRequestModel, $id, $data);
    }

    /**
     * [delete] /api/code/reward-request/delete/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function deleteCodeRewardRequest($id): ResponseInterface
    {
        $this->checkAdmin();
        $body = [
            'is_deleted' => 1,
        ];
        return $this->typicallyUpdate($this->codeRewardRequestModel, $id, $body);
    }
}
