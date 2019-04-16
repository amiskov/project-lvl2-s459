<?php

namespace GenDiff\Helpers;

function boolToString($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return $value;
}
