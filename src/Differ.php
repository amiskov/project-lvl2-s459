<?php

namespace GenDiff\Differ;

use function GenDiff\Ast\buildNodes;

function genDiff($pathToFile1, $pathToFile2, $format = 'pretty'): string
{
    $configDataBefore = getFileData($pathToFile1);
    $configDataAfter = getFileData($pathToFile2);

    $ast = buildNodes($configDataBefore, $configDataAfter);

    try {
        $formatter = getFormatFunction($format);
        return $formatter($ast);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    return '';
}

function getFileType(string $path): string
{
    $pathParts = explode('.', $path);
    return $pathParts[count($pathParts) - 1];
}

function getFileData(string $filePath): array
{
    $fileType = getFileType($filePath);
    $rawData = file_get_contents($filePath);

    try {
        $parser = getParseFunction($fileType);
        return $parser($rawData);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    return [];
}

function getFormatFunction($format)
{
    $nameSpace = "\\GenDiff\\Formatters\\" . ucfirst($format) . "\\";
    $formatFunction = $nameSpace . "buildDiff";

    if (!function_exists($formatFunction)) {
        throw new \Exception('Unsupported format: ' . $format);
    }

    return $formatFunction;
}

function getParseFunction($fileType)
{
    $nameSpace = "\\GenDiff\\Parser\\";
    $parseFunction = $nameSpace . 'parse' . ucfirst($fileType);

    if (!function_exists($parseFunction)) {
        throw new \Exception('Unsupported file type: ' . $fileType);
    }

    return $parseFunction;
}
