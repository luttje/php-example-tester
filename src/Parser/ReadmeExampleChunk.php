<?php

namespace Luttje\ExampleTester\Parser;

use Luttje\ExampleTester\Compiler\ExampleFormatterInterface;
use Luttje\ExampleTester\Extractor\CodeExtractorInterface;

class ReadmeExampleChunk extends ReadmeChunk
{
    public function __construct(
        protected string $content,
        protected ExampleFormatterInterface $exampleFormatter,
        protected CodeExtractorInterface $testExtractor
    )
    { }

    public function getContent(): string
    {
        $content = $this->content;

        $markerConfig = $this->getMarkerConfig($content);
        $example = $this->extractExample($markerConfig);

        $formattedExample = $this->exampleFormatter->format($example, $markerConfig);

        return $formattedExample;
    }

    protected function getMarkerConfig(string $content): MarkerConfig
    {
        $startMarker = ReadmeParser::START_MARKER;
        $endMarker = ReadmeParser::END_MARKER;

        $startMarkerPos = strpos($content, $startMarker);
        $endMarkerPos = strpos($content, $endMarker);

        $markerLength = strlen($startMarker);

        $markerContent = substr($content, $startMarkerPos + $markerLength, $endMarkerPos - $startMarkerPos - $markerLength);
        $markerContent = substr($markerContent, 0, strpos($markerContent, '-->'));

        // If it's a JSON string, decode it, otherwise use it as a symbol name
        if (strpos($markerContent, '{') === 0) {
            $markerConfig = json_decode($markerContent, true);
        } else {
            $markerConfig = [];
            $markerConfig['symbol'] = $markerContent;
        }

        $startMarker = substr($content, $startMarkerPos, $markerLength + strlen($markerContent) + 3);
        $endMarker = substr($content, $endMarkerPos, strlen($endMarker));

        $markerConfig['startMarker'] = $startMarker;
        $markerConfig['endMarker'] = $endMarker;

        $markerConfig = MarkerConfig::fromArray($markerConfig);

        return $markerConfig;
    }

    protected function extractExample(MarkerConfig $markerConfig): string
    {
        $symbol = $markerConfig->getSymbol();

        if (class_exists($symbol)) {
            $example = $markerConfig->isShort()
                ? $this->testExtractor->extractClassBody($symbol)
                : $this->testExtractor->extractClassDefinition($symbol);
        } else {
            $example = $markerConfig->isShort()
                ? $this->testExtractor->extractMethodBody($symbol)
                : $this->testExtractor->extractMethodDefinition($symbol);
        }

        return $example;
    }
}
