<?php

namespace Models;

class CustomFileModel extends BasePriorityModel
{
    protected $table = 'custom_file';
    protected $allowedFields = [
        'id',
        'topic_id',
        'artist_id',
        'type',
        'target',
        'path',
        'path',
        'symbolic_path',
        'relative_path',
        'file_name',
        'mime_type',
        'time',
        'width',
        'height',
        'identifier',
        'priority',
        'created_at',
    ];

    public function getLogos(): array
    {
        $queryResult = $this->db->query("SELECT * FROM {$this->table} WHERE (target = 'logo' OR target = 'footer_logo' OR target = 'favicon' OR target = 'open_graph') AND type = 'image'")->getResultArray();
        $result = [];
        foreach ($queryResult as $row) {
            $target = $row['target'];
            switch ($target) {
                case 'logo':
                case 'footer_logo':
                case 'favicon':
                case 'open_graph':
                    $result[$target] = $row;
            }
        }
        return $result;
    }

    public function getGraphicSettings(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} WHERE 
                              target = 'main' AND type = 'video' OR 
                              target = 'logo' AND type = 'image' OR 
                              target = 'footer_logo' AND type = 'image' OR 
                              target = 'favicon' AND type = 'image' OR 
                              target = 'open_graph' AND type = 'image'")->getResultArray();
    }
}
