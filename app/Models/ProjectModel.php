<?php

namespace Models;

class ProjectModel extends BaseModel
{
    protected $table = 'project';
    protected $allowedFields = [
        'id',
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
        'access_hash',
        'password',
        'priority',
        'updated_at',
        'created_at',
    ];

    public function get($condition = null, $limit = null, $isPriority = false): array
    {
        $builder = !$isPriority ? $this->builder()->orderBy("created_at", "DESC") :
            $this->builder()->orderBy("priority", "ASC");
        if (isset($limit)) {
            $builder = $builder->limit($limit['value'], $limit['offset']);
        }
        return $builder->getWhere($condition)->getResultArray();
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
