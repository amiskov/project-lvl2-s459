<?php

namespace GenDiff\Differ;

use function GenDiff\Ast\buildNodes;

function genDiff($pathToFile1, $pathToFile2, $format = 'pretty')
{
    $configDataBefore = getFileData($pathToFile1);
    $configDataAfter = getFileData($pathToFile2);

    $ast = buildNodes($configDataBefore, $configDataAfter);
    $formatter = getFormatFunction($format);
    return $formatter($ast);
}

function getFileType(string $path): string
{
    $pathParts = explode('.', $path);
    return $pathParts[count($pathParts) - 1];
}

function getFileData(string $filePath): array
{
    $fileType = getFileType($filePath);
    $parser = getParseFunction($fileType);
    $rawData = file_get_contents($filePath);
    return $parser($rawData);
}

function getFormatFunction($format)
{
    $nameSpace = "\\GenDiff\\Formatters\\" . ucfirst($format) . "\\";
    $formatFunction = "buildDiff";
    return $nameSpace . $formatFunction;
}

function getParseFunction($fileType)
{
    $nameSpace = "\\GenDiff\\Parser\\";
    $parseFunction = 'parse' . ucfirst($fileType);
    return $nameSpace . $parseFunction;
}
