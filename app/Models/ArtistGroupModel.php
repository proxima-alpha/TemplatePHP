<?php

namespace Models;

use App\Helpers\ServerLogger;

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
            " GROUP BY artist_group.artist_id, artist_group.project_id, artist_group.priority".
            " ORDER BY artist_group.priority ASC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [$project_id],
            ],
        ]);
    }
    public function getArtistsForReward($project_id): array
    {
        $query = "SELECT artist.*, artist_group.reward_id AS reward_id FROM project" .
            " LEFT JOIN artist_group ON project.id = artist_group.project_id" .
            " LEFT JOIN artist ON artist.id = artist_group.artist_id" .
            " WHERE project.id = " . $project_id . " AND project.is_deleted = 0 AND artist.is_deleted = 0 AND reward_id IS NOT NULL" .
            " GROUP BY artist_group.artist_id, artist_group.project_id, artist_group.priority, artist_group.reward_id".
            " ORDER BY artist_group.priority ASC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [$project_id],
            ],
        ]);
    }
}
