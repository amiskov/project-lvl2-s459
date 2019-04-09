<?php

namespace Differ\Cli;

use Symfony\Component\Yaml\Yaml;
use function Differ\Differ\genDiff;

function getHelp()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]

DOC;

    return $doc;
}

function run()
{
    $args = \Docopt::handle(getHelp());

    echo genDiff(
        getFileData($args['<firstFile>']),
        getFileData($args['<secondFile>'])
    );
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

function parseJson(string $rawData): array
{
    return json_decode($rawData, true);
}

function parseYaml(string $rawData): array
{
    return Yaml::parse($rawData);
}
