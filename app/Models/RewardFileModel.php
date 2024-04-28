<?php

namespace Models;

class RewardFileModel extends BaseModel
{
    protected $table = 'reward_file';
    protected $allowedFields = [
        'id',
        'purchase_item_id',
        'type',
        'path',
        'symbolic_path',
        'relative_path',
        'file_name',
        'mime_type',
        'time',
        'width',
        'height',
        'created_at',
    ];
}
