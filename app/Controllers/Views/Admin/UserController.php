<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\UserModel;

class UserController extends BaseAdminController
{
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->userModel = model('Models\UserModel');
    }

    /**
     * /admin/user/{page}
     * @param $page
     * @return string
     */
    function index($page = 1): string
    {
        $page = Utils::toInt($page);
        $data = $this->getViewData();
        try {
            $result = $this->userModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], ['is_deleted' => 0]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/user',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/user',
                ],
                'js' => [
                    '/admin/popup_input',
                ],
            ])
            . view('/admin/user', $data)
            . parent::loadFooter();
    }
}
