<?php

namespace Views\Admin;

use Exception;
use Models\ProjectModel;
use Models\PurchaseItemModel;
use Models\RewardModel;

/**
 * isRestricted = false 로 하기 위해 controller 분리
 */
class ProjectRewardController extends BaseAdminController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
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
                'project' => $project
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
    function getPurchase($access_hash, $reward_id, $page = 1): string
    {
        $data = $this->getViewData();
        try {
            $reward = $this->rewardModel->getLatest([
                'is_deleted' => 0
            ]);
            if (!isset($reward)) throw new Exception('deleted');
            $result = $this->purchaseItemModel->getPaginated([
                'per_page' => 10,
                'page' => $page,
            ], [
                'purchase.reward_id' => $reward_id,
                'purchase.status' => 'paid',
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/project/' . $access_hash . '/reward/get/' . $reward_id,
            ]);
            $data = array_merge($data, [
                'reward' => $reward
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/uploader',
                    '/admin/project/reward_purchase',
                ],
                'js' => [
                    '/common/delete',
                    '/admin/popup_input',
                    '/admin/reward_uploader',
                ],
            ])
            . view('/admin/project/reward_purchase', $data)
            . parent::loadFooter();
    }
}
