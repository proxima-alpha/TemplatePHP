<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Models\SettingModel;

class SettingController extends BaseApiController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = model('Models\SettingModel');
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
}
