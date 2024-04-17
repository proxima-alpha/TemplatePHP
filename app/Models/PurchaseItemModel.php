<?php

namespace Models;

class PurchaseItemModel extends BaseModel
{
    protected $table = 'purchase_item';
    protected $allowedFields = [
        'id',
        'purchase_id',
        'code_reaction_id',
        'status',
        'price',
        'requirer_name',
        'requirer_email',
        'requirer_comment',
        'memo',
        'is_refunded',
        'created_at',
        'updated_at',
    ];
}
