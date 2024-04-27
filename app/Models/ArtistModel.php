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
        'name_en',
        'job_en',
        'introduction_en',
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
    public function get($condition = null, $limit = null, $isPriority = false): array
    {
        $query = "SELECT artist.*, code_artist.name as code_artist, code_artist.name_en as code_artist_en, code_artist.code as code FROM artist LEFT JOIN code_artist ON code_artist.id = artist.code_artist_id";
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
        $query = "SELECT COUNT(*) AS cnt FROM artist LEFT JOIN code_artist ON code_artist.id = artist.code_artist_id";
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
