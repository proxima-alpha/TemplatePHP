<?php

namespace Models;

class ArtistGroupModel extends BaseModel
{
    protected $table = 'artist_group';
    protected $allowedFields = [
        'id',
        'artist_id',
        'project_id',
        'created_at',
    ];
}
