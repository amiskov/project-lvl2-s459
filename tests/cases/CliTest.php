<?php

namespace Differ\Tests;

use function Differ\Cli\getFileType;

use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    public function testGetFileTypeYaml()
    {
        $path = __DIR__ . '/cases/flat-yaml/after.yaml';
        $this->assertEquals('yaml', getFileType($path));
    }

    public function testGetFileTypeJson()
    {
        $path = __DIR__ . '/cases/flat.json/test.name.after.json';
        $this->assertEquals('json', getFileType($path));
    }
}
