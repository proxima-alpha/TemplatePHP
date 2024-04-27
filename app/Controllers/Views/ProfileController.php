<?php

namespace Views;

use Exception;
use Models\UserModel;

class ProfileController extends BaseClientController
{
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = model('Models\UserModel');
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
        try {
            $data = array_merge($data, $this->userModel->find($this->session->user_id));
            if (!$data) throw new Exception('not exist');
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/input',
                    '/client/form',
                ],
                'js' => [
                    '/client/profile',
                ],
            ])
            . view('/client/profile', $data)
            . parent::loadFooter();
    }
}
