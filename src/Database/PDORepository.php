<?php

namespace Kutabarik\SanitDb\Database;

use PDO;

class PDORepository implements RepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTableData(string $table, array $fields): array
    {
        $fieldList = implode(", ", $fields);
        $stmt = $this->pdo->query("SELECT $fieldList FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDuplicates(string $table, array $fields): array
    {
        $fieldList = implode(", ", $fields);
        $stmt = $this->pdo->query("SELECT $fieldList, COUNT(*) as count FROM {$table} GROUP BY $fieldList HAVING count > 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
