<?php

namespace Views;

use CodeIgniter\HTTP\DownloadResponse;
use Exception;
use Models\CustomFileModel;
use Models\PurchaseItemModel;
use Models\RewardFileModel;

class FileController extends BaseClientController
{
    protected CustomFileModel $customFileModel;
    protected RewardFileModel $rewardFileModel;
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        $this->customFileModel = model('Models\CustomFileModel');
        $this->rewardFileModel = model('Models\RewardFileModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
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
                if (isset($this->session->user_id)) {
                    $purchaseItems = $this->purchaseItemModel->get(['id' => $result['purchase_item_id'], 'reward_file.id' => $id]);
                    if (sizeof($purchaseItems) != 1) {
                        throw new Exception('deleted');
                    }
                    $purchaseItem = $purchaseItems[0];
                    if ($purchaseItem['user_id'] == $this->session->user_id) {
                        $this->purchaseItemModel->update($purchaseItem['id'], [
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
}
