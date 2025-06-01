<?php

namespace Predicates\Attribute;

function Email(array $records, string $key): array {
    $filtered = [];

    foreach ($records as $record) {
        if (isset($record[$key]) && filter_var($record[$key], FILTER_VALIDATE_EMAIL)) {
            $filtered[] = $record;
        }
    }

    return $filtered;
}