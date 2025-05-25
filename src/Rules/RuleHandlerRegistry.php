<?php

namespace Kutabarik\SanitDb\Rules;

use Kutabarik\SanitDb\Contracts\RuleStrategy;
use Kutabarik\SanitDb\Handlers\DuplicateRule;
use Kutabarik\SanitDb\Handlers\IsNullRule;
use Kutabarik\SanitDb\Handlers\RegexRule;
use RuntimeException;

class RuleHandlerRegistry
{
    private array $handlers = [];

    public function __construct()
    {
        $this->register('regex', new RegexRule);
        $this->register('is_null', new IsNullRule);
        $this->register('duplicates', new DuplicateRule);
    }

    public function register(string $type, RuleStrategy $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    public function get(string $type): RuleStrategy
    {
        return $this->handlers[$type]
            ?? throw new RuntimeException("No handler registered for type: $type");
    }

    public function all(): array
    {
        return $this->handlers;
    }
}
