<?php

namespace Differ\Tests;

use function Differ\Ast\buildNodes;
use function Differ\Parser\getFileData;

use PHPUnit\Framework\TestCase;

class AstTest extends TestCase
{
    public function testJsonAst()
    {
        $beforeFilePath = __DIR__ . '/cases/recursive/before.json';
        $afterFilePath = __DIR__ . '/cases/recursive/after.json';
        $expected = file_get_contents( __DIR__ . '/cases/ast.json');

        $beforeData = getFileData($beforeFilePath);
        $afterData = getFileData($afterFilePath);

        $ast = buildNodes($beforeData, $afterData);

        $actual = json_encode($ast, JSON_PRETTY_PRINT) . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }

    public function testYamlAst()
    {
        $beforeFilePath = __DIR__ . '/cases/recursive/before.yaml';
        $afterFilePath = __DIR__ . '/cases/recursive/after.yaml';
        $expected = file_get_contents( __DIR__ . '/cases/ast.json');

        $beforeData = getFileData($beforeFilePath);
        $afterData = getFileData($afterFilePath);

        $ast = buildNodes($beforeData, $afterData);

        $actual = json_encode($ast, JSON_PRETTY_PRINT) . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }
}
