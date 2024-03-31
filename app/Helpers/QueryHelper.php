<?php

namespace App\Helpers;

class QueryHelper
{
    static function getFileAllocation($file_id, $identifier, $field_name, $inserted_row_id, $index = 0)
    {
        return "UPDATE custom_file SET identifier = NULL, " . $field_name . " = '" . $inserted_row_id . "', priority = " . $index + 1
            . " WHERE id = '" . $file_id . "' AND identifier = '" . $identifier . "'";
    }

    static function getFileIndexUpdate($join_field_name, $id, $file_id, $identifier, $index = 0)
    {
        return "UPDATE custom_file SET identifier = NULL, " . $join_field_name . " = '" . $id . "', priority = " . $index + 1
            . " WHERE (id = '" . $file_id . "' AND " . $join_field_name . " = '" . $id . "') OR (id = '" . $file_id . "' AND identifier = '" . $identifier . "')";
    }
}
