<?php

namespace Kutabarik\SanitDb\Handlers;

use Kutabarik\SanitDb\Contracts\RuleStrategy;

class IsNullRule implements RuleStrategy
{
    public function supports(string $type): bool
    {
        return $type === 'is_null';
    }

    public function analyze(array $data, array $rule): array
    {
        $field = $rule['field'];
        $results = [];

        foreach ($data as $row) {
            if (!isset($row[$field]) || $row[$field] === null || $row[$field] === '') {
                $results[] = [
                    'row' => $row,
                    'error' => 'Value is null or empty',
                    'details' => compact('field'),
                ];
            }
        }

        return $results;
    }
}
