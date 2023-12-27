<?php

namespace Luttje\ExampleTester\Parser;

interface ReadmeParserInterface
{
    /**
     * @return ReadmeChunk[]
     */
    public function parse(string $readmePath): array;
}
