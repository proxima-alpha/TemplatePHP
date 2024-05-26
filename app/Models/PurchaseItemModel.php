<?php

namespace Models;

use App\Helpers\ServerLogger;

class PurchaseItemModel extends BaseModel
{
    protected $table = 'purchase_item';
    protected $allowedFields = [
        'id',
        'purchase_id',
        'code_reward_request_id',
        'price',
        'inquirer_name',
        'inquirer_email',
        'inquirer_comment',
        'memo',
        'is_mine',
        'is_refunded',
        'is_agreed',
        'created_at',
        'updated_at',
    ];

    /**
     * select 문을 호출하는 기능
     * purchase 를 join 하기 위해 override
     * @param $condition
     * @param $limit
     * @return array
     * @throws \Exception
     */
    public function get($condition = null, $limit = null, $order = 'DESC'): array
    {
        $query = "SELECT purchase_item.*, code_reward_request.name AS reward_request_name, code_reward_request.name_en AS reward_request_name_en,
             purchase.user_id AS user_id, user.name AS user_name, purchase.pg AS channel FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN user ON user.id = purchase.user_id" .
            " LEFT JOIN code_reward_request ON code_reward_request.id = purchase_item.code_reward_request_id";
        $values = [];
        if (isset($condition)) {
            $dateQueries = [];
            if (isset($condition['start_date'])) {
                $dateQueries[] = $this->table . ".created_at >= '" . $condition['start_date'] . "'";
                unset($condition['start_date']);
            }
            if (isset($condition['end_date'])) {
                $dateQueries[] = $this->table . ".created_at <= '" . $condition['end_date'] . "'";
                unset($condition['end_date']);
            }

            if (sizeof($condition) > 0) {
                $set = $this->getConditionSet($condition);
                $values = array_merge($values, $set['values']);
                $query .= " " . $set['query'];
                if (sizeof($dateQueries) > 0) {
                    $query .= " AND " . join(' AND ', $dateQueries);
                }
            } else {
                $query .= " WHERE " . join(' AND ', $dateQueries);
            }
            $query .= " AND purchase.status != 'created'";
        } else {
            $query .= " WHERE purchase.status != 'created'";
        }
        $query .= " ORDER BY " . $this->table . ".created_at " . $order;
        if (isset($limit)) {
            $query .= " LIMIT " . $limit['offset'] . ", " . $limit['value'];
        }
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
    }

    protected function getCountAll($condition = null)
    {
        $query = "SELECT COUNT(*) AS cnt FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN code_reward_request ON code_reward_request.id = purchase_item.code_reward_request_id" .
            " LEFT JOIN purchase_item_reward ON purchase_item_reward.purchase_item_id = purchase_item.id";
        $values = [];
        if (isset($condition)) {
            $dateQueries = [];
            if (isset($condition['start_date'])) {
                $dateQueries[] = $this->table . ".created_at >= '" . $condition['start_date'] . "'";
                unset($condition['start_date']);
            }
            if (isset($condition['end_date'])) {
                $dateQueries[] = $this->table . ".created_at <= '" . $condition['end_date'] . "'";
                unset($condition['end_date']);
            }

            if (sizeof($condition) > 0) {
                $set = $this->getConditionSet($condition);
                $values = array_merge($values, $set['values']);
                $query .= " " . $set['query'];
                if (sizeof($dateQueries) > 0) {
                    $query .= " AND " . join(' AND ', $dateQueries);
                }
            } else {
                $query .= " WHERE " . join(' AND ', $dateQueries);
            }
            $query .= " AND purchase.status != 'created'";
        } else {
            $query .= " WHERE purchase.status != 'created'";
        }
        $result = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
        return $result[0]['cnt'];
    }


    /**
     * select 문을 호출하는 기능
     * client 용 pagination 과 admin 용 pagination 이 달라서 분리
     * @return array
     * @throws Exception
     */
    public function getForClient($condition = null, $limit = null): array
    {
        $query = "SELECT reward.*, project.title AS project_title, project.title_en AS project_title_en, project.project_image_id AS project_image_id,
            purchase_item.id AS id, purchase_item.price, purchase_item.is_refunded,
            COUNT(purchase_item_reward.id) AS total_reward_count,
            COUNT(CASE WHEN purchase_item_reward.status = 'confirmed' OR purchase_item_reward.status = 'received' THEN 1 ELSE NULL END) AS confirmed_reward_count FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN reward ON reward.id = purchase.reward_id" .
            " LEFT JOIN project ON project.id = reward.project_id" .
            " LEFT JOIN purchase_item_reward ON purchase_item_reward.purchase_item_id = purchase_item.id";
        $values = [];
        if (isset($condition)) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'] . " AND purchase.status != 'created'";
        } else {
            $query .= " WHERE purchase.status != 'created'";
        }
        $query .= "
         GROUP BY " . $this->table . ".id
         ORDER BY " . $this->table . ".created_at DESC";
        if (isset($limit)) {
            $query .= " LIMIT " . $limit['offset'] . ", " . $limit['value'];
        }
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
    }

    /**
     * client 용 pagination 과 admin 용 pagination 이 달라서 분리
     * @param $pagination
     * @param $condition
     * @return array
     */
    public function getPaginatedForClient($pagination, $condition = null): array
    {
        $total = $this->getCountAll($condition);
        $per_page = $pagination['per_page'];
        $total_page = (int)($total / $per_page) + ($total % $per_page == 0 ? 0 : 1);
        $page = $pagination['page'] == 'last' ? $total_page : $pagination['page'];
        $offset = 0;
        if ($page > $total_page) {
            $page = $total_page;
        }
        if ($page > 0) {
            $offset = ($page - 1) * $per_page;
        }
        $result = $this->getForClient($condition, [
            'value' => $per_page,
            'offset' => $offset
        ]);

        return [
            'array' => $result,
            'pagination' => $this->parsePagination($page, $per_page, $total, $total_page),
        ];
    }
}
