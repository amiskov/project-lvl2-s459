<?php

namespace GenDiff\Ast;

use function Funct\Collection\union;

function getNodeTypes()
{
    return [
        [
            'label' => 'added',
            'check' => function ($key, $dataBefore) {
                return !array_key_exists($key, $dataBefore);
            },
            'getValues' => function ($valueBefore, $valueAfter) {
                return [
                    'valueAfter' => $valueAfter
                ];
            }
        ],
        [
            'label' => 'removed',
            'check' => function ($key, $dataBefore, $dataAfter) {
                return !array_key_exists($key, $dataAfter);
            },
            'getValues' => function ($valueBefore) {
                return [
                    'valueBefore' => $valueBefore,
                ];
            }
        ],
        [
            'label' => 'nested',
            'check' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && is_array($dataBefore[$key]) && is_array($dataAfter[$key]);
            },
            'getValues' => function ($valueBefore, $valueAfter, $astBuilder) {
                return [
                    'children' => $astBuilder($valueBefore, $valueAfter)
                ];
            }
        ],
        [
            'label' => 'unchanged',
            'check' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] === $dataAfter[$key];
            },
            'getValues' => function ($valueBefore, $valueAfter) {
                return [
                    'valueBefore' => $valueBefore,
                    'valueAfter' => $valueAfter
                ];
            }
        ],
        [
            'label' => 'changed',
            'check' => function ($key, $dataBefore, $dataAfter) {
                return array_key_exists($key, $dataBefore) && array_key_exists($key, $dataAfter)
                    && $dataBefore[$key] !== $dataAfter[$key];
            },
            'getValues' => function ($valueBefore, $valueAfter) {
                return [
                    'valueBefore' => $valueBefore,
                    'valueAfter' => $valueAfter
                ];
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
            $currentType = current(
                array_filter(
                    $nodeTypes,
                    function ($type) use ($key, $dataBefore, $dataAfter) {
                        return $type['check']($key, $dataBefore, $dataAfter);
                    }
                )
            );

            $nodeValues = $currentType['getValues'](
                $dataBefore[$key] ?? '',
                $dataAfter[$key] ?? '',
                $astBuilder
            );

            $node = new \stdClass();
            $node->type = $currentType['label'];
            $node->key = $key;
            $node->valueBefore = $nodeValues['valueBefore'] ?? '';
            $node->valueAfter = $nodeValues['valueAfter'] ?? '';
            $node->children = $nodeValues['children'] ?? [];

            return $node;
        }, $allKeys);

        return array_values($ast);
    };

    return $astBuilder($dataBefore, $dataAfter);
}
