<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\CodeArtistModel;
use Models\CodeRewardRequestModel;
use Models\SettingModel;

class SettingController extends BaseAdminController
{
    protected SettingModel $settingModel;
    protected CodeRewardRequestModel $codeRewardRequestModel;
    protected CodeArtistModel $codeArtistModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->settingModel = model('Models\SettingModel');
        $this->codeRewardRequestModel = model('Models\CodeRewardRequestModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
    }

    /**
     * /admin/setting/{page}
     * @param $page
     * @return string
     */
    function index(): string
    {
        $queryParams = $this->request->getGet();
        $per_page = 5;
        if (!isset($queryParams['setting-page'])) {
            $queryParams['setting-page'] = 1;
        }
        if (!isset($queryParams['code-reward-request-page'])) {
            $queryParams['code-reward-request-page'] = 1;
        }
        $setting_page = Utils::toInt($queryParams['setting-page']);
        $code_reward_request_page = Utils::toInt($queryParams['code-reward-request-page']);
        $setting_data = $this->getViewData();
        try {
            $result = $this->settingModel->getPaginated([
                'per_page' => $per_page,
                'page' => $setting_page,
            ], [
                'is_editable' => 1
            ]);
            $setting_data = array_merge($setting_data, $result);
            $setting_data = array_merge($setting_data, [
                'pagination_link' => '/admin/setting',
                'pagination_key' => 'setting-page',
                'query_params' => $queryParams
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        // get code request data
        $code_request_data = $this->getViewData();
        try {
            $result = $this->codeRewardRequestModel->getPaginated([
                'per_page' => $per_page,
                'page' => $code_reward_request_page,
            ], ['is_deleted' => 0]);
            $code_request_data = array_merge($code_request_data, $result);
            $code_request_data = array_merge($code_request_data, [
                'pagination_link' => '/admin/setting',
                'pagination_key' => 'code-reward-request-page',
                'query_params' => $queryParams
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        $data = $this->getViewData();
        try {
            $result = $this->codeArtistModel->get();
            $data = array_merge($data, [
                'array' => $result,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/common/table_combination',
                    '/admin/setting',
                ],
                'js' => [
                    '/admin/popup_input',
                    '/module/draggable',
                ],
            ])
            . view('/elements/title', [
                'title' => lang('Service.setting')
            ])
            . view('/admin/setting', $setting_data)
            . view('/admin/code_reward_request', $code_request_data)
            . view('/admin/code_artist', $data)
            . parent::loadFooter();
    }
}
