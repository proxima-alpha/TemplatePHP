<?php

namespace Models;

class PurchaseModel extends BaseModel
{
    protected $table = 'purchase';
    protected $allowedFields = [
        'id',
        'user_id',
        'reward_id',
        'status',
        'paid',
        'refunded',
        'purchaser_name',
        'purchaser_email',
        'pg',
        'imp_uid',
        'merchant_uid',
        'created_at',
        'updated_at',
    ];
}
