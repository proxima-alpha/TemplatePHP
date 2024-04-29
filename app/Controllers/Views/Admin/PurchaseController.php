<?php

namespace Views\Admin;

use Exception;
use Models\ProjectModel;
use Models\PurchaseItemModel;

class PurchaseController extends BaseAdminController
{
    protected ProjectModel $projectModel;
    protected PurchaseItemModel $purchaseItemModel;

    public function __construct()
    {
        parent::__construct();
        $this->isRestricted = true;
        $this->projectModel = model('Models\ProjectModel');
        $this->purchaseItemModel = model('Models\PurchaseItemModel');
    }

    /**
     * /admin/project/{page}
     * @param $page
     * @return string
     */
    function index($page = 1): string
    {
        $data = $this->getViewData();
        try {
            $result = $this->purchaseItemModel->getPaginated([
                'per_page' => 10,
                'page' => $page,
            ]);
            $data = array_merge($data, $result);
            $data = array_merge($data, [
                'pagination_link' => '/admin/purchase',
            ]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/table',
                    '/admin/purchase/table',
                ],
                'js' => [
                    '/admin/popup_input',
                ],
            ])
            . view('/admin/purchase/table', $data)
            . parent::loadFooter();
    }
}
