<?php

namespace Models;

class ProjectModel extends BaseModel
{
    protected $table = 'project';
    protected $allowedFields = [
        'id',
        'project_date_fragment_id',
        'artist_group_id',
        'title_image_id',
        'status',
        'start_date',
        'end_date',
        'title',
        'content',
        'is_deleted',
        'is_authenticated',
        'updated_at',
        'created_at',
    ];
}
