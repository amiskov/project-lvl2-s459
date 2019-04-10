<?php

namespace Differ\Tests;

use function Differ\Differ\genDiff;
use function Differ\Parser\getFileData;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public function testJsonDiff()
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

    public function testYamlDiff()
    {
        $beforeFilePath = __DIR__ . '/cases/flat-yaml/before.yaml';
        $afterFilePath = __DIR__ . '/cases/flat-yaml/after.yaml';
        $resultFilePath = __DIR__ . '/cases/flat-yaml/diff.txt';

        $expected = file_get_contents($resultFilePath);

        $actual = genDiff(
            getFileData($beforeFilePath),
            getFileData($afterFilePath)
        );

        $this->assertEquals($expected, $actual);
    }
}
