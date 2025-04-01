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
     * Starts the data analysis process based on the rules from a file.
     *
     * @param string $rulesFile Path to the JSON file with rules.
     * @return array The analysis result.
     */
    public function process(string $rulesFile): array
    {
        $rules = RulesLoader::loadFromFile($rulesFile);
        $table = $rules['table'];
        $checks = $rules['checks'];

        $results = [];

        foreach ($checks as $check) {
            $data = $this->db->getTableData($table, $check['fields']);
            $analyzer = $this->createAnalyzer($check['type'], $data, $check);
            $results[$check['type']] = $analyzer->analyze();
        }

        return $results;
    }

    /**
     * Creates an analyzer depending on the type of check.
     *
     * @param string $type Type of check.
     * @param array $data Data from the database.
     * @param array $params Additional parameters from the rules.json.
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