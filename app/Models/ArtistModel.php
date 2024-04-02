<?php

namespace Models;

class ArtistModel extends BaseModel
{
    protected $table = 'artist';
    protected $allowedFields = [
        'id',
        'code_artist_id',
        'profile_id',
        'name',
        'job',
        'introduction',
        'password',
        'is_public',
        'is_posted',
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
    public function get($condition = null, $limit = null): array
    {
        $query = "SELECT artist.*, code_artist.name as code_artist FROM artist LEFT JOIN code_artist ON code_artist.id = artist.code_artist_id";
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
     * artist_code 를 조인하기 위해서 override
     * @throws Exception
     */
    public function getPaginated($pagination, $condition = null): array
    {
        $total = $this->builder()->getWhere($condition)->getNumRows();
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
        $result = $this->get($condition, [
            'value' => $per_page,
            'offset' => $offset
        ]);

        return [
            'array' => $result,
            'pagination' => $this->parsePagination($page, $per_page, $total, $total_page),
        ];
    }
}
