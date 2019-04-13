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
            $isValueComplex = !empty($item->children) || is_array($item->valueBefore) || is_array($item->valueAfter);

            $rowOptions = [
                $item->type,
                $item->key,
                ($isValueComplex ? 'complex value' : valueToString($item->valueBefore)),
                ($isValueComplex ? 'complex value' : valueToString($item->valueAfter)),
                $parents
            ];

            if ($isValueComplex) {
                return $diffString
                    . makeRow(...$rowOptions)
                    . buildDiffBody(
                        $item->children,
                        array_merge($parents, [$item->key])
                    );
            }

            return $diffString . makeRow(...$rowOptions);
        },
        ''
    );

    return $diff;
}

function makeRow($type, $key, $valueBefore, $valueAfter, $parents = [])
{
    $fullKeyDepth = getFullKeyDepth($parents, $key);

    switch ($type) {
        case 'changed':
            return "Property '{$fullKeyDepth}' was changed. From '{$valueBefore}' to '{$valueAfter}'" . PHP_EOL;
        case 'added':
            return "Property '{$fullKeyDepth}' was added with value: '{$valueAfter}'" . PHP_EOL;
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
