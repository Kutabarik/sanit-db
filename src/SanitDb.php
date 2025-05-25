<?php

namespace Kutabarik\SanitDb;

use Kutabarik\SanitDb\Rules\RulesLoader;
use Kutabarik\SanitDb\Database\RepositoryInterface;

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

    public function processAndDelete(string $rulesFile): array
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

            foreach ($badEntries as $entry) {
                if (! isset($entry['row']['id'])) {
                    throw new \RuntimeException("Missing 'id' for deletion in table {$rule['table']}");
                }
                $this->db->deleteRows($rule['table'], ['id' => $entry['row']['id']]);
            }
        }

        return $results;
    }
}
