<?php

namespace Views;

use App\Helpers\ServerLogger;
use Exception;
use Models\ArtistModel;
use Models\BoardModel;
use Models\CodeArtistModel;
use Models\CustomFileModel;
use Models\ProjectModel;
use Models\SettingModel;
use Models\TopicModel;

class MainController extends BaseClientController
{

    protected BoardModel $boardModel;
    protected TopicModel $topicModel;
    protected CustomFileModel $customFileModel;
    protected ArtistModel $artistModel;
    protected CodeArtistModel $codeArtistModel;
    protected ProjectModel $projectModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->boardModel = model('Models\BoardModel');
        $this->topicModel = model('Models\TopicModel');
        $this->customFileModel = model('Models\CustomFileModel');
        $this->artistModel = model('Models\ArtistModel');
        $this->codeArtistModel = model('Models\CodeArtistModel');
        $this->projectModel = model('Models\ProjectModel');
        $this->settingModel = model('Models\SettingModel');
    }

    /**
     * /
     * @return string
     */
    public function index()
    {
        $data = $this->getViewData();
        try {
            $graphic_settings = [];
            $main_images = $this->customFileModel->get(['target' => 'main']);
            $relations = $this->customFileModel->get(['target' => 'relation']);
            $projects = $this->projectModel->get(['is_posted' => 1, 'status' => 'open'], null, true);
            $artists = $this->artistModel->get(['is_posted' => 1], null, true);
            $codes = $this->codeArtistModel->get();
            $settings = $this->settingModel->getMainShowSettings();
            $previous_projects = [];
            if (isset($settings['main-show-previous-project']) && $settings['main-show-previous-project'] == 1) {
                $previous_projects = $this->projectModel->getPreviousProjects();
            }
            $artist_parsed = [];
            foreach ($codes as $index => $code) {
                $artist_parsed[$code['code']] = [];
            }
            foreach ($artists as $index => $artist) {
                $artist_parsed[$artist['code']][] = $artist;
            }
            $graphic_settings = array_merge($graphic_settings, [
                'main' => $main_images,
                'relation' => $relations,
                'project' => $projects,
                'artists' => $artist_parsed,
                'previous-project' => $previous_projects,
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
                'css' => ['/client/main'],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/client/main'],
            ])
            . view('/client/main', $data)
            . parent::loadFooter();
    }

    /**
     * /frame-view
     * 이미지를 해당 페이지에 render 해 주는 페이지
     * @return string
     */
    public function getFrameView(): string
    {
        $queryParams = $this->request->getGet();
        $url = $queryParams['url'];
        return parent::loadHeader([
                'css' => [
                    '/client/frame',
                ],
            ])
            . view('/client/frame', [
                'url' => $url,
            ])
            . parent::loadFooter();
    }

    public function getSslChallenge(string $key)
    {
        return $key;
    }

    public function setSession()
    {
        $newdata = [
            'username' => 'johndoe',
            'email' => 'johndoe@some-site.com',
            'logged_in' => true,
        ];

        $this->session->set($newdata);
//        ServerLogger::log($this->session->session_id);
//        session()->remove('username');
        return view('welcome_message');
    }

    public function getSession()
    {
        $name = $this->session->get('username');
        ServerLogger::log($name);
        ServerLogger::log($this->session->has('username'));
        return view('welcome_message');
    }
}
