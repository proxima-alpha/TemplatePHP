<?php

namespace Views\Admin;

use Exception;
use Models\ProjectModel;
use Models\PurchaseItemModel;
use Models\PurchaseItemRewardModel;
use Models\RewardModel;

/**
 * isRestricted = false 로 하기 위해 controller 분리
 */
class ProjectRewardController extends BaseAdminController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected PurchaseItemModel $purchaseItemModel;
    protected PurchaseItemRewardModel $purchaseItemRewardModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
        $this->purchaseItemRewardModel = model('Models\PurchaseItemRewardModel');
    }

    /**
     * /admin/project/{access_hash}/reward/{page}
     * @param $page
     * @return string
     */
    function index($access_hash, $page = 1): string
    {
        $data = $this->getViewData();
        try {
            $project = $this->projectModel->getLatest([
                'access_hash' => $access_hash,
                'is_deleted' => 0
            ]);
            if (!isset($project)) throw new Exception('deleted');
            $report = $this->projectModel->getReport($project['id']);
            $result = $this->rewardModel->getPaginatedForAdmin([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'project_id' => $project['id']
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/project/' . $access_hash . '/reward',
            ]);
            $data = array_merge($data, [
                'project' => $project,
                'report' => $report
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/project/reward',
                ],
                'js' => [
                ],
            ])
            . view('/admin/project/reward', $data)
            . parent::loadFooter();
    }

    /**
     * /admin/project/{access_hash}/reward/get/{reward_id}/{page}
     * @param $page
     * @return string
     */
    function getPurchaseItemReward($access_hash, $reward_id, $page = 1): string
    {
        $data = $this->getViewData();
        try {
            $rewards = $this->rewardModel->getForAdmin([
                'id' => $reward_id,
                'is_deleted' => 0
            ]);
            if (sizeof($rewards) != 1) throw new Exception('deleted');
            $reward = $rewards[0];
            $result = $this->purchaseItemRewardModel->getPaginated([
                'per_page' => 10,
                'page' => $page,
            ], [
                'purchase_item.is_refunded' => 0,
                'purchase.reward_id' => $reward_id,
                'purchase.status' => 'paid',
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/project/' . $access_hash . '/reward/get/' . $reward_id,
            ]);
            $data = array_merge($data, [
                'reward' => $reward,
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/admin/project/reward_for_artist',
                ],
                'js' => [
                    '/common/delete',
                    '/admin/popup_input',
                    '/admin/reward_uploader',
                ],
            ])
            . view('/admin/project/reward_for_artist', $data)
            . parent::loadFooter();
    }
}
