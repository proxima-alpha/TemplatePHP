<?php

namespace Models;

class ArtistModel extends BaseModel
{
    protected $table = 'artist';
    protected $allowedFields = [
        'id',
        'profile_id',
        'name',
        'job',
        'introduction',
        'name_en',
        'job_en',
        'introduction_en',
        'is_posted',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];
}
