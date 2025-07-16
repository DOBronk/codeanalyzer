<?php

namespace App\Utilities;

use Illuminate\Support\Str;

class Results
{
    public static function hasImprovements($arr): array
    {
        if (empty($arr)) {
            return [];
        }

        return array_filter($arr, function ($item) {
            if (is_array($item)) {
                return true;
            }

            return ! Str::contains($item, ['1', 'OK', 'NOT APPLICABLE', 'N/A'], true);
        });
    }

    public static function walkResults(&$value, $key): void
    {
        if (is_array($value)) {
            array_walk($value, [__CLASS__, __METHOD__]);
            $value = "{$key}: ".implode("\r\n", $value);
        } else {
            $value = "{$key}: {$value}";
        }
    }

    public static function resultsToString($arr): string
    {
        if ($results = self::hasImprovements($arr)) {
            array_walk($results, [__CLASS__, 'walkResults']);

            return implode("\r\n", $results);
        }

        return '';
    }
}
