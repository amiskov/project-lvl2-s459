<?php

namespace GenDiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseContent(string $rawData, string $dataType): array
{
    $parseFunction = __NAMESPACE__ . '\\parse' . ucfirst($dataType);

    if (!function_exists($parseFunction)) {
        throw new \Exception('Unsupported file type: ' . $dataType);
    }

    return $parseFunction($rawData);
}

function parseJson(string $rawData): array
{
    return json_decode($rawData, true);
}

function parseYaml(string $rawData): array
{
    return Yaml::parse($rawData);
}
