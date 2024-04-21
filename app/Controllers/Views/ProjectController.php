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
            $guide = [
                [
                    'title' => lang('Client.guide_01_title'),
                    'content' => lang('Client.guide_01_content'),
                ],
                [
                    'title' => lang('Client.guide_02_title'),
                    'content' => lang('Client.guide_02_content'),
                ],
                [
                    'title' => lang('Client.guide_03_title'),
                    'content' => lang('Client.guide_03_content'),
                ],
                [
                    'title' => lang('Client.guide_04_title'),
                    'content' => lang('Client.guide_04_content'),
                ],
                [
                    'title' => lang('Client.guide_05_title'),
                    'content' => lang('Client.guide_05_content'),
                ],
                [
                    'title' => lang('Client.guide_06_title'),
                    'content' => lang('Client.guide_06_content'),
                ],
            ];
            $data = array_merge($data, ['guide' => $guide]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/common/row_uploader_item',
                    '/common/input',
                    '/client/project/view'
                ],
                'js' => [
                    '/library/slick/slick.min.js',
                    '/module/slick_custom',
                    '/client/project',
                ],
            ])
            . view('/client/project/view', $data)
            . parent::loadFooter();
    }

    /**
     * /project/{id}/reward
     * @param $reward_id
     * @return string
     */
    public function getReward($reward_id): string
    {
        $this->checkLogout();
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getRewardData($reward_id));
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/common/input',
                    '/client/project/reward'
                ],
                'js' => [
                    '/client/reward',
                ],
            ])
            . view('/client/project/reward', $data)
            . parent::loadFooter();
    }

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
        foreach ($rewards as $index => $reward) {
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

    /**
     * reward 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getRewardData($id): array
    {
        $imp_shop_id = $this->settingModel->getInitialValue(['code' => 'imp-shop-id'], 'value');
        $rewards = $this->rewardModel->get(['id' => $id, 'is_deleted' => 0]);
        if (sizeof($rewards) != 1) throw new Exception('deleted');
        $reward = $rewards[0];
        if (isset($this->session->user_id)) {
            $reward['paid_count'] = $this->rewardModel->getPaidCount($reward['id'], $this->session->user_id);
        } else {
            $reward['paid_count'] = 0;
        }
        $reward['available_count'] = Utils::calculateAvailableReward($reward);

        $projects = $this->projectModel->get(['id' => $reward['project_id'], 'is_deleted' => 0]);
        if (sizeof($projects) != 1) throw new Exception('deleted');
        $reward_requests = $this->codeRewardRequestModel->get(['is_deleted' => 0, 'is_active' => 1]);
        $project = $projects[0];
        return [
            'project' => $project,
            'reward' => $reward,
            'reward_requests' => $reward_requests,
            'imp_shop_id' => $imp_shop_id
        ];
    }
}
