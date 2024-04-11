<?php

namespace Views\Admin;

use Exception;
use Models\ArtistModel;
use Models\CodeArtistModel;
use Models\CustomFileModel;
use Models\ProjectModel;

class GraphicSettingController extends BaseAdminController
{
    protected CustomFileModel $customFileModel;
    protected ArtistModel $artistModel;
    protected CodeArtistModel $codeArtistModel;
    protected ProjectModel $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->customFileModel = model('Models\CustomFileModel');
        $this->artistModel = model('Models\ArtistModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
        $this->projectModel = model('Models\ProjectModel');
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
            $images = $this->customFileModel->get(['target' => 'main']);
            $relations = $this->customFileModel->get(['target' => 'relation']);
            $projects = $this->projectModel->get(['is_posted' => 1], null, true);
            $artists = $this->artistModel->get(['is_posted' => 1], null, true);
            $codes = $this->codeArtistModel->get();
            $artist_parsed = [];
            foreach ($codes as $index => $code) {
                $artist_parsed[$code['code']] = [];
            }
            foreach ($artists as $index => $artist) {
                $artist_parsed[$artist['code']][] = $artist;
            }
            $graphic_settings = array_merge($graphic_settings, [
                'main' => $images,
                'relation' => $relations,
                'project' => $projects,
                'artists' => $artist_parsed,
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
                    '/admin/search_artist',
                    '/admin/search_project',
                    '/admin/graphic_setting',
                ],
            ])
            . view('/admin/graphic_setting', $data)
            . parent::loadFooter();
    }
}
