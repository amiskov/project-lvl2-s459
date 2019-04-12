<?php

namespace GenDiff\Tests;

use function GenDiff\Ast\buildNodes;
use function GenDiff\Differ\getFileData;

use PHPUnit\Framework\TestCase;

class AstTest extends TestCase
{
    public static function prepareTestData($casesFolderPath, $fileType)
    {
        $beforeFilePath = $casesFolderPath . 'before.' . $fileType;
        $afterFilePath = $casesFolderPath . 'after.' . $fileType;

        try {
            $configDataBefore = getFileData($beforeFilePath);
            $configDataAfter = getFileData($afterFilePath);

            $ast = buildNodes($configDataBefore, $configDataAfter);

            return [
                'expected' => file_get_contents(__DIR__ . '/cases/ast.json'),
                'actual' => json_encode($ast, JSON_PRETTY_PRINT) . PHP_EOL
            ];
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function testJsonAst()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'json');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }

    public function testYamlAst()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'yaml');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }
}
