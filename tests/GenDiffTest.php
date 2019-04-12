<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public static function prepareTestData($casesFolderPath, $fileType)
    {
        $beforeFilePath = $casesFolderPath . 'before.' . $fileType;
        $afterFilePath = $casesFolderPath . 'after.' . $fileType;
        $resultFilePath = $casesFolderPath . 'diff.txt';

        return [
            'expected' => file_get_contents($resultFilePath),
            'actual' => genDiff($beforeFilePath, $afterFilePath)
        ];
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
