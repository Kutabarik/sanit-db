<?php

namespace Kutabarik\SanitDb\Database;

use Illuminate\Support\Facades\DB;

class EloquentRepository implements RepositoryInterface
{
    public function getTableData(string $table, array $fields): array
    {
        return DB::table($table)->select($fields)->get()->toArray();
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
}
