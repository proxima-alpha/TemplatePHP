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
        'name_jp',
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
            "name_jp" => "誕生日",
        ]);
        $this->createIfNotExist(['code' => 'anniversary'], [
            "code" => "anniversary",
            "name" => "기념일",
            "name_en" => "Anniversary",
            "name_jp" => "周年",
        ]);
        $this->createIfNotExist(['code' => 'encouragement'], [
            "code" => "encouragement",
            "name" => "응원",
            "name_en" => "Encouragement",
            "name_jp" => "奨励",
        ]);
        $this->createIfNotExist(['code' => 'question'], [
            "code" => "question",
            "name" => "질문",
            "name_en" => "Question",
            "name_jp" => "質問",
        ]);
    }
}
