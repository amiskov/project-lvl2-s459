<?php

namespace GenDiff\Ast;

use function Funct\Collection\union;

function getNodeTypes()
{
    return [
        [
            'type' => 'added',
            'condition' => function ($key, $dataBefore, $dataAfter) {
                return !array_key_exists($key, $dataBefore);
            },
            'create' => function ($value) {
                return $value;
            }
        ],
        [
            'type' => 'removed',
            'condition' => function ($key, $dataBefore, $dataAfter) {
                return !array_key_exists($key, $dataAfter);
            },
            'create' => function ($valueBefore) {
                return $valueBefore;
            }
        ],
        [
            'type' => 'nested',
            'condition' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && is_array($dataBefore[$key]) && is_array($dataAfter[$key]);
            },
            'create' => function ($valueBefore, $valueAfter, $recursiveFn) {
                return $recursiveFn($valueBefore, $valueAfter);
            }
        ],
        [
            'type' => 'unchanged',
            'condition' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] === $dataAfter[$key];
            },
            'create' => function ($beforeValue) {
                return $beforeValue;
            }
        ],
        [
            'type' => 'changed',
            'condition' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] !== $dataAfter[$key];
            },
            'create' => function ($beforeValue, $afterValue) {
                return [
                    'old' => $beforeValue,
                    'new' => $afterValue
                ];
            }
        ],
    ];
}

function buildAst($dataBefore, $dataAfter)
{
    $allKeys = union(
        array_keys($dataBefore),
        array_keys($dataAfter)
    );

    $nodeTypes = getNodeTypes();

    $ast = array_map(function ($key) use ($dataBefore, $dataAfter, $nodeTypes) {
        $currentTypeElement = current(array_filter($nodeTypes, function ($nodeType) use ($key, $nodeTypes, $dataBefore, $dataAfter) {
            $typeChecker = $nodeType['condition'];
            return $typeChecker($key, $dataBefore, $dataAfter);
        }));

        $valueBefore = $dataBefore[$key] ?? '';
        $valueAfter = $dataAfter[$key] ?? '';


        $type = $currentTypeElement['type'];
        $value = $currentTypeElement['create']($valueBefore, $valueAfter, buildAst($valueBefore, $valueAfter));

        $node = new \stdClass();

        $node->type = $type;
        $node->key = $key;
        $node->valueBefore = $valueBefore;
        $node->valueAfter = $valueAfter;
//        $node->children = $children;

        return $node;
    }, $allKeys);

    dd($ast);
//    $ast = array_map(function ($key) use ($dataBefore, $dataAfter) {
//        return makeNode($key, $dataBefore, $dataAfter);
//    }, $allKeys);

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

    if (is_array($valueBefore) && is_array($valueBefore)) {
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
