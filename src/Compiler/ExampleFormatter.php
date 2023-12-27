<?php

namespace Luttje\ExampleTester\Compiler;

use Luttje\ExampleTester\Parser\MarkerConfig;

class ExampleFormatter implements ExampleFormatterInterface
{
    /**
     * Wraps the example in a markdown code block for PHP
     * and adds the start and end markers back in.
     */
    public function format(string $example, MarkerConfig $markerConfig): string
    {
        $example = "\n```php\n" . $example . "\n```\n";

        $example = $markerConfig->getStartMarker() . "\n" . $example . "\n" . $markerConfig->getEndMarker();

        $prefix = $markerConfig->getPrefix();

        if ($prefix !== null) {
            $example = $prefix . str_replace("\n", "\n" . $prefix, $example);
        }

        return $example . "\n";
    }
}
