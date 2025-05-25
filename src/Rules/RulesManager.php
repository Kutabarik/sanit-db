<?php

namespace Kutabarik\SanitDb\Rules;

use Kutabarik\SanitDb\Rules\RuleDTO;

class RulesManager
{
    private string $path;

    public function __construct(string $jsonFilePath)
    {
        $this->path = $jsonFilePath;
    }

    public function getAll(): array
    {
        $raw = json_decode(file_get_contents($this->path), true);

        return array_map(fn ($r) => RuleDTO::fromArray($r), $raw);
    }

    public function get(string $name): ?RuleDTO
    {
        foreach ($this->getAll() as $rule) {
            if ($rule->name === $name) {
                return $rule;
            }
        }

        return null;
    }

    public function save(RuleDTO $newRule): void
    {
        $all = $this->getAll();

        $updated = array_filter($all, fn (RuleDTO $r) => $r->name !== $newRule->name);
        $updated[] = $newRule;

        file_put_contents(
            $this->path,
            json_encode(array_map(fn ($r) => $r->toArray(), $updated), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function delete(string $name): void
    {
        $all = $this->getAll();

        $filtered = array_filter($all, fn (RuleDTO $r) => $r->name !== $name);

        file_put_contents(
            $this->path,
            json_encode(array_map(fn ($r) => $r->toArray(), $filtered), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
