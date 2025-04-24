<?php

namespace Kutabarik\SanitDb\Database;

use Doctrine\DBAL\Connection;

class DoctrineRepository implements RepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getTableData(string $table, array $fields): array
    {
        $sql = "SELECT " . implode(", ", $fields) . " FROM {$table}";
        return $this->connection->fetchAllAssociative($sql);
    }

    public function getDuplicates(string $table, array $fields): array
    {
        $fieldList = implode(", ", $fields);
        $sql = "SELECT $fieldList, COUNT(*) as count FROM {$table} GROUP BY $fieldList HAVING count > 1";
        return $this->connection->fetchAllAssociative($sql);
    }
}
