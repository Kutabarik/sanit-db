<?php

namespace Kutabarik\SanitDb\Handlers;

use Kutabarik\SanitDb\Contracts\RuleStrategy;

class RegexRule implements RuleStrategy
{
    public function supports(string $type): bool
    {
        return $type === 'regex';
    }

    public function analyze(array $data, array $rule): array
    {
        $field = $rule['field'];
        $regex = $rule['regex'];
        $results = [];

        foreach ($data as $row) {
            $value = $row[$field] ?? null;

            if (! is_string($value) || ! preg_match("/{$regex}/u", $value)) {
                $results[] = [
                    'row' => $row,
                    'error' => 'Regex check failed',
                    'details' => compact('field', 'regex'),
                ];
            }
        }

        return $results;
    }
}
