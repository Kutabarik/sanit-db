<?php

namespace Kutabarik\SanitDb\Database;

interface RepositoryInterface
{
    public function getTableData(string $table, array $fields): array;

    public function getDuplicates(string $table, array $fields): array;

    /**
     * Delete rows based on a condition.
     *
     * @return int Number of deleted rows
     */
    public function deleteRows(string $table, array $conditions): int;
}
