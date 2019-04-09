<?php

namespace Differ\Differ;

use function Funct\Collection\union;

const PREFIX_ADDED = '+ ';
const PREFIX_REMOVED = '- ';
const PREFIX_SAME = '';

function genDiff(array $before, array $after): string
{
    $allKeys = union(
        array_keys($before),
        array_keys($after)
    );

    $diff = array_reduce(
        $allKeys,
        function ($diffString, $key) use ($before, $after) {
            return $diffString . prepareDiffByKey($key, $before, $after);
        },
        ''
    );

    return '{' . PHP_EOL . $diff . '}' . PHP_EOL;
}

function prepareDiffByKey($key, $before, $after)
{
    $keyWasBefore = array_key_exists($key, $before);
    $keyExistsNow = array_key_exists($key, $after);

    if ($keyWasBefore && !$keyExistsNow) {
        return makeRow(PREFIX_REMOVED, $key, $before[$key]);
    }

    if ($keyExistsNow && !$keyWasBefore) {
        return makeRow(PREFIX_ADDED, $key, $after[$key]);
    }

    if ($before[$key] === $after[$key]) {
        return makeRow(PREFIX_SAME, $key, $before[$key]);
    }

    return makeRow(PREFIX_ADDED, $key, $after[$key])
        . makeRow(PREFIX_REMOVED, $key, $before[$key]);
}

function makeRow($prefix, $key, $value)
{
    return "  {$prefix}{$key}: "
        . valueToString($value)
        . PHP_EOL;
}

function valueToString($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return $value;
}
