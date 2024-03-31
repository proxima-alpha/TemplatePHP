<?php

namespace Models;

/*
 * column_name      type            comment
 * -----------------------------------------
 * id               BIGINT
 * user_id          INT             FK(user)
 * topic_id         INT             FK(topic)
 * reply_id         INT             FK(reply)
 * content          TEXT
 * is_deleted       TINYINT(1)
 * is_public        TINYINT(1)
 * depth            INT
 * created_at       DATETIME
 * updated_at       DATETIME
 */

use Exception;

class ReplyModel extends BaseModel
{
    protected $table = 'reply';
    protected $allowedFields = [
        'id',
        'user_id',
        'topic_id',
        'reply_id',
        'content',
        'is_deleted',
        'is_public',
        'depth',
        'created_at',
        'updated_at',
    ];

    /**
     * @throws Exception
     */
    public function get($condition = null, $limit = null): array
    {
        $query = "SELECT reply.*, user.name AS user_name,
            topic.title AS topic_title,
            board.code AS board_code, board.alias AS board_alias FROM reply
            INNER JOIN user ON user.id = reply.user_id
            INNER JOIN topic ON topic.id = reply.topic_id
            INNER JOIN board ON board.id = topic.board_id";
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
