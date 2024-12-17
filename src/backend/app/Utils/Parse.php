<?php

namespace App\Utils;

class Parse
{
    public static function parseBoolean($value): bool
    {
        if (is_bool($value) || is_numeric($value)) {
            return (bool) $value;
        }

        return in_array(strtolower($value), ['true', '1', 'on', 'sim'], true);
    }
}
