<?php

namespace App\Helpers;

class QueryHelper
{
    static function getFileAllocation($file_id, $identifier, $file_name, $inserted_row_id, $index = 0)
    {
        return "UPDATE custom_file SET identifier = NULL, " . $file_name . " = '" . $inserted_row_id . "', priority = " . $index + 1
            . " WHERE id = '" . $file_id . "' AND identifier = '" . $identifier . "'";
    }
}
