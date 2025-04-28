<?php

namespace Kutabarik\SanitDb\Database;

use Illuminate\Support\Facades\DB;

class EloquentRepository implements RepositoryInterface
{
    public function getTableData(string $table, array $fields = ['*']): array
    {
        return DB::table($table)
            ->select($fields)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();
    }

    public function getDuplicates(string $table, array $fields): array
    {
        return DB::table($table)
            ->select(array_merge($fields, [DB::raw('COUNT(*) as count')]))
            ->groupBy($fields)
            ->having('count', '>', 1)
            ->get()
            ->toArray();
    }

    public function deleteRows(string $table, array $conditions): int
    {
        $query = DB::table($table);

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->delete();
    }
}
