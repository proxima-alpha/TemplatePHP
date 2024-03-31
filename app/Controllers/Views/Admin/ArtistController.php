<?php

namespace Views\Admin;

use App\Helpers\Utils;
use Exception;
use Models\ArtistModel;
use Models\CodeArtistModel;

class ArtistController extends BaseAdminController
{
    protected CodeArtistModel $codeArtistModel;
    protected ArtistModel $artistModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->artistModel = model('Models\ArtistModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
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
     * /admin/artist/create
     * @return string
     */
    public function createArtist(): string
    {
        $data = $this->getViewData();
        try {
            $codes = $this->codeArtistModel->get();
            $data['code_artists'] = $codes;
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
                    '/admin/artist/artist',
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/module/draggable',
                    '/module/image_uploader',
                    '/common/artist',
                ],
            ])
            . view('/admin/artist/input', $data)
            . parent::loadFooter();
    }
}
