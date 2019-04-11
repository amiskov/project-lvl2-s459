<?php

namespace Differ\Tests;

use function Differ\Ast\buildNodes;
use function Differ\Differ\genDiff;
use function Differ\Parser\getFileData;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public function testFlatJson()
    {
        $beforeFilePath = __DIR__ . '/cases/flat/before.json';
        $afterFilePath = __DIR__ . '/cases/flat/after.json';
        $resultFilePath = __DIR__ . '/cases/flat/diff.txt';

        $expected = file_get_contents($resultFilePath);

        $ast = buildNodes(
            getFileData($beforeFilePath),
            getFileData($afterFilePath)
        );

        $actual = genDiff($ast);

        $this->assertEquals($expected, $actual);
    }

    public function testFlatYaml()
    {
        $beforeFilePath = __DIR__ . '/cases/flat/before.yaml';
        $afterFilePath = __DIR__ . '/cases/flat/after.yaml';
        $resultFilePath = __DIR__ . '/cases/flat/diff.txt';

        $expected = file_get_contents($resultFilePath);

        $ast = buildNodes(
            getFileData($beforeFilePath),
            getFileData($afterFilePath)
        );

        $actual = genDiff($ast);

        $this->assertEquals($expected, $actual);
    }

    public function testRecursiveYaml()
    {
        $beforeFilePath = __DIR__ . '/cases/recursive/before.yaml';
        $afterFilePath = __DIR__ . '/cases/recursive/after.yaml';
        $resultFilePath = __DIR__ . '/cases/recursive/diff.txt';

        $expected = file_get_contents($resultFilePath);

        $ast = buildNodes(
            getFileData($beforeFilePath),
            getFileData($afterFilePath)
        );

        $actual = genDiff($ast);

        $this->assertEquals($expected, $actual);
    }
}
