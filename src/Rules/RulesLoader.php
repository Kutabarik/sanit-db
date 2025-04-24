<?php

namespace Kutabarik\SanitDb\Rules;

use RuntimeException;

class RulesLoader
{
    private array $rules;

    public function __construct(string $filePath)
    {
        $this->rules = $this->loadFromFile($filePath);
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    private function loadFromFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("Rules file not found: $filePath");
        }

        $json = file_get_contents($filePath);
        $rules = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in rules file: " . json_last_error_msg());
        }

        RulesValidator::validate($rules);

        return $rules;
    }
}

