<?php

namespace Kutabarik\SanitDb;

use Kutabarik\SanitDb\Analyzer\AnalyzerInterface;
use Kutabarik\SanitDb\Analyzer\DuplicateAnalyzer;
use Kutabarik\SanitDb\Analyzer\FormatAnalyzer;
use Kutabarik\SanitDb\Database\DatabaseRepository;

class SanitDb
{
    private DatabaseRepository $db;

    public function __construct(DatabaseRepository $db)
    {
        $this->db = $db;
    }

    /**
     * Starts the data analysis process using an array of rules (e.g. from config).
     *
     * @param  string  $rulesFile  Path to the rules file.
     * @return array The analysis results.
     */
    public function process(string $rulesFile): array
    {
        $loader = new RulesLoader($rulesFile);
        $allRules = $loader->getRules();
        $results = [];

        foreach ($allRules['tables'] as $table => $checks) {
            foreach ($checks as $check) {
                $fields = $check['fields'] ?? [$check['field']];
                $data = $this->db->getTableData($table, $fields);
                $analyzer = $this->createAnalyzer($check['type'], $data, $check);
                $results[$table][$check['type']][] = $analyzer->analyze();
            }
        }

        return $results;
    }

    /**
     * Creates an analyzer depending on the type of check.
     *
     * @param  string  $type  Type of check.
     * @param  array  $data  Data from the database.
     * @param  array  $params  Parameters from rules.
     */
    private function createAnalyzer(string $type, array $data, array $params): AnalyzerInterface
    {
        return match ($type) {
            'duplicates' => new DuplicateAnalyzer($data, $params['fields']),
            'format' => new FormatAnalyzer($data, $params['field'], $params['regex']),
            default => throw new \InvalidArgumentException("Unknown rule type: {$type}")
        };
    }
}
