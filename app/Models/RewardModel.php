<?php

namespace Models;

class RewardModel extends BaseModel
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
        'identifier',
        'created_at',
        'updated_at',
    ];
}
