<?php

namespace Luttje\ExampleTester\Compiler;

use Luttje\ExampleTester\Parser\MarkerConfig;

interface ExampleFormatterInterface
{
    public function format(string $example, MarkerConfig $markerConfig): string;
}
