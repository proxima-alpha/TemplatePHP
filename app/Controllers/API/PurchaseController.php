<?php

namespace API;

use App\Helpers\IMPHelper;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BaseModel;
use Models\ProjectModel;
use Models\PurchaseItemModel;
use Models\PurchaseModel;
use Models\RewardModel;

class PurchaseController extends BaseApiController
{
    protected ProjectModel $projectModel;
    protected RewardModel $rewardModel;
    protected PurchaseModel $purchaseModel;
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->projectModel = model('Models\ProjectModel');
        $this->rewardModel = model('Models\RewardModel');
        $this->purchaseModel = model('Models\PurchaseModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
    }

    /**
     * [post] /api/purchase
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'reward_id' => [
                'label' => 'Reward',
                'rules' => 'required',
            ],
            'paid' => [
                'label' => 'Paid',
                'rules' => 'required',
            ],
            'purchaser_name' => [
                'label' => 'Name',
                'rules' => 'required',
            ],
            'purchaser_email' => [
                'label' => 'Email',
                'rules' => 'required',
            ],
            'purchase_items' => [
                'label' => 'Purchase Item',
                'rules' => 'required',
            ],
            'pg' => [
                'label' => 'PG',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            $reward = $this->rewardModel->getLatest(['id' => $data['reward_id']]);
            $project = $this->projectModel->getLatest(['id' => $reward['project_id']]);
            try {
                // 날짜 체크
                if (isset($project['end_date'])) {
                    $endTimeRaw = strtotime($project['end_date']);
                    $nowTimeRaw = strtotime(date("Y-m-d H:i:s"));
                    if ($nowTimeRaw > $endTimeRaw)
                        throw new Exception('This project is expired.');
                }
                $paid_count = $this->rewardModel->getPaidCount($data['reward_id'], $this->session->user_id);
                if (sizeof($data['purchase_items']) + $paid_count > $reward['limited_count'] ||
                    sizeof($data['purchase_items']) + $reward['purchased_count'] > $reward['total_count']) {
                    throw new Exception('Items to buy are exceeded.');
                }

                if (isset($data['id'])) unset($data['id']);

                $data['user_id'] = $this->session->user_id;
                $this->db->transBegin();
                $inserted_row_id = $this->purchaseModel->insert($data);
                if (!$inserted_row_id) {
                    $response['messages'] = $this->purchaseModel->errors();
                    throw new \Exception();
                }
                foreach ($data['purchase_items'] as $item) {
                    $item['purchase_id'] = $inserted_row_id;
                    $inserted_id = $this->purchaseItemModel->insert($item);
                    if (!$inserted_id) {
                        $response['messages'] = $this->purchaseItemModel->errors();
                        throw new \Exception();
                    }
                }
                $this->db->transCommit();
                $purchase = $this->purchaseModel->getLatest(['id' => $inserted_row_id]);
                $response['success'] = true;
                $response['data'] = $purchase;
            } catch (Exception $e) {
                //todo(log)
                $this->db->transRollback();
                if (!isset($response['message'])) {
                    $response['message'] = $e->getMessage();
                }
            }
        }

        return $this->response->setJSON($response);
    }

    /**
     * [post] /api/purchase/{id}/complete
     * @return ResponseInterface
     */
    public function complete($id): ResponseInterface
    {
        $data = $this->request->getPost();
        $validationRules = [
            'imp_uid' => [
                'label' => 'imp_uid',
                'rules' => 'required',
            ],
            'merchant_uid' => [
                'label' => 'merchant_uid',
                'rules' => 'required',
            ],
        ];

        $response = [
            'success' => false,
        ];

        if ($validationRules != null && !$this->validate($validationRules)) {
            $response['messages'] = $this->validator->getErrors();
        } else {
            try {
                $paidData = IMPHelper::getPaymentData($data['imp_uid']);
                if (!isset($paidData['success']) || !$paidData['success']) {
                    throw new Exception('IMP::' . ($paidData['message'] ?? 'Payment failed'));
                }
                $items = $this->purchaseItemModel->get(['purchase_id' => $id]);
                $purchase = $this->purchaseModel->getLatest(['id' => $id]);
                $this->db->transBegin();
                $inserted_id = $this->purchaseModel->update($id, [
                    'imp_uid' => $data['imp_uid'],
                    'merchant_uid' => $data['merchant_uid'],
                    'status' => 'paid',
                ]);
                if (!$inserted_id) {
                    $response['messages'] = $this->purchaseModel->errors();
                    throw new \Exception();
                }
                $queries = [];
                $queries[] = "UPDATE reward SET purchased_count = purchased_count + " . sizeof($items) . " WHERE id = '" . $purchase['reward_id'] . "';";
                BaseModel::transaction($this->db, $queries);
                $this->db->transCommit();
                $response['success'] = true;
            } catch (Exception $e) {
                //todo(log)
                $this->db->transRollback();
                if (!isset($response['message'])) {
                    $response['message'] = $e->getMessage();
                }
            }
        }

        return $this->response->setJSON($response);
    }
}
