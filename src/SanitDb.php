<?php

namespace Kutabarik\SanitDb;

use Kutabarik\SanitDb\Database\DatabaseRepository;
use Kutabarik\SanitDb\Analyzer\DuplicateAnalyzer;
use Kutabarik\SanitDb\Analyzer\FormatAnalyzer;
use Kutabarik\SanitDb\Analyzer\AnalyzerInterface;

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
     * @param array $rules The rule definitions.
     * @return array The analysis results.
     */
    public function processFromArray(array $rules): array
    {
        $loader = new RulesLoader($rules);
        return $this->processInternal($loader->getRules());
    }

    /**
     * Internal processing method: applies all defined checks to the data.
     *
     * @param array $rules Validated rules.
     * @return array Analysis results by check type.
     */
    private function processInternal(array $rules): array
    {
        $table = $rules['table'];
        $checks = $rules['checks'];
        $results = [];

        foreach ($checks as $check) {
            $fields = $check['fields'] ?? [$check['field']];
            $data = $this->db->getTableData($table, $fields);

            $analyzer = $this->createAnalyzer($check['type'], $data, $check);

            $results[$check['type']][] = $analyzer->analyze();
        }

        return $results;
    }

    /**
     * Creates an analyzer depending on the type of check.
     *
     * @param string $type Type of check.
     * @param array $data Data from the database.
     * @param array $params Parameters from rules.
     * @return AnalyzerInterface
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
