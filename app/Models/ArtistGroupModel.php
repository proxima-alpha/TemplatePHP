<?php

namespace Models;

class ArtistGroupModel extends BaseModel
{
    protected $table = 'artist_group';
    protected $allowedFields = [
        'id',
        'artist_id',
        'project_id',
        'reward_id',
        'priority',
        'created_at',
    ];


    public function getArtists($project_id): array
    {
        $query = "SELECT artist.* FROM project" .
            " LEFT JOIN artist_group ON project.id = artist_group.project_id" .
            " LEFT JOIN artist ON artist.id = artist_group.artist_id" .
            " WHERE project.id = " . $project_id . " AND project.is_deleted = 0 AND artist.is_deleted = 0 AND reward_id IS NULL" .
            " GROUP BY artist_group.artist_id, artist_group.project_id, artist_group.priority" .
            " ORDER BY artist_group.priority ASC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [$project_id],
            ],
        ]);
    }

    /**
     * reward id 가 없는 경우 해당 project 의 프로젝트의 전체 reward 용 artist 조회,
     * 아닌 경우 해당 reward 의 reward 용 artist 할당
     * @param $project_id
     * @param $reward_id
     * @return array
     * @throws \Exception
     */
    public function getArtistsForReward($project_id, $reward_id = null): array
    {
        $query = "SELECT artist.*, artist_group.reward_id AS reward_id FROM project" .
            " LEFT JOIN artist_group ON project.id = artist_group.project_id" .
            " LEFT JOIN artist ON artist.id = artist_group.artist_id" .
            " WHERE project.id = " . $project_id . " AND project.is_deleted = 0 AND artist.is_deleted = 0 AND artist_group.reward_id IS NOT NULL" .
            (isset($reward_id) ? "  AND artist_group.reward_id = " . $reward_id : '') .
            " GROUP BY artist_group.artist_id, artist_group.project_id, artist_group.priority, artist_group.reward_id" .
            " ORDER BY artist_group.priority ASC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [],
            ],
        ]);
    }
}
