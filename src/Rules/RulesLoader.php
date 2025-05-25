<?php

namespace Kutabarik\SanitDb\Rules;

use InvalidArgumentException;
use Kutabarik\SanitDb\Contracts\RuleStrategy;
use RuntimeException;

class RulesLoader
{
    private array $rules;

    private array $handlers = [];

    public function __construct(private RuleHandlerRegistry $registry) {}

    public function getHandler(string $type): RuleStrategy
    {
        return $this->registry->get($type);
    }

    public function loadFromFile(string $path): void
    {
        if (! file_exists($path)) {
            throw new InvalidArgumentException("Rules file not found: {$path}");
        }

        $decoded = json_decode(file_get_contents($path), true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Invalid JSON format in rules file.');
        }

        $this->rules = $decoded;
    }

    public function registerHandler(RuleStrategy $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function filterBy(string $key, string $value): array
    {
        return array_filter($this->rules, fn ($r) => ($r[$key] ?? null) === $value);
    }
}
