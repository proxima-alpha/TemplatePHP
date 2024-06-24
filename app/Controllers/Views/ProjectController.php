<?php

namespace Views;

use App\Helpers\Utils;
use Exception;
use Models\ArtistGroupModel;
use Models\CodeRewardRequestModel;
use Models\ProjectModel;
use Models\RewardModel;

class ProjectController extends BaseClientController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected ArtistGroupModel $artistGroupModel;
    protected CodeRewardRequestModel $codeRewardRequestModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->artistGroupModel = model('Models\ArtistGroupModel');
        $this->codeRewardRequestModel = model('Models\CodeRewardRequestModel');
    }

    /**
     * /project/{id}/view
     * @param $id
     * @return string
     */
    public function get($id): string
    {
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getProjectData($id));
            if ($data['data']['status'] != 'open') {
                return parent::loadHeader([
                        'css' => ['/client/project/complete'],
                        'js' => [],
                    ])
                    . view('/client/project/blocked', $data)
                    . parent::loadFooter();
            }
            $guide = $this->settingModel->findByCode(['guide-how-to-use-' . $data['lang']]);
            $data = array_merge($data, [
                'guide' => $guide['value'] ?? '',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/library/quill',
                    '/common/uploader',
                    '/common/row_uploader_item',
                    '/common/input',
                    '/client/project/view'
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/client/project_reward',
                    '/client/project',
                ],
            ])
            . view('/client/project/view', $data)
            . parent::loadFooter();
    }

    /**
     * /project/category/{code}
     * @param $code
     * @return string
     */
    public function getCategory($code): string
    {
        $data = $this->getViewData();
        try {
            $code = $this->codeProjectModel->findByCode($code);
            $data['code'] = $code;
            $data['array'] = $this->projectModel->get(['code_project_id' => $code['id'], 'is_deleted' => 0]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/client/project/category'
                ],
                'js' => [],
            ])
            . view('/client/project/category', $data)
            . parent::loadFooter();
    }

    /**
     * /project/{id}/reward
     * @param $reward_id
     * @return string
     */
    public function getPurchase($id): string
    {
        $this->checkLogout();
        $data = $this->getViewData();
        try {
            // TODO change to load all rewards from project
            $data = array_merge($data, $this->getProjectData($id));

            $imp_shop_id = $this->settingModel->getInitialValue(['code' => 'imp-shop-id'], 'value');
            $data = array_merge($data, [
                'imp_shop_id' => $imp_shop_id
            ]);

            $reward_requests = $this->codeRewardRequestModel->get(['is_deleted' => 0, 'is_active' => 1]);
            $data = array_merge($data, [
                'reward_requests' => $reward_requests
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/common/input',
                    '/client/project/purchase'
                ],
                'js' => [
                    '/client/project_reward',
                    '/client/project_purchase',
                ],
            ])
            . view('/client/project/purchase', $data)
            . parent::loadFooter();
    }

    /**
     * /project/purchase/complete
     * @return string
     */
    public function getComplete(): string
    {
        $this->checkLogout();
        $data = $this->getViewData();
        return parent::loadHeader([
                'css' => [
                    '/client/project/complete'
                ],
                'js' => [],
            ])
            . view('/client/project/complete', $data)
            . parent::loadFooter();
    }

    /**
     * project 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getProjectData($id): array
    {
        $result = [];
        $projects = $this->projectModel->get(['id' => $id, 'is_deleted' => 0]);
        if (sizeof($projects) != 1) throw new Exception('deleted');
        $project = $projects[0];
        $artists = $this->artistGroupModel->getArtists($id);
        $rewards = $this->rewardModel->get(['project_id' => $id, 'is_deleted' => 0]);
        $rewardArtistResult = $this->artistGroupModel->getArtistsForReward($id);
        $rewardArtists = [];
        foreach ($rewardArtistResult as $artist) {
            $reward_id = $artist['reward_id'] ?? null;
            if (isset($reward_id)) {
                if (!isset($rewardArtists[$reward_id])) $rewardArtists[$reward_id] = [];
                $rewardArtists[$reward_id][] = $artist;
            }
        }
        foreach ($rewards as $index => $reward) {
            if ($reward['type'] == 'random') {
                $rewards[$index]['artists'] = $rewardArtists[$reward['id']] ?? [];
            }
            if (isset($this->session->user_id)) {
                $rewards[$index]['paid_count'] = $this->rewardModel->getPaidCount($reward['id'], $this->session->user_id);
            } else {
                $rewards[$index]['paid_count'] = 0;
            }
            $rewards[$index]['available_count'] = Utils::calculateAvailableReward($rewards[$index]);
        }

        $project['artists'] = $artists;
        $project['rewards'] = $rewards;
        $result['data'] = $project;
        return $result;
    }
}
