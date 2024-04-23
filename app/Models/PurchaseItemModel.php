<?php

namespace Models;

class PurchaseItemModel extends BaseModel
{
    protected $table = 'purchase_item';
    protected $allowedFields = [
        'id',
        'purchase_id',
        'code_reward_request_id',
        'status',
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
     * artist_code 를 조인하기 위해서 override
     * @return array
     * @throws Exception
     */
    public function get($condition = null, $limit = null, $isPriority = false): array
    {
        $query = "SELECT reward.*, project.title AS project_title, project.project_image_id AS project_image_id,".
            " purchase_item.id AS id, purchase_item.status, purchase_item.price FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN reward ON reward.id = purchase.reward_id".
            " LEFT JOIN project ON project.id = reward.project_id";
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'];
        }
        if ($isPriority) {
            $query .= " ORDER BY " . $this->table . ".priority ASC";
        } else {
            $query .= " ORDER BY " . $this->table . ".created_at DESC";
        }
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
            " LEFT JOIN reward ON reward.id = purchase.reward_id";
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'];
        }
        $result = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
        return $result[0]['cnt'];
    }
}
