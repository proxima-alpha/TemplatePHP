<?php

namespace Views;

use Models\SettingModel;

class LoginController extends BaseClientController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = model('Models\SettingModel');
    }

    /**
     * /login
     * @return string
     */
    function index(): string
    {
        if ($this->session->is_login) {
            return view('/redirect', [
                'path' => '/'
            ]);
        }
        $data = $this->getViewData();
        try {
            $kakaoAppKey = $this->settingModel->getInitialValue(['code' => 'kakao-appkey'], 'value');
            $data = array_merge($data, [
                'kakaoAppKey' => $kakaoAppKey,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/input',
                    '/client/login',
                ],
            ], [
                'is_login_page' => true,
            ])
            . view('/client/login', $data)
            . parent::loadFooter();
    }
}
