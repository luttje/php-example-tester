<?php

namespace Luttje\ExampleTester\Compiler;

use Luttje\ExampleTester\Extractor\TestExtractor;
use Luttje\ExampleTester\Parser\ReadmeParser;
use Luttje\ExampleTester\Parser\ReadmeParserInterface;

class ReadmeCompiler implements ReadmeCompilerInterface
{
    public function __construct(
        private ?ReadmeParserInterface $readmeParser = null,
    ) {
        $this->readmeParser = $readmeParser ?? new ReadmeParser(
            new ExampleFormatter(),
            new TestExtractor()
        );
    }

    public function compile(string $inputFile, string $outputFile): void
    {
        $input = file_get_contents($inputFile);
        $chunks = $this->readmeParser->parse($input);

        $content = '';
        foreach ($chunks as $chunk) {
            $content .= $chunk->getContent();
        }

        file_put_contents($outputFile, $content);
    }
}
