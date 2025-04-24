<?php

use Kutabarik\SanitDb\Analyzer\DuplicateAnalyzer;
use PHPUnit\Framework\TestCase;

class DuplicateAnalyzerTest extends TestCase
{
    public function testFindsDuplicates()
    {
        $data = [
            ['email' => 'a@example.com'],
            ['email' => 'b@example.com'],
            ['email' => 'a@example.com'],
        ];

        $analyzer = new DuplicateAnalyzer($data, ['email']);
        $result = $analyzer->analyze();

        $this->assertCount(1, $result);
        $this->assertEquals('a@example.com', $result[0]['email']);
    }

    public function testNoDuplicates()
    {
        $data = [
            ['email' => 'a@example.com'],
            ['email' => 'b@example.com'],
        ];

        $analyzer = new DuplicateAnalyzer($data, ['email']);
        $result = $analyzer->analyze();

        $this->assertEmpty($result);
    }
}
