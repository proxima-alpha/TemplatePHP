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
        $queryParams = $this->request->getGet();
        try {
            $endDate = $queryParams['end_date'] ?? date("Y-m-d");
            $endDateString = $endDate . " 23:59:59";
            $startDate = $queryParams['start_date'] ?? date("Y-m-d", strtotime('-1 month', strtotime($endDate)));
            $startDateString = $startDate . " 00:00:00";
            if (strtotime($startDate) > strtotime($endDate) ||
                abs((strtotime($endDate) - strtotime($startDate)) / 86400) > 31) {
                if(!isset($queryParams['end_date'])) {
                    return view('/redirect', [
                        'path' => '/admin/purchase'
                    ]);
                } else {
                    return view('/redirect', [
                        'path' => '/admin/purchase?end_date=' . $endDate
                    ]);
                }
            }
            $data = array_merge($data, [
                "start_date" => $startDateString,
                "end_date" => $endDateString,
            ]);
            $result = $this->purchaseItemModel->getPaginated([
                'per_page' => $this->per_page,
                'page' => $page,
            ], [
                'start_date' => $startDateString,
                'end_date' => $endDateString,
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
                    '/common/filter',
                    '/common/table',
                    '/admin/purchase/table',
                ],
                'js' => [
                    '/module/calendar',
                    '/common/filter',
                    '/admin/popup_input',
                ],
            ])
            . view('/admin/purchase/table', $data)
            . parent::loadFooter();
    }
}
