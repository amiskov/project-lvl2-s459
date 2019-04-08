<?php

namespace Differ\Tests;

use function Differ\genDiff;

use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $beforeFilePath = __DIR__ . '/cases/flat-json/before.json';
        $afterFilePath = __DIR__ . '/cases/flat-json/after.json';
        $resultFilePath = __DIR__ . '/cases/flat-json/diff.txt';

        $actual = genDiff($beforeFilePath, $afterFilePath);
        $expected = file_get_contents($resultFilePath);

        $this->assertEquals($expected, $actual);
    }
}
