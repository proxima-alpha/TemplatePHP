<?php

namespace Views;

use App\Helpers\ServerLogger;
use Exception;
use Models\BoardModel;
use Models\CustomFileModel;
use Models\TopicModel;

class MainController extends BaseClientController
{

    protected BoardModel $boardModel;
    protected TopicModel $topicModel;
    protected CustomFileModel $customFileModel;

    public function __construct()
    {
        parent::__construct();
        $this->boardModel = model('Models\BoardModel');
        $this->topicModel = model('Models\TopicModel');
        $this->customFileModel = model('Models\CustomFileModel');
    }

    /**
     * /
     * @return string
     */
    public function index()
    {
        $data = $this->getViewData();
        try {
            $data['logos'] = $this->customFileModel->getLogos();
            // for pagination

            // load table view
            $data['topics_table'] = $this->getTopics('notice');
            // load grid view
            $data['topics_grid'] = $this->getTopics('item');

            $images = $this->customFileModel->get(['type' => 'image', 'target' => 'main']);
            $data = array_merge($data, [
                'slider_images' => $images,
            ]);
            $videos = $this->customFileModel->get(['type' => 'video', 'target' => 'main']);
            $data = array_merge($data, [
                'videos' => $videos,
            ]);
            $data = array_merge($data, [
                'company_info' => [
                    'name' => '(주)유닉코퍼레이션',
                    'ceo_name' => '손윤정',
                    'cs_center' => '010-5692-2500 (평일 10:00 ~ 19:00)',
                    'company_number' => '555-81-02851',
                    'certification_number' => '제2024-서울서초-0338호',
                    'address' => '서울 서초구 사임당로8길 13, 4층 402호 N91호',
                    'email' => 'contact@unyk.kr',
                    'manager_name' => '이새글',
                ],
            ]);
            $data = array_merge($data, [
                'terms' => [
                    '(주)유닉코퍼레이션은 통신판매중개자로서 통신판매의 당사자가 아니며 상품, 상품정보, 거래에 관한 의무와 책임은 아티스트에게 있습니다.',
                    '(주)유닉코퍼레이션 사이트의 상품/아티스트/중개 서비스/거래 정보, 콘텐츠, UI 등에 대한 무단 복제, 전송, 배포, 스크래핑 등의 행위는 저작권법, 콘텐츠산업 진흥법 등 관련법령에 의하여 엄격히 금지됩니다.'
                ],
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        $data = array_merge($data, [
            "links" => $this->links,
        ]);
        $data = array_merge($data, $this->getViewData());
        return view('/client/main', $data);
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

    public function getSslChallenge(string $key){
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

    /**
     * 메인에 표시해 줄 topic 을 pagination 정보와 함께 조회하는 기능
     * @param $code
     * @return array|null
     */
    private function getTopics($code): ?array
    {
        $board = $this->boardModel->findByCode($code);
        if ($board) {
            $result = $this->topicModel->getPaginated([
                'per_page' => 8,
                'page' => 1,
            ], [
                'board_id' => $board['id'],
                'is_deleted' => 0,
            ]);
            $result['board'] = $board;
            return array_merge($result, [
                'link' => '/board/' . $code,
            ]);
        }
        return null;
    }
}
