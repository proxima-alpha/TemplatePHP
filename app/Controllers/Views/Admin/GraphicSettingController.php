<?php

namespace Views\Admin;

use Exception;
use Models\ArtistModel;
use Models\CodeProjectModel;
use Models\CustomFileModel;
use Models\ProjectModel;
use Models\SettingModel;

class GraphicSettingController extends BaseAdminController
{
    protected CustomFileModel $customFileModel;
    protected ArtistModel $artistModel;
    protected CodeProjectModel $codeProjectModel;
    protected ProjectModel $projectModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->customFileModel = model('Models\CustomFileModel');
        $this->artistModel = model('Models\ArtistModel');
        $this->codeProjectModel = model('Models\CodeProjectModel');
        $this->projectModel = model('Models\ProjectModel');
        $this->settingModel = model('Models\SettingModel');
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
            $main_images = $this->customFileModel->get(['target' => 'main']);
            $relations = $this->customFileModel->get(['target' => 'relation']);
            $projects = $this->projectModel->get(['is_posted_popular' => 1, 'status' => 'open'], null, true);
            $postedProjects = $this->projectModel->get(['is_posted' => 1], null, true);
            $codes = $this->codeProjectModel->get();
            $settings = $this->settingModel->getMainShowSettings();
            $projectParsed = [];
            foreach ($codes as $index => $code) {
                $projectParsed[$code['code']] = [ 'code' => $code,
                    'array' => []];
            }
            foreach ($postedProjects as $index => $project) {
                $projectParsed[$project['code']]['array'][] = $project;
            }
            $graphic_settings = array_merge($graphic_settings, [
                'main' => $main_images,
                'relation' => $relations,
                'project' => $projects,
                'project_by_code' => $projectParsed,
            ]);
            $data = array_merge($data, [
                'data' => $graphic_settings,
                'data_settings' => $settings,
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
                    '/module/uploader',
                    '/admin/search_artist',
                    '/admin/search_project',
                    '/admin/graphic_setting',
                ],
            ])
            . view('/admin/graphic_setting', $data)
            . parent::loadFooter();
    }
}
