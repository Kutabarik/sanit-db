<?php

namespace Kutabarik\SanitDb\Handlers;

use Kutabarik\SanitDb\Contracts\RuleStrategy;

class DuplicateRule implements RuleStrategy
{
    public function supports(string $type): bool
    {
        return $type === 'duplicates';
    }

    public function analyze(array $data, array $rule): array
    {
        $fields = $rule['fields'];
        $seen = [];
        $duplicates = [];

        foreach ($data as $row) {
            $key = implode('|', array_map(fn ($f) => $row[$f] ?? '', $fields));
            $id = $row['id'] ?? null;

            if (isset($seen[$key])) {
                $duplicates[] = [
                    'row' => $row,
                    'error' => 'Duplicate entry found',
                    'duplicate_ids' => [$seen[$key], $id],
                ];
            } else {
                $seen[$key] = $id;
            }
        }

        return $duplicates;
    }
}
