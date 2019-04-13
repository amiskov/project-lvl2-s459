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
                valueToString($item->beforeValue),
                valueToString($item->afterValue),
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

function makeRow($type, $key, $beforeValue, $afterValue, $depth)
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
            return $rowMaker($beforeValue, '');
        case 'added':
            return $rowMaker($afterValue, '+');
        case 'removed':
            return $rowMaker($beforeValue, '-');
        case 'changed':
            return $rowMaker($afterValue, '+') . PHP_EOL
                . $rowMaker($beforeValue, '-');
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
