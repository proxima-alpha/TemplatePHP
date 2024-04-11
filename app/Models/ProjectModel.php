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
        'is_deleted',
        'is_authenticated',
        'is_posted',
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
}
