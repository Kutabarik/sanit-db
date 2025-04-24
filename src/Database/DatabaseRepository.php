<?php

namespace Kutabarik\SanitDb\Database;

use PDO;

class DatabaseRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTableData(string $table, array $fields): array
    {
        $fieldList = implode(", ", $fields);
        $query = "SELECT $fieldList FROM {$table}";

        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDuplicates(string $table, array $fields): array
    {
        $fieldList = implode(", ", $fields);
        $query = "SELECT $fieldList, COUNT(*) as count FROM {$table} GROUP BY $fieldList HAVING count > 1";

        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
