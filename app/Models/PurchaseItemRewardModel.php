<?php

namespace Models;

class PurchaseItemRewardModel extends BaseModel
{
    protected $table = 'purchase_item_reward';
    protected $allowedFields = [
        'id',
        'purchase_item_id',
        'artist_id',
        'status',
        'created_at',
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
        $query = "SELECT purchase_item.*, code_reward_request.name AS reward_request_name, code_reward_request.name_en AS reward_request_name_en, code_reward_request.name_jp AS reward_request_name_jp,
             purchase.user_id AS user_id, user.name AS user_name,
             artist.name AS artist_name, artist.name_en AS artist_name_en, artist.name_jp AS artist_name_jp,
             purchase_item_reward.status, purchase_item_reward.id, reward_file.id AS reward_file_id, reward_file.poster AS reward_file_poster FROM purchase_item_reward" .
            " LEFT JOIN artist ON artist.id = purchase_item_reward.artist_id" .
            " LEFT JOIN purchase_item ON purchase_item.id = purchase_item_reward.purchase_item_id" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN user ON user.id = purchase.user_id" .
            " LEFT JOIN code_reward_request ON code_reward_request.id = purchase_item.code_reward_request_id" .
            " LEFT JOIN reward_file ON reward_file.purchase_item_reward_id = purchase_item_reward.id";
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
        $query .= " GROUP BY purchase_item.id ";
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
        $query = "SELECT COUNT(*) AS cnt FROM purchase_item_reward" .
            " LEFT JOIN purchase_item ON purchase_item.id = purchase_item_reward.purchase_item_id" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN code_reward_request ON code_reward_request.id = purchase_item.code_reward_request_id" .
            " LEFT JOIN reward_file ON reward_file.purchase_item_reward_id = purchase_item_reward.id";
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

    public function getRewards($condition = null, $limit = null, $order = 'DESC'): array
    {
        $query = "SELECT artist.*, purchase_item_reward.status AS status, reward_file.id AS reward_file_id,
            purchase.user_id AS user_id, purchase_item_reward.id AS id
            FROM purchase_item_reward" .
            " LEFT JOIN purchase_item ON purchase_item.id = purchase_item_reward.purchase_item_id" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN artist ON artist.id = purchase_item_reward.artist_id" .
            " LEFT JOIN reward_file ON reward_file.purchase_item_reward_id = purchase_item_reward.id";
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'];
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
}
