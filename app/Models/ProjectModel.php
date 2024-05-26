<?php

namespace Models;

class ProjectModel extends BaseModel
{
    protected $table = 'project';
    protected $allowedFields = [
        'id',
        'code_project_id',
        'project_image_id',
        'status',
        'start_date',
        'end_date',
        'title',
        'content',
        'title_en',
        'content_en',
        'is_deleted',
        'is_authenticated',
        'is_posted',
        'is_posted_popular',
        'access_hash',
        'password',
        'priority',
        'updated_at',
        'created_at',
    ];

    /**
     * select 문을 호출하는 기능
     * artist_code 를 조인하기 위해서 override
     * @return array
     * @throws Exception
     */
    public function get($condition = null, $limit = null, $isPriority = false): array
    {
        $query = "SELECT project.*, code_project.name AS code_project, code_project.name_en AS code_project_en, code_project.code AS code FROM project".
            " LEFT JOIN code_project ON code_project.id = project.code_project_id";
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
        $query = "SELECT COUNT(*) AS cnt FROM project LEFT JOIN code_project ON code_project.id = project.code_project_id";
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

    public function getPreviousProjects(): array
    {
        $today = date("Y-m-d");
        $query = "SELECT * FROM project WHERE status = 'open' AND end_date < '" . $today . "' ORDER BY created_at DESC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);
    }

    public function getReport($id)
    {
        $query = "SELECT (SELECT COUNT(*) AS cnt FROM reward WHERE project_id = '" . $id . "') AS total_count," .
            " (SELECT COUNT(*) AS cnt FROM reward WHERE is_deleted = 0 AND project_id = '" . $id . "') AS active_count";
        $result = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);
        return $result[0];
    }
}
