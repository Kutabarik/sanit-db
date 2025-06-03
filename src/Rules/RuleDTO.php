<?php

namespace Kutabarik\SanitDb\Rules;

use InvalidArgumentException;

class RuleDTO
{
    public function __construct(
        public string $name,
        public string $table,
        public string $type,
        public ?string $field = null,
        public ?array $fields = null,
        public ?string $regex = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
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
