<?php

namespace Kutabarik\SanitDb;

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
            throw new RuntimeException('Invalid JSON in rules file: '.json_last_error_msg());
        }

        $this->validate($rules);

        return $rules;
    }

    private function validate(array $rules): void
    {
        if (! isset($rules['tables']) || ! is_array($rules['tables'])) {
            throw new RuntimeException("Missing or invalid 'tables' in rules.");
        }

        foreach ($rules['tables'] as $table => $checks) {

            if (! is_array($checks)) {
                throw new RuntimeException("Invalid checks for table '{$table}'");
            }

            foreach ($checks as $i => $check) {
                if (! isset($check['type'])) {
                    throw new RuntimeException("Missing 'type' in check #$i");
                }

                if ($check['type'] === 'duplicates' && ! isset($check['fields'])) {
                    throw new RuntimeException("Missing 'fields' in duplicates check #$i");
                }

                if ($check['type'] === 'format' && (! isset($check['field']) || ! isset($check['regex']))) {
                    throw new RuntimeException("Missing 'field' or 'regex' in format check #$i");
                }
            }
        }
    }
}
