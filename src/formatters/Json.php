<?php

namespace GenDiff\Formatters\Json;

function buildDiff($ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT) . PHP_EOL;
}
