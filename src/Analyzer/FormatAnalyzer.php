<?php

namespace Kutabarik\SanitDb\Analyzer;

class FormatAnalyzer implements AnalyzerInterface
{
    private array $data;
    private string $field;
    private string $regex;

    public function __construct(array $data, string $field, string $regex)
    {
        $this->data = $data;
        $this->field = $field;
        $this->regex = $regex;
    }

    /**
     * Checks if the data matches the regular expression.
     *
     * @return array List of rows where the data does not match the format.
     */
    public function analyze(): array
    {
        $invalidEntries = [];

        foreach ($this->data as $row) {
            if (!isset($row[$this->field])) {
                continue;
            }

            if (!preg_match("/{$this->regex}/", $row[$this->field])) {
                $invalidEntries[] = [
                    'row' => $row,
                    'invalid_value' => $row[$this->field],
                    'expected_format' => $this->regex
                ];
            }
        }

        return $invalidEntries;
    }
}
