<?php

namespace Differ\Differ;

use function Differ\Ast\buildNodes;

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

function genDiff($configDataBefore, $configDataAfter)
{
    $ast = buildNodes($configDataBefore, $configDataAfter);

    return '{' . PHP_EOL . buildDiffBody($ast) . '}' . PHP_EOL;
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
