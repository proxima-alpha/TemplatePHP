<?php

namespace Models;

/*
 * column_name          type            comment
 * ---------------------------------------------
 * id                   BIGINT
 * code                 VARCHAR(20)
 * name                 VARCHAR(50)
 * path                 VARCHAR(255)
 * priority             INT
 * is_main_only         TINYINT(1)
 * has_local            TINYINT(1)
 * created_at           DATETIME
 * updated_at           DATETIME
 */

use CodeIgniter\Database\BaseResult;
use Exception;
use ReflectionException;

class CategoryModel extends BasePriorityModel
{
    protected $table = 'category';
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'path',
        'priority',
        'is_main_only',
        'has_local',
        'created_at',
        'updated_at',
    ];


    /**
     * select 문을 호출하는 기능
     * 하위 category_local 의 갯수 필드 추가를 위해 override
     * @throws Exception
     */
    public function get($condition = null, $limit = null): array
    {
        $query = "SELECT *, (SELECT COUNT(id) FROM category_local WHERE category_id = category.id) AS cnt FROM category";
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " ".$set['query'];
        }
        $query .= " ORDER BY " . $this->table . ".priority ASC";
        if(isset($limit)) {
            $query.= " LIMIT ".$limit['offset'].", ".$limit['value'];
        }
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
    }

    /**
     * insert 기능
     * - priority 추가를 위해 override
     * @param $data
     * @param bool $returnID
     * @return BaseResult|false|int|object|string
     * @throws ReflectionException
     */
    public function insert($data = null, bool $returnID = true)
    {
        $rowCount = $this->getCount() + 1;
        $data['priority'] = $rowCount;
        return parent::insert($data, $returnID);
    }

    /**
     * delete 기능
     * 제거 시 해당 priority 보다 큰 priority 들은 1씩 차감되어야
     * 'row 개수 = priority' 최대값 조건이 유지된다
     * @param $id
     * @param bool $purge
     * @return void
     * @throws Exception
     */
    public function delete($id = null, bool $purge = false): void
    {
        $result = $this->get(['id' => $id]);
        if (sizeof($result) == 0) {
            //todo setting language lang('Database.invalidEvent', [$method])
            throw new Exception("not exist");
        }
        $data = $result[0];
        BaseModel::transaction($this->db, [
            "DELETE FROM " . $this->table . " WHERE id = " . $id,
            "UPDATE " . $this->table . " SET priority = priority - 1 WHERE priority > " . $data['priority'],
        ]);
    }
}
