<?php

namespace Kutabarik\SanitDb\Analyzer;

interface AnalyzerInterface
{
    /**
     * Performs data analysis and returns an array of found issues.
     *
     * @return array An array of found issues.
     */
    public function analyze(): array;

    public function deleteInvalid(): void;

    public function fixInvalid(): void;
}
