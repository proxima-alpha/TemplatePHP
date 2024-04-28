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
     * purchase 를 join 하기 위해 override
     * @param $condition
     * @param $limit
     * @return array
     * @throws \Exception
     */
    public function get($condition = null, $limit = null): array
    {
        $query = "SELECT purchase_item.*, code_reward_request.name AS reward_request_name, code_reward_request.name_en AS reward_request_name_en" .
            " FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN code_reward_request ON code_reward_request.id = purchase_item.code_reward_request_id";
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


    /**
     * select 문을 호출하는 기능
     * client 용 pagination 과 admin 용 pagination 이 달라서 분리
     * @return array
     * @throws Exception
     */
    public function getForClient($condition = null, $limit = null): array
    {
        $query = "SELECT reward.*, project.title AS project_title, project.title_en AS project_title_en, project.project_image_id AS project_image_id," .
            " purchase_item.id AS id, purchase_item.status, purchase_item.price FROM purchase_item" .
            " LEFT JOIN purchase ON purchase.id = purchase_item.purchase_id" .
            " LEFT JOIN reward ON reward.id = purchase.reward_id" .
            " LEFT JOIN project ON project.id = reward.project_id";
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
