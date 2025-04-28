<?php

namespace Predicates\Attribute;

function isNull(array $records, string $key): array {
    $filtered = [];

    foreach ($records as $record) {
        if (isset($record[$key]) && $record[$key] === null) {
            $filtered[] = $record;
        }
    }

    return $filtered;
}