<?php

namespace Models;

class CodeRewardRequestModel extends BaseModel
{
    protected $table = 'code_reward_request';
    protected $allowedFields = [
        'id',
        'code',
        'name',
        'name_en',
        'is_active',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    public function initialize(): void
    {
        $this->createIfNotExist(['code' => 'birthday'], [
            "code" => "birthday",
            "name" => "생일",
            "name_en" => "Birthday",
        ]);
        $this->createIfNotExist(['code' => 'anniversary'], [
            "code" => "anniversary",
            "name" => "기념일",
            "name_en" => "Anniversary",
        ]);
        $this->createIfNotExist(['code' => 'encouragement'], [
            "code" => "encouragement",
            "name" => "응원",
            "name_en" => "Encouragement",
        ]);
        $this->createIfNotExist(['code' => 'question'], [
            "code" => "question",
            "name" => "질문",
            "name_en" => "Question",
        ]);
    }
}
