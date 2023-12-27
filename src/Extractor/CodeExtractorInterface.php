<?php

namespace Luttje\ExampleTester\Extractor;

interface CodeExtractorInterface
{
    public function extractMethodDefinition(string $fullyQualifiedMethodName): string;

    public function extractMethodBody(string $fullyQualifiedMethodName): string;

    public function extractClassDefinition(string $fullyQualifiedClassName): string;

    public function extractClassBody(string $fullyQualifiedClassName): string;
}
