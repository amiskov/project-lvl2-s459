<?php

namespace GenDiff\Cli;

use function GenDiff\Differ\genDiff;

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

    $pathToFile1 = $args['<firstFile>'];
    $pathToFile2 = $args['<secondFile>'];

    echo genDiff($pathToFile1, $pathToFile2);
}
