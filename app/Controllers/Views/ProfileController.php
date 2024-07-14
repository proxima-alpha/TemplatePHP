<?php

namespace Views;

use Exception;
use Models\SettingModel;
use Models\UserModel;

class ProfileController extends BaseClientController
{
    protected UserModel $userModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = model('Models\UserModel');
        $this->settingModel = model('Models\SettingModel');
    }

    /**
     * /profile
     * @return string
     */
    public function index(): string
    {
        if (!$this->session->is_login) {
            return view('/redirect', [
                'path' => '/'
            ]);
        }
        $queryParams = $this->request->getGet();
        $data = $this->getViewData();
        if (isset($queryParams['sub'])) {
            $data['sub'] = $queryParams['sub'];
        }
        $queryParams = $this->request->getGet();
        $data = array_merge($data, $queryParams);
        try {
            $data = array_merge($data, $this->userModel->find($this->session->user_id));
            if (!$data) throw new Exception('not exist');
            $kakaoAppKey = $this->settingModel->getInitialValue(['code' => 'kakao-appkey'], 'value');
            $naverClientId = $this->settingModel->getInitialValue(['code' => 'naver-client-id'], 'value');
            $googleClientId = $this->settingModel->getInitialValue(['code' => 'google-client-id'], 'value');
            $data = array_merge($data, [
                'kakaoAppKey' => $kakaoAppKey,
                'naverClientId' => $naverClientId,
                'googleClientId' => $googleClientId,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/input',
                    '/client/form',
                    '/client/profile',
                ],
                'js' => [
                    '/client/profile',
                ],
            ])
            . view('/client/profile', $data)
            . parent::loadFooter();
    }
}
