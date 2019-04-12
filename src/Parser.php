<?php

namespace GenDiff\Parser;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $rawData): array
{
    return json_decode($rawData, true);
}

function parseYaml(string $rawData): array
{
    return Yaml::parse($rawData);
}
