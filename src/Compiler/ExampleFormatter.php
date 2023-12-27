<?php

namespace Luttje\ExampleTester\Compiler;

use Luttje\ExampleTester\Parser\MarkerConfig;

class ExampleFormatter implements ExampleFormatterInterface
{
    public function format(string $example, MarkerConfig $markerConfig): string
    {
        $example = $this->wrapInCodeBlock($example);
        $example = $this->wrapInMarkers($example, $markerConfig);
        $example = $this->prefixEachLine($example, $markerConfig);
        $example = $this->trimSpacesFromEachLine($example);

        return $example . "\n";
    }

    private function wrapInCodeBlock(string $example): string
    {
        return "\n```php\n" . $example . "\n```\n";
    }

    private function wrapInMarkers(string $example, MarkerConfig $markerConfig): string
    {
        return $markerConfig->getStartMarker() . "\n" . $example . "\n" . $markerConfig->getEndMarker();
    }

    private function prefixEachLine(string $example, MarkerConfig $markerConfig): string
    {
        $prefix = $markerConfig->getPrefix();

        if ($prefix !== null) {
            $example = $prefix . str_replace("\n", "\n" . $prefix, $example);
        }

        return $example;
    }

    private function trimSpacesFromEachLine(string $example): string
    {
        return preg_replace('/[ \t]+$/m', '', $example);
    }
}
