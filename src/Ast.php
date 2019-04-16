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

function makeNode($key, $dataBefore, $dataAfter)
{
    $valueBefore = $dataBefore[$key] ?? '';
    $valueAfter = $dataAfter[$key] ?? '';

    if (!array_key_exists($key, $dataBefore)) {
        return nodeMaker('added', $key, $valueBefore, $valueAfter);
    }

    if (!array_key_exists($key, $dataAfter)) {
        return nodeMaker('removed', $key, $valueBefore, $valueAfter);
    }

    if (is_array($valueBefore) && is_array($valueAfter)) {
        return nodeMaker('nested', $key, '', '', buildAst($valueBefore, $valueAfter));
    }

    if ($valueBefore === $valueAfter) {
        return nodeMaker('unchanged', $key, $valueBefore, $valueAfter);
    }

    return nodeMaker('changed', $key, $valueBefore, $valueAfter);
}

function nodeMaker($type, $key, $valueBefore, $valueAfter, $children = [])
{
    $node = new \stdClass();

    $node->type = $type;
    $node->key = $key;
    $node->valueBefore = $valueBefore;
    $node->valueAfter = $valueAfter;
    $node->children = $children;

    return $node;
}
