<?php

use Kutabarik\SanitDb\RulesLoader;
use PHPUnit\Framework\TestCase;

class RulesLoaderTest extends TestCase
{
    private string $tmpFile;

    protected function tearDown(): void
    {
        if (isset($this->tmpFile) && file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function test_loads_valid_multi_table_rules()
    {
        $rules = [
            'tables' => [
                'users' => [
                    ['type' => 'duplicates', 'fields' => ['email']],
                    ['type' => 'format', 'field' => 'phone', 'regex' => '/^\+373[0-9]{8}$/'],
                ],
                'posts' => [
                    ['type' => 'format', 'field' => 'title', 'regex' => '/^[A-Z].{3,}$/'],
                ],
            ],
        ];

        $this->tmpFile = tempnam(sys_get_temp_dir(), 'rules_');
        file_put_contents($this->tmpFile, json_encode($rules));

        $loader = new RulesLoader($this->tmpFile);
        $parsed = $loader->getRules();

        $this->assertArrayHasKey('tables', $parsed);
        $this->assertCount(2, $parsed['tables']);
        $this->assertCount(2, $parsed['tables']['users']);
        $this->assertCount(1, $parsed['tables']['posts']);
    }

    public function test_missing_tables_key_throws_exception()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing or invalid 'tables'");

        $this->tmpFile = tempnam(sys_get_temp_dir(), 'rules_');
        file_put_contents($this->tmpFile, json_encode(['invalid_key' => []]));

        new RulesLoader($this->tmpFile);
    }

    public function test_invalid_check_in_table_throws_exception()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'type' in check #0");

        $rules = [
            'tables' => [
                'users' => [
                    ['field' => 'email'],
                ],
            ],
        ];

        $this->tmpFile = tempnam(sys_get_temp_dir(), 'rules_');
        file_put_contents($this->tmpFile, json_encode($rules));

        new RulesLoader($this->tmpFile);
    }
}

