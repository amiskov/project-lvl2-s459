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
            return $diffString . makeRow($item, $depth) . PHP_EOL;
        },
        ''
    );

    return $diff;
}

function makeRow($item, $depth)
{
    $type = $item->type;
    $key = $item->key;
    $valueBefore = $item->valueBefore;
    $valueAfter = $item->valueAfter;
    $children = $item->children;

    $fullSpacesQty = $depth * SPACES_IN_INDENT;

    $rowMaker = function ($value, $sign) use ($key, $depth, $fullSpacesQty) {
        $signedIndent = padLeft("{$sign} ", $fullSpacesQty);

        if (is_array($value)) {
            $outerIndent = times(' ', $fullSpacesQty);
            $innerIndent = $outerIndent . times(' ', SPACES_IN_INDENT);

            $innerRows = array_reduce(
                array_keys($value),
                function ($rows, $key) use ($signedIndent, $value, $innerIndent) {
                    return $rows . "{$innerIndent}{$key}: " . valueToString($value[$key]);
                },
                ''
            );

            return "{$signedIndent}{$key}: {\n{$innerRows}\n{$outerIndent}}";
        }

        return "{$signedIndent}{$key}: " . valueToString($value);
    };

    switch ($type) {
        case 'nested':
            return $rowMaker($valueBefore, '') . '{' . PHP_EOL
                . buildDiffBody($children, $depth + 1)
                . times(' ', $depth * SPACES_IN_INDENT) . '}';
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
            throw new \Exception('Unknown type in AST: ' . $type);
    }
}
