<?php

namespace API;

use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Models\BoardModel;
use Models\CustomFileModel;
use Models\PurchaseItemModel;
use Models\TopicModel;

class PurchaseItemController extends BaseApiController
{
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
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
}
