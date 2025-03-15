<?php

namespace Kutabarik\SanitDb;

class RulesLoader
{
    public static function loadFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Rules file not found: $filePath");
        }

        $json = file_get_contents($filePath);
        $rules = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in rules file: " . json_last_error_msg());
        }

        if (!isset($rules['table']) || !isset($rules['checks']) || !is_array($rules['checks'])) {
            throw new \RuntimeException("Invalid structure in rules file.");
        }

        return $rules;
    }
}
