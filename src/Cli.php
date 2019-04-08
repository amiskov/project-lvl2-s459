<?php

namespace Differ\Cli;

use function Differ\genDiff;

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
    echo genDiff($args['<firstFile>'], $args['<secondFile>']);
}
