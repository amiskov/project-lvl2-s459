<?php

namespace GenDiff\Differ;

use function GenDiff\Ast\buildNodes;
use function GenDiff\Parser\parseJson;
use function GenDiff\Parser\parseYaml;

function buildDiffBody(array $ast, $spacer = '  ')
{
    $diff = array_reduce(
        $ast,
        function ($diffString, $item) use ($spacer) {
            $prefix = makePrefix($item->type);

            if (!empty($item->children)) {
                return $diffString
                    . $spacer . $prefix . $item->key . ':' . ' {' . PHP_EOL
                    . buildDiffBody($item->children, $spacer . '    ')
                    . $spacer . '  }' . PHP_EOL;
            }

            return $diffString
                . $spacer
                . makeRow($item->type, $item->key, $item->beforeValue, $item->afterValue, $spacer);
        },
        ''
    );

    return $diff;
}

function genDiff($pathToFile1, $pathToFile2)
{
    try {
        $configDataBefore = getFileData($pathToFile1);
        $configDataAfter = getFileData($pathToFile2);

        $ast = buildNodes($configDataBefore, $configDataAfter);

        return '{' . PHP_EOL . buildDiffBody($ast) . '}' . PHP_EOL;
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function makeRow($type, $key, $beforeValue, $afterValue, $spacer)
{
    if ($type === 'changed') {
        return makePrefix('added') . "{$key}: {$afterValue}" . PHP_EOL
            . $spacer . makePrefix('removed') . "{$key}: {$beforeValue}" . PHP_EOL;
    }

    if ($type === 'added') {
        return makePrefix($type) . $key . ': ' . valueToString($afterValue) . PHP_EOL;
    }

    if ($type === 'removed') {
        return makePrefix($type) . $key . ': ' . valueToString($beforeValue) . PHP_EOL;
    }

    return makePrefix($type) . $key . ': ' . valueToString($beforeValue) . PHP_EOL;
}

function makePrefix($type)
{
    if ($type === 'added') {
        return '+ ';
    } elseif ($type === 'removed') {
        return '- ';
    }

    return '  ';
}

function valueToString($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return $value;
}

function getFileType(string $path): string
{
    $pathParts = explode('.', $path);
    return $pathParts[count($pathParts) - 1];
}

/**
 * @param string $filePath
 * @return array
 * @throws \Exception
 */
function getFileData(string $filePath): array
{
    $rawData = file_get_contents($filePath);

    switch (getFileType($filePath)) {
        case 'json':
            return parseJson($rawData);
        case 'yaml':
            return parseYaml($rawData);
        default:
            throw new \Exception('Unknown file format.');
    }
}
