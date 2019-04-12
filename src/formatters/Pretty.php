<?php

namespace GenDiff\Formatters\Pretty;

use function GenDiff\Helpers\valueToString;

function buildDiff($ast)
{
    return '{' . PHP_EOL . buildDiffBody($ast) . '}' . PHP_EOL;
}

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

            $rowOptions = [
                $item->type,
                $item->key,
                valueToString($item->beforeValue),
                valueToString($item->afterValue),
                $spacer
            ];

            return $diffString . $spacer . makeRow(...$rowOptions);
        },
        ''
    );

    return $diff;
}

function makeRow($type, $key, $beforeValue, $afterValue, $spacer)
{
    switch ($type) {
        case 'changed':
            return makePrefix('added') . "{$key}: {$afterValue}" . PHP_EOL
                . $spacer . makePrefix('removed') . "{$key}: {$beforeValue}" . PHP_EOL;
        case 'added':
            return makePrefix($type) . $key . ': ' . $afterValue . PHP_EOL;
        case 'removed':
            return makePrefix($type) . $key . ': ' . $beforeValue . PHP_EOL;
        default:
            return makePrefix($type) . $key . ': ' . $beforeValue . PHP_EOL;
    }
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
