<?php

namespace Luttje\ExampleTester\Compiler;

use Luttje\ExampleTester\Extractor\CodeExtractor;
use Luttje\ExampleTester\Parser\ReadmeParser;
use Luttje\ExampleTester\Parser\ReadmeParserInterface;

class ReadmeCompiler implements ReadmeCompilerInterface
{
    public function __construct(
        private ?ReadmeParserInterface $readmeParser = null,
    ) {
        $this->readmeParser = $readmeParser ?? new ReadmeParser(
            new ExampleFormatter(),
            new CodeExtractor()
        );
    }

    public function compile(string $input, mixed $outputHandle): void
    {
        $chunks = $this->readmeParser->parse($input);

        $content = '';
        foreach ($chunks as $chunk) {
            $content .= $chunk->getContent();
        }

        // truncate file
        fwrite($outputHandle, $content);
    }
}
