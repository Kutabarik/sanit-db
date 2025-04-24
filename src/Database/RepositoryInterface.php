<?php

namespace Kutabarik\SanitDb\Database;

interface RepositoryInterface
{
    public function getTableData(string $table, array $fields): array;

    public function getDuplicates(string $table, array $fields): array;
}
