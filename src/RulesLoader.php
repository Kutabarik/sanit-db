<?php

namespace Kutabarik\SanitDb;

use RuntimeException;

class RulesLoader
{
    public static function loadFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Rules file not found: $filePath");
        }

        $json = file_get_contents($filePath);
        $rules = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in rules file: " . json_last_error_msg());
        }

        foreach ($rules['checks'] as $i => $check) {
            if (!isset($check['type'])) {
                throw new RuntimeException("Missing 'type' in check #$i");
            }

            if ($check['type'] === 'duplicates' && !isset($check['fields'])) {
                throw new RuntimeException("Missing 'fields' in duplicates check #$i");
            }

            if ($check['type'] === 'format' && (!isset($check['field']) || !isset($check['regex']))) {
                throw new RuntimeException("Missing 'field' or 'regex' in format check #$i");
            }
        }

        return $rules;
    }
}
