<?php

namespace Models;

class PurchaseItemGroupModel extends BaseModel
{
    protected $table = 'purchase_item_group';
    protected $allowedFields = [
        'id',
        'purchase_item_id',
        'reward_file_id',
        'created_at',
    ];
}
