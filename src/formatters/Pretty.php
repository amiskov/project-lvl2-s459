<?php

namespace GenDiff\Formatters\Pretty;

use function Funct\Strings\times;
use function Funct\Strings\padLeft;
use function GenDiff\Helpers\valueToString;

const SPACES_IN_INDENT = 4;

function buildDiff($ast)
{
    return '{' . PHP_EOL . buildDiffBody($ast) . '}' . PHP_EOL;
}

function buildDiffBody(array $ast, $depth = 1)
{
    $diff = array_reduce(
        $ast,
        function ($diffString, $item) use ($depth) {
            $rowOptions = [
                $item->type,
                $item->key,
                valueToString($item->valueBefore),
                valueToString($item->valueAfter),
                $depth
            ];

            $hasChildren = !empty($item->children);

            if ($hasChildren) {
                return $diffString
                    . openParentBlock($rowOptions)
                    . buildDiffBody($item->children, $depth + 1)
                    . closeParentBlock($depth);
            }

            return $diffString . makeRow(...$rowOptions) . PHP_EOL;
        },
        ''
    );

    return $diff;
}

function makeRow($type, $key, $valueBefore, $valueAfter, $depth)
{
    $rowMaker = function ($value, $sign) use ($key, $depth) {
        $fullSpacesQty = $depth * SPACES_IN_INDENT;
        $signedIndent = padLeft("{$sign} ", $fullSpacesQty);

        if (is_array($value)) {
            $outerIndent = times(' ', $fullSpacesQty);
            $innerIndent = $outerIndent . times(' ', SPACES_IN_INDENT);

            $row = array_reduce(
                array_keys($value),
                function ($row, $key) use ($signedIndent, $value, $innerIndent) {
                    return $row . "{$innerIndent}{$key}: {$value[$key]}";
                },
                ''
            );

            return "{$signedIndent}{$key}: {\n{$row}\n{$outerIndent}}";
        }

        return "{$signedIndent}{$key}: {$value}";
    };

    switch ($type) {
        case 'unchanged':
            return $rowMaker($valueBefore, '');
        case 'added':
            return $rowMaker($valueAfter, '+');
        case 'removed':
            return $rowMaker($valueBefore, '-');
        case 'changed':
            return $rowMaker($valueAfter, '+') . PHP_EOL
                . $rowMaker($valueBefore, '-');
        default:
            return '';
    }
}

function openParentBlock($rowOptions)
{
    return makeRow(...$rowOptions) . '{' . PHP_EOL;
}

function closeParentBlock($depth)
{
    return times(' ', $depth * SPACES_IN_INDENT) . '}' . PHP_EOL;
}
