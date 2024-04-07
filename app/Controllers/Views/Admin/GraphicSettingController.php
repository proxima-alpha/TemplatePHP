<?php

namespace Views\Admin;

use Exception;
use Models\CustomFileModel;

class GraphicSettingController extends BaseAdminController
{
    protected CustomFileModel $customFileModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->customFileModel = model('Models\CustomFileModel');
    }

    /**
     * /admin/graphic-setting
     * @return string
     */
    function index(): string
    {
        $data = $this->getViewData();
        try {
            $graphic_settings = [];
            $images = $this->customFileModel->get(['type' => 'image', 'target' => 'main']);
            $graphic_settings = array_merge($graphic_settings, [
                'main' => $images,
            ]);
            $data = array_merge($data, [
                'graphic_settings' => $graphic_settings,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/admin/graphic_setting',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/admin/graphic_setting',
                ],
            ])
            . view('/admin/graphic_setting', $data)
            . parent::loadFooter();
    }
}
