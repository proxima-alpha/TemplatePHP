<?php

namespace Models;

class ExchangeRateModel extends BaseModel
{
    protected $table = 'exchange_rate';
    protected $allowedFields = [
        'id',
        'exchange_rate_id',
        'date',
        'data',
        'is_empty',
        'created_at',
    ];

}
