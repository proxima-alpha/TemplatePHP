<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\ProjectModel;

class ProjectController extends BaseAdminController
{
    protected ProjectModel $projectModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->projectModel = model('Models\ProjectModel');
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
            $result = $this->projectModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'is_deleted' => 0
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/project',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/project/table',
                ],
                'js' => [
                ],
            ])
            . view('/admin/project/table', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/{id}
     * @param $id
     * @return string
     */
    public function get($id): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getArtistData($id));
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/common/uploader_slider_box',
                    '/common/input',
                    '/admin/project/common',
                    '/admin/project/view',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/common/delete',
                    '/common/artist',
                ],
            ])
            . view('/admin/project/view', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/{id}/edit
     * @param $id
     * @return string
     */
    public function edit($id = 1): string
    {
        $data = $this->getViewData();
        try {
            $codes = $this->codeArtistModel->get();
            $data['code_artists'] = $codes;
            $data = array_merge($data, $this->getArtistData($id));
            $data = array_merge($data, [
                'type' => 'edit'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/common/input',
                    '/admin/project/common',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/artist',
                ],
            ])
            . view('/admin/project/input', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/create
     * @return string
     */
    public function create(): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, [
                'type' => 'create'
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/common/input',
                    '/admin/project/common',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/calendar',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/artist',
                    '/admin/project_input',
                ],
            ])
            . view('/admin/project/input', $data)
            . parent::loadFooter();
    }

    /**
     * artist 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getArtistData($id): array
    {
        $result = [];
        $artists = $this->artistModel->get(['id' => $id, 'is_deleted' => 0]);
        if (sizeof($artists) != 1) throw new Exception('deleted');
        $artist = $artists[0];
        $files = $this->customFileModel->get(['artist_id' => $id, 'target' => 'artist_preview']);
        $artist['files'] = $files;
        $result['data'] = $artist;
        return $result;
    }
}
