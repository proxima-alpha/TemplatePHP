<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\ArtistModel;
use Models\CustomFileModel;

class ArtistController extends BaseAdminController
{
    protected ArtistModel $artistModel;
    protected CustomFileModel $customFileModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->artistModel = model('Models\ArtistModel');
        $this->customFileModel = model('Models\CustomFileModel');
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
            $result = $this->artistModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'is_deleted' => 0
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/artist',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/artist/table',
                ],
                'js' => [
                ],
            ])
            . view('/admin/artist/table', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/{id}
     * @param $id
     * @return string
     */
    public function getArtist($id): string
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
                    '/common/uploader',
                    '/common/uploader_slider_box',
                    '/common/input',
                    '/common/tab_box',
                    '/admin/artist/common',
                    '/admin/artist/view',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/common/tab_box',
                    '/common/delete',
                    '/common/artist',
                ],
            ])
            . view('/admin/artist/view', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/{id}/edit
     * @param $id
     * @return string
     */
    public function editArtist($id = 1): string
    {
        $data = $this->getViewData();
        try {
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
                    '/common/tab_box',
                    '/admin/artist/common',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/tab_box',
                    '/common/artist',
                ],
            ])
            . view('/admin/artist/input', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/artist/create
     * @return string
     */
    public function createArtist(): string
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
                    '/common/tab_box',
                    '/admin/artist/common',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/tab_box',
                    '/common/artist',
                ],
            ])
            . view('/admin/artist/input', $data)
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
        $previews = $this->customFileModel->get(['artist_id' => $id, 'target' => 'artist_preview']);
        $artist['previews'] = $previews;
        $result['data'] = $artist;
        return $result;
    }
}
