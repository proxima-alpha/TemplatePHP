<?php

namespace Models;

class ArtistModel extends BaseModel
{
    protected $table = 'artist';
    protected $allowedFields = [
        'id',
        'image_id',
        'name',
        'job',
        'introduction',
        'name_en',
        'job_en',
        'introduction_en',
        'name_jp',
        'job_jp',
        'introduction_jp',
        'is_posted',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];
}
