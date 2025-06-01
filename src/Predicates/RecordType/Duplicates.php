<?php

namespace Predicates\RecordType;

function Duplicates(array $records, string $key): array {
    $duplicates = [];

    foreach ($records as $record) {
        if (isset($record[$key])) {
            $value = $record[$key];
            if (!isset($duplicates[$value])) {
                $duplicates[$value] = [];
            }
            $duplicates[$value][] = $record;
        }
    }

    return array_filter($duplicates, function ($group) {
        return count($group) > 1;
    });
}