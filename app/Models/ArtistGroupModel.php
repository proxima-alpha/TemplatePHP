<?php

namespace Models;

class ArtistGroupModel extends BaseModel
{
    protected $table = 'artist_group';
    protected $allowedFields = [
        'id',
        'artist_id',
        'project_id',
        'priority',
        'created_at',
    ];


    public function getArtists($project_id): array
    {
        $query = "SELECT artist.*, code_artist.name as code_artist FROM project" .
            " LEFT JOIN artist_group ON project.id = artist_group.project_id" .
            " LEFT JOIN artist ON artist.id = artist_group.artist_id" .
            " LEFT JOIN code_artist ON code_artist.id = artist.code_artist_id" .
            " WHERE project.id = " . $project_id . " AND project.is_deleted = 0 AND artist.is_deleted = 0" .
            " ORDER BY artist_group.priority ASC";
        return BaseModel::transaction($this->db, [
            [
                "query" => $query,
                "values" => [$project_id],
            ],
        ]);
    }
}
