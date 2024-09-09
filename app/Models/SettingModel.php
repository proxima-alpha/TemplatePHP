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
        $this->createIfNotExist(['code' => 'imp-shop-id'], [
            "code" => "imp-shop-id",
            "type" => "text",
            "name" => "고객사 식별코드",
        ]);
        $this->createIfNotExist(['code' => 'imp-api-key'], [
            "code" => "imp-api-key",
            "type" => "text",
            "name" => "REST API KEY",
        ]);
        $this->createIfNotExist(['code' => 'imp-api-secret'], [
            "code" => "imp-api-secret",
            "type" => "text",
            "name" => "REST API Secret",
        ]);;
        $this->createIfNotExist(['code' => 'kakao-appkey'], [
            "code" => "kakao-appkey",
            "type" => "text",
            "name" => "카카오 APPKEY",
        ]);
        $this->createIfNotExist(['code' => 'kakao-restapikey'], [
            "code" => "kakao-restapikey",
            "type" => "text",
            "name" => "카카오 REST API KEY",
        ]);
        $this->createIfNotExist(['code' => 'naver-client-id'], [
            "code" => "naver-client-id",
            "type" => "text",
            "name" => "네이버 CLIENT ID",
        ]);
        $this->createIfNotExist(['code' => 'google-client-id'], [
            "code" => "google-client-id",
            "type" => "text",
            "name" => "구글 CLIENT ID",
        ]);
        $this->createIfNotExist(['code' => 'google-client-secret'], [
            "code" => "google-client-secret",
            "type" => "text",
            "name" => "구글 CLIENT SECRET",
        ]);
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
        $this->createIfNotExist(['code' => 'koreaexim-auth-key'], [
            "code" => "koreaexim-auth-key",
            "type" => "text",
            "name" => "한국수출입은행 인증 키",
        ]);
        $this->createIfNotExist(['code' => 'main-content-text'], [
            "code" => "main-content-text",
            "type" => "long-text",
            "name" => "메인 화면 내용",
        ]);
        $this->createIfNotExist(['code' => 'web-title'], [
            "code" => "web-title",
            "type" => "text",
            "name" => "페이지 상단 이름",
        ]);
        $this->createIfNotExist(['code' => 'guide-how-to-use_ko'], [
            "code" => "guide-how-to-use_ko",
            "type" => "long-text",
            "name" => "이용방법 (한글)",
        ]);
        $this->createIfNotExist(['code' => 'guide-how-to-use_en'], [
            "code" => "guide-how-to-use_en",
            "type" => "long-text",
            "name" => "이용방법 (영어)",
        ]);
        $this->createIfNotExist(['code' => 'guide-how-to-use_jp'], [
            "code" => "guide-how-to-use_jp",
            "type" => "long-text",
            "name" => "이용방법 (일본어)",
        ]);
        $this->createIfNotExist(['code' => 'main-link-guide_ko'], [
            "code" => "main-link-guide_ko",
            "type" => "text",
            "name" => "메인 이용가이드 링크 (한글)",
        ]);
        $this->createIfNotExist(['code' => 'main-link-guide_en'], [
            "code" => "main-link-guide_en",
            "type" => "text",
            "name" => "메인 이용가이드 링크 (영어)",
        ]);
        $this->createIfNotExist(['code' => 'main-link-guide_jp'], [
            "code" => "main-link-guide_jp",
            "type" => "text",
            "name" => "메인 이용가이드 링크 (일본어)",
        ]);
        $codes = ['project', 'previous-project', 'artist', 'actor', 'creator'];

        foreach ($codes as $code) {
            $this->createIfNotExist(['code' => "main-show-" . $code], [
                "code" => "main-show-" . $code,
                "type" => "bool",
                "value" => "1",
                "is_editable" => "0",
                "name" => "메인 활성화",
            ]);
        }
    }

    public function getMainShowSettings()
    {
        $query = "SELECT * FROM setting WHERE code LIKE 'main-show-%'";
        $queryResult = BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);

        $result = [];
        foreach ($queryResult as $item) {
            $result[$item['code']] = $item['value'];
        }
        return $result;
    }
}
