<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BaseModel;
use Models\PurchaseItemRewardModel;

class PurchaseItemRewardController extends BaseApiController
{
    protected PurchaseItemRewardModel $purchaseItemRewardModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->purchaseItemRewardModel = model('Models\PurchaseItemRewardModel');
    }

    /**
     * [get] /api/purchase-item-reward/get/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function get($id): ResponseInterface
    {
        $response = [
            'success' => false,
        ];

        try {
            $result = $this->purchaseItemRewardModel->get(['id' => $id]);
            if (sizeof($result) == 0) throw new Exception('not exist');
            $response['success'] = true;
            $response['data'] = $result[0];
        } catch (Exception $e) {
            //todo(log)
            $response['message'] = $e->getMessage();
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/purchase-item-reward/update/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        $this->checkAdmin();
        $data = $this->request->getPost();
        $response = [
            'success' => false,
        ];

        if (strlen($id) == 0) {
            $response['message'] = "field 'id' should not be empty.";
        } else {
            try {
                if (isset($data['id'])) unset($data['id']);
                $queries = [];
                $queries[] =
                    "UPDATE purchase_item
                        LEFT JOIN purchase_item_reward ON purchase_item_reward.purchase_item_id = purchase_item.id
                        SET purchase_item.memo = '" . $data['memo'] . "'  WHERE purchase_item_reward.id = '" . $id . "';";
                BaseModel::transaction($this->db, $queries);
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
                $response['message'] = $e->getMessage();
            }
        }
        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/purchase-item-reward/confirm/{id}
     * @param $id
     * @return ResponseInterface
     */
    public function confirm($id): ResponseInterface
    {
        $this->checkAdmin();

        $purchaseItem = $this->purchaseItemRewardModel->find($id);
        if ($purchaseItem['status'] != 'waiting') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action.'
            ]);
        }
        return $this->typicallyUpdate($this->purchaseItemRewardModel, $id, [
            'status' => 'confirmed'
        ]);
    }
}
