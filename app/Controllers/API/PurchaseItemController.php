<?php

namespace API;

use App\Helpers\IMPHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BaseModel;
use Models\PurchaseItemModel;
use Models\PurchaseItemRewardModel;
use Models\PurchaseModel;

class PurchaseItemController extends BaseApiController
{
    protected PurchaseItemModel $purchaseItemModel;
    protected PurchaseItemRewardModel $purchaseItemRewardModel;
    protected PurchaseModel $purchaseModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
        $this->purchaseItemRewardModel = model('Models\PurchaseItemRewardModel');
        $this->purchaseModel = model('Models\PurchaseModel');
    }

    /**
     * [get] /api/purchase-item/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function get($id): ResponseInterface
    {
        return $this->typicallyFind($this->purchaseItemModel, $id);
    }

    /**
     * [post] /api/purchase-item/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        return $this->typicallyUpdate($this->purchaseItemModel, $id, [
            'memo' => $data['memo']
        ]);
    }

    /**
     * [delete] /api/purchase-item/refund/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function refund($id): ResponseInterface
    {
        $this->checkAdmin();

        try {
            $purchaseItem = $this->purchaseItemModel->find($id);
            if (!isset($purchaseItem)) {
                throw new Exception('Data does not exist.');
            }
            $purchase = $this->purchaseModel->find($purchaseItem['purchase_id']);
            if (!isset($purchase)) {
                throw new Exception('Data does not exist.');
            }
            if ($purchase['status'] != 'paid' || $purchase['paid'] - $purchase['refunded'] - $purchaseItem['price'] < 0) {
                throw new Exception('Invalid action.');
            }
            $requestData = [
                'imp_uid' => $purchase['imp_uid'],
                'merchant_uid' => $purchase['merchant_uid'],
                'amount' => $purchaseItem['price']
            ];

            $impResponse = IMPHelper::refund($requestData);
            if (!isset($impResponse['success']) || !$impResponse['success']) {
                throw new Exception('IMP::' . ($impResponse['message'] ?? 'Payment failed'));
            }
            $queries[] = "UPDATE purchase_item SET is_refunded = 1 WHERE id = '" . $purchaseItem['id'] . "';";
            $queries[] = "UPDATE purchase SET refunded = refunded + " . $purchaseItem['price'] . " WHERE id = '" . $purchase['id'] . "';";
            if ($purchase['paid'] - $purchase['refunded'] - $purchaseItem['price'] == 0) {
                $queries[] = "UPDATE purchase SET status = 'refunded'  WHERE id = '" . $purchase['id'] . "';";
            }
            BaseModel::transaction($this->db, $queries);
            $this->rewardModel->updatePurchasedCount($purchase['reward_id']);
            $response['success'] = true;
        } catch (Exception $e) {
            //todo(log)
            if (!isset($response['message'])) {
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }
}
