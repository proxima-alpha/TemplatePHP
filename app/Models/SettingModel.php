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
        $this->createIfNotExist(['code' => 'main-content-text'], [
            "code" => "main-content-text",
            "type" => "long-text",
            "name" => "메인 화면 내용",
        ]);
        $codes = ['project', 'previous-project', 'artist', 'actor', 'creator'];

        foreach ($codes as $code) {
            $this->createIfNotExist(['code' => "main-show-".$code], [
                "code" => "main-show-".$code,
                "type" => "bool",
                "value" => "1",
                "is_editable" => "0",
                "name" => "메인 활성화",
            ]);
        }
    }

    public function getMainShowSettings() {
        $query = "SELECT * FROM setting WHERE code LIKE 'main-show-%'";
        $queryResult = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);

        $result = [];
        foreach($queryResult as $item) {
            $result[$item['code']] = $item['value'];
        }
        return $result;
    }
}
