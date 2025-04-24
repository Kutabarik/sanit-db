<?php

namespace Kutabarik\SanitDb\Rules;

use RuntimeException;

class RulesManager
{
    public function __construct(
        private string $filePath
    ) {}

    public function all(): array
    {
        if (! file_exists($this->filePath)) {
            return ['tables' => []];
        }

        $json = file_get_contents($this->filePath);
        $rules = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON: '.json_last_error_msg());
        }

        return $rules;
    }

    public function getTableRules(string $table): array
    {
        $rules = $this->all();

        return $rules['tables'][$table] ?? [];
    }

    public function addRule(string $table, array $newRule): void
    {
        $rules = $this->all();
        $rules['tables'][$table][] = $newRule;
        $this->save($rules);
    }

    public function updateRule(string $table, int $index, array $updatedRule): void
    {
        $rules = $this->all();

        if (! isset($rules['tables'][$table][$index])) {
            throw new RuntimeException("Rule at index $index not found in table '$table'.");
        }

        $rules['tables'][$table][$index] = $updatedRule;
        $this->save($rules);
    }

    public function deleteRule(string $table, int $index): void
    {
        $rules = $this->all();

        if (! isset($rules['tables'][$table][$index])) {
            throw new RuntimeException("Rule at index $index not found in table '$table'.");
        }

        unset($rules['tables'][$table][$index]);
        $rules['tables'][$table] = array_values($rules['tables'][$table]);
        $this->save($rules);
    }

    public function save(array $rules): void
    {
        RulesValidator::validate($rules);

        $encoded = json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($encoded === false) {
            throw new RuntimeException('Failed to encode rules to JSON.');
        }

        file_put_contents($this->filePath, $encoded);
    }
}
