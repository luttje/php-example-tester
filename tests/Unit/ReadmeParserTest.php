<?php

namespace Luttje\ExampleTester\Tests\Unit;

use Luttje\ExampleTester\Parser\ReadmeChunk;
use Luttje\ExampleTester\Parser\ReadmeExampleChunk;
use Luttje\ExampleTester\Parser\ReadmeParser;
use Luttje\ExampleTester\Tests\TestCase;

/**
 * @group parser
 */
final class ReadmeParserTest extends TestCase
{
    private function getMocks(): array
    {
        $mockFormatter = $this->createMock(\Luttje\ExampleTester\Compiler\ExampleFormatterInterface::class);
        $mockFormatter->method('format')->willReturn('formatted example');

        $mockExtractor = $this->createMock(\Luttje\ExampleTester\Extractor\CodeExtractorInterface::class);
        $mockExtractor->method('extractMethodDefinition')->willReturn('method definition');
        $mockExtractor->method('extractMethodBody')->willReturn('method body');
        $mockExtractor->method('extractClassDefinition')->willReturn('class definition');
        $mockExtractor->method('extractClassBody')->willReturn('class body');

        return [$mockFormatter, $mockExtractor];
    }

    public function testParseWithStartAndEndMarkers()
    {
        $readmeContent = "Normal content\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Example content\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(3, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
    }

    public function testParseWithStartAndEndMarkersAndIgnoreMarkers()
    {
        $readmeContent = "Normal content\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should become an example\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "<!-- #EXAMPLE_COPY_IGNORE_START -->\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should stay as it is\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "<!-- #EXAMPLE_COPY_IGNORE_END -->\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(3, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
    }

    public function testParseWithStartAndEndMarkersAndIgnoreMarkersAndMultipleExamples()
    {
        $readmeContent = "Normal content\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should become an example\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "<!-- #EXAMPLE_COPY_IGNORE_START -->\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should stay as it is\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "<!-- #EXAMPLE_COPY_IGNORE_END -->\n" .
                         "<!-- #EXAMPLE_COPY_START = 'example2' -->\n" .
                         "Should become another example\n" .
                         "<!-- #EXAMPLE_COPY_END -->\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(5, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[3]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[4]);
    }

    public function testParseWhereMarkerPrefixedWithWhitespace()
    {
        $readmeContent = "Normal content\n" .
                         "    <!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should become an example\n" .
                         "    <!-- #EXAMPLE_COPY_END -->\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(3, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
    }

    public function testParseWhereMarkerPrefixedWithWhitespaceAndNewline()
    {
        $readmeContent = "Normal content\n" .
                         "\n" .
                         "    <!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "Should become an example\n" .
                         "    <!-- #EXAMPLE_COPY_END -->\n" .
                         "\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(3, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
    }

    public function testParseWhereMarkerPrefixedWithBlockQuote()
    {
        $readmeContent = "Normal content\n" .
                         "> <!-- #EXAMPLE_COPY_START = 'example1' -->\n" .
                         "> Should become an example\n" .
                         "> <!-- #EXAMPLE_COPY_END -->\n" .
                         "More content";

        [$mockFormatter, $mockExtractor] = $this->getMocks();

        $parser = new ReadmeParser($mockFormatter, $mockExtractor);
        $chunks = $parser->parse($readmeContent);

        $this->assertCount(3, $chunks);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[0]);
        $this->assertInstanceOf(ReadmeExampleChunk::class, $chunks[1]);
        $this->assertInstanceOf(ReadmeChunk::class, $chunks[2]);
    }
}
