<?php

namespace GenDiff\Ast;

use function Funct\Collection\union;

function buildAst($dataBefore, $dataAfter)
{
    $allKeys = union(
        array_keys($dataBefore),
        array_keys($dataAfter)
    );

    $ast = array_map(function ($key) use ($dataBefore, $dataAfter) {
        return makeNode($key, $dataBefore, $dataAfter);
    }, $allKeys);

    return array_values($ast);
}

function makeNode($key, $beforeData, $afterData)
{
    $valueBefore = $beforeData[$key] ?? '';
    $valueAfter = $afterData[$key] ?? '';

    $nodeMaker = function ($type, $children = []) use ($valueBefore, $valueAfter, $key) {
        $hasChildren = !empty($children);

        $node = new \stdClass();
        $node->type = $type;
        $node->key = $key;
        $node->beforeValue = $hasChildren ? '' : $valueBefore;
        $node->afterValue = $hasChildren ? '' : $valueAfter;
        $node->children = $children;

        return $node;
    };

    if (!array_key_exists($key, $beforeData)) {
        return $nodeMaker('added');
    }

    if (!array_key_exists($key, $afterData)) {
        return $nodeMaker('removed');
    }

    if (is_array($valueBefore) && is_array($valueBefore)) {
        return $nodeMaker('unchanged', buildAst($valueBefore, $valueAfter));
    }

    if ($valueBefore === $valueAfter) {
        return $nodeMaker('unchanged');
    }

    return $nodeMaker('changed');
}
