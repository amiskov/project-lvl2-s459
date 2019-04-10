<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $rawData): array
{
    return json_decode($rawData, true);
}

function parseYaml(string $rawData): array
{
    return Yaml::parse($rawData);
}

function getFileData(string $filePath): array
{
    $rawData = file_get_contents($filePath);

    switch (getFileType($filePath)) {
        case 'json':
            return parseJson($rawData);
        case 'yaml':
            return parseYaml($rawData);
        default:
            return [];
    }
}

function getFileType(string $path): string
{
    $pathParts = explode('.', $path);
    return $pathParts[count($pathParts) - 1];
}
