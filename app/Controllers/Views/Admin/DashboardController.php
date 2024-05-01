<?php

namespace Views\Admin;

use Exception;
use Models\BaseModel;

/**
 * isRestricted = false 로 하기 위해 controller 분리
 */
class DashboardController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }

    /**
     * /admin/dashboard
     * @param $page
     * @return string
     */
    function index(): string
    {
        $data = $this->getViewData();
        try {
            $queries = [];
            $queries[] = "SELECT (" .
                " SELECT SUM(purchase_item.price) FROM purchase_item LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id WHERE purchase_item.is_refunded = 0 AND purchase.status != 'created') AS total_amount, (" .
                " SELECT SUM(purchased_count) FROM reward WHERE is_deleted = 0) AS purchased_count, (" .
                " SELECT SUM(total_count) FROM reward WHERE is_deleted = 0) AS stock_count, (" .
                " SELECT COUNT(id) FROM reward WHERE is_deleted = 0) AS total_reward_count";
            $result = BaseModel::transaction($this->db, $queries);
            if (sizeof($result) == 0) throw new Exception('deleted');
            $data = array_merge($data, $result[0]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/admin/dashboard',
                ],
                'js' => [
                ],
            ])
            . view('/admin/dashboard', $data)
            . parent::loadFooter();
    }
}
