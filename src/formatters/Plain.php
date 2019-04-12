<?php

namespace GenDiff\Formatters\Plain;

use function GenDiff\Helpers\valueToString;

function buildDiff($ast)
{
    return buildDiffBody($ast);
}

function buildDiffBody(array $ast, $parents = [])
{
    $diff = array_reduce(
        $ast,
        function ($diffString, $item) use ($parents) {
            if (!empty($item->children)) {
                if ($item->type !== 'unchanged') {
                    $complexRow = makeRow($item->type, $item->key, '', 'complex value', $parents);
                }

                $complexRow = $complexRow ?? '';

                return $diffString . $complexRow
                    . buildDiffBody($item->children, array_merge($parents, [$item->key]));
            }

            return $diffString
                . makeRow(
                    $item->type,
                    $item->key,
                    valueToString($item->beforeValue),
                    valueToString($item->afterValue),
                    $parents
                );
        },
        ''
    );

    return $diff;
}

function makeRow($type, $key, $beforeValue, $afterValue, $parents = [])
{
    $fullKeyDepth = getFullKeyDepth($parents, $key);

    switch ($type) {
        case 'changed':
            return "Property '{$fullKeyDepth}' was changed. From '{$beforeValue}' to '{$afterValue}'" . PHP_EOL;
        case 'added':
            return "Property '{$fullKeyDepth}' was added with value: '{$afterValue}'" . PHP_EOL;
        case 'removed':
            return "Property '{$fullKeyDepth}' was removed" . PHP_EOL;
        default:
            return '';
    }
}

function getFullKeyDepth(array $parents, string $currentKey): string
{
    if (empty($parents)) {
        return $currentKey;
    }

    return implode('.', $parents) . '.' . $currentKey;
}
