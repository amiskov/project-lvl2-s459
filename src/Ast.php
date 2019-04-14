<?php

namespace GenDiff\Ast;

use function Funct\Collection\union;

function getNodeTypes()
{
    return [
        [
            'type' => 'added',
            'checker' => function ($key, $dataBefore) {
                return !array_key_exists($key, $dataBefore);
            },
            'creator' => function ($type, $key, $valueBefore, $valueAfter) {
                return makeNode($type, $key, $valueBefore, $valueAfter);
            }
        ],
        [
            'type' => 'removed',
            'checker' => function ($key, $dataBefore, $dataAfter) {
                return !array_key_exists($key, $dataAfter);
            },
            'creator' => function ($type, $key, $valueBefore) {
                return makeNode($type, $key, $valueBefore);
            }
        ],
        [
            'type' => 'nested',
            'checker' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && is_array($dataBefore[$key]) && is_array($dataAfter[$key]);
            },
            'creator' => function ($type, $key, $valueBefore, $valueAfter, $astBuilder) {
                return makeNode($type, $key, '', '', $astBuilder($valueBefore, $valueAfter));
            }
        ],
        [
            'type' => 'unchanged',
            'checker' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] === $dataAfter[$key];
            },
            'creator' => function ($type, $key, $beforeValue, $afterValue) {
                return makeNode($type, $key, $beforeValue, $afterValue);
            }
        ],
        [
            'type' => 'changed',
            'checker' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] !== $dataAfter[$key];
            },
            'creator' => function ($type, $key, $beforeValue, $afterValue) {
                return makeNode($type, $key, $beforeValue, $afterValue);
            }
        ]
    ];
}

function buildAst($dataBefore, $dataAfter)
{
    $nodeTypes = getNodeTypes();

    $astBuilder = function ($dataBefore, $dataAfter) use (&$astBuilder, $nodeTypes) {
        $allKeys = union(
            array_keys($dataBefore),
            array_keys($dataAfter)
        );

        $ast = array_map(function ($key) use ($dataBefore, $dataAfter, $nodeTypes, $astBuilder) {
            $typeElement = current(
                array_filter(
                    $nodeTypes,
                    function ($nodeType) use ($key, $dataBefore, $dataAfter) {
                        $checkType = $nodeType['checker'];
                        return $checkType($key, $dataBefore, $dataAfter);
                    }
                )
            );

            $createNode = $typeElement['creator'];

            return $createNode(
                $typeElement['type'],
                $key,
                $dataBefore[$key] ?? '',
                $dataAfter[$key] ?? '',
                $astBuilder
            );
        }, $allKeys);

        return array_values($ast);
    };

    return $astBuilder($dataBefore, $dataAfter);
}

function makeNode($type, $key, $valueBefore = '', $valueAfter = '', $children = [])
{
    $node = new \stdClass();

    $node->type = $type;
    $node->key = $key;
    $node->valueBefore = $valueBefore;
    $node->valueAfter = $valueAfter;
    $node->children = $children;

    return $node;
}
