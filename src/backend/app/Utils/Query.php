<?php

namespace App\Utils;

class Query
{
    public static function qualifyColumn(string $table, string $column, string $alias = null): string
    {
        $alias = $alias ? " as $alias" : $alias;
        return "$table.$column$alias";
    }
}
