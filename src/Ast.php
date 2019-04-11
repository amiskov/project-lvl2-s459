<?php

namespace Differ\Ast;

use function Funct\Collection\union;

function buildNodes($configBeforeData, $configAfterData)
{
    $allKeys = union(
        array_keys($configBeforeData),
        array_keys($configAfterData)
    );

    $ast = array_map(function ($key) use ($configBeforeData, $configAfterData) {
        $keyWasBefore = array_key_exists($key, $configBeforeData);
        $keyExistsNow = array_key_exists($key, $configAfterData);

        $isBeforeDataCompound = $keyWasBefore && is_array($configBeforeData[$key]);
        $isAfterDataCompound = $keyExistsNow && is_array($configAfterData[$key]);

        // Leave children nodes with no `+` or `-` if parent node is `added` or `removed`
        $isChildrenUnchanged = empty($configBeforeData) || empty($configAfterData);

        // Compound values
        if ($isBeforeDataCompound && $isAfterDataCompound) {
            return makeNode('unchanged', $key, '', '', buildNodes($configBeforeData[$key], $configAfterData[$key]));
        }

        if ($isBeforeDataCompound && !$keyExistsNow) {
            $type = $isChildrenUnchanged ? 'unchanged' : 'removed';
            return makeNode($type, $key, '', '', buildNodes($configBeforeData[$key], []));
        }

        if ($isAfterDataCompound && !$keyWasBefore) {
            $type = $isChildrenUnchanged ? 'unchanged' : 'added';
            return makeNode($type, $key, '', '', buildNodes([], $configAfterData[$key]));
        }


        // Simple values
        if ($keyWasBefore && $keyExistsNow
            && !$isBeforeDataCompound && !$isAfterDataCompound
            && $configBeforeData[$key] === $configAfterData[$key]) {
            return makeNode('unchanged', $key, $configBeforeData[$key], $configAfterData[$key]);
        }

        if ($keyWasBefore && $keyExistsNow
            && !$isBeforeDataCompound && !$isAfterDataCompound
            && $configBeforeData[$key] !== $configAfterData[$key]) {
            return makeNode('changed', $key, $configBeforeData[$key], $configAfterData[$key]);
        }

        if ($keyWasBefore && !$keyExistsNow && !$isBeforeDataCompound) {
            $type = $isChildrenUnchanged ? 'unchanged' : 'removed';
            $afterValue = $isChildrenUnchanged ? $configBeforeData[$key] : '';

            return makeNode($type, $key, $configBeforeData[$key], $afterValue);
        }

        if ($keyExistsNow && !$keyWasBefore && !$isAfterDataCompound) {
            $type = $isChildrenUnchanged ? 'unchanged' : 'added';
            $beforeValue = $isChildrenUnchanged ? $configAfterData[$key] : '';

            return makeNode($type, $key, $beforeValue, $configAfterData[$key]);
        }

        return null;
    }, $allKeys);

    return array_values($ast);
}

function makeNode($type, $key, $beforeValue, $afterValue, $children = [])
{
    $node = new \stdClass();

    $node->type = $type;
    $node->key = $key;
    $node->beforeValue = $beforeValue;
    $node->afterValue = $afterValue;
    $node->children = $children;

    return $node;
}
