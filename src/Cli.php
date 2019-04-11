<?php

namespace Differ\Cli;

use function Differ\Ast\buildNodes;
use function Differ\Differ\genDiff;
use function Differ\Parser\getFileData;

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

    $configDataBefore = getFileData($args['<firstFile>']);
    $configDataAfter = getFileData($args['<secondFile>']);

    $ast = buildNodes(
        $configDataBefore,
        $configDataAfter
    );

    echo genDiff($ast);
}
