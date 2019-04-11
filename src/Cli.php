<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;
use function Differ\Parser\parseYaml;
use function Differ\Parser\parseJson;

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

    try {
        $configDataBefore = getFileData($args['<firstFile>']);
        $configDataAfter = getFileData($args['<secondFile>']);

        echo genDiff($configDataBefore, $configDataAfter);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

function getFileType(string $path): string
{
    $pathParts = explode('.', $path);
    return $pathParts[count($pathParts) - 1];
}

/**
 * @param string $filePath
 * @return array
 * @throws \Exception
 */
function getFileData(string $filePath): array
{
    $rawData = file_get_contents($filePath);

    switch (getFileType($filePath)) {
        case 'json':
            return parseJson($rawData);
        case 'yaml':
            return parseYaml($rawData);
        default:
            throw new \Exception('Unknown file format.');
    }
}
