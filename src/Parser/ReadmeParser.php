<?php

namespace Luttje\ExampleTester\Parser;

use Luttje\ExampleTester\Compiler\ExampleFormatterInterface;
use Luttje\ExampleTester\Extractor\TestExtractorInterface;

class ReadmeParser implements ReadmeParserInterface
{
    public const START_MARKER = '<!-- #EXAMPLE_COPY_START = ';
    public const END_MARKER = '<!-- #EXAMPLE_COPY_END -->';

    public function __construct(
        protected ExampleFormatterInterface $exampleFormatter,
        protected TestExtractorInterface $testExtractor
    )
    { }

    protected function isStartMarker(string $line): bool
    {
        return strpos($line, self::START_MARKER) !== false;
    }

    protected function isEndMarker(string $line): bool
    {
        return strpos($line, self::END_MARKER) !== false;
    }

    protected function makeChunk(string $chunk): ReadmeChunk
    {
        return new ReadmeChunk($chunk);
    }

    protected function makeExampleChunk(string $chunk): ReadmeExampleChunk
    {
        return new ReadmeExampleChunk($chunk, $this->exampleFormatter, $this->testExtractor);
    }

    /**
     * Parses the README file contents and finds markers
     *
     * Returns an array with ReadmeChunk objects containing unchanged markdown
     * content. The array contains ReadmeExampleChunk objects where markers
     * were found.
     */
    public function parse(string $readme): array
    {
        $chunks = $this->splitIntoChunks($readme);

        return $chunks;
    }

    /**
     * Splits the readme into chunks, using ReadmeChunk objects for unchanged
     * markdown content and ReadmeExampleChunk objects for markdown content
     * containing markers.
     */
    protected function splitIntoChunks(string $readme): array
    {
        $chunks = [];

        $lines = explode("\n", $readme);

        $chunk = '';

        foreach ($lines as $line) {
            if ($this->isStartMarker($line)) {
                $chunks[] = $this->makeChunk($chunk);

                $chunk = $line . "\n";
            } elseif ($this->isEndMarker($line)) {
                $chunk .= $line;

                $chunks[] = $this->makeExampleChunk($chunk);

                $chunk = '';
            } else {
                $chunk .= $line . "\n";
            }
        }

        // Trim off the last newline
        $chunk = substr($chunk, 0, -1);

        $chunks[] = $this->makeChunk($chunk);

        return $chunks;
    }
}
