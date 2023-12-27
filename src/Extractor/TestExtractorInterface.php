<?php

namespace Luttje\ExampleTester\Extractor;

interface TestExtractorInterface
{
    public function extractMethodBody(string $fullyQualifiedMethodName): string;
}
