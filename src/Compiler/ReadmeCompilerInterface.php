<?php

namespace Luttje\ExampleTester\Compiler;

interface ReadmeCompilerInterface
{
    public function compile(string $input, mixed $outputHandle): void;
}
