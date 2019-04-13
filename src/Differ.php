<?php

namespace GenDiff\Differ;

use function GenDiff\Ast\buildAst;
use function GenDiff\Parser\parseContent;

function genDiff($pathToFile1, $pathToFile2, $format = 'pretty'): string
{
    $dataBefore = getData($pathToFile1);
    $dataAfter = getData($pathToFile2);

    $ast = buildAst($dataBefore, $dataAfter);

    return getFormattedDiff($ast, $format);
}

function getFormattedDiff($content, $format)
{
    $nameSpace = "\\GenDiff\\Formatters\\" . ucfirst($format) . "\\";
    $formatFunction = $nameSpace . "buildDiff";

    if (!function_exists($formatFunction)) {
        throw new \Exception('Unsupported format: ' . $format);
    }

    return $formatFunction($content);
}

function getData($filePath)
{
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    $rawData = file_get_contents($filePath);
    return parseContent($rawData, $fileType);
}
