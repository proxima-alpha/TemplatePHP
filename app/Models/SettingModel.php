<?php

namespace Models;

/*
 * column_name      type            comment
 * -----------------------------------------
 * id               BIGINT
 * code             VARCHAR(20)
 * type             VARCHAR(20)     text|long-text|number|bool
 * name             VARCHAR(50)
 * value            TEXT
 * created_at       DATETIME
 * updated_at       DATETIME
 */

class SettingModel extends BaseModel
{
    protected $table = 'setting';
    protected $allowedFields = [
        'id',
        'code',
        'type',
        'name',
        'value',
        'is_editable',
        'created_at',
        'updated_at',
    ];

    public function initialize(): void
    {
        $this->createIfNotExist(['code' => 'gmail-password-key'], [
            "code" => "gmail-password-key",
            "type" => "text",
            "name" => "메일 송신용 지메일 비밀번호 키",
        ]);
        $this->createIfNotExist(['code' => 'gmail-address'], [
            "code" => "gmail-address",
            "type" => "text",
            "name" => "메일 송신용 지메일 메일주소",
        ]);
    }
}
