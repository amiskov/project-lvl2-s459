<?php

namespace GenDiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseContent(string $rawData, string $fileType): array
{
    $parseFunction = __NAMESPACE__ . '\\parse' . ucfirst($fileType);

    if (!function_exists($parseFunction)) {
        throw new \Exception('Unsupported file type: ' . $fileType);
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
