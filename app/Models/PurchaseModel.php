<?php

namespace Models;

class PurchaseModel extends BaseModel
{
    protected $table = 'purchase';
    protected $allowedFields = [
        'id',
        'user_id',
        'reword_id',
        'status',
        'paid',
        'refunded',
        'purchaser_name',
        'purchaser_email',
        'created_at',
        'updated_at',
    ];
}
