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
        $this->isRestricted = true;
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
        $queryParams = $this->request->getGet();
        try {
            $endDate = $queryParams['end_date'] ?? date("Y-m-d");
            $endDateString = $endDate . " 23:59:59";
            $startDate = $queryParams['start_date'] ?? date("Y-m-d", strtotime('-1 month', strtotime($endDate)));
            $startDateString = $startDate . " 00:00:00";
            if (strtotime($startDate) > strtotime($endDate) ||
                abs((strtotime($endDate) - strtotime($startDate)) / 86400) > 31) {
                if (!isset($queryParams['end_date'])) {
                    return view('/redirect', [
                        'path' => '/admin/dashboard'
                    ]);
                } else {
                    return view('/redirect', [
                        'path' => '/admin/dashboard?end_date=' . $endDate
                    ]);
                }
            }
            $data = array_merge($data, [
                "start_date" => $startDateString,
                "end_date" => $endDateString,
            ]);
            $queries = [];
            $queries[] =
                " SELECT SUM(purchase_item.price) AS total_amount, COUNT(purchase_item.id) AS purchased_count FROM purchase_item LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
                " WHERE purchase_item.is_refunded = 0 AND purchase.status != 'created' AND purchase.created_at >= '" . $startDateString . "' AND purchase.created_at <= '" . $endDateString . "'";
            $result = BaseModel::transaction($this->db, $queries);
            if (sizeof($result) == 0) throw new Exception('deleted');
            $data = array_merge($data, $result[0]);
            $queries = [];
            $queries[] = "SELECT SUM(total_count) AS stock_count, COUNT(reward.id) AS total_reward_count" .
                " FROM reward LEFT JOIN project ON project.id = reward.project_id" .
                " WHERE reward.is_deleted = 0 AND" .
                " (project.start_date <= '" . $endDateString . "' AND project.start_date >= '" . $startDateString . "' OR" .
                " project.end_date >= '" . $startDateString . "' AND project.end_date <= '" . $endDateString . "' OR" .
                " project.start_date < '" . $startDateString . "' AND project.end_date > '" . $endDateString . "');";
            $result = BaseModel::transaction($this->db, $queries);
            if (sizeof($result) == 0) throw new Exception('deleted');
            $data = array_merge($data, $result[0]);
        } catch (Exception $e) {
            //todo(log)
            $this->handleException($e);
        }
        return parent::loadHeader([
                'css' => [
                    '/common/filter',
                    '/admin/dashboard',
                ],
                'js' => [
                    '/module/calendar',
                    '/common/filter',
                ],
            ])
            . view('/admin/dashboard', $data, [
                'test' => 1
            ])
            . parent::loadFooter();
    }
}
