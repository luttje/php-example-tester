<?php

namespace Luttje\ExampleTester\Compiler;

interface ReadmeCompilerInterface
{
    public function compile(string $inputFile, string $outputFile): void;
}
