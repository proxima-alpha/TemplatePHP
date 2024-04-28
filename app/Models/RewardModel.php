<?php

namespace Models;

use App\Helpers\ServerLogger;

class RewardModel extends BasePriorityModel
{
    protected $table = 'reward';
    protected $allowedFields = [
        'id',
        'project_id',
        'title',
        'content',
        'title_en',
        'content_en',
        'total_count',
        'price',
        'limited_count',
        'purchased_count',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];

    /**
     * select 문을 호출하는 기능
     * artist_code 를 조인하기 위해서 override
     * @return array
     * @throws Exception
     */
    public function getForAdmin($condition = null, $limit = null): array
    {
        $query = "SELECT reward.*, (" .
            "SELECT COUNT(*) AS cnt FROM purchase" .
            " LEFT JOIN purchase_item ON purchase_item.purchase_id = purchase.id" .
            " WHERE purchase_item.is_refunded = 0 AND purchase.reward_id = reward.id  AND purchase.status = 'paid'" .
            " ) AS total_paid_count, (" .
            "SELECT COUNT(*) AS cnt FROM purchase" .
            " LEFT JOIN purchase_item ON purchase_item.purchase_id = purchase.id" .
            " LEFT JOIN reward_file ON reward_file.purchase_item_id = purchase_item.id" .
            " WHERE purchase_item.is_refunded = 0 AND (purchase_item.status != 'waiting' OR reward_file.id IS NOT NULL) AND purchase.reward_id = reward.id  AND purchase.status = 'paid'" .
            " ) AS uploaded_count" .
            " FROM reward";
        ServerLogger::log($query);
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'];
        }
        $query .= " ORDER BY " . $this->table . ".created_at DESC";
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
    public function getPaginatedForAdmin($pagination, $condition = null): array
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
        $result = $this->getForAdmin($condition, [
            'value' => $per_page,
            'offset' => $offset
        ]);

        return [
            'array' => $result,
            'pagination' => $this->parsePagination($page, $per_page, $total, $total_page),
        ];
    }

    public function getPaidCount($reward_id, $user_id)
    {
        $query = "SELECT COUNT(*) AS cnt FROM purchase" .
            " LEFT JOIN purchase_item ON purchase_item.purchase_id = purchase.id" .
            " WHERE purchase_item.is_refunded = 0 AND purchase.reward_id = '" . $reward_id . "' AND purchase.user_id = '" . $user_id . "' AND purchase.status = 'paid'";
        $result = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);
        return $result[0]['cnt'];
    }
}
