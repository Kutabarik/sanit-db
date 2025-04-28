<?php

namespace Kutabarik\SanitDb\Analyzer;

class DuplicateAnalyzer implements AnalyzerInterface
{
    private array $data;

    private array $fields;

    public function __construct(array $data, array $fields)
    {
        $this->data = $data;
        $this->fields = $fields;
    }

    public function analyze(): array
    {
        $seen = [];
        $duplicates = [];

        foreach ($this->data as $row) {
            $key = implode('|', array_map(fn ($field) => $row[$field] ?? '', $this->fields));
            if (isset($seen[$key])) {
                $duplicates[] = [
                    'row' => $row,
                    'error' => 'Duplicate entry detected',
                    'details' => [
                        'duplicate_fields' => $this->fields,
                        'duplicate_key' => $key,
                    ],
                ];

            } else {
                $seen[$key] = true;
            }
        }

        return $duplicates;
    }
}
