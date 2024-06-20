<?php

namespace Models;

class QuestionBoardModel extends BaseModel
{
    protected $table = 'question_board';
    protected $allowedFields = [
        'id',
        'code',
        'alias',
        'description',
        'default_accept_comment',
        'is_public',
        'is_time_select',
        'is_deletable',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    public function initialize(): void
    {
        $this->createIfNotExist(['code' => 'inquiry'], [
            "code" => "inquiry",
            "type" => "table",
            "alias" => "inquiry",
            "description" => "문의하기 게시판",
            "is_editable" => "0",
            "is_reply" => "1",
            "is_deletable" => "0",
        ]);
    }
}
