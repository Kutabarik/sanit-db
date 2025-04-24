<?php

use Kutabarik\SanitDb\Database\DatabaseRepository;
use Kutabarik\SanitDb\SanitDb;
use PHPUnit\Framework\TestCase;

class SanitDbTest extends TestCase
{
    private string $tmpFile;

    protected function tearDown(): void
    {
        if (isset($this->tmpFile) && file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function test_process_multi_table_json_rules()
    {
        $mockDb = $this->createMock(DatabaseRepository::class);

        $mockDb->method('getTableData')
            ->willReturnCallback(function ($table, $fields) {
                return match ($table) {
                    'users' => match ($fields) {
                        ['email'] => [
                            ['email' => 'a@example.com'],
                            ['email' => 'a@example.com'],
                            ['email' => 'b@example.com'],
                        ],
                        ['phone'] => [
                            ['phone' => '+37312345678'],
                            ['phone' => 'invalid'],
                            ['phone' => '+37387654321'],
                        ],
                        default => [],
                    },
                    'posts' => match ($fields) {
                        ['title'] => [
                            ['title' => 'Valid Title'],
                            ['title' => 'bad title'],
                        ],
                        default => [],
                    },
                    default => [],
                };
            });

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

        $sanitDb = new SanitDb($mockDb);
        $result = $sanitDb->process($this->tmpFile);

        $this->assertArrayHasKey('users', $result);
        $this->assertArrayHasKey('posts', $result);

        $this->assertArrayHasKey('duplicates', $result['users']);
        $this->assertCount(1, $result['users']['duplicates']);
        $this->assertArrayHasKey('format', $result['users']);
        $this->assertCount(1, $result['users']['format']);

        $this->assertArrayHasKey('format', $result['posts']);
        $this->assertCount(1, $result['posts']['format']);
    }
}
