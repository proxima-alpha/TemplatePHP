<?php

namespace Views;

use App\Helpers\Utils;
use Exception;
use Models\CodeRewardRequestModel;
use Models\ProjectModel;
use Models\PurchaseItemModel;
use Models\PurchaseModel;
use Models\RewardModel;

class PurchaseController extends BaseClientController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected PurchaseItemModel $purchaseItemModel;
    protected PurchaseModel $purchaseModel;
    protected CodeRewardRequestModel $codeRewardRequestModel;

    public function __construct()
    {
        parent::__construct();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->purchaseModel = model('Models\PurchaseModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
        $this->codeRewardRequestModel = model('Models\CodeRewardRequestModel');
    }

    /**
     * /purchase/{id}
     * @param $id
     * @return string
     */
    function index($page = 1): string
    {
        $page = Utils::toInt($page);
        $data = $this->getViewData();
        try {
            $result = $this->purchaseItemModel->getPaginatedForClient([
                'per_page' => 15,
                'page' => $page,
            ], [
                'is_refunded' => 0,
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/purchase',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/client/purchase/table'
                ],
                'js' => [],
            ])
            . view('/client/purchase/table', $data)
            . parent::loadFooter();
    }

    /**
     * /purchase/{id}/view
     * @param $id
     * @return string
     */
    public function getView($id): string
    {
        $this->checkLogout();
        $data = $this->getViewData();
        try {
            $data = array_merge($data, $this->getPurchasedData($id));
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }

        return parent::loadHeader([
                'css' => [
                    '/client/purchase/view'
                ],
            ])
            . view('/client/purchase/view', $data)
            . parent::loadFooter();
    }

    /**
     * reward 조회시 필요한 데이터 불러오는 기능
     * @throws Exception
     */
    private function getPurchasedData($id): array
    {
        $purchaseItem = $this->purchaseItemModel->getLatest(['id' => $id]);
        if (!isset($purchaseItem)) throw new Exception('deleted');
        $purchase = $this->purchaseModel->getLatest(['id' => $purchaseItem['purchase_id']]);
        if (!isset($purchase)) throw new Exception('deleted');
        $rewards = $this->rewardModel->get(['id' => $purchase['reward_id'], 'is_deleted' => 0]);
        if (sizeof($rewards) != 1) throw new Exception('deleted');
        $reward = $rewards[0];

        $codeRewardRequest = $this->codeRewardRequestModel->getLatest(['id' => $purchaseItem['code_reward_request_id']]);
        if (!isset($codeRewardRequest)) throw new Exception('deleted');

        $projects = $this->projectModel->get(['id' => $reward['project_id'], 'is_deleted' => 0]);
        if (sizeof($projects) != 1) throw new Exception('deleted');
        $project = $projects[0];
        return [
            'project' => $project,
            'reward' => $reward,
            'purchase_item' => $purchaseItem,
            'purchase' => $purchase,
            'code_reward_request' => $codeRewardRequest,
        ];
    }
}
