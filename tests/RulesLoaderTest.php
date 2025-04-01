<?php

use Kutabarik\SanitDb\RulesLoader;
use PHPUnit\Framework\TestCase;

class RulesLoaderTest extends TestCase
{
    public function testValidRulesAreLoadedCorrectly()
    {
        $rules = [
            'table' => 'users',
            'checks' => [
                [
                    'type' => 'duplicates',
                    'fields' => ['email'],
                ],
                [
                    'type' => 'format',
                    'field' => 'phone',
                    'regex' => '/^\+373\d{8}$/',
                ]
            ],
        ];

        $loader = new RulesLoader($rules);
        $result = $loader->getRules();

        $this->assertEquals('users', $result['table']);
        $this->assertCount(2, $result['checks']);
    }

    public function testMissingTableThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Invalid structure in rules");

        new RulesLoader([
            'checks' => [['type' => 'duplicates', 'fields' => ['email']]],
        ]);
    }

    public function testMissingChecksThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Invalid structure in rules");

        new RulesLoader([
            'table' => 'users',
        ]);
    }

    public function testMissingTypeInCheckThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'type' in check #0");

        new RulesLoader([
            'table' => 'users',
            'checks' => [
                ['fields' => ['email']],
            ],
        ]);
    }

    public function testMissingFieldsInDuplicatesCheckThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'fields' in duplicates check #0");

        new RulesLoader([
            'table' => 'users',
            'checks' => [
                ['type' => 'duplicates'],
            ],
        ]);
    }

    public function testMissingRegexInFormatCheckThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'field' or 'regex' in format check #0");

        new RulesLoader([
            'table' => 'users',
            'checks' => [
                ['type' => 'format', 'field' => 'phone'],
            ],
        ]);
    }
}