<?php

namespace Models;

class RewardModel extends BasePriorityModel
{
    protected $table = 'reward';
    protected $allowedFields = [
        'id',
        'project_id',
        'title',
        'content',
        'total_count',
        'price',
        'limited_count',
        'purchased_count',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];

    public function getPaidCount($reward_id, $user_id)
    {
        $query = "SELECT COUNT(*) AS cnt FROM purchase" .
            " LEFT JOIN purchase_item ON purchase_item.purchase_id = purchase.id" .
            " WHERE purchase.reward_id = '" . $reward_id . "' AND purchase.user_id = '" . $user_id . "' AND purchase.status = 'paid'";
        $result = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);
        return $result[0]['cnt'];
    }
}
