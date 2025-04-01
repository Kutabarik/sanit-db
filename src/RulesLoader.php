<?php

namespace Kutabarik\SanitDb;

use RuntimeException;

class RulesLoader
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->validate($rules);
        $this->rules = $rules;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    private function validate(array $rules): void
    {
        if (!isset($rules['table']) || !isset($rules['checks']) || !is_array($rules['checks'])) {
            throw new RuntimeException("Invalid structure in rules.");
        }

        foreach ($rules['checks'] as $i => $check) {
            if (!isset($check['type'])) {
                throw new RuntimeException("Missing 'type' in check #$i");
            }

            if ($check['type'] === 'duplicates' && !isset($check['fields'])) {
                throw new RuntimeException("Missing 'fields' in duplicates check #$i");
            }

            if ($check['type'] === 'format' && (!isset($check['field']) || !isset($check['regex']))) {
                throw new RuntimeException("Missing 'field' or 'regex' in format check #$i");
            }
        }
    }
}
