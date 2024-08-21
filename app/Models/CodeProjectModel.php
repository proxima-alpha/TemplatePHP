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
        'name_jp',
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
            "name_jp" => "アーティスト",
            "priority" => "1",
        ]);
        $this->createIfNotExist(['code' => 'actor'], [
            "code" => "actor",
            "name" => "배우",
            "name_en" => "Actor",
            "name_jp" => "俳優",
            "priority" => "2",
        ]);
        $this->createIfNotExist(['code' => 'creator'], [
            "code" => "creator",
            "name" => "크리에이터",
            "name_en" => "Creator",
            "name_jp" => "クリエイター",
            "priority" => "3",
        ]);
    }
}
