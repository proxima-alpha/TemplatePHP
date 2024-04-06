<?php

namespace Models;

class RewardModel extends BaseModel
{
    protected $table = 'reward';
    protected $allowedFields = [
        'id',
        'project_id',
        'content',
        'total_count',
        'price',
        'limited_count',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];
}
