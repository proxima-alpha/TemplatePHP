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
