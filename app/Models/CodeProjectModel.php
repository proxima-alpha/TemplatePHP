<?php

namespace Models;

class CodeProjectModel extends BasePriorityModel
{
    protected $table = 'code_project';
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'name_en',
        'is_active',
        'is_deleted',
        'priority',
        'created_at',
        'updated_at',
    ];

    public function initialize(): void
    {
        $this->createIfNotExist(['code' => 'artist'], [
            "code" => "artist",
            "name" => "아티스트",
            "name_en" => "Artist",
            "priority" => "1",
        ]);
        $this->createIfNotExist(['code' => 'actor'], [
            "code" => "actor",
            "name" => "배우",
            "name_en" => "Actor",
            "priority" => "2",
        ]);
        $this->createIfNotExist(['code' => 'creator'], [
            "code" => "creator",
            "name" => "크리에이터",
            "name_en" => "Creator",
            "priority" => "3",
        ]);
    }
}
