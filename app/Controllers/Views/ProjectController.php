<?php

namespace Views;

use App\Helpers\HtmlHelper;
use App\Helpers\ServerLogger;
use Exception;
use Models\ArtistGroupModel;
use Models\ProjectModel;
use Models\RewardModel;

class ProjectController extends BaseClientController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected ArtistGroupModel $artistGroupModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->artistGroupModel = model('Models\ArtistGroupModel');
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
        $rewards = $this->rewardModel->get(['project_id' => $id, 'is_deleted' => 0]);
        if (sizeof($rewards) != 1) throw new Exception('deleted');
        $reward = $rewards[0];
        $projects = $this->projectModel->get(['id' => $reward['project_id'], 'is_deleted' => 0]);
        if (sizeof($projects) != 1) throw new Exception('deleted');
        $project = $projects[0];
        return [
            'project' => $project,
            'reward' => $reward,
        ];
    }
}
