<?php

namespace Views;

use Exception;
use Models\ArtistModel;
use Models\CodeArtistModel;

class ArtistController extends BaseClientController
{
    protected ArtistModel $artistModel;
    protected CodeArtistModel $codeArtistModel;

    public function __construct()
    {
        parent::__construct();
        $this->artistModel = model('Models\ArtistModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
    }

    /**
     * /artist/{code}
     * @param $code
     * @return string
     */
    public function index($code): string
    {
        $data = $this->getViewData();
        try {
            $codeArtist = $this->codeArtistModel->findByCode($code);
            $data['code'] = $codeArtist;
            $data['array'] = $this->artistModel->get(['code_artist_id' => $codeArtist['id']]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/client/artist'
                ],
                'js' => [],
            ])
            . view('/client/artist', $data)
            . parent::loadFooter();
    }
}
