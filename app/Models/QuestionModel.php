<?php

namespace Models;

use Exception;

class QuestionModel extends BaseModel
{
    protected $table = 'question';
    protected $allowedFields = [
        'id',
        'question_board_id',
        'questioner_id',
        'respondent_id',
        'status',
        'question_comment',
        'respond_comment',
        'created_at',
        'updated_at',
    ];

    /**
     * select 문을 호출하는 기능
     * @throws Exception
     */
    public function get($condition = null, $limit = null): array
    {
        $query = "SELECT question.*,
                questioner.name AS questioner_name, questioner.email AS questioner_email,questioner.is_notification AS questioner_is_notification,
                respondent.name AS respondent_name
                FROM question
                LEFT JOIN user AS questioner ON questioner.id = question.questioner_id
                LEFT JOIN user AS respondent ON respondent.id = question.respondent_id";
        $values = [];
        if ($condition) {
            $set = $this->getConditionSet($condition);
            $values = array_merge($values, $set['values']);
            $query .= " " . $set['query'];
        }
        $query .= " ORDER BY " . $this->table . ".created_at DESC";
        if (isset($limit)) {
            $query .= " LIMIT " . $limit['offset'] . ", " . $limit['value'];
        }
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => $values,
            ],
        ]);
    }
}
