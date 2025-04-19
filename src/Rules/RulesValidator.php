<?php

namespace Kutabarik\SanitDb\Rules;

use RuntimeException;

class RulesValidator
{
    public static function validate(array $rules): void
    {
        if (! isset($rules['tables']) || ! is_array($rules['tables'])) {
            throw new RuntimeException("Missing or invalid 'tables' in rules.");
        }

        foreach ($rules['tables'] as $table => $checks) {
            if (! is_array($checks)) {
                throw new RuntimeException("Invalid checks for table '{$table}'");
            }

            foreach ($checks as $i => $check) {
                if (! isset($check['type'])) {
                    throw new RuntimeException("Missing 'type' in check #$i");
                }

                if ($check['type'] === 'duplicates' && ! isset($check['fields'])) {
                    throw new RuntimeException("Missing 'fields' in duplicates check #$i");
                }

                if ($check['type'] === 'format' && (! isset($check['field']) || ! isset($check['regex']))) {
                    throw new RuntimeException("Missing 'field' or 'regex' in format check #$i");
                }
            }
        }
    }
}
