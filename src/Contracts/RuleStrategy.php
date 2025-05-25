<?php

namespace Kutabarik\SanitDb\Contracts;

interface RuleStrategy
{
    public function supports(string $type): bool;

    public function analyze(array $data, array $rule): array;
}
