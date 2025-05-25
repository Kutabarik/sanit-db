<?php

namespace Kutabarik\SanitDb\Rules;

use InvalidArgumentException;

class RuleDTO
{
    public function __construct(
        public string $name,
        public string $table,
        public string $scope,
        public string $level,
        public string $category,
        public string $type,
        public ?string $field = null,
        public ?array $fields = null,
        public ?string $regex = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!in_array($this->scope, ['attribute', 'record', 'record_type', 'source'], true)) {
            throw new InvalidArgumentException("Invalid scope: {$this->scope}");
        }

        if (!in_array($this->level, ['schema', 'instance'], true)) {
            throw new InvalidArgumentException("Invalid level: {$this->level}");
        }

        if (!in_array($this->category, ['single-source', 'multi-source'], true)) {
            throw new InvalidArgumentException("Invalid category: {$this->category}");
        }

        if ($this->type === 'regex' && !$this->regex) {
            throw new InvalidArgumentException("Regex rule requires a 'regex' field.");
        }

        if ($this->type === 'duplicates' && empty($this->fields)) {
            throw new InvalidArgumentException("Duplicates rule requires 'fields'.");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['table'],
            $data['scope'],
            $data['level'],
            $data['category'],
            $data['type'],
            $data['field'] ?? null,
            $data['fields'] ?? null,
            $data['regex'] ?? null
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
