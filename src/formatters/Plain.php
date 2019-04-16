<?php

namespace GenDiff\Formatters\Plain;

use function GenDiff\Helpers\valueToString;

function buildDiff($ast)
{
    return buildDiffBody($ast);
}

function buildDiffBody(array $ast, $parentKeys = [])
{
    $diff = array_reduce(
        $ast,
        function ($diffString, $astNode) use ($parentKeys) {
            return $diffString . makeDiffRow($astNode, $parentKeys);
        },
        ''
    );

    return $diff;
}

function makeDiffRow(object $astNode, array $parentKeys = []): string
{
    $type = $astNode->type;
    $fullKeyPath = getFullKeyPath($parentKeys, $astNode->key);
    $valueBefore = normalizeValue($astNode->valueBefore);
    $valueAfter = normalizeValue($astNode->valueAfter);

    switch ($type) {
        case 'unchanged':
            return '';
        case 'changed':
            return "Property '{$fullKeyPath}' was changed. From '{$valueBefore}' to '{$valueAfter}'" . PHP_EOL;
        case 'added':
            return "Property '{$fullKeyPath}' was added with value: '{$valueAfter}'" . PHP_EOL;
        case 'removed':
            return "Property '{$fullKeyPath}' was removed" . PHP_EOL;
        case 'nested':
            $currentKey = $astNode->key;

            return buildDiffBody(
                $astNode->children,
                array_merge($parentKeys, [$currentKey])
            );
        default:
            throw new \Exception('Unknown type in AST: ' . $type);
    }
}

function getFullKeyPath(array $parents, string $currentKey): string
{
    if (empty($parents)) {
        return $currentKey;
    }

    return implode('.', $parents) . '.' . $currentKey;
}

function normalizeValue($value): string
{
    $isValueComplex = is_array($value) || is_array($value);
    return $isValueComplex ? 'complex value' : valueToString($value);
}
