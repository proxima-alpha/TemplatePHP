<?php

namespace Models;

class RewardFileModel extends BasePriorityModel
{
    protected $table = 'reward_file';
    protected $allowedFields = [
        'id',
        'purchase_item_id',
        'type',
        'path',
        'symbolic_path',
        'file_name',
        'thumb_file_name',
        'mime_type',
        'time',
        'width',
        'height',
        'created_at',
    ];
}
