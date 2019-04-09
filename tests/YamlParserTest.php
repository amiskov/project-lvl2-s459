<?php

namespace Differ\Tests;

use function Differ\Cli\parseYaml;

use PHPUnit\Framework\TestCase;

class YamlParserTest extends TestCase
{
    public function testParser()
    {
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 20,
            'verbose' => true
        ];

        $actual = parseYaml(
            file_get_contents(__DIR__ . '/cases/flat-yaml/after.yaml')
        );

        $this->assertSame(
            array_diff($expected, $actual),
            array_diff($actual, $expected)
        );
    }
}
