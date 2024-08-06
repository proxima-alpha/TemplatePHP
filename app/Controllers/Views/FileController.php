<?php

namespace Views;

use CodeIgniter\HTTP\DownloadResponse;
use Config\Services;
use Exception;
use Models\CustomFileModel;
use Models\PurchaseItemModel;
use Models\PurchaseItemRewardModel;
use Models\RewardFileModel;

class FileController extends BaseClientController
{
    protected CustomFileModel $customFileModel;
    protected RewardFileModel $rewardFileModel;
    protected PurchaseItemModel $purchaseItemModel;
    protected PurchaseItemRewardModel $purchaseItemRewardModel;

    public function __construct()
    {
        $this->customFileModel = model('Models\CustomFileModel');
        $this->rewardFileModel = model('Models\RewardFileModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
        $this->purchaseItemRewardModel = model('Models\PurchaseItemRewardModel');
    }

    /**
     * [get] /file/{id}
     * @param $id
     * @return DownloadResponse|null
     */
    public function getFile($id): DownloadResponse|null
    {
        try {
            $result = $this->customFileModel->find($id);

            if ($result) {
//                if ($result['type'] != 'image') {
//                    throw new Exception('bad request');
//                }
//                $data = file_get_contents($result['path'] . "/" . $result['file_name']);
//                $this->response
//                    ->setStatusCode(200)
//                    ->setContentType($result['mime_type'])
//                    ->setBody($data)
//                    ->send();

                return $this->response->download($result['path'] . "/" . $result['file_name'], null);
            } else {
                throw new Exception('not found');
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }


    /**
     * [get] /reward-file/{id}
     * @param $id
     * @return DownloadResponse|null
     */
    public function getRewardFile($id): DownloadResponse|null
    {
        try {
            $result = $this->rewardFileModel->find($id);

            if ($result) {
//                if ($result['type'] != 'image') {
//                    throw new Exception('bad request');
//                }
//                $data = file_get_contents($result['path'] . "/" . $result['file_name']);
//                $this->response
//                    ->setStatusCode(200)
//                    ->setContentType($result['mime_type'])
//                    ->setBody($data)
//                    ->send();

                return $this->response->download($result['path'] . "/" . $result['file_name'], null);
            } else {
                throw new Exception('not found');
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    /**
     * [get] /reward-file/{id}/download
     * @param $id
     * @return DownloadResponse|null
     */
    public function getRewardFileDownload($id): DownloadResponse|null
    {
        try {
            $result = $this->rewardFileModel->find($id);

            if ($result) {
                if (isset($this->session->user_id)) {
                    $purchaseItems = $this->purchaseItemRewardModel->get(['id' => $result['purchase_item_reward_id'], 'reward_file.id' => $id]);
                    if (sizeof($purchaseItems) != 1) {
                        throw new Exception('deleted');
                    }
                    $purchaseItem = $purchaseItems[0];
                    if ($purchaseItem['user_id'] == $this->session->user_id) {
                        $this->purchaseItemRewardModel->update($purchaseItem['id'], [
                            'status' => 'received',
                        ]);
                    }
                }
//                if ($result['type'] != 'image') {
//                    throw new Exception('bad request');
//                }
//                $data = file_get_contents($result['path'] . "/" . $result['file_name']);
//                $this->response
//                    ->setStatusCode(200)
//                    ->setContentType($result['mime_type'])
//                    ->setBody($data)
//                    ->send();

                return $this->response->download($result['path'] . "/" . $result['file_name'], null);
            } else {
                throw new Exception('not found');
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * [get] /file/{id}/thumbnail
     * @param $id
     * @return DownloadResponse|null
     */
    public function getFileThumbnail($id): DownloadResponse|null
    {
        try {
            $result = $this->customFileModel->find($id);

            if ($result && isset($result['thumb_file_name'])) {
                return $this->response->download($result['path'] . "/" . $result['thumb_file_name'], null);
            } else {
                throw new Exception('not found');
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * [get] /file/purchase-item
     */
    public function downloadPurchaseItem()
    {
        if (!$this->session->is_admin) {
            $response = Services::response();
            $response->setBody(view('/error', [
                'code' => '403',
                'title' => 'Forbidden',
                'message' => 'this page is not allowed for your account.<br/> please check your account type is \'admin\' or \'member\'.',
            ]));
            $response->sendBody();
            exit;
        }
        $queryParams = $this->request->getGet();
        $endDate = $queryParams['end_date'] ?? date("Y-m-d");
        $endDateString = $endDate . " 23:59:59";
        $startDate = $queryParams['start_date'] ?? date("Y-m-d", strtotime('-1 month', strtotime($endDate)));
        $startDateString = $startDate . " 00:00:00";
        if (strtotime($startDate) > strtotime($endDate) ||
            abs((strtotime($endDate) - strtotime($startDate)) / 86400) > 31) {
            if (!isset($queryParams['end_date'])) {
                return view('/redirect', [
                    'path' => '/purchase-item'
                ]);
            } else {
                return view('/redirect', [
                    'path' => '/purchase-item?end_date=' . $endDate
                ]);
            }
        }
        $lang = $this->session->lang;
        $file_name = ($lang == 'ko' ? "결제내역_" : "payment_") . $startDate . "_" . $endDate . "_at_" . time() . ".xls";
        $result = $this->purchaseItemModel->get([
            'start_date' => $startDateString,
            'end_date' => $endDateString,
        ], null, "ASC");
        ob_get_clean();
        header("Content-type: application/octet-stream; charset=utf-8");
        header('Content-Disposition: attachment; filename=' . $file_name);
        $output = fopen('php://output', 'w');
        fputcsv($output, array('번호',
            lang('Service.name'),
            lang('Service.email'),
            lang('Client.reward'),
            lang('Service.price'),
            lang('Service.currency'),
            lang('Service.channel'),
            lang('Service.paid_at'),
        ));
        foreach ($result as $i => $item) {
            $row = array($i + 1,
                $item['inquirer_name'],
                $item['inquirer_email'],
                $lang == 'ko' ? $item['title'] : $item['title_en'],
                $item['price'],
                'KRW',
                \App\Helpers\HtmlHelper::getPaymentChannel($item['channel']),
                $item['created_at'],
            );
            fputcsv($output, $row);
        }
    }
}
