<?php

namespace Differ\Tests;

use function Differ\Ast\buildNodes;
use function Differ\Differ\genDiff;
use function Differ\Cli\getFileData;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public static function prepareTestData($casesFolderPath, $fileType)
    {
        $beforeFilePath = $casesFolderPath . 'before.' . $fileType;
        $afterFilePath = $casesFolderPath . 'after.' . $fileType;
        $resultFilePath = $casesFolderPath . 'diff.txt';

        try {
            $configDataBefore = getFileData($beforeFilePath);
            $configDataAfter = getFileData($afterFilePath);

            $ast = buildNodes($configDataBefore, $configDataAfter);

            return [
                'expected' => file_get_contents($resultFilePath),
                'actual' => genDiff($ast)
            ];
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function testFlatJson()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/flat/', 'json');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }

    public function testFlatYaml()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/flat/', 'yaml');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }

    public function testRecursiveYaml()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'yaml');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }
}
