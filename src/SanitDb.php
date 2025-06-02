<?php

namespace Kutabarik\SanitDb;

use Kutabarik\SanitDb\Database\RepositoryInterface;
use Kutabarik\SanitDb\Rules\RulesLoader;

class SanitDb
{
    public function __construct(
        private RepositoryInterface $db,
        private RulesLoader $loader
    ) {}

    public function process(string $rulesFile): array
    {
        $this->loader->loadFromFile($rulesFile);
        $rules = $this->loader->getRules();
        $results = [];

        foreach ($rules as $rule) {
            $fields = array_unique(array_merge(['id'], $rule['fields'] ?? [$rule['field'] ?? 'id']));
            $data = $this->db->getTableData($rule['table'], $fields);

            $handler = $this->loader->getHandler($rule['type']);
            $badEntries = $handler->analyze($data, $rule);

            $results[$rule['table']][$rule['name'] ?? $rule['type']] = $badEntries;
        }

        return $results;
    }

    public function deleteFromResults(array $results): int
    {
        $count = 0;

        foreach ($results as $table => $rules) {
            foreach ($rules as $entries) {
                foreach ($entries as $entry) {
                    if (isset($entry['row']['id'])) {
                        $this->db->deleteRows($table, ['id' => $entry['row']['id']]);
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    public function processRules(array $rules): array
    {
        $results = [];

        foreach ($rules as $rule) {
            $fields = array_unique(array_merge(['id'], $rule->fields ?? [$rule->field ?? 'id']));
            $data = $this->db->getTableData($rule->table, $fields);
            $handler = $this->loader->getHandler($rule->type);
            $badEntries = $handler->analyze($data, $rule->toArray());

            $results[$rule->table][$rule->name ?? $rule->type] = $badEntries;
        }

        return $results;
    }
}
