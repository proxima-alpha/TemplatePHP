<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
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
            'payment_method' => [
                'label' => 'Payment Method',
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
//                    $queries = [];
//                    $queries[] = "UPDATE reward SET purchased_count = purchased_count + ".sizeof($data['purchase_items'])." WHERE id = '" . $data['reward_id'] . "';";
//
//                    BaseModel::transaction($this->db, $queries);
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
