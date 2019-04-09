<?php

namespace Differ\Tests;

use function Differ\genDiff;
use function Differ\Cli\getFileData;

use PHPUnit\Framework\TestCase;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $beforeFilePath = __DIR__ . '/cases/flat-json/before.json';
        $afterFilePath = __DIR__ . '/cases/flat-json/after.json';
        $resultFilePath = __DIR__ . '/cases/flat-json/diff.txt';

        $expected = file_get_contents($resultFilePath);

        $actual = genDiff(
            getFileData($beforeFilePath),
            getFileData($afterFilePath)
        );

        $this->assertEquals($expected, $actual);
    }
}
