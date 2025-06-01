<?php

namespace Predicates\Attribute;

function RegEx(array $records, string $key, string $pattern): array {
    $filtered = [];

    foreach ($records as $record) {
        if (isset($record[$key]) && preg_match($pattern, $record[$key])) {
            $filtered[] = $record;
        }
    }

    return $filtered;
}