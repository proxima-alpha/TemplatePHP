<?php

namespace App\Helpers;

class QueryHelper
{
    static function getFileAllocation($id_field_name, $id, $file_id, $identifier, $index = 0)
    {
        return "UPDATE custom_file SET identifier = NULL, " . $id_field_name . " = '" . $id . "', priority = " . $index + 1
            . " WHERE id = '" . $file_id . "' AND identifier = '" . $identifier . "'";
    }

    static function getFileIndexUpdate($id_field_name, $id, $file_id, $identifier, $index = 0)
    {
        return "UPDATE custom_file SET identifier = NULL, " . $id_field_name . " = '" . $id . "', priority = " . $index + 1
            . " WHERE (id = '" . $file_id . "' AND " . $id_field_name . " = '" . $id . "') OR (id = '" . $file_id . "' AND identifier = '" . $identifier . "')";
    }

    static function getGroupCreate($artist_ids, $project_id)
    {
        $query = "INSERT INTO artist_group(artist_id, project_id) VALUES";
        $prefix = '';
        foreach ($artist_ids as $artist_id) {
            $query .= $prefix."('" . $artist_id . "','" . $project_id . "')";
            $prefix = ',';
        }
        return $query.';';
    }
    static function getRewardCreate($artist_ids, $project_id)
    {
        $query = "INSERT INTO artist_group(artist_id, project_id) VALUES";
        $prefix = '';
        foreach ($artist_ids as $artist_id) {
            $query .= $prefix."('" . $artist_id . "','" . $project_id . "')";
            $prefix = ',';
        }
        return $query;
    }
}
