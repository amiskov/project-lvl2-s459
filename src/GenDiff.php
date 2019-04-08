<?php

namespace Hexlet;

class GenDiff
{
    const DOC = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]

DOC;

    public function run()
    {
        \Docopt::handle(self::DOC);
    }
}
