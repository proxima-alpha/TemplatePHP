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
        $query = "REPLACE INTO artist_group(artist_id, project_id, priority) VALUES";
        $prefix = '';
        foreach ($artist_ids as $index => $artist_id) {
            $query .= $prefix . "('" . $artist_id . "','" . $project_id . "', '" . ($index + 1) . "')";
            $prefix = ',';
        }
        return $query . ';';
    }

    static function getRewardGroupCreate($artist_ids, $project_id, $reward_id)
    {
        $query = "REPLACE INTO artist_group(artist_id, project_id, reward_id, priority) VALUES";
        $prefix = '';
        foreach ($artist_ids as $index => $artist_id) {
            $query .= $prefix . "('" . $artist_id . "','" . $project_id. "','" . $reward_id . "', '" . ($index + 1) . "')";
            $prefix = ',';
        }
        return $query . ';';
    }

    static function getPurchaseItemRewardCreate($artists, $purchase_items)
    {
        $query = "INSERT INTO purchase_item_reward(artist_id, purchase_item_id) VALUES";
        $prefix = '';
        foreach ($artists as $i => $artist) {
            foreach($purchase_items as $j => $purchase_item) {
                ServerLogger::log($i , $j);
                $query .= $prefix . "('" . $artist['id'] . "','" . $purchase_item['id'] . "')";
                $prefix = ',';
            }
        }
        return $query . ';';
    }
}
