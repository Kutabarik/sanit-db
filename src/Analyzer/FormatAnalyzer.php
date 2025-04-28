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

    public function analyze(): array
    {
        $invalidEntries = [];

        foreach ($this->data as $row) {
            if (! isset($row[$this->field])) {
                continue;
            }

            if (! preg_match("/{$this->regex}/", $row[$this->field])) {
                $invalidEntries[] = [
                    'row' => $row,
                    'error' => "Field '{$this->field}' does not match format {$this->regex}",
                    'details' => [
                        'invalid_value' => $row[$this->field],
                        'expected_format' => $this->regex,
                    ],
                ];
            }
        }

        return $invalidEntries;
    }
}
