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

        $isBeforeCompound = $keyWasBefore && is_array($configBeforeData[$key]);
        $isAfterCompound = $keyExistsNow && is_array($configAfterData[$key]);

        $leaveChildrenUnchanged = (count($configBeforeData) === 0) || (count($configAfterData) === 0);

        // Compound values
        if ($isBeforeCompound && $isAfterCompound) {
            return makeNode('unchanged', $key, '', '', buildNodes($configBeforeData[$key], $configAfterData[$key]));
        }

        if ($isBeforeCompound && !$keyExistsNow) {
            $type = $leaveChildrenUnchanged ? 'unchanged' : 'removed';
            return makeNode($type, $key, '', '', buildNodes($configBeforeData[$key], []));
        }

        if ($isAfterCompound && !$keyWasBefore) {
            $type = $leaveChildrenUnchanged ? 'unchanged' : 'added';
            return makeNode($type, $key, '', '', buildNodes([], $configAfterData[$key]));
        }


        // Simple values
        if ($keyWasBefore && $keyExistsNow
            && !$isBeforeCompound && !$isAfterCompound
            && $configBeforeData[$key] === $configAfterData[$key]) {
            return makeNode('unchanged', $key, $configBeforeData[$key], $configAfterData[$key]);
        }

        if ($keyWasBefore && $keyExistsNow
            && !$isBeforeCompound && !$isAfterCompound
            && $configBeforeData[$key] !== $configAfterData[$key]) {
            return makeNode('changed', $key, $configBeforeData[$key], $configAfterData[$key]);
        }

        if ($keyWasBefore && !$keyExistsNow && !$isBeforeCompound) {
            $type = $leaveChildrenUnchanged ? 'unchanged' : 'removed';
            return makeNode($type, $key, $configBeforeData[$key], $afterValue = $leaveChildrenUnchanged
                ? $configBeforeData[$key] : '');
        }

        if ($keyExistsNow && !$keyWasBefore && !$isAfterCompound) {
            $type = $leaveChildrenUnchanged ? 'unchanged' : 'added';
            return makeNode($type, $key, $beforeValue = $leaveChildrenUnchanged
                ? $configAfterData[$key] : '', $configAfterData[$key]);
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
