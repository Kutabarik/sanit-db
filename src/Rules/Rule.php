<?php

namespace Rules;

use Kutabarik\SanitDb\Database\PDORepository;

/*
  {
    "name": "email_is_null",
    "table": "users",
    "scope": "attribute",
    "field": "email",
    "type": "is_null"
  }
*/

class Rule {
    private ?string $name;
    private string $table;
    private string $scope;
    private string $field;
    private string $type;
    private array $results = [];
    private PDORepository $repository;

    public function __construct(object $rule, PDORepository $repository) {
        $this->repository = $repository;
        $this->name = $rule->name ?? null;
        $this->table = $rule->table;
        $this->scope = $rule->scope;
        $this->field = $rule->field;
        $this->type = $rule->type;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getTable(): string {
        return $this->table;
    }

    public function getScope(): string {
        return $this->scope;
    }

    public function getField(): string {
        return $this->field;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getResults(): array {
        return $this->results;
    }

    public function Validate(): bool {
        // validate data
        $function = "Predicates\\{$this->scope}\\" . snakeToCamelCase($this->type);
        if(function_exists($function)) {
            // get data from database
            $data = $this->repository->getTableData($this->table, ["*"]);
            $this->results = call_user_func($function, $data, $this->field);
        } else {
            throw new \Exception("Validation type {$function} not found.");
        }
        return !empty($this->results);
    }

}