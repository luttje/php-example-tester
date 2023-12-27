<?php

namespace Luttje\ExampleTester\Parser;

use Luttje\ExampleTester\Compiler\ExampleFormatterInterface;
use Luttje\ExampleTester\Extractor\CodeExtractorInterface;

class ReadmeParser implements ReadmeParserInterface
{
    public const COPY_MARKER_START = '<!-- #EXAMPLE_COPY_START = ';
    public const COPY_MARKER_END = '<!-- #EXAMPLE_COPY_END -->';

    public const IGNORE_MARKER_START = '<!-- #EXAMPLE_COPY_IGNORE_START -->';
    public const IGNORE_MARKER_END = '<!-- #EXAMPLE_COPY_IGNORE_END -->';

    public function __construct(
        protected ExampleFormatterInterface $exampleFormatter,
        protected CodeExtractorInterface $testExtractor
    )
    { }

    protected function isStartMarker(string $line): bool
    {
        return strpos($line, self::COPY_MARKER_START) !== false;
    }

    protected function isEndMarker(string $line): bool
    {
        return strpos($line, self::COPY_MARKER_END) !== false;
    }

    protected function isIgnoreStartMarker(string $line): bool
    {
        return strpos($line, self::IGNORE_MARKER_START) !== false;
    }

    protected function isIgnoreEndMarker(string $line): bool
    {
        return strpos($line, self::IGNORE_MARKER_END) !== false;
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

        $isIgnoringMarkers = false;

        foreach ($lines as $line) {
            if ($this->isIgnoreStartMarker($line)) {
                $isIgnoringMarkers = true;
            } elseif ($this->isIgnoreEndMarker($line)) {
                $isIgnoringMarkers = false;
            } elseif (!$isIgnoringMarkers) {
                if ($this->isStartMarker($line)) {
                    $chunks[] = $this->makeChunk($chunk);

                    $chunk = '';
                } elseif ($this->isEndMarker($line)) {
                    $chunk .= $line;

                    $chunks[] = $this->makeExampleChunk($chunk);

                    $chunk = '';

                    continue;
                }
            }

            $chunk .= $line . "\n";
        }

        // Trim off the last newline
        $chunk = substr($chunk, 0, -1);

        $chunks[] = $this->makeChunk($chunk);

        return $chunks;
    }
}
