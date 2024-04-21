<?php

namespace Models;

class PurchaseItemModel extends BaseModel
{
    protected $table = 'purchase_item';
    protected $allowedFields = [
        'id',
        'purchase_id',
        'code_reward_request_id',
        'status',
        'price',
        'inquirer_name',
        'inquirer_email',
        'inquirer_comment',
        'memo',
        'is_mine',
        'is_refunded',
        'is_agreed',
        'created_at',
        'updated_at',
    ];
}
