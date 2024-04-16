<?php

namespace Models;

class RewardModel extends BasePriorityModel
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
        'purchased_count',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];
}
