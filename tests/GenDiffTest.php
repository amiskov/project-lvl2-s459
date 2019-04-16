<?php

namespace GenDiff\Tests;

use function GenDiff\Differ\genDiff;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public static function prepareTestData($casesFolderPath, $fileType, $format = 'pretty')
    {
        $beforeFilePath = $casesFolderPath . 'before.' . $fileType;
        $afterFilePath = $casesFolderPath . 'after.' . $fileType;
        $resultFilePath = $casesFolderPath . 'diff-' . $format . '.txt';

        return [
            'expected' => file_get_contents($resultFilePath),
            'actual' => genDiff($beforeFilePath, $afterFilePath, $format)
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

    public function testRecursiveJson()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'json');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }

    public function testRecursiveYaml()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'yaml');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }

    public function testPlainFormat()
    {
        $testDataJson = self::prepareTestData(__DIR__ . '/cases/recursive/', 'json', 'plain');
        $testDataYaml = self::prepareTestData(__DIR__ . '/cases/recursive/', 'yaml', 'plain');

        $this->assertEquals($testDataJson['expected'], $testDataJson['actual']);
        $this->assertEquals($testDataYaml['expected'], $testDataJson['actual']);
    }

    public function testJsonFormat()
    {
        $testData = self::prepareTestData(__DIR__ . '/cases/recursive/', 'json', 'json');
        $this->assertEquals($testData['expected'], $testData['actual']);
    }
}
